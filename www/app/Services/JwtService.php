<?php

namespace Services;
use Config\App;
use Services\ApiService;
Class JwtService {

        public static function getToken(string $email, string $password) {
        $apiUrl = App::jwtTokenAPI(); // Obtém a URL da API a partir do arquivo .env

        $postData = [
            'email' => $email,
            'password' => $password,
        ];  // Dados a serem enviados na requisição POST para obter o token JWT

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        $apiResponse = ApiService::doPostRequest($apiUrl, $postData, $headers); 
        /**O método instanciado já retorna a resposta decodificada em JSON como array associativo, 
         * ou null se houver algum erro na requisição ou na decodificação da resposta. */
        if ($apiResponse === null) { 
            error_log('Falha ao obter o token JWT da API. Resposta da API: ' . $apiResponse);
            return null;
        }

        $token = $apiResponse['access_token'] ?? $apiResponse['token'] ?? null; // Obtém o token JWT da resposta da API, se estiver presente. Se não estiver presente, define como null.
        
        if(is_array($token)) { // Se o token JWT estiver presente na resposta da API, mas for um array, converte para string JSON
            $token = json_encode($token);
        }
        if ($token === null) { // Se o token JWT não estiver presente na resposta da API, retorna null e registra um erro
            error_log('Token JWT não encontrado na resposta da API. Resposta da API: ' . json_encode($apiResponse));
            return null;
        }

        return $token; // Retorna o token JWT obtido da API

}

}