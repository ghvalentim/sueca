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
        
    case 'create_room':
        // Rota para criar nova sala (Sprint 3)
        if (class_exists('\Controller\RoomController')) {
            (new \Controller\RoomController())->create();
        }
        break;

    case 'join_room':
        // Rota para entrar numa sala existente (Sprint 3)
        if (class_exists('\Controller\RoomController')) {
            (new \Controller\RoomController())->join();
        }
        break;

    case 'room':
        // Rota para visualizar o interior da sala / mesa de jogo (Sprint 3)
        if (class_exists('\Controller\RoomController')) {
            (new \Controller\RoomController())->showRoom();
        }
        break;

    case 'home':
    default:
        // Delegação da Página Inicial para o RoomController (Sprint 3)
        // Todo o HTML provisório foi removido. A apresentação é feita pelo Lobby!
        if (class_exists('\Controller\RoomController')) {
            (new \Controller\RoomController())->showLobby();
        } else {
            echo "Erro: O Controlador de Salas ainda não foi criado na pasta src/Controller.";
        }
        break;
}