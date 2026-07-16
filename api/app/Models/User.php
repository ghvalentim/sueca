<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{

    use Notifiable;
    protected $table = 'users';
    
    public $timestamps = false;

    protected $fillable = [
        'username', 'email', 'password', 'activation_token', 'is_active','avatar','bio','discord','steam','instagram','games_played','games_won','recovery_token'
    ];

    protected $hidden = [
        'password', 'activation_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}