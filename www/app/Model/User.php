<?php
namespace Model;

use Services\JwtService;
use Database\Database;

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

    public function recoveryAccount(string $token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ? AND is_active = 0");
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    public function verifyCredentials(string $username, string $password) {
        $db = Database::getConnection(); 
        $stmt = $db->prepare("SELECT id, username, email, password, is_active FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        $getPasswordHash = $this->getPasswordHash($username);

        if ($user && password_verify($password, $getPasswordHash)) {
            return $user;
        }
        return false;
    }

    public static function getBotUser() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username FROM users WHERE username LIKE 'Bot_%' ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, email, avatar, bio, discord, steam, instagram, games_played, games_won, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getLastInsertedId() {
        $db = Database::getConnection();
        return $db->lastInsertId();
    }

    public function updatePassword(int $id, string $newPassword) {
        $db = Database::getConnection();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    public function updateProfileInfo(int $id, string $avatar, string $bio, string $discord, string $steam, string $instagram) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET avatar = ?, bio = ?, discord = ?, steam = ?, instagram = ? WHERE id = ?");
        return $stmt->execute([$avatar, $bio, $discord, $steam, $instagram, $id]);
    }

    public function findByUsername(string $username) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, email, avatar, bio, discord, steam, instagram, games_played, games_won, created_at FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function findByEmail(string $email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getJwtTokenForUser(string $email, string $password) {
        return JwtService::getToken($email, $password);
    }

    public function getPasswordHash(string $username) { 
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user ? $user['password'] : null;
    }


    public function deleteAccount(int $id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function setRecoveryToken(int $userId, string $token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET recovery_token = ? WHERE id = ?");
        $token = $stmt->execute([$token, $userId]);
        return $token;
    }

    public function findByRecoveryToken(string $token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE recovery_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function resetPasswordWithToken(string $token, string $newPassword) {
        $db = Database::getConnection();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ?, recovery_token = NULL WHERE recovery_token = ?");
        return $stmt->execute([$hash, $token]);
    }

}