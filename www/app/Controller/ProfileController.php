<?php
namespace Controller;

use Model\User;

class ProfileController {
    
    // Construtor: Protege as rotas desta classe, exigindo que o utilizador tenha sessão iniciada
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }

    // Mostra a página de perfil
    public function show($success = null, $error = null) {
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        require_once __DIR__ . '/../../src/view/profile.php';
    }

    // Processa a atualização da password
    public function updatePassword() {
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($newPassword) || empty($confirmPassword)) {
            $error = "Preencha todos os campos da password.";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "As passwords não coincidem.";
        } else {
            $userModel = new User();
            if ($userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                $success = "Password atualizada com sucesso!";
            } else {
                $error = "Erro ao atualizar a password.";
            }
        }

        // Recarregar os dados atualizados para exibir a vista novamente
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        require_once __DIR__ . '/../../src/view/profile.php';
    }

    public function handlePost() {
        $formType = $_POST['form_type'] ?? '';

        if ($formType === 'details') {
            $this->updateDetails();
        } elseif ($formType === 'password') {
            $this->updatePassword();
        } else {
            $error = "Ação inválida.";
            $this->show(null, $error);
        }
    }

    private function updateDetails() {
        $avatar = trim($_POST['avatar'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $external = trim($_POST['external_username'] ?? '');

        $userModel = new User();
        if ($userModel->updateProfileInfo($_SESSION['user_id'], $avatar, $bio, $external)) {
            $this->show("Informações atualizadas com sucesso!");
        } else {
            $this->show(null, "Erro ao atualizar as informações do perfil.");
        }
    }
}