<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        
        // Vérifier si l'utilisateur a un des rôles autorisés
        if (in_array($user->type, $roles)) {
            return $next($request);
        }

        // Si l'utilisateur n'a pas le bon rôle, on le redirige selon son rôle
        return $this->redirectToUserRole($user);
    }

    /**
     * Redirige l'utilisateur vers sa section selon son rôle
     */
    private function redirectToUserRole($user)
    {
        switch ($user->type) {
            case 'biologiste':
                return redirect()->route('biologiste.analyse.index');
            case 'technicien':
                return redirect()->route('technicien.index');
            case 'secretaire':
                return redirect()->route('secretaire.prescription.index');
            case 'admin':
                return redirect()->route('dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }
}