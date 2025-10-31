<?php

namespace App\Command;

use App\Entity\CarMake;
use App\Entity\CarModel;
use App\Entity\CarYear;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AsCommand(name: 'app:import-fipe', description: 'Importa marcas, modelos e anos de carros da API da FIPE com retentativas em caso de falha.')]
class ImportFipeCommand extends Command {
    private $entityManager;
    private $httpClient;
    private $fipeApiToken;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient, string $fipeApiToken) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->fipeApiToken = $fipeApiToken;
    }

    /**
     * Tenta fazer uma requisição HTTP, com retries em caso de erro 429 (Too Many Requests).
     * Implementa um backoff exponencial para lidar com a limitação de taxa.
     */
    private function getWithRetry(string $url, int $retries = 5): ResponseInterface {
        $attempt = 0;
        while ($attempt < $retries) {
            try {
                return $this->httpClient->request('GET', $url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->fipeApiToken,
                    ],
                ]);
            } catch (HttpExceptionInterface $e) {
                if ($e->getResponse()->getStatusCode() === 429) {
                    $attempt++;
                    $sleepTime = 15 * ($attempt); // Backoff exponencial: 15s, 30s, 45s...
                    sleep($sleepTime);
                } else {
                    // Re-throw if it's not a 429 error
                    throw $e;
                }
            } catch (TransportExceptionInterface $e) {
                // Catch network-related errors and retry
                $attempt++;
                $sleepTime = 15 * ($attempt); // Backoff exponencial
                sleep($sleepTime);
            }
        }
        throw new \Exception("Falha na requisição para a URL: $url após $retries tentativas.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        ini_set('memory_limit', '1024M');
        $io = new SymfonyStyle($input, $output);
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $io->title('Iniciando a importação de dados da FIPE...');

        try {
            // Passo 1: Obter todas as marcas
            $io->text('Obtendo marcas da FIPE...');
            $response = $this->getWithRetry('https://fipe.parallelum.com.br/api/v2/cars/brands');
            $marcas = $response->toArray();
            $totalMarcas = count($marcas);
            $io->progressStart($totalMarcas);

            // Passo 2: Processar cada marca
            foreach ($marcas as $dadosMarca) {
                $carMake = $this->entityManager->getRepository(CarMake::class)->findOneBy(['name' => $dadosMarca['name']]);

                if (!$carMake) {
                    $carMake = new CarMake();
                    $carMake->setName($dadosMarca['name']);
                    $this->entityManager->persist($carMake);
                }

                // Passo 3: Obter modelos para a marca atual
                $responseModelos = $this->getWithRetry("https://fipe.parallelum.com.br/api/v2/cars/brands/{$dadosMarca['code']}/models");
                $modelosResponse = $responseModelos->toArray();

                // Passo 4: Processar cada modelo
                foreach ($modelosResponse as $dadosModelo) {
                    $carModel = $this->entityManager->getRepository(CarModel::class)->findOneBy(['name' => $dadosModelo['name'], 'car_make' => $carMake]);

                    if (!$carModel) {
                        $carModel = new CarModel();
                        $carModel->setName($dadosModelo['name']);
                        $carModel->setCarMake($carMake);
                        $this->entityManager->persist($carModel);
                    }

                    // Passo 5: Obter anos para o modelo atual
                    $responseAnos = $this->getWithRetry("https://fipe.parallelum.com.br/api/v2/cars/brands/{$dadosMarca['code']}/models/{$dadosModelo['code']}/years");
                    $anos = $responseAnos->toArray();

                    // Passo 6: Processar cada ano
                    foreach ($anos as $dadosAno) {
                        $carYear = $this->entityManager->getRepository(CarYear::class)->findOneBy(['year' => (int)$dadosAno['name'], 'car_model' => $carModel]);

                        if (!$carYear) {
                            $carYear = new CarYear();
                            $carYear->setYear((int)$dadosAno['name']);
                            $carYear->setCarModel($carModel);
                            $this->entityManager->persist($carYear);
                        }
                    }
                }
                $this->entityManager->flush();
                $this->entityManager->clear();
                $io->progressAdvance();
            }

            $io->progressFinish();
            $io->success('Importação de dados da FIPE concluída com sucesso!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Ocorreu um erro durante a importação: ' . $e->getMessage());
            return Command::FAILURE;
        } finally {
            // Mantendo uma pequena pausa no final por segurança
        }
    }
}
