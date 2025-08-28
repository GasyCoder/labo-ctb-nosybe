<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


// ============================================
// Middleware pour Biologiste uniquement
// ============================================
class BiologisteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->type !== 'biologiste') {
            abort(403, 'Accès réservé aux biologistes.');
        }

        return $next($request);
    }
}