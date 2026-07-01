<?php
namespace Controller;

use Model\User;
use Controller\MailController;


class AuthController {
    
    // Mostra o formulário de registo (Fluxo 1)
    public function showRegister() {
        require_once __DIR__ . '/../View/register.php';
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
            require_once __DIR__ . '/../View/register.php';
            return;
        }

        // Criar o utilizador
        $token = $userModel->create($username, $email, $password);

        if ($token) {
            // 1. Gerar o link de ativação
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $activationLink = $scheme . '://' . $host . '/?action=activate&token=' . $token;
            
            // 2. Tentar enviar via PHPMailer (conforme aula 2.6 PHP - Emails)
            
            if ($mailController->sendActivationEmail($email, $activationLink)) {
                $success = "Registo efetuado com sucesso! Verifique o seu email para ativar a conta.";
                require_once __DIR__ . '/../View/register.php';
            } else {
                $userModel->deleteAccount($userModel->getLastInsertedId()); // Remove o utilizador criado
                $error = "Erro ao enviar o email de ativação. Por favor, tente novamente mais tarde.";
                require_once __DIR__ . '/../View/register.php';
            }
        } else {
            $userModel->deleteAccount($userModel->getLastInsertedId());
            $error = "Erro ao criar a conta. Por favor, tente novamente.";
            require_once __DIR__ . '/../View/register.php';
        }
    }

    // Processa a ativação da conta via URL (Fluxo 2)
    public function activate() {
        $token = $_GET['token'] ?? '';
                
        if (empty($token)) {
            $error = "Token de ativação inválido.";
            require_once __DIR__ . '/../../View/activation-error.php';
            return;
        } else {
            $userModel = new User();
            if ($userModel->activateAccount($token)) {
                $success = "Conta ativada com sucesso! Pode agora efetuar o login.";
                require_once __DIR__ . '/../View/activation-success.php';
                return;
            } else {
                $error = "Token de ativação inválido ou a conta já foi ativada.";
                require_once __DIR__ . '/../View/activation-error.php';
                return;
            }
        }
    }

    // Mostra o formulário de Login (Fluxo 3)
    public function showLogin() {
        require_once __DIR__ . '/../View/login.php';
    }

    // Processa o Login (Fluxo 3)
    public function login() {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = "Preencha todos os campos.";
            require_once __DIR__ . '/../View/login.php';
            return;
        }

        $userModel = new User();
        $user = $userModel->verifyCredentials($username, $password);

        if (!$user) {
            $error = "Credenciais inválidas.";
            require_once __DIR__ . '/../View/login.php';
            return;
        }

        // Regra de Negócio: O utilizador tem de estar ativo (RF05/Ativação)
        if ($user['is_active'] == 0) {
            $error = "A sua conta ainda não foi ativada. Verifique o seu email.";
            require_once __DIR__ . '/../View/login.php';
            return;
        }

        // Inicia sessão no Portal PHP
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

       try {
            // 1. Tentar obter o Token JWT da API
            $jwtToken = $userModel->getJwtToken($username, $password);
            
            $_SESSION['jwt_token'] = $jwtToken; // Guardar o Token JWT na sessão

            if ($jwtToken === null) {
                $error = "Erro ao obter o Token JWT da API.";

            } else {
                $success = "Login efetuado com sucesso! Token JWT obtido.";
            }
        
        } catch (\Exception $e) {
            $error = "Erro ao comunicar com a API: " . $e->getMessage();
            require_once __DIR__ . '/../View/login.php';
            return;
        }

        header("Location: ?action=home");
        exit;
    }

    // Processa o Logout (Fluxo 4)
    public function logout() {
        session_destroy();
        header("Location: ?action=home");
        exit;
    }
}