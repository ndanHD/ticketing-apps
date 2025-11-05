<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class RequireLogin
{
    /**
     * Menangani request dan memastikan user telah login (session-based).
     * Jika belum login, redirect ke route login.show dan simpan intended url.
     * Untuk request yang mengharapkan JSON, kembalikan respons JSON 401.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            // jika request mengharapkan JSON (AJAX/API), kembalikan JSON 401
            if ($request->expectsJson() || $request->isJson() || $request->wantsJson()) {
                return response()->json(['error' => 'Silakan login terlebih dahulu'], 401);
            }

            // simpan url yang diminta supaya bisa redirect setelah login
            $intended = $request->fullUrl();
            session(['url.intended' => $intended]);

            return redirect()->route('login.show')->with('error', 'Silakan login terlebih dahulu');
        }

        return $next($request);
    }
}
