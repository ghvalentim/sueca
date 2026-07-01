<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{

    public function __construct()
    {
        // Aplica o middleware de autenticação a todas as rotas deste controlador
        $this->middleware('auth:api');
    }
    // [RF22] Iniciar a Partida
    public function startGame(Request $request, int $roomId)
    {
        // 1. Validar se a sala existe e está à espera de jogadores
        $room = DB::table('rooms')->where('id', $roomId)->first();
        if (!$room || $room->status !== 'Waiting') {
            return response()->json(['error' => 'Sala inválida ou jogo já iniciado.'], 400);
        }

        // 2. Obter os 4 jogadores da sala
        $players = DB::table('room_players')->where('room_id', $roomId)->pluck('user_id')->toArray();
        if (count($players) !== 4) {
            return response()->json(['error' => 'A sala precisa de exatamente 4 jogadores.'], 400);
        }

        // 3. Criar e baralhar o baralho da Sueca
        $suits = ['H', 'S', 'D', 'C']; // Copas (Hearts), Espadas (Spades), Ouros (Diamonds), Paus (Clubs)
        $ranks = ['A', '7', 'K', 'J', 'Q', '6', '5', '4', '3', '2'];
        $deck = [];
        
        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $deck[] = $suit . '-' . $rank;
            }
        }
        shuffle($deck); // Baralhar as cartas

        // 4. Distribuir 10 cartas a cada jogador
        $hands = [];
        foreach ($players as $index => $playerId) {
            // Retira 10 cartas do baralho para cada jogador
            $hands[$playerId] = array_slice($deck, $index * 10, 10);
        }

        // 5. Definir o Trunfo (Vamos usar a última carta do último jogador a receber)
        $trumpCard = $hands[$players[3]][9];
        $trumpSuit = explode('-', $trumpCard)[0]; // Ex: "H-A" -> "H"

        // 6. Definir quem começa a jogar (O primeiro jogador a entrar na sala após o dono, ou de forma aleatória)
        // Por convenção simples, vamos colocar o jogador 0 a começar
        $startingPlayerId = $players[0];

        // 7. Guardar o estado do jogo na Base de Dados
        $game = Game::create([
            'room_id' => $roomId,
            'hands' => $hands, // O Laravel converte para JSON automaticamente graças ao $casts
            'table_cards' => [], // Mesa começa vazia
            'trump_suit' => $trumpSuit,
            'trump_card' => $trumpCard,
            'current_player_id' => $startingPlayerId,
            'starting_player_id' => $startingPlayerId,
            'team_a_score' => 0,
            'team_b_score' => 0,
            'trick_count' => 0,
        ]);

        // 8. Atualizar o estado da sala para 'Playing'
        DB::table('rooms')->where('id', $roomId)->update(['status' => 'Playing']);

        return response()->json([
            'message' => 'Partida iniciada com sucesso!',
            'game_id' => $game->id,
            'trump_card' => $trumpCard
        ], 201);
    }
}