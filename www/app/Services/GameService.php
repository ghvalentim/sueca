<?php

namespace Services;

use Config\App;
use Services\ApiService;

class GameService {

    public static function postRoom(int $roomId) {
        $apiUrl = App::gameAPI() . "{$roomId}/start";
         // Método HTTP a ser usado na requisição, neste caso GET para obter os detalhes da sala

        $token = $_SESSION['jwt_token'];

        if (is_array($token)) {
            $token = $token['access_token'] ?? $token['token'] ?? json_encode($token); // Extrai o token JWT do array, se estiver presente. Se não estiver presente, define como null.
        }
        // Obtém o token JWT da sessão, se estiver presente. Se não estiver presente, define como null.
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $token, // Adiciona o cabeçalho Authorization com o token JWT
        ]; // Cabeçalhos HTTP para a requisição, especificando que o conteúdo é JSON e que aceita respostas em JSON

        $apiResponse = ApiService::doPostRequest($apiUrl, [], $headers);
        // Faz a requisição à API usando o método request da classe ApiService, passando a URL da API, o método HTTP, os dados do POST (vazio neste case) e os cabeçalhos HTTP. O resultado da requisição é armazenado na variável $apiResponse.
        
        if ($apiResponse === null) { // Se não for possível obter os detalhes da sala da API, retorna null e registra um erro
            error_log('Falha ao obter os detalhes da sala da API. Resposta da API: ' . json_encode($apiResponse));
            return null;
        } else { // Se os detalhes da sala forem obtidos com sucesso, registra uma mensagem de sucesso e retorna os detalhes
            error_log('Detalhes da sala obtidos com sucesso!');
            return $apiResponse;
        }
    }

}