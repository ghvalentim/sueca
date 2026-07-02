<?php

require_once __DIR__ . '/../vendor/autoload.php'; 
// Carrega automaticamente as classes usando o autoloader do Composer

use Dotenv\Dotenv;
// Carrega as variáveis de ambiente do arquivo .env

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
// Cria uma instância do Dotenv apontando para o diretório raiz do projeto

$dotenv->load();
// Carrega as variáveis de ambiente definidas no arquivo .env para a superglobal $_ENV

session_start();
// Inicia a sessão do PHP para gerenciar dados de sessão do usuário

//Importa a classe Router do namespace Router
use Router\Router;

$router = new Router();
// Cria uma instância do roteador para gerenciar as rotas da aplicação

(require __DIR__ . '/../routes/web.php')($router);
// Carrega as rotas definidas no arquivo web.php e passa a instância do roteador para registrar as rotas

$router->dispatch();
// Dispara o roteador para processar a requisição atual e chamar o controlador apropriado

// Define o fuso horário para Portugal
date_default_timezone_set('Europe/Lisbon');


?>