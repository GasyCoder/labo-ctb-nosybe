<?php

namespace App\Services;

use App\Models\Examen;
use App\Models\Analyse;
use App\Models\Resultat;
use App\Models\Prescription;
use App\Models\Antibiogramme;
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

        return $this->buildExamensStructure($validatedResultats, $prescription->id);
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

        return $this->buildExamensStructure($allResultats, $prescription->id);
    }

    /**
     * Construire la structure des examens avec leurs résultats et antibiogrammes
     */
    private function buildExamensStructure($resultats, $prescriptionId)
    {
        $analysesIds = $resultats->pluck('analyse_id')->unique();

        // ÉTAPE 1: Récupérer toutes les analyses liées
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
                'enfantsRecursive' => function($query) {
                    $query->orderBy('ordre', 'asc')->with(['type']);
                }
            ])
            ->orderBy('ordre', 'asc')
            ->get();

        // ÉTAPE 2: ✅ CORRECTION MAJEURE - Récupérer TOUS les antibiogrammes de la prescription
        $antibiogrammes = collect();
        if ($prescriptionId) {
            // Récupérer TOUS les antibiogrammes de cette prescription, pas seulement ceux des analyses avec résultats
            $antibiogrammes = Antibiogramme::where('prescription_id', $prescriptionId)
                ->with([
                    'bacterie.famille',
                    'analyse',
                    'resultatsAntibiotiques' => function($query) {
                        $query->with('antibiotique')->orderBy('interpretation', 'asc');
                    }
                ])
                ->get()
                ->groupBy('analyse_id');
                
            Log::info('Antibiogrammes récupérés:', [
                'prescription_id' => $prescriptionId,
                'total_antibiogrammes' => $antibiogrammes->flatten()->count(),
                'analyses_avec_antibiogrammes' => $antibiogrammes->keys()->toArray()
            ]);
        }

        // ÉTAPE 3: Enrichir toutes les analyses avec des IDs de résultats ET d'antibiogrammes
        $toutesAnalysesIds = $allAnalyses->pluck('id')
            ->merge($antibiogrammes->keys())
            ->unique();

        // ÉTAPE 4: Traiter chaque analyse
        $analysesAvecResultats = $allAnalyses->map(function($analyse) use ($resultats, $antibiogrammes) {
            $resultatsAnalyse = $resultats->where('analyse_id', $analyse->id);
            
            $analyse->resultats = $resultatsAnalyse;
            $analyse->has_results = $resultatsAnalyse->isNotEmpty();
            $analyse->is_parent = is_null($analyse->parent_id) || $analyse->level === 'PARENT';
            $analyse->pdf_level = $analyse->is_parent ? 0 : 1;

            // ✅ CORRECTION : Récupérer les antibiogrammes pour cette analyse
            $antibiogrammesAnalyse = $antibiogrammes->get($analyse->id, collect())->map(function($antibiogramme) {
                Log::info('Traitement antibiogramme:', [
                    'antibiogramme_id' => $antibiogramme->id,
                    'analyse_id' => $antibiogramme->analyse_id,
                    'bacterie' => $antibiogramme->bacterie->designation ?? 'Unknown',
                    'nb_antibiotiques' => $antibiogramme->resultatsAntibiotiques->count()
                ]);
                
                // Organiser les antibiotiques par interprétation
                $antibiotiques = $antibiogramme->resultatsAntibiotiques->groupBy('interpretation');
                
                // Créer un objet simple avec les propriétés nécessaires
                $antibiogrammeFormatted = (object) [
                    'id' => $antibiogramme->id,
                    'bacterie' => $antibiogramme->bacterie,
                    'notes' => $antibiogramme->notes,
                    'antibiotiques_sensibles' => $antibiotiques->get('S', collect()),
                    'antibiotiques_resistants' => $antibiotiques->get('R', collect()),
                    'antibiotiques_intermediaires' => $antibiotiques->get('I', collect()),
                ];
                
                return $antibiogrammeFormatted;
            });

            $analyse->antibiogrammes = $antibiogrammesAnalyse;
            $analyse->has_antibiogrammes = $antibiogrammesAnalyse->isNotEmpty();

            // ✅ CORRECTION : Traiter les enfants récursivement
            if ($analyse->is_parent && $analyse->enfantsRecursive && $analyse->enfantsRecursive->isNotEmpty()) {
                $children = $this->processChildrenRecursively($analyse->enfantsRecursive, $resultats, $antibiogrammes);
                $analyse->children = $children;
            } else {
                $analyse->children = collect();
            }

            return $analyse;
        });

        // ✅ CORRECTION : Ajouter les analyses qui ont SEULEMENT des antibiogrammes (sans résultats)
        $analysesSeulementAntibiogrammes = $antibiogrammes->keys()->diff($allAnalyses->pluck('id'));
        
        if ($analysesSeulementAntibiogrammes->isNotEmpty()) {
            $analysesSupplementaires = Analyse::whereIn('id', $analysesSeulementAntibiogrammes)
                ->with(['type', 'examen', 'parent'])
                ->get()
                ->map(function($analyse) use ($resultats, $antibiogrammes) {
                    $analyse->resultats = collect();
                    $analyse->has_results = false;
                    $analyse->is_parent = is_null($analyse->parent_id) || $analyse->level === 'PARENT';
                    $analyse->pdf_level = $analyse->is_parent ? 0 : 1;
                    
                    // Antibiogrammes pour cette analyse
                    $antibiogrammesAnalyse = $antibiogrammes->get($analyse->id, collect())->map(function($antibiogramme) {
                        $antibiotiques = $antibiogramme->resultatsAntibiotiques->groupBy('interpretation');
                        
                        return (object) [
                            'id' => $antibiogramme->id,
                            'bacterie' => $antibiogramme->bacterie,
                            'notes' => $antibiogramme->notes,
                            'antibiotiques_sensibles' => $antibiotiques->get('S', collect()),
                            'antibiotiques_resistants' => $antibiotiques->get('R', collect()),
                            'antibiotiques_intermediaires' => $antibiotiques->get('I', collect()),
                        ];
                    });
                    
                    $analyse->antibiogrammes = $antibiogrammesAnalyse;
                    $analyse->has_antibiogrammes = $antibiogrammesAnalyse->isNotEmpty();
                    $analyse->children = collect();
                    
                    return $analyse;
                });
                
            $analysesAvecResultats = $analysesAvecResultats->merge($analysesSupplementaires);
        }

        // ÉTAPE 5: Grouper par examens et filtrer
        return $analysesAvecResultats->groupBy('examen_id')
            ->map(function($analyses, $examenId) use ($resultats) {
                $examen = $analyses->first()->examen;
                
                // ✅ CORRECTION : Filtrer pour inclure les analyses avec antibiogrammes
                $analysesFiltered = $analyses->filter(function($analyse) {
                    $hasResults = $analyse->has_results;
                    $hasChildrenWithResults = $analyse->children->where('has_results', true)->isNotEmpty();
                    $hasAntibiogrammes = $analyse->has_antibiogrammes;
                    $hasChildrenWithAntibiogrammes = $analyse->children->where('has_antibiogrammes', true)->isNotEmpty();
                    $isInfoLine = !$hasResults && $analyse->designation && 
                                  ($analyse->prix == 0 || $analyse->level === 'PARENT');
                    
                    $shouldDisplay = $hasResults || $hasChildrenWithResults || $hasAntibiogrammes || 
                                   $hasChildrenWithAntibiogrammes || $isInfoLine;
                    
                    Log::info('Filtrage analyse:', [
                        'analyse_id' => $analyse->id,
                        'designation' => $analyse->designation,
                        'has_results' => $hasResults,
                        'has_antibiogrammes' => $hasAntibiogrammes,
                        'should_display' => $shouldDisplay
                    ]);
                    
                    return $shouldDisplay;
                });

                if ($analysesFiltered->isEmpty()) {
                    return null;
                }

                $examen->analyses = $analysesFiltered->sortBy('ordre')->values();
                $examen->conclusions = $this->getExamenConclusions($examen, $resultats);
                
                return $examen;
            })
            ->filter()
            ->values();
    }

    /**
     * Traiter les enfants récursivement
     */
    private function processChildrenRecursively($children, $resultats, $antibiogrammes)
    {
        return $children->map(function($child) use ($resultats, $antibiogrammes) {
            $resultatsEnfant = $resultats->where('analyse_id', $child->id);
            
            $child->resultats = $resultatsEnfant;
            $child->has_results = $resultatsEnfant->isNotEmpty();
            $child->is_parent = false;
            $child->pdf_level = 1;
            
            // ✅ CORRECTION : Antibiogrammes pour les enfants
            $antibiogrammesEnfant = $antibiogrammes->get($child->id, collect())->map(function($antibiogramme) {
                $antibiotiques = $antibiogramme->resultatsAntibiotiques->groupBy('interpretation');
                
                return (object) [
                    'id' => $antibiogramme->id,
                    'bacterie' => $antibiogramme->bacterie,
                    'notes' => $antibiogramme->notes,
                    'antibiotiques_sensibles' => $antibiotiques->get('S', collect()),
                    'antibiotiques_resistants' => $antibiotiques->get('R', collect()),
                    'antibiotiques_intermediaires' => $antibiotiques->get('I', collect()),
                ];
            });

            $child->antibiogrammes = $antibiogrammesEnfant;
            $child->has_antibiogrammes = $antibiogrammesEnfant->isNotEmpty();
            
            // Récursion pour les petits-enfants
            if ($child->enfantsRecursive && $child->enfantsRecursive->isNotEmpty()) {
                $child->children = $this->processChildrenRecursively($child->enfantsRecursive, $resultats, $antibiogrammes);
            } else {
                $child->children = collect();
            }
            
            return $child;
        });
    }

    /**
     * Récupérer les conclusions pour un examen
     */
    private function getExamenConclusions($examen, $resultats)
    {
        $conclusions = collect();

        foreach ($examen->analyses as $analyse) {
            // Conclusions de l'analyse parent
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

            // Conclusions des enfants
            $this->collectConclusionsRecursively($analyse->children, $resultats, $conclusions);
        }

        return $conclusions;
    }

    /**
     * Collecter les conclusions récursivement
     */
    private function collectConclusionsRecursively($children, $resultats, &$conclusions)
    {
        foreach ($children as $child) {
            $resultatsEnfant = $resultats->where('analyse_id', $child->id);
            
            foreach ($resultatsEnfant as $resultat) {
                if (!empty($resultat->conclusion)) {
                    $conclusions->push([
                        'analyse_id' => $child->id,
                        'analyse_designation' => $child->designation,
                        'conclusion' => $resultat->conclusion,
                        'resultat_id' => $resultat->id
                    ]);
                }
            }

            if ($child->children && $child->children->isNotEmpty()) {
                $this->collectConclusionsRecursively($child->children, $resultats, $conclusions);
            }
        }
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

        $hasAntibiogrammes = Antibiogramme::where('prescription_id', $prescription->id)->exists();

        if (!$hasValidResults && !$hasAntibiogrammes) {
            throw new \Exception('Aucun résultat validé ou antibiogramme trouvé pour cette prescription');
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

        $hasAntibiogrammes = Antibiogramme::where('prescription_id', $prescription->id)->exists();

        if (!$hasAnyResults && !$hasAntibiogrammes) {
            throw new \Exception('Aucun résultat saisi ou antibiogramme trouvé pour cette prescription');
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
     * Debug: Afficher les antibiogrammes d'une prescription
     */
    public function debugAntibiogrammes(Prescription $prescription)
    {
        $antibiogrammes = Antibiogramme::where('prescription_id', $prescription->id)
            ->with([
                'bacterie.famille',
                'analyse',
                'resultatsAntibiotiques.antibiotique'
            ])
            ->get();

        $debug = [
            'prescription_id' => $prescription->id,
            'total_antibiogrammes' => $antibiogrammes->count(),
            'antibiogrammes' => $antibiogrammes->map(function($antibiogramme) {
                return [
                    'id' => $antibiogramme->id,
                    'analyse_id' => $antibiogramme->analyse_id,
                    'analyse_name' => $antibiogramme->analyse->designation ?? 'N/A',
                    'bacterie_id' => $antibiogramme->bacterie_id,
                    'bacterie_name' => $antibiogramme->bacterie->designation ?? 'N/A',
                    'nb_antibiotiques' => $antibiogramme->resultatsAntibiotiques->count(),
                    'antibiotiques' => $antibiogramme->resultatsAntibiotiques->map(function($ra) {
                        return [
                            'antibiotique' => $ra->antibiotique->designation ?? 'N/A',
                            'interpretation' => $ra->interpretation,
                            'diametre_mm' => $ra->diametre_mm
                        ];
                    })->toArray()
                ];
            })->toArray()
        ];

        Log::info('Debug Antibiogrammes', $debug);
        return $debug;
    }

    // Les autres méthodes restent identiques...
    public function canGenerateFinalPdf(Prescription $prescription): bool
    {
        return $prescription->status === Prescription::STATUS_VALIDE && 
               (Resultat::where('prescription_id', $prescription->id)
                   ->where('status', 'VALIDE')
                   ->whereNotNull('validated_by')
                   ->exists() ||
                Antibiogramme::where('prescription_id', $prescription->id)->exists());
    }

    public function canGeneratePreviewPdf(Prescription $prescription): bool
    {
        return Resultat::where('prescription_id', $prescription->id)
                   ->where(function($query) {
                       $query->whereNotNull('valeur')
                             ->where('valeur', '!=', '')
                             ->orWhereNotNull('resultats');
                   })
                   ->exists() ||
               Antibiogramme::where('prescription_id', $prescription->id)->exists();
    }
}