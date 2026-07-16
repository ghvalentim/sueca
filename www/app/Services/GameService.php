<?php

namespace Services;

use Config\App;
use Services\ApiService;

class GameService {

    public static function postRoom(int $roomId) {
        $apiUrl = App::gameAPI() . "{$roomId}/start";

        $token = $_SESSION['jwt_token'];

        if (is_array($token)) {
            $token = $token['access_token'] ?? $token['token'] ?? json_encode($token); }

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        ]; 

        $apiResponse = ApiService::doPostRequest($apiUrl, [], $headers);
        
        if ($apiResponse === null) {
        } else {
            return $apiResponse;
        }
    }

}