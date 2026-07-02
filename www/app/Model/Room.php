<?php
namespace Model;

use Database\Database;

class Room {
    
    // Lista apenas as salas que ainda estão à espera de jogadores
    public function getWaitingRooms() {
        $db = Database::getConnection();
        /**Prepara uma declaração SQL para selecionar as salas que estão à espera de jogadores, 
        juntamente com o nome do dono e a contagem de jogadores */
        $stmt = $db->query("
            SELECT r.id, r.name, u.username as owner_name, 
            (SELECT COUNT(*) FROM room_players rp WHERE rp.room_id = r.id) as player_count 
            FROM rooms r 
            JOIN users u ON r.owner_id = u.id 
            WHERE r.status = 'Waiting'
        ");
        // Executa a declaração SQL e retorna todas as salas que estão à espera de jogadores

        $rooms = $stmt->fetchAll();

        if (!$rooms) {
            return [];
        }
        return $rooms;

        
    }

    // Cria a sala e insere automaticamente o dono como o 1º jogador
    public function createRoom(string $name, int $ownerId) {
        $db = Database::getConnection();
        try { // Inicia uma transação para garantir que ambas as operações (criação da sala e inserção do dono) sejam atômicas
            $db->beginTransaction(); 
            
            $stmt = $db->prepare("INSERT INTO rooms (name, owner_id, status) VALUES (?, ?, 'Waiting')");
            $stmt->execute([$name, $ownerId]);
            $roomId = $db->lastInsertId(); // Obtém o ID da sala recém-criada
            
            $stmt2 = $db->prepare("INSERT INTO room_players (room_id, user_id) VALUES (?, ?)");
            $stmt2->execute([$roomId, $ownerId]);
            
            $db->commit(); // Confirma a transação, garantindo que ambas as operações sejam aplicadas ao banco de dados
            return $roomId; // Retorna o ID da sala recém-criada
        } catch (\Exception $e) {
            $db->rollBack(); // Se ocorrer algum erro durante a transação, desfaz todas as operações realizadas até o momento
            return false;
        }
    }

    // Processa a entrada de um jogador na sala, garantindo que o limite é 4
    public function joinRoom(int $roomId, int $userId) {
        $db = Database::getConnection();
        
        $stmt = $db->prepare("SELECT status, (SELECT COUNT(*) FROM room_players WHERE room_id = ?) as count FROM rooms WHERE id = ?");
        $stmt->execute([$roomId, $roomId]);
        $room = $stmt->fetch();

        if ($room && $room['status'] === 'Waiting' && $room['count'] < 4) {
            // Verifica se o jogador já está lá dentro
            $check = $db->prepare("SELECT 1 FROM room_players WHERE room_id = ? AND user_id = ?");
            $check->execute([$roomId, $userId]);
            
            if (!$check->fetch()) { // Se o jogador não estiver na sala, insere-o na tabela room_players
                $insert = $db->prepare("INSERT INTO room_players (room_id, user_id) VALUES (?, ?)");
                $insert->execute([$roomId, $userId]);
            }
            return true;
        }
        return false;
    }

    // Traz os dados da sala e a lista de jogadores atuais
    public function getRoomDetails(int $roomId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT r.*, u.username as owner_name FROM rooms r JOIN users u ON r.owner_id = u.id WHERE r.id = ?");
        $stmt->execute([$roomId]);
        $room = $stmt->fetch();
        
        if ($room) {
            $stmt2 = $db->prepare("SELECT u.username FROM room_players rp JOIN users u ON rp.user_id = u.id WHERE rp.room_id = ?");
            $stmt2->execute([$roomId]);
            $room['players'] = $stmt2->fetchAll(\PDO::FETCH_COLUMN);
        }
        return $room;
    }

    public function getCurrentRoomId(int $userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT room_id FROM room_players WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function getPlayerCount(int $roomId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM room_players WHERE room_id = ?");
        $stmt->execute([$roomId]);
        return (int) $stmt->fetchColumn();
    }

    public function deleteRoom(int $roomId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->execute([$roomId]);
        return $stmt->rowCount() > 0; // Retorna true se a sala foi deletada, false caso contrário
    }

    public function deleteIfEmpty(int $roomId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM rooms WHERE id = ? AND NOT EXISTS (SELECT 1 FROM room_players WHERE room_id = ?)");
        $stmt->execute([$roomId, $roomId]);
        return $stmt->rowCount() > 0; // Retorna true se a sala foi deletada, false caso contrário
    }

    public function isOwner(int $roomId, int $userId) :bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT 1 FROM rooms WHERE id = ? AND owner_id = ?");
        $stmt->execute([$roomId, $userId]);
        return (bool) $stmt->fetch();
    }

    public function isInRoom(int $roomId, int $userId) :bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT 1 FROM room_players WHERE room_id = ? AND user_id = ?");
        $stmt->execute([$roomId, $userId]);
        return (bool) $stmt->fetch();
    }

    public function leaveRoom(int $roomId, int $userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM room_players WHERE room_id = ? AND user_id = ?");
        return $stmt->execute([$roomId, $userId]);
    }
}