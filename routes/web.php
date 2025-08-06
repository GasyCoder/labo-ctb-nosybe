<?php

use App\Livewire\Archives;
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
use App\Livewire\Secretaire\Patients;
use Illuminate\Support\Facades\Route;
use App\Livewire\Secretaire\Paiements;
use App\Livewire\Secretaire\PatientDetail;
use App\Livewire\Secretaire\Prescripteurs;
use App\Http\Controllers\ProfileController;
use App\Livewire\Biologiste\IndexBiologiste;
use App\Livewire\Techniciens\IndexTechniciens;
use App\Livewire\Secretaire\Prescription\AddPrescription;
use App\Livewire\Secretaire\Prescription\EditPrescription;
use App\Livewire\Secretaire\Prescription\PrescriptionIndex;
use App\Livewire\Admin\BacterieFamilies; // ✅ Corrigé : BacterieFamilies au lieu de BacteryFamilies

// ============================================
// ROUTES PUBLIQUES ET REDIRECTIONS
// ============================================
Route::redirect('/', '/login')->name('home');
Route::redirect('/register', '/login')->name('register.redirect');

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
})->name('root');

// ============================================
// ROUTES COMMUNES (TOUS LES UTILISATEURS CONNECTÉS)
// ============================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard principal
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Profil utilisateur (inspiré de l'exemple avec plus de détails)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Archives
    Route::get('/archives', Archives::class)->name('archives');
    
});


// ============================================
// ROUTES SPÉCIFIQUES AUX SECRÉTAIRES
// ============================================
Route::middleware(['auth', 'verified', 'role:secretaire'])->prefix('secretaire')->name('secretaire.')->group(function () {
    Route::get('prescription/listes', PrescriptionIndex::class)->name('prescription.index');
    Route::get('nouvel-prescription', AddPrescription::class)->name('prescription.create');
    Route::get('/prescription/edit/{prescriptionId}', EditPrescription::class)->name('prescription.edit');
    Route::get('patients', Patients::class)->name('patients');
    Route::get('/secretaire/patients/{patient}', PatientDetail::class)->name('patient.detail');
    Route::get('prescripteurs', Prescripteurs::class)->name('prescripteurs');
});

// ============================================
// ROUTES SPÉCIFIQUES AUX TECHNICIENS
// ============================================
Route::middleware(['auth', 'verified', 'role:technicien'])->prefix('technicien')->name('technicien.')->group(function () {
    Route::get('dashboard', IndexTechniciens::class)->name('dashboard');
});

// ============================================
// ROUTES SPÉCIFIQUES AUX BIOLOGISTES
// ============================================
Route::middleware(['auth', 'verified', 'role:biologiste'])->prefix('biologiste')->name('biologiste.')->group(function () {
    Route::get('dashboard', IndexBiologiste::class)->name('dashboard');
});

// ============================================
// ROUTES SPÉCIFIQUES AUX ADMINS, BIOLOGISTES, TECHNICIENS
// ============================================
Route::middleware(['auth', 'verified', 'role:technicien,biologiste,admin'])->prefix('laboratoire')->name('laboratoire.')->group(function () {
    // Section Analyses
    Route::prefix('analyses')->name('analyses.')->group(function () {
        Route::get('examens', Examens::class)->name('examens');
        Route::get('types', Types::class)->name('types');
        Route::get('listes', Analyses::class)->name('listes');
        Route::get('prelevements', Prelevements::class)->name('prelevements');
    });

    // Section Microbiologie
    Route::prefix('microbiologie')->name('microbiologie.')->group(function () {
        Route::get('familles-bacteries', BacterieFamilies::class)->name('familles-bacteries');
        Route::get('bacteries', Bacteries::class)->name('bacteries');
        Route::get('antibiotiques', Antibiotiques::class)->name('antibiotiques');
    });
});

// ============================================
// ROUTES SPÉCIFIQUES AUX ADMINS
// ============================================
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Administration
    Route::get('utilisateurs', UsersIndex::class)->name('users');
    Route::get('parametres', Settings::class)->name('settings');
});

require __DIR__ . '/auth.php';


