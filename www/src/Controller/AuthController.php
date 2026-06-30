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
            try {
                $mailController = new MailController();
                $emailSent = $mailController->sendActivationEmail($email, $activationLink);
            } catch (\Exception $e) {
                // PHPMailer não está disponível (ou sem vendor), simulamos o envio
                $emailSent = false;
            }

            if ($emailSent) {
                // Cenário Real / Produção: O servidor SMTP está configurado e o email seguiu
                $success = "Conta criada com sucesso! Verifique a sua caixa de entrada ($email) para ativar a conta.";
            } else {
                // Cenário de Teste / Desenvolvimento: PHPMailer falhou (ou sem vendor), mostramos simulação
                $success = "Conta criada com sucesso!<br><br>";
                $success .= "<small class='text-muted'>Aviso de Sistema: O envio de email via PHPMailer falhou (falta de SMTP configurado ou pasta vendor). Utilize o botão abaixo para simular o clique no email.</small><br><br>";
                $success .= "<a href='?action=activate&token=$token' class='btn btn-sm btn-outline-success'>Simular clique no Email de Ativação</a>";
            }
            
            require_once __DIR__ . '/../View/register.php';
        } else {
            $error = "Ocorreu um erro ao criar a conta. Tente novamente.";
            require_once __DIR__ . '/../View/register.php';
        }
    }

    // Processa a ativação da conta via URL (Fluxo 2)
    public function activate() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            echo "<div class='container mt-5 text-center'><h3 class='text-danger'>Token inválido.</h3><a href='?action=home'>Voltar ao início</a></div>";
            return;
        }

        $userModel = new User();
        
        if ($userModel->activateAccount($token)) {
            echo "<div class='container mt-5 text-center' style='font-family:sans-serif;'>";
            echo "<h2 class='text-success mb-4'>Conta ativada com sucesso!</h2>";
            echo "<a href='?action=login' class='btn btn-success'>Ir para o Login</a>";
            echo "</div>";
        } else {
            echo "<div class='container mt-5 text-center' style='font-family:sans-serif;'>";
            echo "<h2 class='text-danger mb-4'>Token inválido ou conta já ativada.</h2>";
            echo "<a href='?action=home' class='btn btn-outline-secondary'>Voltar ao início</a>";
            echo "</div>";
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