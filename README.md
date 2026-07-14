# 🃏 Sueca Online

Projeto desenvolvido no âmbito da unidade curricular **Desenvolvimento para a Web II**.

A aplicação implementa uma solução web completa para o jogo **Sueca Online Multijogador**, organizada em três módulos:

- **Portal Web** em **PHP Vanilla** com arquitetura MVC
- **Motor de Jogo** em **Laravel** como API REST
- **Interface de Cliente** em **JavaScript Vanilla**

O objetivo do projeto é demonstrar uma arquitetura híbrida com autenticação, gestão de utilizadores, lobby de salas e motor de jogo com comunicação assíncrona.

## Funcionalidades

### Portal Web
- Registo de utilizadores
- Login e logout
- Recuperação de password
- Ativação de conta por email
- Gestão de perfil com avatar e biografia
- Lobby com listagem de salas
- Criação e entrada em salas
- Sala de espera
- Internacionalização em português e inglês

### Motor de Jogo
- Autenticação stateless com JWT
- Gestão de salas
- Início da partida com 4 jogadores
- Distribuição de 40 cartas e 10 cartas por jogador
- Definição de trunfo
- Validação de jogadas legais e ilegais
- Resolução de vazas
- Cálculo de pontuação
- Determinação da equipa vencedora

### Interface de Jogo
- Renderização do tabuleiro sem refresh
- Seleção e jogada de cartas com clique
- Atualização automática do estado do jogo
- Manutenção do estado após recarregar a página
- Ecrã final com vencedor e pontuação

## Tecnologias

- PHP 8+
- Laravel 13
- MySQL
- JavaScript Vanilla
- HTML5
- CSS3
- Bootstrap 5
- Apache
- JWT
- PDO com Prepared Statements
- cURL
- Fetch API

## Estrutura geral

```text
.
├── api/            # Motor de jogo em Laravel
├── app/            # Portal Web em PHP vanilla
├── public/         # Assets públicos
├── database/       # Scripts e estrutura da BD
├── docker/         # Configuração dos containers
├── compose.yml     # Orquestração dos serviços
└── README.md
```

## Requisitos

- Docker e Docker Compose
- ou, em alternativa:
  - PHP 8+
  - Composer
  - MySQL
  - Apache

## Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/ghvalentim/sueca.git
cd sueca
```

### 2. Subir os serviços

```bash
docker compose up -d --build
```

### 3. Instalar dependências da API

```bash
cd api
composer install
```

### 4. Configurar o ambiente

Criar o ficheiro `.env` para o Portal Web e para a API com as credenciais corretas de:

- base de dados
- URL da aplicação
- JWT
- SMTP
- restantes variáveis do projeto

### 5. Executar as migrations da API

```bash
php artisan migrate
```

## Acesso local

- **Portal Web:** `http://localhost:8000`
- **API Laravel:** `http://localhost:8001`

## Fluxo de autenticação

O login é feito no Portal Web, que envia as credenciais para a API através de **cURL**. Se os dados forem válidos, a API devolve um **JWT**, que fica guardado na sessão do utilizador para ser reutilizado nos pedidos ao motor de jogo.

## Regras de implementação seguidas

- Portal Web desenvolvido em **PHP vanilla**
- Motor de Jogo desenvolvido em **Laravel**
- Interface de Cliente desenvolvida em **JavaScript vanilla**
- Persistência centralizada numa única base de dados MySQL
- Comunicação assíncrona com a API através de **Fetch API**
- Comunicação servidor-a-servidor com **cURL** no login
- CORS configurado para comunicação entre módulos
- Uso de sessões, JWT e validação de dados

## Entrega final

Este repositório corresponde à versão final do projeto.

Tag de entrega: `v-final`

## Autor

**Gabriel Valentim Carvalho**

Projeto académico desenvolvido para a unidade curricular **Desenvolvimento para a Web II**.
