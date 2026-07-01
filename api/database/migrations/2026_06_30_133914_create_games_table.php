<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            // Ligação à tabela rooms (criada pelo PHP Vanilla)
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            
            // Estado do Jogo guardado em JSON (simplifica muito a lógica)
            $table->json('hands')->nullable(); // Cartas na mão de cada jogador
            $table->json('table_cards')->nullable(); // Cartas jogadas na vaza atual
            
            // Regras da Sueca
            $table->string('trump_suit', 1)->nullable(); // Naipe do trunfo (H=Copas, S=Espadas, D=Ouros, C=Paus)
            $table->string('trump_card', 3)->nullable(); // Ex: H-A (Ás de Copas)
            
            // Controlo de Turnos
            $table->unsignedBigInteger('current_player_id')->nullable(); // De quem é a vez
            $table->unsignedBigInteger('starting_player_id')->nullable(); // Quem começou a vaza atual
            
            // Pontuações
            $table->integer('team_a_score')->default(0); // Equipa A (Jogadores 1 e 3 da sala)
            $table->integer('team_b_score')->default(0); // Equipa B (Jogadores 2 e 4 da sala)
            $table->integer('trick_count')->default(0); // Número da vaza (0 a 10)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};