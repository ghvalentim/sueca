<?php

namespace Config;

class App {
    public static function apiURL(): string {
        return $_ENV['API_URL'];
    }

    public static function appURL(): string {
        return $_ENV['APP_URL'];
    }

    public static function gameAPI(): string {
        return $_ENV['GAME_API_URL'];
    }

    public static function jwtTokenAPI(): string {
        $apiUrl = $_ENV['JWT_TOKEN_URL'];
        if (empty($apiUrl)) {
            error_log('JWT_TOKEN_URL não definido no arquivo .env');
            return '';
        }
        return $apiUrl;
    }

    public static function externalGameAPI(): string {
        return $_ENV['EXTERNAL_GAME_API_URL'];
    }
}

?>