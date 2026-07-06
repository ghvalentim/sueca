<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{

    // [RF22] Iniciar a Partida
    public function startGame(Request $request, int $roomId)
    {
        $room = DB::table('rooms')->where('id', $roomId)->first();
        if (!$room || $room->status !== 'Waiting') {
            return response()->json(['error' => 'Sala inválida ou jogo já iniciado.'], 400);
        }

        $players = DB::table('room_players')->where('room_id', $roomId)->pluck('user_id')->toArray();
        if (count($players) !== 4) {
            return response()->json(['error' => 'A sala precisa de exatamente 4 jogadores.'], 400);
        }

        $suits = ['H', 'S', 'D', 'C'];
        $ranks = ['A', '7', 'K', 'J', 'Q', '6', '5', '4', '3', '2'];
        $deck = [];
        
        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $deck[] = $suit . '-' . $rank;
            }
        }
        shuffle($deck);

        $hands = [];
        foreach ($players as $index => $playerId) {
            $hands[$playerId] = array_slice($deck, $index * 10, 10);
        }

        $trumpCard = $hands[$players[3]][9];
        $trumpSuit = explode('-', $trumpCard)[0];

        $startingPlayerId = $players[0];

        $game = Game::create([
            'room_id' => $roomId,
            'hands' => $hands,
            'table_cards' => [],
            'trump_suit' => $trumpSuit,
            'trump_card' => $trumpCard,
            'current_player_id' => $startingPlayerId,
            'starting_player_id' => $startingPlayerId,
            'team_a_score' => 0,
            'team_b_score' => 0,
            'trick_count' => 0,
        ]);

        DB::table('rooms')->where('id', $roomId)->update(['status' => 'Playing']);

        return response()->json([
            'message' => 'Partida iniciada com sucesso!',
            'game_id' => $game->id,
            'trump_card' => $trumpCard
        ], 201);
    }

    // [RF23] Consultar o Estado Atual do Jogo (Novo)
    public function getState(int $roomId)
    {
        $game = Game::where('room_id', $roomId)->first();

        if (!$game) {
            return response()->json(['error' => 'Jogo não encontrado.'], 404);
        }

        // Descobrir quem é o utilizador que está a fazer o pedido pelo Token JWT
        $userId = auth('api')->id();

        // SEGURANÇA: Mascarar as mãos. O jogador só recebe as suas próprias cartas!
        $hands = $game->hands;
        $myHand = $hands[$userId] ?? [];
        $playerIds = array_keys($hands);

        $playersinfo = DB::table('users')
        ->whereIn('id',$playerIds)
        ->get(['id','username','avatar'])
        ->keyBy('id');

        // Para os adversários, enviamos apenas o número de cartas que eles têm na mão
        $cardCounts = [];
        $publicPlayersInfo = [];
        foreach ($hands as $pId => $hand) {
            $cardCounts[$pId] = count($hand);
            $publicPlayersInfo[$pId] = [
                'username' => $playersinfo[$pId]->username ?? 'Desconhecido',
                'avatar' => $playersinfo[$pId]->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($playersinfo[$pId]->username ?? 'D').'&background=198754&color=fff',
            ];
        }

        return response()->json([
            'game_id' => $game->id,
            'trump_suit' => $game->trump_suit,
            'trump_card' => $game->trump_card,
            'current_player_id' => $game->current_player_id,
            'my_id' => $userId,
            'my_hand' => $myHand,
            'table_cards' => $game->table_cards,
            'card_counts' => $cardCounts,
            'players_info' => $publicPlayersInfo,
            'team_a_score' => $game->team_a_score,
            'team_b_score' => $game->team_b_score
        ]);
    }

}