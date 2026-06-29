<?php
// Iniciar sessões para o futuro (necessário para guardar o JWT no Portal PHP)
session_start();

// Autoloader manual simples (Evita usar o Composer no Portal, mantendo a simplicidade e as regras de PHP Vanilla)
spl_autoload_register(function ($class_name) {
    // Transforma namespaces como "Controller\AuthController" no caminho "Controller/AuthController.php"
    $path = __DIR__ . '/../src/' . str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// Sistema de Rotas Básico (via query string: ?action=...)
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'register':
        // Apenas instanciamos o controlador se a classe já existir para evitar erros fatais
        if (class_exists('\Controller\AuthController')) {
            $authController = new \Controller\AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->register();
            } else {
                $authController->showRegister();
            }
        } else {
            echo "Erro: Controlador de autenticação ainda não foi criado na pasta src/Controller.";
        }
        break;
        
    case 'home':
    default:
        // Página Inicial (Lobby) temporária do Portal
        ?>
        <!DOCTYPE html>
        <html lang="pt">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Lobby - Jogosueca</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container text-center mt-5">
                <h1 class="text-success fw-bold mb-4">Lobby Jogosueca</h1>
                <div class="card shadow-sm mx-auto p-5" style="max-width: 500px;">
                    <p class="lead mb-4">Bem-vindo ao Portal Web!</p>
                    <div class="d-grid gap-3">
                        <a href="?action=register" class="btn btn-primary btn-lg">Criar Conta</a>
                        <!-- Futuramente adicionaremos aqui o botão de Login -->
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        break;
}