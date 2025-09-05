<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class ResultatController extends Controller
{
    /**
     * Générer le PDF des résultats d'analyse - VERSION CORRIGÉE
     */
    public function generatePdf(Request $request, Prescription $prescription)
    {
        try {
            // ✅ CHARGEMENT OPTIMISÉ avec relations nécessaires
            $prescription = $this->chargerDonneesCompletes($prescription);

            if (!$prescription) {
                abort(404, 'Prescription non trouvée');
            }

            // ✅ ORGANISATION OPTIMISÉE des résultats
            $donneesOrganisees = $this->preparerDonneesPourPdf($prescription);

            // ✅ GÉNÉRATION PDF avec options optimisées
            $pdf = Pdf::loadView('pdf.resultats-analyse', $donneesOrganisees)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'Arial',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'enable_php' => false,
                    'chroot' => public_path(),
                    'debugKeepTemp' => false,
                ]);

            $fileName = $this->genererNomFichier($prescription);

            // ✅ LOGGING optimisé
            $this->loggerGenerationPdf($prescription);

            return $pdf->stream($fileName, ['Attachment' => false]);

        } catch (\Exception $e) {
            return $this->gererErreur($e, $prescription);
        }
    }

    /**
     * ✅ CHARGEMENT OPTIMISÉ des données avec relations - CORRIGÉ (enfants au lieu de children)
     */
    private function chargerDonneesCompletes(Prescription $prescription): Prescription
    {
        return $prescription->load([
            // ✅ PATIENT - Colonnes existantes
            'patient:id,numero_dossier,nom,prenom,telephone,email,statut,date_naissance',
            
            // ✅ PRESCRIPTEUR - Colonnes de base
            'prescripteur:id,nom,prenom,grade,specialite',
            
            // ✅ ANALYSES avec leurs résultats - CORRECTION: utiliser 'enfants' au lieu de 'children'
            'analyses' => function ($query) {
                $query->with([
                    'resultats' => function ($q) {
                        $q->whereNotNull('valeur')
                          ->orWhereNotNull('resultats');
                    },
                    'type:id,libelle',
                    'examen:id,name,abr,status',
                    'enfants' => function ($q) { // CORRECTION: 'enfants' au lieu de 'children'
                        $q->with(['resultats' => function ($qr) {
                            $qr->whereNotNull('valeur')
                               ->orWhereNotNull('resultats');
                        }]);
                    }
                ])
                ->orderBy('ordre');
            }
        ]);
    }

    /**
     * ✅ PRÉPARATION OPTIMISÉE des données pour le PDF
     */
    private function preparerDonneesPourPdf(Prescription $prescription): array
    {
        // ✅ CORRECTION : Utiliser les champs disponibles du patient
        $patient = $prescription->patient;
        
        return [
            'prescription' => $prescription,
            'patient' => $patient,
            'medecin' => $prescription->prescripteur->nom . ' ' . $prescription->prescripteur->prenom,
            'datePrelevement' => $prescription->created_at->format('d/m/Y'),
            'examens' => $this->organiserExamens($prescription),
        ];
    }

    /**
     * ✅ ORGANISATION des résultats par examen - CORRIGÉ
     */
    private function organiserExamens(Prescription $prescription): Collection
    {
        $examens = collect();
        
        // Récupérer tous les examens distincts des analyses
        $examenIds = $prescription->analyses
            ->pluck('examen_id')
            ->unique()
            ->filter();
            
        foreach ($examenIds as $examenId) {
            // Récupérer les analyses pour cet examen
            $analysesExamen = $prescription->analyses->filter(function ($analyse) use ($examenId) {
                return $analyse->examen_id == $examenId;
            });
            
            if ($analysesExamen->isNotEmpty()) {
                $examen = $analysesExamen->first()->examen;
                
                $examens->push((object)[
                    'id' => $examen->id,
                    'name' => $examen->name,
                    'analyses' => $this->organiserAnalyses($analysesExamen),
                ]);
            }
        }
        
        return $examens;
    }

    /**
     * ✅ ORGANISATION des analyses - CORRIGÉ (enfants au lieu de children)
     */
    private function organiserAnalyses(Collection $analyses): Collection
    {
        $analysesOrganisees = collect();
        $patient = optional($analyses->first()?->prescription?->patient);

        foreach ($analyses as $analyse) {
            // Vérifier si l'analyse a des résultats
            $hasResults = $analyse->resultats->isNotEmpty() || 
                         ($analyse->enfants->isNotEmpty() && $analyse->enfants->some(fn($enfant) => $enfant->resultats->isNotEmpty()));
            
            if ($hasResults) {
                $analysesOrganisees->push((object)[
                    'id' => $analyse->id,
                    'name' => $analyse->designation,
                    'code' => $analyse->code,
                    'unite' => $analyse->unite,
                    'age' => $patient->age ?? null,
                    'valeur_min' => $this->extraireValeurMin($analyse->valeur_ref),
                    'valeur_max' => $this->extraireValeurMax($analyse->valeur_ref),
                    'valeur_normal' => $analyse->valeur_ref,
                    'level_value' => $analyse->level,
                    'parent_code' => $analyse->parent_id,
                    'resultats' => $analyse->resultats,
                    'enfants' => $analyse->enfants->filter(fn($enfant) => $enfant->resultats->isNotEmpty())
                ]);
            }
        }
        
        return $analysesOrganisees;
    }

    /**
     * ✅ EXTRAIRE valeur minimum depuis valeur_ref
     */
    private function extraireValeurMin(?string $valeurRef): ?string
    {
        if (!$valeurRef) return null;
        
        // Formats possibles : "10-20", "≥10", ">10", "10 - 20"
        if (preg_match('/^(\d+(?:\.\d+)?)\s*[-–]\s*\d+/', $valeurRef, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/[≥>]\s*(\d+(?:\.\d+)?)/', $valeurRef, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * ✅ EXTRAIRE valeur maximum depuis valeur_ref
     */
    private function extraireValeurMax(?string $valeurRef): ?string
    {
        if (!$valeurRef) return null;
        
        // Formats possibles : "10-20", "≤20", "<20", "10 - 20"
        if (preg_match('/\d+(?:\.\d+)?\s*[-–]\s*(\d+(?:\.\d+)?)/', $valeurRef, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/[≤<]\s*(\d+(?:\.\d+)?)/', $valeurRef, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * ✅ GÉNÉRATION du nom de fichier optimisé
     */
    private function genererNomFichier(Prescription $prescription): string
    {
        $reference = str_replace(['/', '\\', ' '], '_', $prescription->reference);
        $patient = str_replace([' ', '.'], '_', $prescription->patient->nom);
        $date = date('Y-m-d_H-i-s');
        
        return "Resultats_{$reference}_{$patient}_{$date}.pdf";
    }

    /**
     * ✅ LOGGING optimisé
     */
    private function loggerGenerationPdf(Prescription $prescription): void
    {
        Log::info('Génération PDF résultats réussie', [
            'prescription_id' => $prescription->id,
            'reference' => $prescription->reference,
            'patient' => $prescription->patient->nom . ' ' . $prescription->patient->prenom,
            'user_id' => Auth::id(),
            'analyses_count' => $prescription->analyses->count(),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * ✅ GESTION d'erreur optimisée
     */
    private function gererErreur(\Exception $e, ?Prescription $prescription): \Illuminate\Http\RedirectResponse
    {
        Log::error('Erreur génération PDF résultats', [
            'prescription_id' => $prescription->id ?? 'N/A',
            'reference' => $prescription->reference ?? 'N/A',
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'user_id' => Auth::id(),
            'timestamp' => now()->toISOString()
        ]);

        return back()->with('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
    }
}