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
    private $anterioriteService;

       
    public function __construct(AnterioriteService $anterioriteService)
    {
        $this->anterioriteService = $anterioriteService;
    }
    /**
     * Récupérer les examens avec leurs analyses et résultats validés OU terminés
     */
    private function getValidatedExamens(Prescription $prescription)
    {
        // ✅ NOUVEAU : Calculer les antériorités avant de récupérer les données
        $this->anterioriteService->calculerAnteriorites($prescription);
        
        // 1. Récupérer les résultats selon le statut de la prescription
        $query = Resultat::where('prescription_id', $prescription->id);
        
        if ($prescription->status === Prescription::STATUS_VALIDE) {
            // Pour prescriptions validées : seulement les résultats validés
            $query->where('status', 'VALIDE')->whereNotNull('validated_by');
        } else {
            // Pour prescriptions terminées : tous les résultats saisis
            $query->where(function($q) {
                $q->whereNotNull('valeur')
                  ->where('valeur', '!=', '')
                  ->orWhereNotNull('resultats');
            });
        }
        
        // ✅ MODIFIÉ : Charger les relations d'antériorité
        $validatedResultats = $query->with([
            'analyse' => function($query) {
                $query->with(['type' => function($q) {
                    $q->withDefault(['name' => 'UNKNOWN']);
                }, 'examen'])->orderBy('ordre', 'asc');
            },
            'anteriorite_prescription' // ✅ NOUVEAU : Charger la prescription d'antériorité
        ])->get();

        if ($validatedResultats->isEmpty()) {
            return collect();
        }

        // 2. Récupérer les IDs d'analyses
        $analysesIds = $validatedResultats->pluck('analyse_id')->unique();

        $analysesDosageSansResultats = $prescription->analyses()
            ->whereHas('type', function($q) {
                $q->where('name', 'DOSAGE');
            })
            ->whereNotIn('analyse_id', $analysesIds)
            ->pluck('analyse_id');
        $analysesIds = $analysesIds->merge($analysesDosageSansResultats)->unique();

        // 3. Récupérer les analyses avec hiérarchie
        $analyses = Analyse::where(function($query) use ($analysesIds) {
            $query->whereIn('id', $analysesIds)
                ->orWhereHas('children', function($q) use ($analysesIds) {
                    $q->whereIn('id', $analysesIds);
                });
        })->with(['enfantsRecursive' => function($query) use ($analysesIds) {
                $query->whereIn('id', $analysesIds)
                    ->with('type')
                    ->orderBy('ordre', 'asc')
                    ->with(['enfantsRecursive' => function($q) use ($analysesIds) {
                        $q->whereIn('id', $analysesIds)
                            ->with('type')
                            ->orderBy('ordre', 'asc');
                    }]);
            }])->orderBy('ordre', 'asc')->get();

        // 4. Récupérer les antibiogrammes
        $antibiogrammes = Antibiogramme::where('prescription_id', $prescription->id)
            ->with([
                'bacterie.famille',
                'analyse',
                'resultatsAntibiotiques' => function($query) {
                    $query->with('antibiotique')->orderBy('interpretation', 'asc');
                }
            ])
            ->get()
            ->groupBy('analyse_id');

        // 5. Associer les résultats aux analyses (INCHANGÉ mais avec antériorités incluses)
        $analyses = $analyses->map(function($analyse) use ($validatedResultats, $antibiogrammes, $prescription) {

            $resultatsAnalyse = $validatedResultats->where('analyse_id', $analyse->id);
            if ($resultatsAnalyse->isEmpty() && $analyse->type && $analyse->type->name === 'DOSAGE') {
                $analyse->resultats = collect([new Resultat([
                    'prescription_id' => $prescription->id,
                    'analyse_id' => $analyse->id,
                    'valeur' => null,
                    'resultats' => null,
                    'est_pathologique' => false,
                    'status' => 'EN_ATTENTE'
                ])]);
            } else {
                $analyse->resultats = $resultatsAnalyse;
            }

            // Ajouter les antibiogrammes
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

            if ($analyse->children) {
                $analyse->children = $analyse->children->map(function($child) use ($validatedResultats, $antibiogrammes, $prescription) {
                    $resultatsEnfant = $validatedResultats->where('analyse_id', $child->id);
                    if ($resultatsEnfant->isEmpty() && $child->type && $child->type->name === 'DOSAGE') {
                        $child->resultats = collect([new Resultat([
                            'prescription_id' => $prescription->id,
                            'analyse_id' => $child->id,
                            'valeur' => null,
                            'resultats' => null,
                            'est_pathologique' => false,
                            'status' => 'EN_ATTENTE'
                        ])]);
                    } else {
                        $child->resultats = $resultatsEnfant;
                    }

                    // Antibiogrammes pour enfants
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

                    if ($child->children) {
                        $child->children = $child->children->map(function($subChild) use ($validatedResultats, $antibiogrammes, $prescription) {
                            $resultatsSubChild = $validatedResultats->where('analyse_id', $subChild->id);
                            if ($resultatsSubChild->isEmpty() && $subChild->type && $subChild->type->name === 'DOSAGE') {
                                $subChild->resultats = collect([new Resultat([
                                    'prescription_id' => $prescription->id,
                                    'analyse_id' => $subChild->id,
                                    'valeur' => null,
                                    'resultats' => null,
                                    'est_pathologique' => false,
                                    'status' => 'EN_ATTENTE'
                                ])]);
                            } else {
                                $subChild->resultats = $resultatsSubChild;
                            }

                            // Antibiogrammes pour petits-enfants
                            $antibiogrammesSubChild = $antibiogrammes->get($subChild->id, collect())->map(function($antibiogramme) {
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

                            $subChild->antibiogrammes = $antibiogrammesSubChild;
                            $subChild->has_antibiogrammes = $antibiogrammesSubChild->isNotEmpty();

                            return $subChild;
                        });
                    }
                    return $child;
                });
            }
            return $analyse;
        });

        // 6. Regrouper et ordonner les examens (INCHANGÉ)
        return Examen::whereHas('analyses', function($query) use ($analyses) {
            $query->whereIn('id', $analyses->pluck('id'));
        })
        ->with(['analyses' => function($query) {
            $query->orderBy('ordre', 'asc')
                ->with(['children' => function($q) {
                    $q->orderBy('ordre', 'asc')
                        ->with(['children' => function($sq) {
                            $sq->orderBy('ordre', 'asc');
                        }]);
                }]);
        }])
        ->get()
        ->map(function($examen) use ($analyses) {
            $analysesUniques = collect();

            $examen->analyses->each(function($analyse) use ($analyses, &$analysesUniques) {
                $matchingAnalyse = $analyses->firstWhere('id', $analyse->id);
                if ($matchingAnalyse && !$analysesUniques->contains('id', $matchingAnalyse->id)) {
                    $analyse->resultats = $matchingAnalyse->resultats;
                    $analyse->children = $matchingAnalyse->children;
                    $analyse->antibiogrammes = $matchingAnalyse->antibiogrammes;
                    $analyse->has_antibiogrammes = $matchingAnalyse->has_antibiogrammes;
                    $analysesUniques->push($analyse);
                }
            });

            $examen->analyses = $analysesUniques->sortBy('ordre');
            return $examen;
        });
    }


    /**
     * Récupérer les examens avec tous les résultats saisis
     */
    private function getAllResultsExamens(Prescription $prescription)
    {
        // 1. Récupérer tous les résultats saisis
       $this->anterioriteService->calculerAnteriorites($prescription);
        
        // 1. Récupérer tous les résultats saisis
        $allResultats = Resultat::where('prescription_id', $prescription->id)
            ->where(function($query) {
                $query->whereNotNull('valeur')
                      ->where('valeur', '!=', '')
                      ->orWhereNotNull('resultats');
            })
            ->with([
                'analyse' => function($query) {
                    $query->with(['type', 'examen'])
                          ->orderBy('ordre', 'asc');
                },
                'anteriorite_prescription' // ✅ NOUVEAU : Charger la prescription d'antériorité
            ])
            ->get();

        if ($allResultats->isEmpty()) {
            return collect();
        }


        // 2. Récupérer les IDs d'analyses
        $analysesIds = $allResultats->pluck('analyse_id')->unique();

        // 3. Récupérer les analyses avec hiérarchie
        $analyses = Analyse::where(function($query) use ($analysesIds) {
            $query->whereIn('id', $analysesIds)
                ->orWhereHas('children', function($q) use ($analysesIds) {
                    $q->whereIn('id', $analysesIds);
                });
        })
        ->with(['children' => function($query) use ($analysesIds) {
            $query->whereIn('id', $analysesIds)
                ->orderBy('ordre', 'asc')
                ->with(['children' => function($q) use ($analysesIds) {
                    $q->whereIn('id', $analysesIds)
                        ->orderBy('ordre', 'asc');
                }]);
        }])
        ->orderBy('ordre', 'asc')
        ->get();

        // 4. Récupérer les antibiogrammes
        $antibiogrammes = Antibiogramme::where('prescription_id', $prescription->id)
            ->with([
                'bacterie.famille',
                'analyse',
                'resultatsAntibiotiques' => function($query) {
                    $query->with('antibiotique')->orderBy('interpretation', 'asc');
                }
            ])
            ->get()
            ->groupBy('analyse_id');

        // 5. Associer les résultats aux analyses
        $analyses = $analyses->map(function($analyse) use ($allResultats, $antibiogrammes) {
            $analyse->resultats = $allResultats->where('analyse_id', $analyse->id);

            // Ajouter les antibiogrammes
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

            if ($analyse->children) {
                $analyse->children = $analyse->children->map(function($child) use ($allResultats, $antibiogrammes) {
                    $child->resultats = $allResultats->where('analyse_id', $child->id);

                    // Antibiogrammes pour enfants
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

                    if ($child->children) {
                        $child->children = $child->children->map(function($subChild) use ($allResultats, $antibiogrammes) {
                            $subChild->resultats = $allResultats->where('analyse_id', $subChild->id);

                            // Antibiogrammes pour petits-enfants
                            $antibiogrammesSubChild = $antibiogrammes->get($subChild->id, collect())->map(function($antibiogramme) {
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

                            $subChild->antibiogrammes = $antibiogrammesSubChild;
                            $subChild->has_antibiogrammes = $antibiogrammesSubChild->isNotEmpty();

                            return $subChild;
                        });
                    }
                    return $child;
                });
            }
            return $analyse;
        });

        // 6. Regrouper et ordonner les examens
        return Examen::whereHas('analyses', function($query) use ($analyses) {
            $query->whereIn('id', $analyses->pluck('id'));
        })
        ->with(['analyses' => function($query) {
            $query->orderBy('ordre', 'asc')
                ->with(['children' => function($q) {
                    $q->orderBy('ordre', 'asc')
                        ->with(['children' => function($sq) {
                            $sq->orderBy('ordre', 'asc');
                        }]);
                }]);
        }])
        ->get()
        ->map(function($examen) use ($analyses) {
            $analysesUniques = collect();

            $examen->analyses->each(function($analyse) use ($analyses, &$analysesUniques) {
                $matchingAnalyse = $analyses->firstWhere('id', $analyse->id);
                if ($matchingAnalyse && !$analysesUniques->contains('id', $matchingAnalyse->id)) {
                    $analyse->resultats = $matchingAnalyse->resultats;
                    $analyse->children = $matchingAnalyse->children;
                    $analyse->antibiogrammes = $matchingAnalyse->antibiogrammes;
                    $analyse->has_antibiogrammes = $matchingAnalyse->has_antibiogrammes;
                    $analysesUniques->push($analyse);
                }
            });

            $examen->analyses = $analysesUniques->sortBy('ordre');
            return $examen;
        });
    }

    /**
     * Générer le PDF FINAL des résultats - CORRIGÉ pour accepter TERMINE et VALIDE
     */
    public function generateFinalPDF(Prescription $prescription)
    {
        // Vérifications existantes...
        if (!in_array($prescription->status, [Prescription::STATUS_VALIDE, Prescription::STATUS_TERMINE])) {
            throw new \Exception('La prescription doit être terminée ou validée pour générer le PDF final');
        }

        $hasResults = false;
        
        if ($prescription->status === Prescription::STATUS_VALIDE) {
            $hasResults = Resultat::where('prescription_id', $prescription->id)
                ->where('status', 'VALIDE')
                ->whereNotNull('validated_by')
                ->exists();
        } else {
            $hasResults = Resultat::where('prescription_id', $prescription->id)
                ->where(function($query) {
                    $query->whereNotNull('valeur')
                          ->where('valeur', '!=', '')
                          ->orWhereNotNull('resultats');
                })
                ->exists();
        }

        $hasAntibiogrammes = Antibiogramme::where('prescription_id', $prescription->id)->exists();

        if (!$hasResults && !$hasAntibiogrammes) {
            throw new \Exception('Aucun résultat saisi ou antibiogramme trouvé pour cette prescription');
        }

        $examens = $this->getValidatedExamens($prescription);

        if ($examens->isEmpty()) {
            throw new \Exception('Aucun résultat trouvé pour cette prescription');
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
        $prescription->load(['patient', 'prescripteur']);

        // Créer le nom de fichier avec timestamp
        $timestamp = time();
        $prefix = $type === 'final' ? 'resultats-final' : 'apercu-resultats';
        $filename = $prefix . '-' . $prescription->reference . '-' . $timestamp . '.pdf';

        // ✅ NOUVEAU : Statistiques d'antériorités pour le PDF
        $totalAnteriorites = Resultat::where('prescription_id', $prescription->id)
            ->whereNotNull('anteriorite')
            ->count();

        $data = [
            'prescription' => $prescription,
            'examens' => $examens,
            'type_pdf' => $type,
            'laboratoire_name' => config('app.laboratoire_name', 'LABORATOIRE D\'ANALYSE CTB NOSYBE'),
            'date_generation' => now()->format('d/m/Y H:i'),
            'total_anteriorites' => $totalAnteriorites, // ✅ NOUVEAU
        ];

        $pdf = PDF::loadView('pdf.analyses.resultats-analyses', $data);
        $pdf->setPaper('A4', 'portrait');

        $path = 'pdfs/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        // ✅ NOUVEAU : Log des antériorités générées
        if ($totalAnteriorites > 0) {
        }

        return Storage::disk('public')->url($path);
    }


    /**
     * Forcer le recalcul des antériorités avant génération PDF
     */
    public function generatePDFWithFreshAnteriorites(Prescription $prescription, string $type = 'final')
    {
        // Forcer le recalcul des antériorités
        $this->anterioriteService->calculerAnteriorites($prescription);
        
        if ($type === 'preview') {
            return $this->generatePreviewPDF($prescription);
        }
        
        return $this->generateFinalPDF($prescription);
    }

    /**
     * Obtenir les statistiques d'antériorités pour une prescription
     */
    public function getAnterioriteStats(Prescription $prescription): array
    {
        $resultats = Resultat::where('prescription_id', $prescription->id)->get();
        
        $totalResultats = $resultats->count();
        $resultatsAvecAnteriorite = $resultats->whereNotNull('anteriorite')->count();
        
        $analyses = $resultats->whereNotNull('anteriorite')
            ->groupBy('analyse.designation')
            ->map(function($group) {
                return $group->count();
            });

        return [
            'total_resultats' => $totalResultats,
            'avec_anteriorite' => $resultatsAvecAnteriorite,
            'pourcentage' => $totalResultats > 0 ? round(($resultatsAvecAnteriorite / $totalResultats) * 100, 1) : 0,
            'analyses_avec_anteriorite' => $analyses->toArray()
        ];
    }


    /**
     * Vérifier si on peut générer le PDF final - CORRIGÉ
     */
    public function canGenerateFinalPdf(Prescription $prescription): bool
    {
        // CORRECTION : Accepter TERMINE et VALIDE
        if (!in_array($prescription->status, [Prescription::STATUS_VALIDE, Prescription::STATUS_TERMINE])) {
            return false;
        }

        $hasResults = false;
        
        if ($prescription->status === Prescription::STATUS_VALIDE) {
            $hasResults = Resultat::where('prescription_id', $prescription->id)
                ->where('status', 'VALIDE')
                ->whereNotNull('validated_by')
                ->exists();
        } else {
            $hasResults = Resultat::where('prescription_id', $prescription->id)
                ->where(function($query) {
                    $query->whereNotNull('valeur')
                          ->where('valeur', '!=', '')
                          ->orWhereNotNull('resultats');
                })
                ->exists();
        }

        $hasAntibiogrammes = Antibiogramme::where('prescription_id', $prescription->id)->exists();

        return $hasResults || $hasAntibiogrammes;
    }

    /**
     * Vérifier si on peut générer l'aperçu PDF
     */
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