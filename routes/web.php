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
use App\Livewire\Admin\TracePatient;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\Antibiotiques;
use App\Livewire\Secretaire\Patients;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\BacterieFamilies;
use App\Livewire\Biologiste\AnalyseValide;
use App\Livewire\Secretaire\PatientDetail;
use App\Livewire\Secretaire\Prescripteurs;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResultatController;
use App\Livewire\Technicien\IndexTechnicien;
use App\Livewire\Technicien\ShowPrescription;
use App\Livewire\Biologiste\BiologisteAnalysisForm;
use App\Livewire\Secretaire\Prescription\AddPrescription;
use App\Livewire\Secretaire\Prescription\EditPrescription;
use App\Livewire\Secretaire\Prescription\PrescriptionIndex;
use App\Http\Controllers\BiologistePrescriptionController;
use App\Http\Controllers\FactureController;




// ============================================
// ROUTES PUBLIQUES ET REDIRECTIONS
// ============================================
Route::redirect('/', '/login')->name('home');
Route::redirect('/register', '/login')->name('register.redirect');

Route::get('/', function () {
    if (Auth::check()) {
        // Redirection selon le type d'utilisateur
        switch (Auth::user()->type) {
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
    return redirect('/login');
})->name('root');
// ============================================
// ROUTES COMMUNES (TOUS LES UTILISATEURS CONNECTÉS)
// ============================================
Route::middleware(['auth', 'verified', 'role.redirect'])->group(function () {
    // Dashboard principal
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Profil utilisateur
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
    Route::get('patients/{patient}', PatientDetail::class)->name('patient.detail');
    Route::get('prescripteurs', Prescripteurs::class)->name('prescripteurs');
    Route::get('dashboard', Dashboard::class)->name('dashboard');
});


// ============================================
// ROUTES SPÉCIFIQUES AUX TECHNICIENS
// ============================================
Route::middleware(['auth', 'verified', 'role:technicien'])->prefix('technicien')->name('technicien.')->group(function () {
    Route::get('traitement', IndexTechnicien::class)->name('index');
    Route::get('/technicien/prescription/{prescription}', ShowPrescription::class)->name('prescription.show');
    // Route::get('/prescription/{prescription}/pdf', [ResultatController::class, 'generatePdf'])->name('prescription.pdf');
});

// ============================================
// ROUTES SPÉCIFIQUES AUX BIOLOGISTES
// ============================================
Route::middleware(['auth', 'verified', 'role:biologiste'])->prefix('biologiste')->name('biologiste.')->group(function () {
    Route::get('/analyse-valide', AnalyseValide::class)->name('analyse.index'); // Nom de route unifié
    Route::get('/prescription/{prescription}', ShowPrescription::class)->name('prescription.show');
    Route::get('/valide/{prescription}/analyse', BiologisteAnalysisForm::class)->name('valide.show');
    Route::post('/prescription/{prescription}/validate', [BiologistePrescriptionController::class, 'validate'])->name('prescription.validate');

    // ✅ NOUVELLES ROUTES POUR LES RÉSULTATS PDF
    Route::get('/prescription/{prescription}/pdf', [ResultatController::class, 'generatePdf'])->name('prescription.pdf');
    Route::get('/prescription/{prescription}/preview', [ResultatController::class, 'preview'])->name('prescription.preview');
    Route::get('/prescription/{prescription}/stats', [ResultatController::class, 'statistics'])->name('prescription.statistics');
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
    Route::get('utilisateurs', UsersIndex::class)->name('users');
    Route::get('parametres', Settings::class)->name('settings');
    Route::get('trace-patients', TracePatient::class)->name('trace-patients');
    
});

// // Route temporaire pour debug - à supprimer ensuite
// Route::get('/logout', function () {
//     Auth::logout();
//     return redirect('/');
// })->name('logout.get');


require __DIR__ . '/auth.php';