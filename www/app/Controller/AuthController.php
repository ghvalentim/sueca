<?php
namespace Controller;

use Model\User;
use Controller\MailController;

class AuthController {
    
    // Mostra o formulário de registo (Fluxo 1)
    public function showRegister() {
        require_once __DIR__ . '/../../src/view/register.php';
    }

    // Processa a submissão do formulário de registo (Fluxo 1)
    public function register() {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validação simples
        if (empty($username) || empty($email) || empty($password)) {
            $error = "Todos os campos são de preenchimento obrigatório.";
            require_once __DIR__ . '/../View/register.php';
            return;
        }

        $userModel = new User();
        $mailController = new MailController();

        // Regra de Negócio: O username e email devem ser únicos
        if ($userModel->checkExists($username, $email)) {
            $error = "O username ou email já se encontram registados.";
            require_once __DIR__ . '/../../src/view/register.php';
            return;
        }

        // Criar o utilizador
        $token = $userModel->create($username, $email, $password);

        if ($token) {
            // 1. Gerar o link de ativação com a NOVA rota (/activate)
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $activationLink = $scheme . '://' . $host . '/activate?token=' . $token;
            
            // 2. Tentar enviar via PHPMailer
            if ($mailController->sendActivationEmail($email, $activationLink)) {
                $success = "Registo efetuado com sucesso! Verifique o seu email para ativar a conta.";
                require_once __DIR__ . '/../../src/view/register.php';
            } else {
                $userModel->deleteAccount($userModel->getLastInsertedId()); // Remove o utilizador criado
                $error = "Erro ao enviar o email de ativação. Por favor, tente novamente mais tarde.";
                require_once __DIR__ . '/../../src/view/register.php';
            }
        } else {
            // Caso $userModel->getLastInsertedId() não exista, tem cuidado com esta linha
            if(method_exists($userModel, 'getLastInsertedId')) {
                $userModel->deleteAccount($userModel->getLastInsertedId());
            }
            $error = "Erro ao criar a conta. Por favor, tente novamente.";
            require_once __DIR__ . '/../../src/view/register.php';
        }
    }

    /** A função activate faz a ativação da conta via URL enviada por email */
    public function activate() {
        $token = $_GET['token'] ?? ''; // Obter o token da query string
                
        if (empty($token)) { // Se o token estiver vazio, mostrar a página de erro
            $error = "Token de ativação inválido."; 
            require_once __DIR__ . '/../../src/view/activation-error.php'; 
            return;
        } else {
            $userModel = new User();
            if ($userModel->activateAccount($token)) { // Se a ativação for bem-sucedida, mostrar a página de sucesso
                $success = "Conta ativada com sucesso! Pode agora efetuar o login.";
                require_once __DIR__ . '/../../src/view/activation-success.php';
                return;
            } else { // Se a ativação falhar, mostrar a página de erro
                $error = "Token de ativação inválido ou a conta já foi ativada.";
                require_once __DIR__ . '/../../src/view/activation-error.php';
                return;
            }
        }
    }

    // Mostra o formulário de Login (Fluxo 3)
    public function showLogin() {
        require_once __DIR__ . '/../../src/view/login.php'; //apenas mostra a View de Login, não processa o Login
    }

   
    public function login() { // Recebe os dados do formulário de login
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = "Preencha todos os campos.";
            require_once __DIR__ . '/../../src/view/login.php';
            return;
        }

        $userModel = new User();
        $user = $userModel->verifyCredentials($username, $password);

        if (!$user) { // Se as credenciais forem inválidas, mostrar a página de login com erro
            $error = "Credenciais inválidas.";
            require_once __DIR__ . '/../../src/view/login.php';
            return;
        }

        
        if ($user['is_active'] == 0) { // Se a conta não estiver ativada, mostrar a página de login com erro
            $error = "A sua conta ainda não foi ativada. Verifique o seu email.";
            require_once __DIR__ . '/../../src/view/login.php';
            return;
        }

      
        $_SESSION['user_id'] = $user['id']; // Guardar o ID do utilizador na sessão
        $_SESSION['username'] = $user['username']; // Guardar o username do utilizador na sessão

        try {
            // 1. Tentar obter o Token JWT da API
            $jwtToken = $userModel->getJwtToken($username, $password);
            
            $_SESSION['jwt_token'] = $jwtToken; // Guardar o Token JWT na sessão

            if ($jwtToken === null) { // Se não for possível obter o Token JWT, mostrar a página de login com erro
                $error = "Erro ao obter o Token JWT da API.";
                session_destroy(); 
                require_once __DIR__ . '/../../src/view/login.php';
                return;
            }
        
        } catch (\Exception $e) { // Se ocorrer algum erro ao comunicar com a API, mostrar a página de login com erro
            $error = "Erro ao comunicar com a API: " . $e->getMessage();
            session_destroy();
            require_once __DIR__ . '/../../src/view/login.php';
            return;
        }

        // Redireciona para o Lobby, que está na raiz ("/")
        header("Location: /");
        exit;
    }

    // Processa o Logout (Fluxo 4)
    public function logout() {
        session_destroy();
        // Redireciona para o Lobby, que está na raiz ("/")
        header("Location: /");
        exit;
    }

    public function isAuthenticated() {
        if (isset($_SESSION['user_id']) && isset($_SESSION['jwt_token'])) {
            return true;
        }
        return false;
    }
}