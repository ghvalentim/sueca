<?php

namespace Services;
use Config\App;
use Services\ApiService;
Class JwtService {

        public static function getToken(string $email, string $password) {
        $apiUrl = App::jwtTokenAPI();

        $postData = [
            'email' => $email,
            'password' => $password,
        ];

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        $apiResponse = ApiService::doPostRequest($apiUrl, $postData, $headers); 

        if ($apiResponse === null) { 
            error_log('Falha ao obter o token JWT da API. Resposta da API: ' . json_encode($apiResponse));
            return null;
        }

        $token = $apiResponse['access_token'] ?? $apiResponse['token'] ?? null;
        
        if(is_array($token)) {
            $token = json_encode($token);
        }
        if ($token === null) {
            error_log('Token JWT não encontrado na resposta da API. Resposta da API: ' . json_encode($apiResponse));
            return null;
        }

        return $token;

}
    public static function isExpired(string $jwt): bool {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return true;
        }

        $payload = json_decode(base64_decode(strtr($parts[1],'-_','+/')), true);

        if (!isset($payload['exp'])) {
            return true;
        }

        return $payload['exp'] < time();

    }

}