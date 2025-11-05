<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle permintaan HTTP yang masuk.
     * Jika user sudah login, redirect ke halaman yang sesuai dengan role.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Redirect berdasarkan role user
                if ($user->role?->name === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role?->name === 'handler') {
                    return redirect()->route('handler.dashboard');
                } else {
                    return redirect()->route('user.dashboard');
                }
            }
        }

        return $next($request);
    }
}
