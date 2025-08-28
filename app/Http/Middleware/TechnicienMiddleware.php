<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


// ============================================
// Middleware pour Technicien uniquement
// ============================================
class TechnicienMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->type !== 'technicien') {
            abort(403, 'Accès réservé aux techniciens.');
        }

        return $next($request);
    }
}
