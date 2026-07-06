<?php

namespace Services;

class ApiService {

     public static function doPostRequest(string $url, array $data = [], array $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        if (!empty($headers)) {
            $headers = array_values(array_unique($headers)); // Remove cabeçalhos duplicados, se houver
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            error_log('Cabeçalhos enviados na requisição POST: ' . json_encode($headers));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        error_log('Resposta da API (POST): ' . $response);

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log(json_last_error_msg());
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        if ($httpCode != 200) {
            error_log("HTTP $httpCode");
            return null;
        }

        return $decodedResponse; // Retorna a resposta decodificada em JSON como array associativo
    }

    public static function doGetRequest(string $url, array $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        if (!empty($headers)) {
            $headers = array_values(array_unique($headers)); // Remove cabeçalhos duplicados, se houver
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            error_log('Cabeçalhos enviados na requisição GET: ' . json_encode($headers));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        error_log('Resposta da API (GET): ' . $response);

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log(json_last_error_msg());
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        if ($httpCode != 200) {
            error_log("HTTP $httpCode");
            return null;
        }

        return $decodedResponse; // Retorna a resposta decodificada em JSON como array associativo
    }
}