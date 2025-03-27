<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Tenta autenticar o token
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'message' => 'Não autenticado.',
                    'status' => 'error'
                ], 401);
            }
        } catch (JWTException $e) {
            // Qualquer erro relacionado ao token (ausente, inválido, expirado)
            return response()->json([
                'message' => 'Não autenticado.',
                'status' => 'error'
            ], 401);
        }

        return $next($request);
    }
}
