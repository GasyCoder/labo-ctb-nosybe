<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $this->redirectUserByRole();
    }

    /**
     * Redirige l'utilisateur selon son rôle après authentification
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
                // Fallback vers le dashboard pour les autres cas
                return redirect()->route('dashboard');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}