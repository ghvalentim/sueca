# TP-SUECA

Projeto desenvolvido no âmbito da unidade curricular de Desenvolvimento Web.

## Objetivo

Desenvolver uma plataforma para gestão e execução de partidas do jogo Sueca utilizando uma arquitetura composta por um Portal Web e uma API.

## Arquitetura

O sistema é composto por três serviços Docker:

* Portal Web (PHP MVC)
* API (Laravel)
* Base de Dados MySQL

```
Utilizador
      │
      ▼
Portal Web (PHP MVC)
      │
      ├───────────────┐
      ▼               ▼
 Base de Dados      API Laravel
                    Motor do Jogo
```

## Responsabilidades

### Portal

* Autenticação de utilizadores
* Gestão de sessões
* Registo
* CRUDs administrativos
* Interface do utilizador
* Comunicação com a API

### API

* Motor do jogo da Sueca
* Validação das jogadas
* Gestão das partidas
* Cálculo da pontuação
* Regras do jogo

## Base de Dados

Existe apenas uma base de dados.

A separação entre Portal e API é feita através da responsabilidade de cada aplicação e da organização das tabelas, evitando duplicação de informação.

## Tecnologias

### Portal

* PHP
* MVC
* Apache

### API

* Laravel
* PHP

### Base de Dados

* MySQL

### Infraestrutura

* Docker
* Docker Compose

## Estrutura do Projeto

```
api/
database/
www/
compose.yml
```

## Fluxo de Desenvolvimento

O projeto segue GitHub Flow.

Cada funcionalidade é desenvolvida através de:

1. Issue
2. Branch
3. Commits
4. Pull Request
5. Merge
6. Encerramento da Issue

## Versionamento

O projeto utiliza Semantic Versioning.

## Licença

Projeto académico desenvolvido para fins educativos.
