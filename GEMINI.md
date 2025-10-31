# Guia do Projeto para Gemini

Este documento fornece um guia para a IA Gemini sobre a estrutura, tecnologias e comandos comuns deste projeto.

## 1. Visão Geral do Projeto

Este é um projeto web full-stack construído com o framework **Symfony** para o back-end e **Webpack Encore** para o gerenciamento de assets do front-end. Ele utiliza **Docker** e **FrankenPHP** para o ambiente de desenvolvimento, com persistência de dados gerenciada pelo **Doctrine ORM**.

## 2. Tecnologias Principais

- **Back-end:** PHP 8.4+, Symfony 7.3+
- **Front-end:** JavaScript, Webpack Encore, SCSS, Bootstrap 4, jQuery
- **Banco de Dados:** Doctrine ORM (provavelmente PostgreSQL ou MySQL, verificar `compose.yaml`)
- **Servidor:** FrankenPHP
- **Ambiente:** Docker / Docker Compose

## 3. Configuração e Instalação

Para configurar o ambiente de desenvolvimento, siga estes passos:

1.  **Iniciar os contêineres Docker:**
    ```bash
    docker-compose up -d --build
    ```

2.  **Instalar dependências do PHP:**
    ```bash
    docker-compose exec php composer install
    ```

3.  **Instalar dependências do Node.js:**
    ```bash
    docker-compose exec php npm install
    ```

4.  **Executar as migrações do banco de dados:**
    ```bash
    docker-compose exec php php bin/console doctrine:migrations:migrate
    ```

5.  **Compilar os assets do front-end:**
    ```bash
    docker-compose exec php npm run build
    ```

## 4. Comandos Comuns

- **Executar comandos do Symfony:**
  ```bash
  docker-compose exec php php bin/console <comando>
  ```

- **Compilar assets para desenvolvimento:**
  ```bash
  docker-compose exec php npm run dev
  ```

- **Observar mudanças nos assets (watch):**
  ```bash
  docker-compose exec php npm run watch
  ```

- **Limpar o cache do Symfony:**
  ```bash
  docker-compose exec php php bin/console cache:clear
  ```

- **Testes:** O comando para rodar os testes não está definido nos scripts do `composer.json`. É provável que seja `phpunit` ou um comando similar. A suíte de testes está localizada no diretório `tests/`.

## 5. Estrutura do Projeto

- `src/`: Contém todo o código-fonte da aplicação PHP (Entidades, Controllers, Repositórios, etc.).
- `templates/`: Contém os templates Twig para renderização do front-end.
- `assets/`: Contém os assets brutos do front-end (JavaScript, SCSS, imagens).
- `public/`: É o diretório raiz da web. Contém o `index.php` e os assets compilados.
- `config/`: Arquivos de configuração do Symfony.
- `migrations/`: Migrações do banco de dados geradas pelo Doctrine.
- `compose.yaml`: Define os serviços, redes e volumes do Docker.
- `webpack.config.js`: Arquivo de configuração para o Webpack Encore.
