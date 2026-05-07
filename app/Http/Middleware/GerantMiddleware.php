<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GerantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Si l'utilisateur n'est pas connecté ou n'est pas gérant → refus
        if (!auth()->check() || !auth()->user()->isGerant()) {
            return redirect()->route('login')
                   ->with('error', 'Accès réservé aux gérants de restaurant.');
        }

        return $next($request);
    }
}