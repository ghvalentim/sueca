<?php
namespace src\Model;

use src\Services\JwtService;
use src\Model\Database;

class User {

    // Cria um novo utilizador e retorna o token de ativação
    public function create(string $username, string $email, string $password) {
        $db = Database::getConnection();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16)); 
        $stmt = $db->prepare("INSERT INTO users (username, email, password, activation_token, is_active) VALUES (?, ?, ?, ?, 0)");
        
        if ($stmt->execute([$username, $email, $hash, $token])) {
            return $token;
        }
        return false;
    }

       // Verifica se o username ou email já existem na base de dados 
    public function checkExists(string $username, string $email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch() !== false;
    }

        // Ativa a conta do utilizador com base no token de ativação
    public function activateAccount(string $token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ? AND is_active = 0");
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    // Verifica as credenciais do utilizador (username e password) e retorna os dados do utilizador se forem válidas
    public function verifyCredentials(string $username, string $password) {
        $db = Database::getConnection();
        // Adicionado 'email' ao SELECT para podermos enviar à API
        $stmt = $db->prepare("SELECT id, username, password, is_active FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Obtém os detalhes do utilizador pelo ID
    public function findById(int $id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Obtém o último ID inserido na tabela users
    public function getLastInsertedId() {
        $db = Database::getConnection();
        return $db->lastInsertId();
    }

    // Atualiza a password do utilizador
    public function updatePassword(int $id, string $newPassword) {
        $db = Database::getConnection();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    // Obtém o token JWT da API para o utilizador, se as credenciais forem válidas
    public function getJwtToken(string $username, string $password) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT email FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $this->getPasswordHash($username))) {
            return null;
        }

        $jwtService = new JwtService();
        $token = $jwtService->getToken($user['email'], $password);
        if ($token === null) {
            error_log('Falha ao obter o token JWT da API.');
            return null;
        }
        return $token;
    }

        // Obtém o hash da password do utilizador pelo username
    public function getPasswordHash(string $username) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user ? $user['password'] : null;
    }


        // Deleta a conta do utilizador pelo ID
    public function deleteAccount(int $id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

}