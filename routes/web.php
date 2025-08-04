<?php

use App\Livewire\Dashboard;
use App\Livewire\Admin\Types;
use App\Livewire\Admin\Examens;
use App\Livewire\Admin\Analyses;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\Bacteries;
use App\Livewire\Admin\UsersIndex;
use App\Livewire\Admin\Prelevements;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\Antibiotiques;
use App\Livewire\Secretaire\Archives;
use App\Livewire\Secretaire\Patients;
use Illuminate\Support\Facades\Route;
use App\Livewire\Secretaire\Paiements;
use App\Livewire\Admin\BacteryFamilies;
use App\Livewire\Secretaire\Prescripteurs;
use App\Livewire\Secretaire\Prescriptions;
use App\Http\Controllers\ProfileController;
use App\Livewire\Biologiste\IndexBiologiste;
use App\Livewire\Techniciens\IndexTechniciens;

// ============================================
// ROUTES PUBLIQUES ET REDIRECTIONS
// ============================================
Route::redirect('/', '/login');
Route::redirect('/register', '/login');

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

// ============================================
// ROUTES COMMUNES (TOUS LES UTILISATEURS CONNECTÉS)
// ============================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/archives', Archives::class)->name('archives');
});



// ============================================
// ROUTES SPÉCIFIQUES AUX SECRÉTAIRES
// ============================================
Route::middleware(['auth', 'verified', 'role:secretaire'])->prefix('secretaire')->name('secretaire.')->group(function () {

    Route::get('/prescriptions', Prescriptions::class)->name('prescriptions');
    Route::get('/examens', Examens::class)->name('examens');
    Route::get('/paiements', Paiements::class)->name('paiements');
    Route::get('/patients', Patients::class)->name('patients');
    Route::get('/prescripteurs', Prescripteurs::class)->name('prescripteurs');
});



// ============================================
// ROUTES SPÉCIFIQUES AUX TECHNICIENS
// ============================================
Route::middleware(['auth', 'verified', 'role:technicien'])->prefix('technicien')->group(function () {
    
    Route::get('/techniciens', IndexTechniciens::class)->name('techniciens');

});


// ============================================
// ROUTES SPÉCIFIQUES AUX BIOLOGISTES
// ============================================
Route::middleware(['auth', 'verified', 'role:biologiste'])->prefix('biologiste')->group(function () {
    
    Route::get('/biologistes', IndexBiologiste::class)->name('biologistes');

});




// ============================================
// ROUTES SPÉCIFIQUES AUX ADMINS, BIOLOGISTES, TECHNICIENS
// ============================================
Route::middleware(['auth', 'verified', 'role:technicien,biologiste,admin'])->prefix('admin')->group(function () {
    
    // Section Analyses
    Route::get('/examens', Examens::class)->name('examens');
    Route::get('/types-analyses', Types::class)->name('types-analyses');
    Route::get('/listes-analyses', Analyses::class)->name('listes-analyses');
    Route::get('/prelevements', Prelevements::class)->name('prelevements');
    
    // Section Microbiologie
    Route::get('/familles-bacteries', BacteryFamilies::class)->name('familles-bacteries');
    Route::get('/bacteries', Bacteries::class)->name('bacteries');
    Route::get('/antibiotiques', Antibiotiques::class)->name('antibiotiques');
});

// ============================================
// ROUTES SPÉCIFIQUES AUX ADMINS
// ============================================
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    
    // Administration
    Route::get('/users', UsersIndex::class)->name('users');
    Route::get('/settings', Settings::class)->name('settings');
});



require __DIR__.'/auth.php';