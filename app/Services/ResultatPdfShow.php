<?php

namespace App\Services;

use App\Models\Examen;
use App\Models\Analyse;
use App\Models\Resultat;
use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ResultatPdfShow
{
    /**
     * Récupérer les examens avec leurs analyses et résultats validés
     * CORRECTION: Mieux gérer la relation entre analyses et résultats
     */
    private function getValidatedExamens(Prescription $prescription)
    {
        try {
            // 1. Récupérer les résultats validés avec leurs analyses
            $validatedResultats = Resultat::where('prescription_id', $prescription->id)
                ->whereNotNull('validated_by')
                ->where('status', 'VALIDE')
                ->with(['analyse' => function($query) {
                    $query->with(['type', 'examen'])
                          ->orderBy('ordre', 'asc');
                }])
                ->get();

            if ($validatedResultats->isEmpty()) {
                Log::warning('Aucun résultat validé trouvé pour la prescription', [
                    'prescription_id' => $prescription->id
                ]);
                return collect();
            }

            // 2. Récupérer les IDs d'analyses validées
            $analysesIds = $validatedResultats->pluck('analyse_id')->unique();

            // 3. Récupérer les analyses avec hiérarchie complète
            $analyses = Analyse::where(function($query) use ($analysesIds) {
                    $query->whereIn('id', $analysesIds)
                          ->orWhereHas('enfants', function($q) use ($analysesIds) {
                              $q->whereIn('id', $analysesIds);
                          })
                          ->orWhereHas('enfantsRecursive', function($q) use ($analysesIds) {
                              $q->whereIn('id', $analysesIds);
                          });
                })
                ->with([
                    'type',
                    'examen', 
                    'enfantsRecursive' => function($query) use ($analysesIds) {
                        $query->whereIn('id', $analysesIds)
                              ->orderBy('ordre', 'asc')
                              ->with('type');
                    }
                ])
                ->orderBy('ordre', 'asc')
                ->get();

            // 4. Associer les résultats aux analyses
            $analyses = $analyses->map(function($analyse) use ($validatedResultats) {
                // CORRECTION: Utiliser directement la collection des résultats validés
                $resultatsAnalyse = $validatedResultats->where('analyse_id', $analyse->id);
                
                // ✅ CORRECTION: Stocker les résultats directement sur l'analyse
                $analyse->resultats = $resultatsAnalyse;

                // Traiter récursivement les enfants
                if ($analyse->enfantsRecursive && $analyse->enfantsRecursive->isNotEmpty()) {
                    $analyse->children = $analyse->enfantsRecursive->map(function($child) use ($validatedResultats) {
                        $resultatsEnfant = $validatedResultats->where('analyse_id', $child->id);
                        
                        // ✅ CORRECTION: Stocker les résultats directement sur l'enfant
                        $child->resultats = $resultatsEnfant;
                        
                        return $child;
                    });
                } else {
                    $analyse->children = collect();
                }

                return $analyse;
            });

            // 5. Regrouper par examens
            $examensWithResults = Examen::whereHas('analyses', function($query) use ($analysesIds) {
                    $query->whereIn('id', $analysesIds);
                })
                ->with(['analyses' => function($query) use ($analysesIds) {
                    $query->whereIn('id', $analysesIds)
                          ->orderBy('ordre', 'asc')
                          ->with(['type', 'enfantsRecursive' => function($q) use ($analysesIds) {
                              $q->whereIn('id', $analysesIds)
                                ->orderBy('ordre', 'asc')
                                ->with('type');
                          }]);
                }])
                ->get()
                ->map(function($examen) use ($analyses, $validatedResultats) {
                    // Associer les analyses enrichies avec leurs résultats
                    $analysesEnrichies = collect();

                    foreach ($examen->analyses as $analyse) {
                        $analyseEnrichie = $analyses->firstWhere('id', $analyse->id);
                        if ($analyseEnrichie) {
                            $analysesEnrichies->push($analyseEnrichie);
                        }
                    }

                    $examen->analyses = $analysesEnrichies->sortBy('ordre');
                    
                    // ✅ CORRECTION: Ajouter les conclusions directement à l'examen
                    $examen->conclusions = $this->getExamenConclusions($examen, $validatedResultats);
                    
                    return $examen;
                })
                ->filter(function($examen) {
                    return $examen->analyses->isNotEmpty();
                });

            return $examensWithResults;

        } catch (\Exception $e) {
            Log::error('Erreur dans getValidatedExamens:', [
                'prescription_id' => $prescription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE: Récupérer les conclusions pour un examen
     */
    private function getExamenConclusions($examen, $validatedResultats)
    {
        $conclusions = collect();

        // Récupérer tous les résultats de cet examen qui ont une conclusion
        foreach ($examen->analyses as $analyse) {
            $resultatsAnalyse = $validatedResultats->where('analyse_id', $analyse->id);
            
            foreach ($resultatsAnalyse as $resultat) {
                if (!empty($resultat->conclusion)) {
                    $conclusions->push([
                        'analyse_id' => $analyse->id,
                        'analyse_designation' => $analyse->designation,
                        'conclusion' => $resultat->conclusion,
                        'resultat_id' => $resultat->id
                    ]);
                }
            }
        }

        return $conclusions;
    }

    /**
     * Générer le PDF des résultats avec la structure de votre modèle
     */
    public function generatePDF(Prescription $prescription)
    {
        try {
            // Vérifier que la prescription est validée
            if ($prescription->status !== Prescription::STATUS_VALIDE) {
                throw new \Exception('La prescription doit être validée pour générer le PDF');
            }

            // Vérifier qu'il y a des résultats validés en utilisant votre modèle
            $hasValidResults = Resultat::where('prescription_id', $prescription->id)
                ->where('status', 'VALIDE')
                ->whereNotNull('validated_by')
                ->exists();

            if (!$hasValidResults) {
                throw new \Exception('Aucun résultat validé trouvé pour cette prescription');
            }

            // Récupérer les examens avec résultats validés
            $examens = $this->getValidatedExamens($prescription);

            if ($examens->isEmpty()) {
                throw new \Exception('Aucun résultat validé trouvé pour cette prescription');
            }

            // Charger les relations nécessaires selon votre modèle
            $prescription->load([
                'patient', 
                'prescripteur',
                'resultats' => function($query) {
                    $query->where('status', 'VALIDE')
                          ->with(['analyse', 'validatedBy']);
                }
            ]);

            // ✅ CORRECTION: Récupérer la conclusion générale de la prescription
            $conclusionGenerale = $this->getConclusionGenerale($prescription);

            // Créer le nom de fichier avec timestamp
            $timestamp = time();
            $filename = 'resultats-' . $prescription->reference . '-' . $timestamp . '.pdf';

            // Préparer les données pour la vue
            $data = [
                'prescription' => $prescription,
                'examens' => $examens,
                'conclusion_generale' => $conclusionGenerale, // ✅ AJOUT
                'laboratoire_name' => config('app.laboratoire_name', 'LABORATOIRE'),
                'date_generation' => now()->format('d/m/Y H:i'),
                'validated_by' => $prescription->resultats->first()->validatedBy->name ?? 'Non spécifié'
            ];

            // Générer le PDF
            $pdf = PDF::loadView('pdf.analyses.resultats-analyses', $data);
            
            // Définir les options du PDF
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Helvetica'
            ]);

            // Sauvegarder le fichier
            $path = 'pdfs/' . $filename;
            Storage::disk('public')->put($path, $pdf->output());

            // Retourner l'URL publique
            return Storage::disk('public')->url($path);

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF ResultatPdfShow:', [
                'prescription_id' => $prescription->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE: Récupérer la conclusion générale de la prescription
     */
    private function getConclusionGenerale(Prescription $prescription)
    {
        // Récupérer la première conclusion non vide trouvée
        $resultat = Resultat::where('prescription_id', $prescription->id)
            ->where('status', 'VALIDE')
            ->whereNotNull('conclusion')
            ->where('conclusion', '!=', '')
            ->with('analyse')
            ->first();

        return $resultat ? $resultat->conclusion : null;
    }

    /**
     * Vérifier si une prescription peut générer un PDF
     */
    public function canGeneratePdf(Prescription $prescription): bool
    {
        return $prescription->status === Prescription::STATUS_VALIDE && 
               Resultat::where('prescription_id', $prescription->id)
                   ->where('status', 'VALIDE')
                   ->whereNotNull('validated_by')
                   ->exists();
    }

    /**
     * Récupérer les statistiques des résultats pour une prescription
     */
    public function getResultatsStats(Prescription $prescription): array
    {
        return [
            'total' => Resultat::where('prescription_id', $prescription->id)->count(),
            'valides' => Resultat::where('prescription_id', $prescription->id)
                ->where('status', 'VALIDE')
                ->count(),
            'pathologiques' => Resultat::where('prescription_id', $prescription->id)
                ->where('interpretation', 'PATHOLOGIQUE')
                ->count(),
            'normaux' => Resultat::where('prescription_id', $prescription->id)
                ->where('interpretation', 'NORMAL')
                ->count(),
        ];
    }
}