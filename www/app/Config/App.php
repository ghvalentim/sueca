<?php

namespace Config;

class App {
    public static function apiURL(): string { // Retorna a URL da API definida no arquivo .env
        return $_ENV['API_URL'];
    }

    public static function appURL(): string { // Retorna a URL da aplicação definida no arquivo .env
        return $_ENV['APP_URL'];
    }

    public static function jwtTokenAPI(): string { // Retorna a URL da API para obtenção do token JWT definida no arquivo .env
        $apiUrl = $_ENV['JWT_TOKEN_URL'];
        if (empty($apiUrl)) {
            error_log('JWT_TOKEN_URL não definido no arquivo .env');
            return '';
        }
        return $apiUrl;
    }
}

?>