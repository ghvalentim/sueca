<?php
namespace src\Controller;

use src\Model\Room;

class RoomController {
    
    // Apresenta o Lobby geral
    public function showLobby() {
        $rooms = [];
        if (isset($_SESSION['user_id'])) {
            $roomModel = new Room();
            $rooms = $roomModel->getWaitingRooms();
        }
        require_once __DIR__ . '/../View/lobby.php';
    }

    // Processa a criação de sala
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /create_room");
            exit;
        }
        
        $name = trim($_POST['room_name'] ?? '');
        if (!empty($name)) {
            $roomModel = new Room();
            $roomId = $roomModel->createRoom($name, $_SESSION['user_id']);
            if ($roomId) {
                header("Location: /room&id=" . $roomId);
                exit;
            }
        }
        header("Location: /");
        exit;
    }

    // Processa o clique no botão "Entrar"
    public function join() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
        
        $roomId = $_GET['id'] ?? null;
        if ($roomId) {
            $roomModel = new Room();
            $roomModel->joinRoom($roomId, $_SESSION['user_id']);
            header("Location: /room&id=" . $roomId);
            exit;
        }
        header("Location: /");
        exit;
    }
    
    // Mostra o ecrã interno de uma sala (O Tabuleiro da Sueca futuramente)
    public function showRoom() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /");
            exit;
        }
        
        $roomId = $_GET['id'] ?? null;
        if ($roomId) {
            $roomModel = new Room();
            $room = $roomModel->getRoomDetails($roomId);
            if ($room) {
                require_once __DIR__ . '/../View/room.php';
                return;
            }
        }
        header("Location: /home");
        exit;
    }
}