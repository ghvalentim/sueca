<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'hands', 'table_cards', 'trump_suit', 'trump_card', 
        'current_player_id', 'starting_player_id', 'team_a_score', 'team_b_score', 'trick_count'
    ];

    // O Laravel converte automaticamente as colunas JSON em Arrays de PHP (e vice-versa)
    protected $casts = [
        'hands' => 'array',
        'table_cards' => 'array',
    ];
}