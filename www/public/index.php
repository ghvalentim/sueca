<?php
// Iniciar sessões (necessário para guardar o estado de login no Portal PHP)
session_start();

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

switch ($action) {
    case 'register':
        if (class_exists('\Controller\AuthController')) {
            $authController = new \Controller\AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->register();
            } else {
                $authController->showRegister();
            }
        }
        break;

    case 'activate':
        if (class_exists('\Controller\AuthController')) {
            $authController = new \Controller\AuthController();
            $authController->activate();
        }
        break;

    case 'login':
        if (class_exists('\Controller\AuthController')) {
            $authController = new \Controller\AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->login();
            } else {
                $authController->showLogin();
            }
        }
        break;

    case 'logout':
        if (class_exists('\Controller\AuthController')) {
            $authController = new \Controller\AuthController();
            $authController->logout();
        }
        break;

    case 'profile':
        // Rota para o Perfil (Sprint 2)
        if (class_exists('\Controller\ProfileController')) {
            $profileController = new \Controller\ProfileController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $profileController->updatePassword();
            } else {
                $profileController->show();
            }
        } else {
            echo "Erro: Controlador de perfil ainda não foi criado na pasta src/Controller.";
        }
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
            <link rel="stylesheet" href="../css/style.css">
        </head>
        <body>
            <div class="container text-center mt-5">
                <h1 class="text-white fw-bold mb-4">Lobby Jogosueca</h1>
                <div class="card shadow-sm mx-auto p-5" style="max-width: 500px;">
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Interface para Utilizador Autenticado -->
                        <p class="lead mb-4">Bem-vindo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
                        
                        <?php if (isset($_SESSION['jwt_token'])): ?>
                            <div class="alert alert-success">
                                ✅ Sessão iniciada e <strong>Token JWT</strong> obtido da API com sucesso!
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                ⚠️ Sessão iniciada localmente, mas sem Token JWT.
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-3 mt-4">
                            <!-- Botão para a Sprint 2 (Perfil) -->
                            <a href="?action=profile" class="btn btn-hearts-card btn-lg text-white">O Meu Perfil</a>
                            
                            <!-- Mais tarde adicionaremos aqui o botão para as Salas -->
                            
                            <a href="?action=logout" class="btn btn-outline-danger btn-lg">Terminar Sessão</a>
                        </div>
                    <?php else: ?>
                        <!-- Interface para Visitante -->
                        <p class="lead mb-4">Bem-vindo ao Portal Web!</p>
                        <div class="d-grid gap-3">
                            <a href="?action=login" class="btn btn-hearts-card btn-lg">Iniciar Sessão</a>
                            <a href="?action=register" class="btn btn-outline-spades btn-lg">Criar Conta</a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </body>
        </html>
        <?php
        break;
}