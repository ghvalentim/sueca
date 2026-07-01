<?php
namespace Model;

class Room {
    
    // Lista apenas as salas que ainda estão à espera de jogadores
    public function getWaitingRooms() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT r.id, r.name, u.username as owner_name, 
            (SELECT COUNT(*) FROM room_players rp WHERE rp.room_id = r.id) as player_count 
            FROM rooms r 
            JOIN users u ON r.owner_id = u.id 
            WHERE r.status = 'Waiting'
        ");
        return $stmt->fetchAll();
    }

    // Cria a sala e insere automaticamente o dono como o 1º jogador
    public function createRoom(string $name, int $ownerId) {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();
            
            $stmt = $db->prepare("INSERT INTO rooms (name, owner_id, status) VALUES (?, ?, 'Waiting')");
            $stmt->execute([$name, $ownerId]);
            $roomId = $db->lastInsertId();
            
            $stmt2 = $db->prepare("INSERT INTO room_players (room_id, user_id) VALUES (?, ?)");
            $stmt2->execute([$roomId, $ownerId]);
            
            $db->commit();
            return $roomId;
        } catch (\Exception $e) {
            $db->rollBack();
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
            
            if (!$check->fetch()) {
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
}