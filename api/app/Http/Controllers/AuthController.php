<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    // Endpoint responsável por receber as credenciais do Portal PHP e devolver o JWT [RF21]
    public function login(Request $request)
    {
        // O Requisito [RF21] exige especificamente a validação por email e password
        $credentials = $request->only('email', 'password');

        // O método attempt() valida as credenciais contra a base de dados
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciais inválidas na API'], 401);
        }

        // Se estiver tudo correto, devolve o token gerado
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}