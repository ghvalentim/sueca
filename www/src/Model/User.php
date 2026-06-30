<?php
namespace Model;

class User {
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

    public function checkExists(string $username, string $email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch() !== false;
    }

    public function activateAccount(string $token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ? AND is_active = 0");
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    // [ATUALIZADO] Verifica credenciais para o Login e traz também o email
    public function verifyCredentials(string $username, string $password) {
        $db = Database::getConnection();
        // Adicionado 'email' ao SELECT para podermos enviar à API
        $stmt = $db->prepare("SELECT id, username, email, password, is_active FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Busca os dados do utilizador pelo ID (Sprint 2 - Perfil)
    public function findById(int $id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Atualiza a password do utilizador (Sprint 2 - Perfil)
    public function updatePassword(int $id, string $newPassword) {
        $db = Database::getConnection();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

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

        $apiUrl = 'http://localhost:8001/api/auth/login';
        $postData = json_encode([
            'email' => $user['email'],
            'password' => $password,
        ]);

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            error_log('Erro ao obter JWT da API: ' . $response);
            return null;
        }

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    public function getPasswordHash(string $username) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user ? $user['password'] : null;
    }
}