<?php
namespace Model;

class User {
    // Insere o utilizador na base de dados com palavra-passe encriptada (RF03)
    public function create($username, $email, $password) {
        $db = Database::getConnection();
        
        // A password nunca deve ser armazenada em texto simples (Regras de Negócio)
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Token para futura ativação (RF05)
        $token = bin2hex(random_bytes(16)); 

        // Uso de Prepared Statements para evitar SQL Injection (RNF05)
        $stmt = $db->prepare("INSERT INTO users (username, email, password, activation_token, is_active) VALUES (?, ?, ?, ?, 0)");
        
        if ($stmt->execute([$username, $email, $hash, $token])) {
            return $token; // Retornamos o token para poder simular o envio de email
        }
        return false;
    }

    // Verifica se o username ou email já existem (RF01, RF02)
    public function checkExists($username, $email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch() !== false;
    }

    // Ativa a conta usando o token (Fluxo 2)
    public function activateAccount($token) {
        $db = Database::getConnection();
        // Apenas ativa se o token coincidir e a conta ainda não estiver ativa
        $stmt = $db->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ? AND is_active = 0");
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    // Verifica credenciais para o Login (Fluxo 3)
    public function verifyCredentials($username, $password) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, password, is_active FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verifica se o user existe e se a hash da password corresponde à inserida (Segurança)
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}