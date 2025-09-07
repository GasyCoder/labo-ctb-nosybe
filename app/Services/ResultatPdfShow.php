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
     * Récupérer les examens avec leurs analyses et résultats validés (pour PDF final)
     */
    private function getValidatedExamens(Prescription $prescription)
    {
        $validatedResultats = Resultat::where('prescription_id', $prescription->id)
            ->whereNotNull('validated_by')
            ->where('status', 'VALIDE')
            ->with(['analyse' => function($query) {
                $query->with(['type', 'examen'])
                      ->orderBy('ordre', 'asc');
            }])
            ->get();

        if ($validatedResultats->isEmpty()) {
            return collect();
        }

        return $this->buildExamensStructure($validatedResultats);
    }

    /**
     * Récupérer les examens avec tous les résultats saisis (pour aperçu PDF)
     */
    private function getAllResultsExamens(Prescription $prescription)
    {
        $allResultats = Resultat::where('prescription_id', $prescription->id)
            ->where(function($query) {
                $query->whereNotNull('valeur')
                      ->where('valeur', '!=', '')
                      ->orWhereNotNull('resultats');
            })
            ->with(['analyse' => function($query) {
                $query->with(['type', 'examen'])
                      ->orderBy('ordre', 'asc');
            }])
            ->get();

        if ($allResultats->isEmpty()) {
            return collect();
        }

        return $this->buildExamensStructure($allResultats);
    }

    /**
     * Construire la structure des examens avec leurs résultats
     */
    private function buildExamensStructure($resultats)
    {
        $analysesIds = $resultats->pluck('analyse_id')->unique();

        // Récupérer toutes les analyses liées
        $allAnalyses = Analyse::where(function($query) use ($analysesIds) {
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
                'parent',
                'enfantsRecursive' => function($query) use ($analysesIds) {
                    $query->whereIn('id', $analysesIds)
                          ->orderBy('ordre', 'asc')
                          ->with(['type']);
                }
            ])
            ->orderBy('ordre', 'asc')
            ->get();

        // Associer les résultats et structurer
        $analysesAvecResultats = $allAnalyses->map(function($analyse) use ($resultats) {
            $resultatsAnalyse = $resultats->where('analyse_id', $analyse->id);
            
            $analyse->resultats = $resultatsAnalyse;
            $analyse->has_results = $resultatsAnalyse->isNotEmpty();
            $analyse->is_parent = is_null($analyse->parent_id) || $analyse->level === 'PARENT';
            $analyse->pdf_level = $analyse->is_parent ? 0 : 1;

            // Traiter les enfants
            if ($analyse->enfantsRecursive && $analyse->enfantsRecursive->isNotEmpty()) {
                $analyse->children = $analyse->enfantsRecursive->map(function($child) use ($resultats) {
                    $resultatsEnfant = $resultats->where('analyse_id', $child->id);
                    
                    $child->resultats = $resultatsEnfant;
                    $child->has_results = $resultatsEnfant->isNotEmpty();
                    $child->is_parent = false;
                    $child->pdf_level = 1;
                    
                    return $child;
                });
            } else {
                $analyse->children = collect();
            }

            return $analyse;
        });

        // Grouper par examens et filtrer
        return $analysesAvecResultats->groupBy('examen_id')
            ->map(function($analyses, $examenId) use ($resultats) {
                $examen = $analyses->first()->examen;
                
                $analysesFiltered = $analyses->filter(function($analyse) {
                    return $analyse->has_results || $analyse->children->where('has_results', true)->isNotEmpty();
                });

                if ($analysesFiltered->isEmpty()) {
                    return null;
                }

                $examen->analyses = $analysesFiltered->sortBy('ordre');
                $examen->conclusions = $this->getExamenConclusions($examen, $resultats);
                
                return $examen;
            })
            ->filter()
            ->values();
    }

    /**
     * Récupérer les conclusions pour un examen
     */
    private function getExamenConclusions($examen, $resultats)
    {
        $conclusions = collect();

        foreach ($examen->analyses as $analyse) {
            $resultatsAnalyse = $resultats->where('analyse_id', $analyse->id);
            
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
     * Générer le PDF FINAL des résultats validés uniquement
     */
    public function generateFinalPDF(Prescription $prescription)
    {
        if ($prescription->status !== Prescription::STATUS_VALIDE) {
            throw new \Exception('La prescription doit être validée pour générer le PDF final');
        }

        $hasValidResults = Resultat::where('prescription_id', $prescription->id)
            ->where('status', 'VALIDE')
            ->whereNotNull('validated_by')
            ->exists();

        if (!$hasValidResults) {
            throw new \Exception('Aucun résultat validé trouvé pour cette prescription');
        }

        $examens = $this->getValidatedExamens($prescription);

        if ($examens->isEmpty()) {
            throw new \Exception('Aucun résultat validé trouvé pour cette prescription');
        }

        return $this->generatePDF($prescription, $examens, 'final');
    }

    /**
     * Générer l'APERÇU PDF de tous les résultats saisis
     */
    public function generatePreviewPDF(Prescription $prescription)
    {
        $hasAnyResults = Resultat::where('prescription_id', $prescription->id)
            ->where(function($query) {
                $query->whereNotNull('valeur')
                      ->where('valeur', '!=', '')
                      ->orWhereNotNull('resultats');
            })
            ->exists();

        if (!$hasAnyResults) {
            throw new \Exception('Aucun résultat saisi trouvé pour cette prescription');
        }

        $examens = $this->getAllResultsExamens($prescription);

        if ($examens->isEmpty()) {
            throw new \Exception('Aucun résultat saisi trouvé pour cette prescription');
        }

        return $this->generatePDF($prescription, $examens, 'preview');
    }

    /**
     * Méthode commune pour générer les PDFs
     */
    private function generatePDF(Prescription $prescription, $examens, $type = 'final')
    {
        // Charger les relations nécessaires
        $prescription->load([
            'patient', 
            'prescripteur',
            'resultats' => function($query) use ($type) {
                if ($type === 'final') {
                    $query->where('status', 'VALIDE')
                          ->with(['analyse', 'validatedBy']);
                } else {
                    $query->where(function($q) {
                              $q->whereNotNull('valeur')
                                ->where('valeur', '!=', '')
                                ->orWhereNotNull('resultats');
                          })
                          ->with(['analyse']);
                }
            }
        ]);

        $conclusionGenerale = $this->getConclusionGenerale($prescription, $type);

        // Créer le nom de fichier
        $timestamp = time();
        $prefix = $type === 'final' ? 'resultats-final' : 'apercu-resultats';
        $filename = $prefix . '-' . $prescription->reference . '-' . $timestamp . '.pdf';

        // Préparer les données pour la vue
        $data = [
            'prescription' => $prescription,
            'examens' => $examens,
            'conclusion_generale' => $conclusionGenerale,
            'type_pdf' => $type,
            'laboratoire_name' => config('app.laboratoire_name', 'LABORATOIRE'),
            'date_generation' => now()->format('d/m/Y H:i'),
            'validated_by' => $type === 'final' 
                ? ($prescription->resultats->first()?->validatedBy?->name ?? 'Non spécifié')
                : null
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
    }

    /**
     * Récupérer la conclusion générale de la prescription
     */
    private function getConclusionGenerale(Prescription $prescription, $type = 'final')
    {
        $query = Resultat::where('prescription_id', $prescription->id)
            ->whereNotNull('conclusion')
            ->where('conclusion', '!=', '')
            ->with('analyse');

        if ($type === 'final') {
            $query->where('status', 'VALIDE');
        }

        $resultat = $query->first();
        return $resultat ? $resultat->conclusion : null;
    }

    /**
     * Vérifier si une prescription peut générer un PDF final
     */
    public function canGenerateFinalPdf(Prescription $prescription): bool
    {
        return $prescription->status === Prescription::STATUS_VALIDE && 
               Resultat::where('prescription_id', $prescription->id)
                   ->where('status', 'VALIDE')
                   ->whereNotNull('validated_by')
                   ->exists();
    }

    /**
     * Vérifier si une prescription peut générer un aperçu PDF
     */
    public function canGeneratePreviewPdf(Prescription $prescription): bool
    {
        return Resultat::where('prescription_id', $prescription->id)
                   ->where(function($query) {
                       $query->whereNotNull('valeur')
                             ->where('valeur', '!=', '')
                             ->orWhereNotNull('resultats');
                   })
                   ->exists();
    }

    /**
     * Récupérer les statistiques des résultats pour une prescription
     */
    public function getResultatsStats(Prescription $prescription): array
    {
        return [
            'total' => Resultat::where('prescription_id', $prescription->id)->count(),
            'saisis' => Resultat::where('prescription_id', $prescription->id)
                ->where(function($query) {
                    $query->whereNotNull('valeur')
                          ->where('valeur', '!=', '')
                          ->orWhereNotNull('resultats');
                })
                ->count(),
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