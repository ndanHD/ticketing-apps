<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Menangani request dan memeriksa apakah user memiliki role yang dibutuhkan.
     * Jika belum login -> 401. Jika tidak memiliki role -> 403.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();
        if (!$user->role) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        // Split roles by pipe and check if user has any of the required roles
        $allowedRoles = explode('|', $roles);
        if (!in_array($user->role->name, $allowedRoles)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        return $next($request);
    }
}