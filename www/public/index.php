<?php
// Iniciar sessões (necessário para guardar o estado de login no Portal PHP)
session_start();

require_once __DIR__ . '/../vendor/autoload.php'; // PHPMailer e outras dependências

// Autoloader manual simples (Evita usar o Composer no Portal, mantendo a simplicidade de PHP Vanilla)
spl_autoload_register(function ($class_name) {
    // Transforma namespaces como "Controller\AuthController" no caminho "Controller/AuthController.php"
    $path = __DIR__ . '/../src/' . str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// Sistema de Rotas Básico (via query string: ?action=...)
$action = $_GET['action'] ?? 'home';

// Função auxiliar para instanciar o controlador com segurança
$getAuthController = function() {
    if (class_exists('\Controller\AuthController')) {
        return new \Controller\AuthController();
    }
    die("Erro: Controlador de autenticação ainda não foi criado na pasta src/Controller.");
};

switch ($action) {
    case 'register':
        $authController = $getAuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->register();
        } else {
            $authController->showRegister();
        }
        break;

    case 'activate':
        // Rota para a ativação da conta via link de email (Fluxo 2)
        $authController = $getAuthController();
        $authController->activate();
        break;

    case 'login':
        // Rota para o Login (Fluxo 3)
        $authController = $getAuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
        } else {
            $authController->showLogin();
        }
        break;

    case 'logout':
        // Rota para o Logout (Fluxo 4)
        $authController = $getAuthController();
        $authController->logout();
        break;
        
    case 'home':
    default:
        // Página Inicial (Lobby temporário)
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
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Interface para Utilizador Autenticado -->
                        <p class="lead mb-4">Bem-vindo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
                        <p class="text-muted mb-4">Sessão iniciada com sucesso no Portal.</p>
                        <div class="d-grid gap-3">
                            <!-- Mais tarde adicionaremos aqui a lista de salas disponíveis -->
                            <a href="?action=logout" class="btn btn-outline-danger btn-lg">Terminar Sessão</a>
                        </div>
                    <?php else: ?>
                        <!-- Interface para Visitante -->
                        <p class="lead mb-4">Bem-vindo ao Portal Web!</p>
                        <div class="d-grid gap-3">
                            <a href="?action=login" class="btn btn-success btn-lg">Iniciar Sessão</a>
                            <a href="?action=register" class="btn btn-outline-primary btn-lg">Criar Conta</a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </body>
        </html>
        <?php
        break;
}