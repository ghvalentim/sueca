<?php

namespace Services;
use Config\App;
use Services\ApiService;
Class JwtService {

        public function getToken(string $email, string $password) {
        $apiUrl = App::jwtTokenAPI(); // Obtém a URL da API a partir do arquivo .env

        $postData = [
            'email' => $email,
            'password' => $password,
        ];  // Dados a serem enviados na requisição POST para obter o token JWT

        $headers = [
            'Accept: application/json',
        ]; // Cabeçalhos HTTP para a requisição, especificando que o conteúdo é JSON e que aceita respostas em JSON

        $method = 'POST'; // Método HTTP a ser usado na requisição, neste caso POST para enviar os dados de login e obter o token JWT

        $api = new ApiService(); // Instancia a classe ApiService para fazer a requisição à API
        $apiResponse = $api->request($apiUrl, $method, $postData, $headers); 
        // Faz a requisição à API usando o método request da classe ApiService, passando a URL da API, o método HTTP, os dados do POST e os cabeçalhos HTTP. O resultado da requisição é armazenado na variável $apiResponse.

        $token = $apiResponse['access_token'] ?? null; // Extrai o token JWT da resposta da API, se estiver presente. Se não estiver presente, define como null.
        if ($token === null) { // Se não for possível obter o token JWT da API, retorna null e registra um erro
            error_log('Falha ao obter o token JWT da API.');
            return null;
        } else { // Se o token JWT for obtido com sucesso, registra uma mensagem de sucesso e retorna o token
            error_log('Token JWT obtido com sucesso!');
            return $token;
        }

}

}