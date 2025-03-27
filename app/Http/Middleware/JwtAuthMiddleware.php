<?php
namespace App\Http\Middleware;

use App\Enums\JwtResponses;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'message' => JwtResponses::UNAUTHENTICATED,
                    'status' => JwtResponses::ERROR
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => JwtResponses::UNAUTHENTICATED,
                'status' => JwtResponses::ERROR
            ], 401);
        }

        return $next($request);
    }
}
