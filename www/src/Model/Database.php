<?php
namespace Model;

use PDO;
use PDOException;

class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                // Credenciais conforme definidas no compose.yml
                $host = 'db'; 
                $db   = 'jogosueca';
                $user = 'sueca_user';
                $pass = 'sueca_password';
                
                $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die("Erro de ligação à base de dados: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}