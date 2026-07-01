# TP-SUECA

Projeto desenvolvido no âmbito da Unidade Curricular de **Desenvolvimento Web**.

O objetivo é implementar uma plataforma para gestão e execução de partidas do jogo **Sueca**, recorrendo a uma arquitetura distribuída composta por um Portal Web e uma API especializada.

---

# Arquitetura

O sistema encontra-se dividido em três serviços independentes:

* **Portal Web** (PHP MVC)
* **API** (Laravel)
* **Base de Dados** (MySQL)

Todos os serviços são executados em containers Docker e comunicam através da rede interna definida no Docker Compose.

```text
                Utilizador
                     │
                     ▼
           Portal Web (PHP MVC)
                     │
         ┌───────────┴───────────┐
         │                       │
         ▼                       ▼
    Base de Dados           API Laravel
        MySQL              Motor da Sueca
```

---

# Responsabilidades

## Portal Web

O Portal constitui a interface principal da aplicação e é responsável por:

* Autenticação dos utilizadores;
* Registo e ativação de contas por email;
* Gestão de sessões;
* Gestão do perfil;
* Gestão de salas (Lobby);
* Comunicação com a API;
* Interface gráfica.

---

## API

A API implementa exclusivamente a lógica do jogo.

As suas responsabilidades incluem:

* Autenticação entre Portal e API através de JWT;
* Gestão das partidas;
* Validação das regras da Sueca;
* Estado da partida;
* Cálculo da pontuação;
* Exposição dos endpoints REST.

---

## Base de Dados

O Portal e a API partilham a mesma instância MySQL.

A separação de responsabilidades é efetuada ao nível da arquitetura da aplicação e da organização das tabelas, evitando duplicação de informação.

---

# Estrutura do Projeto

```text
.
├── api/
├── database/
├── www/
├── compose.yml
└── README.md
```

## API

Aplicação Laravel responsável pelo motor do jogo.

Principais componentes:

* Controllers
* Models
* Configuração JWT
* Rotas REST
* Migrations
* Testes

---

## Portal

Aplicação PHP MVC responsável pela interação com o utilizador.

Estrutura:

* Controllers
* Models
* Views
* Recursos estáticos
* Integração com a API

---

# Tecnologias

## Backend

* PHP 8
* Laravel
* MySQL
* Apache

## Infraestrutura

* Docker
* Docker Compose

## Autenticação

* JSON Web Token (JWT)
* PHPMailer

---

# Funcionalidades Implementadas

* Infraestrutura Docker
* Portal MVC
* Sistema de autenticação
* Login e Logout
* Registo de utilizadores
* Ativação de contas por email
* Perfil de utilizador
* Estrutura inicial do Lobby
* Infraestrutura da API
* Estrutura inicial do motor do jogo

---

# Funcionalidades em Desenvolvimento

* Configuração completa do JWT
* Integração Portal ⇄ API
* Motor da Sueca
* Gestão das salas
* Frontend assíncrono

---

# Fluxo de Desenvolvimento

O projeto segue GitHub Flow.

Cada funcionalidade é desenvolvida através do seguinte ciclo:

1. Criação de uma Issue.
2. Criação de uma branch `feature/*`.
3. Desenvolvimento da funcionalidade.
4. Commits semânticos (Conventional Commits).
5. Pull Request para a branch `dev`.
6. Revisão.
7. Merge.
8. Encerramento automático da Issue.

---

# Versionamento

O projeto utiliza Semantic Versioning.

As integrações são realizadas na branch `dev`.

A branch `main` contém apenas versões estáveis.

---

# Estado Atual

## Concluído

* Infraestrutura do projeto
* Portal MVC
* Sistema de autenticação
* Login e Logout

## Em desenvolvimento

* JWT
* Comunicação Portal ⇄ API

## Planeado

* Perfil
* Lobby
* Motor da Sueca
* Frontend Assíncrono

---

# Licença

Projeto académico desenvolvido para fins educativos no âmbito da Unidade Curricular de Desenvolvimento Web.
