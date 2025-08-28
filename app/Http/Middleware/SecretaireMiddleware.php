<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


// ============================================
// Middleware pour Secrétaire uniquement
// ============================================
class SecretaireMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->type !== 'secretaire') {
            abort(403, 'Accès réservé aux secrétaires.');
        }

        return $next($request);
    }
}
