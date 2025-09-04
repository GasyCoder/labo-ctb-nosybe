<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRedirectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && $request->route()->getName() === 'dashboard') {
            $user = Auth::user();
            
            // Rediriger les utilisateurs non-admin vers leurs sections spécifiques
            switch ($user->type) {
                case 'biologiste':
                    return redirect()->route('biologiste.analyse.index');
                case 'technicien':
                    return redirect()->route('technicien.index');
                case 'secretaire':
                    return redirect()->route('secretaire.prescription.index');
                case 'admin':
                    // L'admin reste sur le dashboard
                    break;
            }
        }

        return $next($request);
    }
}