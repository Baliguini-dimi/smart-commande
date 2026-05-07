<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            return redirect()->route('login')
                   ->with('error', 'Accès super administrateur uniquement.');
        }

        return $next($request);
    }
}