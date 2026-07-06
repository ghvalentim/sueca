<?php
namespace Database;


require_once __DIR__ . '/../../vendor/autoload.php';

use PDO;
use PDOException;
use Dotenv\Dotenv;



class Database {
    private static ?PDO $pdo = null; // Instância única de PDO (Singleton Pattern)

    public static function getConnection() { // Retorna a instância de PDO, criando-a se ainda não existir
        if (self::$pdo === null) {
            try {
                $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
                $dotenv->load();
                // Credenciais conforme definidas no compose.yml
                $host = $_ENV['DB_HOST'];
                $db   = $_ENV['DB_DATABASE'];
                $user = $_ENV['DB_USERNAME'];
                $pass = $_ENV['DB_PASSWORD'];
                
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