<?php

namespace App\Services;

use App\Models\Examen;
use App\Models\Analyse;
use App\Models\Resultat;
use App\Models\Prescription;
use App\Models\Antibiogramme;
use App\Models\ResultatAntibiotique;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ResultatPdfShow
{
    /**
     * ✅ CORRECTION : Récupérer les antibiogrammes selon la vraie structure
     */
    private function getAntibiogrammes($prescriptionId, $analyseId)
    {
        return Antibiogramme::where('prescription_id', $prescriptionId)
            ->where('analyse_id', $analyseId)
            ->with([
                'bacterie' => function($query) {
                    $query->with('famille');
                }
            ])
            ->get()
            ->map(function($antibiogramme) {
                // ✅ CORRECTION : Récupérer les résultats d'antibiotiques selon la vraie table
                $resultatsAntibiotiques = ResultatAntibiotique::where('antibiogramme_id', $antibiogramme->id)
                    ->with('antibiotique')
                    ->get();
                
                // ✅ CORRECTION : Grouper par interpretation (S, I, R)
                $antibiogramme->antibiotiques_sensibles = $resultatsAntibiotiques
                    ->where('interpretation', 'S');
                    
                $antibiogramme->antibiotiques_resistants = $resultatsAntibiotiques
                    ->where('interpretation', 'R');
                    
                $antibiogramme->antibiotiques_intermediaires = $resultatsAntibiotiques
                    ->where('interpretation', 'I');
                
                return $antibiogramme;
            });
    }

    /**
     * Récupérer les examens avec leurs analyses et résultats validés
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

            // 4. Associer les résultats aux analyses avec antibiogrammes
            $analyses = $analyses->map(function($analyse) use ($validatedResultats, $prescription) {
                $resultatsAnalyse = $validatedResultats->where('analyse_id', $analyse->id);
                $analyse->resultats = $resultatsAnalyse;

                // ✅ CORRECTION : Ajouter les antibiogrammes pour les analyses GERME/CULTURE
                if ($analyse->type && in_array(strtoupper($analyse->type->name), ['GERME', 'CULTURE'])) {
                    $analyse->antibiogrammes = $this->getAntibiogrammes($prescription->id, $analyse->id);
                }

                // Traiter récursivement les enfants
                if ($analyse->enfantsRecursive && $analyse->enfantsRecursive->isNotEmpty()) {
                    $analyse->children = $analyse->enfantsRecursive->map(function($child) use ($validatedResultats, $prescription) {
                        $resultatsEnfant = $validatedResultats->where('analyse_id', $child->id);
                        $child->resultats = $resultatsEnfant;
                        
                        // ✅ CORRECTION : Ajouter les antibiogrammes pour les enfants GERME/CULTURE
                        if ($child->type && in_array(strtoupper($child->type->name), ['GERME', 'CULTURE'])) {
                            $child->antibiogrammes = $this->getAntibiogrammes($prescription->id, $child->id);
                        }
                        
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
                    $analysesEnrichies = collect();

                    foreach ($examen->analyses as $analyse) {
                        $analyseEnrichie = $analyses->firstWhere('id', $analyse->id);
                        if ($analyseEnrichie) {
                            $analysesEnrichies->push($analyseEnrichie);
                        }
                    }

                    $examen->analyses = $analysesEnrichies->sortBy('ordre');
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
     * Récupérer les conclusions pour un examen
     */
    private function getExamenConclusions($examen, $validatedResultats)
    {
        $conclusions = collect();

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
     * Générer le PDF des résultats avec antibiogrammes
     */
    public function generatePDF(Prescription $prescription)
    {
        try {
            // Vérifications préalables
            if ($prescription->status !== Prescription::STATUS_VALIDE) {
                throw new \Exception('La prescription doit être validée pour générer le PDF');
            }

            $hasValidResults = Resultat::where('prescription_id', $prescription->id)
                ->where('status', 'VALIDE')
                ->whereNotNull('validated_by')
                ->exists();

            if (!$hasValidResults) {
                throw new \Exception('Aucun résultat validé trouvé pour cette prescription');
            }

            // Récupérer les examens avec résultats et antibiogrammes
            $examens = $this->getValidatedExamens($prescription);

            if ($examens->isEmpty()) {
                throw new \Exception('Aucun résultat validé trouvé pour cette prescription');
            }

            // Charger les relations nécessaires
            $prescription->load([
                'patient', 
                'prescripteur',
                'resultats' => function($query) {
                    $query->where('status', 'VALIDE')
                          ->with(['analyse', 'validatedBy']);
                }
            ]);

            // Récupérer la conclusion générale
            $conclusionGenerale = $this->getConclusionGenerale($prescription);

            // Créer le nom de fichier
            $timestamp = time();
            $filename = 'resultats-' . $prescription->reference . '-' . $timestamp . '.pdf';

            // Préparer les données pour la vue
            $data = [
                'prescription' => $prescription,
                'examens' => $examens,
                'conclusion_generale' => $conclusionGenerale,
                'laboratoire_name' => config('app.laboratoire_name', 'LABORATOIRE'),
                'date_generation' => now()->format('d/m/Y H:i'),
                'validated_by' => $prescription->resultats->first()->validatedBy->name ?? 'Non spécifié'
            ];

            // Générer le PDF
            $pdf = PDF::loadView('pdf.analyses.resultats-analyses', $data);
            
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Helvetica'
            ]);

            // Sauvegarder le fichier
            $path = 'pdfs/' . $filename;
            Storage::disk('public')->put($path, $pdf->output());

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
     * Récupérer la conclusion générale de la prescription
     */
    private function getConclusionGenerale(Prescription $prescription)
    {
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