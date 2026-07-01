<?php

namespace src\Services;
use src\Config\App;
Class JwtService {

    // Obtém o token JWT da API usando as credenciais do utilizador
    public function getToken(string $email, string $password) {
        $apiUrl = App::jwtTokenAPI(); // Obtém a URL da API a partir do arquivo .env
        $postData = json_encode([
            'email' => $email,
            'password' => $password,
        ]);

        $apiResponse = $this->callAPI($apiUrl, $postData); // Faz a chamada à API e obtém a resposta

        if ($apiResponse === null) { // Se não for possível obter o token JWT da API, retorna null e registra um erro
            error_log('Falha ao obter o token JWT da API.');
            return null;
        } else { // Se o token JWT for obtido com sucesso, registra uma mensagem de sucesso e retorna o token
            error_log('Token JWT obtido com sucesso!');
            return $apiResponse;
        }
    }

        // Faz a chamada à API usando cURL e retorna a resposta
    public function callAPI(string $url, string $postData) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($postData)]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);

        $request = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($request === false) { // Se ocorrer um erro na chamada cURL, registra o erro e retorna null
            error_log(curl_error($ch));
            curl_close($ch);
            return null;
        }

        $data = json_decode($request, true); // Decodifica a resposta JSON da API para um array associativo

        if (json_last_error() !== JSON_ERROR_NONE) { // Se ocorrer um erro ao decodificar a resposta JSON, registra o erro e retorna null
            error_log('Erro ao decodificar a resposta JSON da API: ' . $request);
            curl_close($ch);
            return null;
        }

        if ($httpCode != 200) { // Se o código HTTP da resposta não for 200 (OK), registra o erro e retorna null
            error_log('Erro na resposta da API. Código HTTP: ' . $httpCode . '. Resposta: ' . $request);
            curl_close($ch);
            return null;
        }

        if (empty($data['access_token'])) { // Se o token JWT não estiver presente na resposta da API, registra o erro e retorna null
            error_log('Token JWT não encontrado na resposta da API. Resposta: ' . $request);
            error_log('Resposta completa da API: ' . print_r($data, true));
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        $response = $data['access_token']; // Retorna o token JWT obtido da resposta da API
        return $response;
        
    }
}