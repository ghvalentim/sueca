<?php
namespace Model;

use Services\JwtService;
use Database\Database;

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
        // Prepara uma declaração SQL para selecionar o utilizador com base no username fornecido
        $stmt = $db->prepare("SELECT id, username, password, is_active FROM users WHERE username = ?");
        // Executa a declaração SQL com o username fornecido como parâmetro
        $stmt->execute([$username]);
        // Retorna os dados do utilizador se as credenciais forem válidas, caso contrário retorna false
        $user = $stmt->fetch();
        // Obtém o hash da password do utilizador pelo username
        $getPasswordHash = $this->getPasswordHash($username);

        // Verifica se o utilizador existe e se a password fornecida corresponde ao hash armazenado na base de dados
        if ($user && password_verify($password, $getPasswordHash)) {
            // Se as credenciais forem válidas, retorna os dados do utilizador
            return $user;
        } // Se as credenciais forem inválidas, retorna false
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
        // Verifica as credenciais do utilizador
         $user = $this->verifyCredentials($username, $password);
         // Se as credenciais forem inválidas, retorna null
        if (!$user) {
            return null;
        }

        $email = $this->findById($user['id'])['email']; // Obtém o email do utilizador a partir dos dados retornados pela função findById
        // Se as credenciais forem válidas, obtém o token JWT da API usando a classe JwtService
        $jwtService = new JwtService();
        // Obtém o token JWT da API usando o email do utilizador e a password fornecida
        $token = $jwtService->getToken($email, $password);
        // Se não for possível obter o token JWT da API, retorna null e registra um erro
        if ($token === null) {
            error_log('Falha ao obter o token JWT da API.');
            return null;
        } // Se o token JWT for obtido com sucesso, retorna o token
        return $token;
    }

        // Obtém o hash da password do utilizador pelo username
    public function getPasswordHash(string $username) { 
        $db = Database::getConnection(); // Obtém a conexão com a base de dados usando a classe Database
        $stmt = $db->prepare("SELECT password FROM users WHERE username = ?");
        // Prepara uma declaração SQL para selecionar o hash da password do utilizador com base no username fornecido
        $stmt->execute([$username]);
        // Executa a declaração SQL com o username fornecido como parâmetro
        $user = $stmt->fetch();
        // Retorna o hash da password se o utilizador for encontrado, caso contrário retorna null
        return $user ? $user['password'] : null;
    }


        // Deleta a conta do utilizador pelo ID
    public function deleteAccount(int $id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

}