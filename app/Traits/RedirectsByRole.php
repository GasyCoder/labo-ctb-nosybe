<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

trait RedirectsByRole
{
    /**
     * Redirige l'utilisateur selon son rôle
     */
    protected function redirectUserByRole(): RedirectResponse
    {
        $user = Auth::user();

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

    /**
     * Obtient la route par défaut selon le rôle
     */
    protected function getDefaultRouteByRole(): string
    {
        $user = Auth::user();

        switch ($user->type) {
            case 'biologiste':
                return 'biologiste.analyse.index';
            case 'technicien':
                return 'technicien.index';
            case 'secretaire':
                return 'secretaire.prescription.index';
            case 'admin':
                return 'dashboard';
            default:
                return 'dashboard';
        }
    }
}