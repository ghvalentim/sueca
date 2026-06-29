<?php
namespace Controller;

use Model\User;

class AuthController {
    
    // Mostra o formulário de registo
    public function showRegister() {
        require_once __DIR__ . '/../View/register.php';
    }

    // Processa a submissão do formulário
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
            // Em ambiente real, aqui enviaríamos um email (RF04). 
            // Para efeitos práticos e de teste, mostramos uma mensagem de sucesso.
            $success = "Conta criada com sucesso! (Simulação de Email: O seu token de ativação é $token)";
            require_once __DIR__ . '/../View/register.php';
        } else {
            $error = "Ocorreu um erro ao criar a conta. Tente novamente.";
            require_once __DIR__ . '/../View/register.php';
        }
    }
}