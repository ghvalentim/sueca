<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function startGame(Request $request, int $roomId)
    {
        $room = DB::table('rooms')->where('id', $roomId)->first();
        
        // [MODIFICADO] Permite iniciar o jogo se a sala estiver 'Waiting' OU 'Finished'
        if (!$room || !in_array($room->status, ['Waiting', 'Finished'])) {
            return response()->json(['error' => 'Sala inválida para iniciar jogo.'], 400);
        }

        $players = DB::table('room_players')->where('room_id', $roomId)->pluck('user_id')->toArray();
        if (count($players) !== 4) return response()->json(['error' => 'Faltam jogadores.'], 400);

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
        return response()->json(['message' => 'Partida iniciada!', 'game_id' => $game->id], 201);
    }

    public function getState(int $roomId)
    {
        // [MODIFICADO] latest('id') garante que vamos buscar sempre o jogo mais recente da sala!
        $game = Game::where('room_id', $roomId)->latest('id')->first();
        if (!$game) return response()->json(['error' => 'Jogo não encontrado.'], 404);

        $userId = auth('api')->id();
        $hands = $game->hands;
        $myHand = $hands[$userId] ?? [];
        $playerIds = array_keys($hands);

        $playersInfo = DB::table('users')->whereIn('id', $playerIds)->get(['id', 'username', 'avatar'])->keyBy('id');

        $cardCounts = [];
        $publicPlayersData = [];
        
        foreach ($hands as $pId => $hand) {
            $cardCounts[$pId] = count($hand);
            $publicPlayersData[$pId] = [
                'username' => $playersInfo[$pId]->username ?? 'Desconhecido',
                'avatar' => $playersInfo[$pId]->avatar ?? 'https://ui-avatars.com/api/?name=User&background=198754&color=fff'
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
            'players_info' => $publicPlayersData,
            'team_a_score' => $game->team_a_score,
            'team_b_score' => $game->team_b_score,
            'trick_count' => $game->trick_count // Importante para o frontend saber quando o jogo acaba
        ]);
    }

    // [RF24] Realizar Jogada e Aplicar Regras da Sueca
    public function playCard(Request $request, int $roomId) {
        // [MODIFICADO] latest('id')
        $game = Game::where('room_id', $roomId)->latest('id')->first();
        if (!$game) return response()->json(['error' => 'Jogo não encontrado'], 404);

        $userId = auth('api')->id();
        
        if ($game->current_player_id !== $userId) {
            return response()->json(['error' => 'Não é a tua vez de jogar!'], 403);
        }

        $card = $request->input('card');
        $hands = $game->hands;
        $myHand = $hands[$userId];

        if (!in_array($card, $myHand)) {
            return response()->json(['error' => 'Não tens essa carta.'], 400);
        }

        $tableCards = $game->table_cards ?? [];

        // Se existiam 4 cartas na mesa da jogada anterior, limpamos agora
        if (count($tableCards) === 4) {
            $tableCards = [];
        }

        $processHandResult = $this->processHumanHand($myHand, $tableCards, $card);

        if ($processHandResult) {
            return $processHandResult;
        }

        $tableCards[$userId] = $card;
        $hands[$userId] = array_values(array_diff($myHand, [$card]));
        
        $game->table_cards = $tableCards;
        $game->hands = $hands;

        $playerIds = array_keys($hands);
        $myIndex = array_search($userId, $playerIds);

        $this->processTrick($tableCards, $game, $playerIds, $roomId, $myIndex);

        $game->save();

        return response()->json(['success' => true]);
    }

    public function playBot(int $roomId) {
        $game = Game::where('room_id', $roomId)->latest('id')->first();
        if (!$game) return response()->json(['error' => 'Jogo não encontrado'], 404);

        $botId = $game->current_player_id;
        $botUser = DB::table('users')->where('id', $botId)->first();

        if (!$botUser || !str_starts_with($botUser->username, 'Bot_')) {
            return response()->json(['error' => 'Não é a vez de um bot.'], 403);
        }
    
        $hands = $game->hands;
        $botHand = $hands[$botId];
        $tableCards = $game->table_cards ?? [];

        if (count($tableCards) === 4) {
            $tableCards = [];
        }

        $validCards = $this->processBotHand($botHand, $tableCards);

        $cardToPlay = $validCards[array_rand($validCards)];
        $tableCards[$botId] = $cardToPlay;
        $hands[$botId] = array_values(array_diff($botHand, [$cardToPlay]));
        $game->table_cards = $tableCards;
        $game->hands = $hands;
        $playerIds = array_keys($hands);
        $myIndex = array_search($botId, $playerIds);


        $this->processTrick($tableCards, $game, $playerIds, $roomId, $myIndex);
        $game->save();
        return response()->json(['success' => true, 'bot_played' => $cardToPlay]);
    }


    public function processHumanHand(array $hand, array $tableCards, string $card) {
        if(count($tableCards) === 0) {
            return null;
        }

        $leadCard = reset($tableCards);
        $leadSuit = explode('-', $leadCard)[0];
        $playedSuit = explode('-', $card)[0];

        if ($playedSuit === $leadSuit) {
            return null;
        }

        foreach ($hand as $c) {
            if (str_starts_with($c, $leadSuit . '-')) {
                return response()->json(['error' => 'Regra da Sueca: És obrigado a assistir ao naipe (jogar ' . $leadSuit . ').'], 400);
            }
        }

        return null;
    }
    
    public function processBotHand(array $playerHand, array $tableCards): array {
        if (count($tableCards) > 0) {
            $leadCard = reset($tableCards);
            $leadSuit = explode('-', $leadCard)[0];

            $suitCards = array_filter(
            $playerHand,
            fn($c) => str_starts_with($c, $leadSuit . '-')
            );

        if (!empty($suitCards)) {
            return array_values($suitCards);
            }
        }

        return $playerHand;
    }

 
    public function processTrick(array $tableCards, Game $game, array $playerIds, int $roomId, int $myIndex): void {

        if (count($tableCards) === 4) {
            
            $winnerId = $this->calculateTrickWinner($tableCards, $game->trump_suit);
            $points = $this->calculateTrickPoints($tableCards);

            if ($winnerId === $playerIds[0] || $winnerId === $playerIds[2]) {
                $game->team_a_score += $points;
            } else {
                $game->team_b_score += $points;
            }

            $game->current_player_id = $winnerId;
            $game->trick_count+= 1;

            if ($game->trick_count === 10) {
                DB::table('rooms')
                    ->where('id', $roomId)
                    ->update(['status' => 'Finished']);

                $game->current_player_id = null;
                $isTeamAWinner = $game->team_a_score > $game->team_b_score;
                $isTeamBWinner = $game->team_b_score > $game->team_a_score;

                $this->increasePlayerStats($playerIds, $isTeamAWinner, $isTeamBWinner);
            }

        } else {

            $game->current_player_id = $playerIds[($myIndex + 1) % 4];

        }
    }

    public function increasePlayerStats(array $playerIds, bool $isTeamAWinner, bool $isTeamBWinner): void {
        foreach ($playerIds as $index => $pId) {
            DB::table('users')->where('id', $pId)->increment('games_played');
            if (($index === 0 || $index === 2) && $isTeamAWinner) {
                DB::table('users')->where('id', $pId)->increment('games_won');
            } elseif (($index === 1 || $index === 3) && $isTeamBWinner) {
                DB::table('users')->where('id', $pId)->increment('games_won');
            }
        }
    }

    private function calculateTrickWinner(array $tableCards, string $trumpSuit) {
        $cardPower = ['A' => 10, '7' => 9, 'K' => 8, 'J' => 7, 'Q' => 6, '6' => 5, '5' => 4, '4' => 3, '3' => 2, '2' => 1];
        $leadCard = reset($tableCards);
        $leadSuit = explode('-', $leadCard)[0];
        
        $winnerId = null;
        $highestPower = -1;
        $hasTrump = false;

        foreach ($tableCards as $pId => $card) {
            [$suit, $rank] = explode('-', $card);
            $power = $cardPower[$rank];

            if ($suit === $trumpSuit) {
                if (!$hasTrump || $power > $highestPower) {
                    $hasTrump = true;
                    $highestPower = $power;
                    $winnerId = $pId;
                }
            } elseif (!$hasTrump && $suit === $leadSuit) {
                if ($power > $highestPower) {
                    $highestPower = $power;
                    $winnerId = $pId;
                }
            }
        }
        return $winnerId;
    }

    private function calculateTrickPoints(array $tableCards) {
        $cardValues = ['A' => 11, '7' => 10, 'K' => 4, 'J' => 3, 'Q' => 2, '6' => 0, '5' => 0, '4' => 0, '3' => 0, '2' => 0];
        $points = 0;
        foreach ($tableCards as $card) {
            $rank = explode('-', $card)[1];
            $points += $cardValues[$rank];
        }
        return $points;
    }
}