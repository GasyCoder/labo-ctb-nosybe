<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->type, $roles)) {
            // Si l'utilisateur n'est pas connecté ou n'a pas le bon rôle
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}