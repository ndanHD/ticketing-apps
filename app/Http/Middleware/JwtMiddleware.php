<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class JwtMiddleware
{
    /**
     * Menangani request masuk dan memvalidasi JWT pada header Authorization.
     * Jika token valid, pengguna akan di-authenticate (session-less) untuk request ini.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Token tidak ditemukan'], 401);
            }

            // gunakan konfigurasi dari config/jwt.php
            $credentials = JWT::decode($token, new Key(config('jwt.secret'), 'HS256'));
            
            $user = User::find($credentials->sub);
            if (!$user) {
                return response()->json(['error' => 'User tidak ditemukan'], 401);
            }

            auth()->login($user);
            return $next($request);

        } catch (Exception $e) {
            return response()->json(['error' => 'Token tidak valid'], 401);
        }
    }
}