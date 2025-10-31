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

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
    }

    /**
     * Tenta fazer uma requisição HTTP, com retries em caso de erro 429 (Too Many Requests).
     * Implementa um backoff exponencial para lidar com a limitação de taxa.
     */
    private function getWithRetry(string $url, int $retries = 5): ResponseInterface {
        $attempt = 0;
        while ($attempt < $retries) {
            try {
                return $this->httpClient->request('GET', $url);
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

    public function __invoke(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Iniciando a importação de dados da FIPE...');

        try {
            // Passo 1: Obter todas as marcas
            $io->text('Obtendo marcas da FIPE...');
            // Correção: Remove a formatação de link do Markdown da URL
            $response = $this->getWithRetry('https://fipe.parallelum.com.br/api/v2/cars/brands');
            $marcas = $response->toArray();
            $totalMarcas = count($marcas);
            $io->progressStart($totalMarcas);

            // Passo 2: Processar cada marca
            foreach ($marcas as $dadosMarca) {
                sleep(2);
                // Busca a marca no banco de dados. Se não existir, cria uma nova.
                $carMake = $this->entityManager->getRepository(CarMake::class)->findOneBy(['name' => $dadosMarca['name']]);

                if (!$carMake) {
                    $carMake = new CarMake();
                    $carMake->setName($dadosMarca['name']);
                    $this->entityManager->persist($carMake);
                }

                // Passo 3: Obter modelos para a marca atual
                // Correção: Remove a formatação de link do Markdown da URL
                $responseModelos = $this->getWithRetry("https://fipe.parallelum.com.br/api/v2/cars/brands/{$dadosMarca['code']}/models");
                $modelosResponse = $responseModelos->toArray();

                $this->entityManager->flush();
                // Passo 4: Processar cada modelo
                foreach ($modelosResponse as $dadosModelo) {
                    sleep(2);
                    // Busca o modelo no banco de dados. Se não existir, cria um novo.
                    $carModel = $this->entityManager->getRepository(CarModel::class)->findOneBy(['name' => $dadosModelo['name'], 'car_make' => $carMake]);

                    if (!$carModel) {
                        $carModel = new CarModel();
                        $carModel->setName($dadosModelo['name']);
                        $carModel->setCarMake($carMake);
                        $this->entityManager->persist($carModel);
                    }

                    // Passo 5: Obter anos para o modelo atual
                    // Correção: Remove a formatação de link do Markdown da URL
                    $responseAnos = $this->getWithRetry("https://fipe.parallelum.com.br/api/v2/cars/brands/{$dadosMarca['code']}/models/{$dadosModelo['code']}/years");
                    $anos = $responseAnos->toArray();

                    // Passo 6: Processar cada ano
                    foreach ($anos as $dadosAno) {
                        // Busca o ano no banco de dados. Se não existir, cria um novo.
                        // Correção: A chave do ano é 'nome', não 'name'
                        $carYear = $this->entityManager->getRepository(CarYear::class)->findOneBy(['year' => (int)$dadosAno['name'], 'car_model' => $carModel]);

                        if (!$carYear) {
                            $carYear = new CarYear();
                            $carYear->setYear((int)$dadosAno['name']);
                            $carYear->setCarModel($carModel);
                            $this->entityManager->persist($carYear);
                        }
                    }
                }
                // Otimização de Performance: Chama flush() apenas uma vez por marca,
                // agrupando todas as inserções e atualizações.
                $this->entityManager->flush();
                $io->progressAdvance();
            }

            $io->progressFinish();
            $io->success('Importação de dados da FIPE concluída com sucesso!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Ocorreu um erro durante a importação: ' . $e->getMessage());
            return Command::FAILURE;
        } finally {
            sleep(2);
        }
    }
}
