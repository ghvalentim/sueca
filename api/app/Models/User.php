<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

// Adicionamos a interface JWTSubject conforme indicado nos slides da aula
class User extends Authenticatable implements JWTSubject
{
    // Forçamos o Laravel a usar a tabela que criámos no script init.sql
    protected $table = 'users';
    
    // Desativamos os timestamps do Laravel porque na nossa tabela só temos o created_at e não o updated_at
    public $timestamps = false;

    protected $fillable = [
        'username', 'email', 'password', 'activation_token', 'is_active'
    ];

    protected $hidden = [
        'password', 'activation_token',
    ];

    // Métodos obrigatórios da interface JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}