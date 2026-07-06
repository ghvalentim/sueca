<?php
// ==========================================
// [NOVO] MODO DETETIVE: Mostrar todos os erros
// ==========================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Carregar dependências do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Carregar variáveis de ambiente
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 3. Iniciar Sessões
session_start();

// 4. Configuração Global de CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 5. Configurar Fuso Horário
date_default_timezone_set('Europe/Lisbon');

// 6. Inicializar o Router e processar o pedido
use Router\Router;

$router = new Router();
(require __DIR__ . '/../routes/web.php')($router);
$router->dispatch();

?>