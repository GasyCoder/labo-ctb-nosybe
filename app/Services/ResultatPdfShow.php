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
     * Récupérer les examens avec leurs analyses et résultats validés (SIMPLIFIÉ selon ancien code)
     */
    private function getValidatedExamens(Prescription $prescription)
    {
        // 1. Récupérer les résultats validés
        $validatedResultats = Resultat::where('prescription_id', $prescription->id)
            ->where('status', 'VALIDE')
            ->whereNotNull('validated_by')
            ->with(['analyse' => function($query) {
                $query->with(['type', 'examen'])
                      ->orderBy('ordre', 'asc');
            }])
            ->get();

        if ($validatedResultats->isEmpty()) {
            return collect();
        }

        // 2. Récupérer les IDs d'analyses
        $analysesIds = $validatedResultats->pluck('analyse_id')->unique();

        // 3. Récupérer les analyses avec hiérarchie (SIMPLIFIÉ comme ancien code)
        $analyses = Analyse::where(function($query) use ($analysesIds) {
            $query->whereIn('id', $analysesIds)
                ->orWhereHas('children', function($q) use ($analysesIds) {
                    $q->whereIn('id', $analysesIds);
                });
        })
        ->with(['enfantsRecursive' => function($query) use ($analysesIds) {
            $query->whereIn('id', $analysesIds)
                ->orderBy('ordre', 'asc')
                ->with(['enfantsRecursive' => function($q) use ($analysesIds) {
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

        // 5. Associer les résultats aux analyses (SIMPLIFIÉ)
        $analyses = $analyses->map(function($analyse) use ($validatedResultats, $antibiogrammes) {
            $analyse->resultats = $validatedResultats->where('analyse_id', $analyse->id);

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
                $analyse->children = $analyse->children->map(function($child) use ($validatedResultats, $antibiogrammes) {
                    $child->resultats = $validatedResultats->where('analyse_id', $child->id);

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
                        $child->children = $child->children->map(function($subChild) use ($validatedResultats, $antibiogrammes) {
                            $subChild->resultats = $validatedResultats->where('analyse_id', $subChild->id);

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

        // 6. Regrouper et ordonner les examens (COMME L'ANCIEN CODE)
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
     * Récupérer les examens avec tous les résultats saisis (SIMPLIFIÉ pour aperçu)
     */
    private function getAllResultsExamens(Prescription $prescription)
    {
        // 1. Récupérer tous les résultats saisis
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
     * Méthode commune pour générer les PDFs (SIMPLIFIÉ comme ancien code)
     */
    private function generatePDF(Prescription $prescription, $examens, $type = 'final')
    {
        $prescription->load(['patient', 'prescripteur']);

        // Créer le nom de fichier avec timestamp
        $timestamp = time();
        $prefix = $type === 'final' ? 'resultats-final' : 'apercu-resultats';
        $filename = $prefix . '-' . $prescription->reference . '-' . $timestamp . '.pdf';

        $data = [
            'prescription' => $prescription,
            'examens' => $examens,
            'type_pdf' => $type,
            'laboratoire_name' => config('app.laboratoire_name', 'LABORATOIRE LA REFERENCE'),
            'date_generation' => now()->format('d/m/Y H:i'),
        ];

        $pdf = PDF::loadView('pdf.analyses.resultats-analyses', $data);
        $pdf->setPaper('A4', 'portrait');

        $path = 'pdfs/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        return Storage::disk('public')->url($path);
    }

    /**
     * Vérifier si on peut générer le PDF final
     */
    public function canGenerateFinalPdf(Prescription $prescription): bool
    {
        return $prescription->status === Prescription::STATUS_VALIDE && 
               (Resultat::where('prescription_id', $prescription->id)
                   ->where('status', 'VALIDE')
                   ->whereNotNull('validated_by')
                   ->exists() ||
                Antibiogramme::where('prescription_id', $prescription->id)->exists());
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