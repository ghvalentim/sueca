<?php

namespace Services;

class ApiService {

    // Faz uma requisição HTTP para a API, retornando a resposta decodificada em JSON
    public function request(string $url, string $method, array $data = [], array $headers = []) {
        // Inicializa uma sessão cURL
        $ch = curl_init();

        // Configura as opções da requisição cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        // Define a URL para a requisição cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Define que a resposta da requisição será retornada como string
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        // Define o método HTTP da requisição (GET, POST, PUT, DELETE, etc.)

        if (!empty($data)) { // Se houver dados a serem enviados na requisição (para métodos como POST ou PUT)
        // Converte os dados em JSON e adiciona ao corpo da requisição
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            // Adiciona o cabeçalho Content-Type para indicar que o corpo da requisição é JSON
            $headers[] = 'Content-Type: application/json';
        }
    // Adiciona os cabeçalhos HTTP à requisição, se houver
        if (!empty($headers)) {
            // Adiciona os cabeçalhos HTTP à requisição
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Adiciona os cabeçalhos HTTP à requisição
        }

        // Executa a requisição
        $response = curl_exec($ch);

        // Verifica se houve erro na requisição
        if (curl_errno($ch)) {
            error_log('Erro na requisição cURL: ' . curl_error($ch));
            return null;
        }

        // Fecha a sessão cURL
        curl_close($ch);

        return json_decode($response, true); // Retorna a resposta decodificada em JSON como array associativo
    }
}