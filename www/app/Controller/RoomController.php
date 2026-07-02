<?php
namespace Controller;

use Model\Room;
use Services\ApiService;
use Controller\AuthController;

class RoomController {
    
    // Apresenta o Lobby geral
    public function showLobby() {
        $rooms = [];
        if (isset($_SESSION['user_id'])) {
            $roomModel = new Room();
            $rooms = $roomModel->getWaitingRooms();
        }
        require_once __DIR__ . '/../../src/view/lobby.php';
    }

    // Processa a criação de sala
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
        
        $name = trim($_POST['room_name'] ?? '');
        if (!empty($name)) {
            $roomModel = new Room();
            $roomId = $roomModel->createRoom($name, $_SESSION['user_id']);
            if ($roomId) {
                header("Location: /room?id=" . $roomId);
                exit;
            }
        }
        header("Location: /");
        exit;
    }

    // Processa o clique no botão "Entrar"
    public function join() {
        $user = new AuthController();
        if (!$user->isAuthenticated()) {
            header("Location: /login");
            exit;
        }
        // Obtém o ID da sala a partir do parâmetro GET
        $roomId = $_GET['id'] ?? null;
        if ($roomId) {
            $roomModel = new Room();
            if ($roomModel->getCurrentRoomId($_SESSION['user_id'])) {
                $_SESSION['error'] = "Você já está em uma sala. Saia da sala atual antes de entrar em outra.";
                header("Location: /");
                exit;
            }
            $roomModel->joinRoom($roomId, $_SESSION['user_id']);
            header("Location: /room?id=" . $roomId);
            exit;
        }
        header("Location: /");
        exit;
    }
    
    // Mostra o ecrã interno de uma sala (O Tabuleiro da Sueca futuramente)
    public function showRoom() {
        $user = new AuthController();
        if (!$user->isAuthenticated()) {
            header("Location: /login");
            exit;
        }
        // Obtém o ID da sala a partir do parâmetro GET
        $roomId = $_GET['id'] ?? null;
        if ($roomId) { // Se o ID da sala estiver presente, obtém os detalhes da sala usando o modelo Room
            $roomModel = new Room();
            $room = $roomModel->getRoomDetails($roomId);
            if ($room) { // Se a sala existir, carrega a vista da sala com os detalhes da sala
                require_once __DIR__ . '/../../src/view/room.php';
                return;
            }
        } // Se o ID da sala não estiver presente, redireciona para a página inicial
        header("Location: /");
        exit;
    }

    public function startGame() {
        // Verifica se o utilizador está autenticado e se possui um token JWT válido
        $user = new AuthController();
        if (!$user->isAuthenticated()) {
            header("Location: /login");
            exit;
        }

        $roomId = $_GET['id'] ?? null;
        if ($roomId) { 
            $apiURL = $_ENV['API_ROOMS_URL'] . "/$roomId/start";
            $method = 'POST';
            $postData = [];
            $request = $this->callAPI($method, $apiURL, $postData); 
            if ($request === null) {
                $error = "Erro ao iniciar o jogo na API.";
                require_once __DIR__ . '/../../src/view/room.php';
                return;
            } 
             header("Location: /room?id=" . $roomId);
                exit;
        }
        header("Location: /");
        exit;
    }

    public function leave() {
        $user = new AuthController();
        if (!$user->isAuthenticated()) {
            header("Location: /login");
            exit;
        }

        $roomId = $_GET['id'] ?? null;
        if ($roomId) {
            $roomModel = new Room();
            $roomModel->leaveRoom($roomId, $_SESSION['user_id']);
            $roomModel->deleteIfEmpty($roomId);
            header("Location: /");
            exit;
        }
        header("Location: /");
        exit;
    }


        public function delete() {
            $user = new AuthController();
            if (!$user->isAuthenticated()) {
                header("Location: /login");
                exit;
            }
            $roomModel = new Room();
            $countPlayers = $roomModel->getPlayerCount($_GET['id']);
            if ($countPlayers <= 1) { // Permite eliminar a sala apenas se houver 0 ou 1 jogador
                
                if ($roomModel->isOwner($_GET['id'], $_SESSION['user_id'])) {
                    $roomModel->deleteRoom($_GET['id']);
                    header("Location: /");
                    exit;
                } else {
                    $error = "Não é possível eliminar a sala, você não é o dono.";
                    header("Location: /");
                    exit;
                }}
                
                else {
                    $error = "Não é possível eliminar a sala, existem jogadores dentro da sala.";
                    header("Location: /");
                    exit;
                }
            }

    public function callAPI(string $method, string $url, $data = null) {
        $apiService = new ApiService();
        $headers = [
            'Authorization: Bearer ' . $_SESSION['jwt_token'],
            'Accept: application/json'
        ]; 
        $data = $apiService->request($url, $method, $data, $headers);
         if (!$data) { 
            return null;
        }
        return $data;
    }
}