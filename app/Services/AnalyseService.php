<?php

namespace App\Services;

use App\Models\Analyse;
use App\Models\Patient;
use App\Repositories\AnalyseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyseService
{
    protected AnalyseRepository $analyseRepository;

    public function __construct(AnalyseRepository $analyseRepository)
    {
        $this->analyseRepository = $analyseRepository;
    }

    /**
     * Récupérer analyse avec tous les détails nécessaires
     */
    public function getAnalyseAvecDetails(int $analyseId): Analyse
    {
        $cacheKey = "analyse_details_{$analyseId}";
        
        return Cache::remember($cacheKey, 3600, function() use ($analyseId) {
            return $this->analyseRepository->getAvecDetails($analyseId);
        });
    }

    /**
     * Recherche analyses avec suggestions intelligentes
     */
    public function rechercherAvecSuggestions(string $terme, int $limite = 10): array
    {
        $resultats = $this->analyseRepository->rechercher($terme, $limite);
        
        // Ajouter suggestions de frappe (fuzzy search)
        $suggestions = $this->genererSuggestionsFrappe($terme);
        
        return [
            'resultats_exacts' => $resultats,
            'suggestions_frappe' => $suggestions,
            'analyses_similaires' => $this->getAnalysesSimilaires($terme),
        ];
    }

    private function genererSuggestionsFrappe(string $terme): array
    {
        // Algorithme simple de suggestion basé sur distance de Levenshtein
        $touteAnalyses = Cache::remember('toutes_analyses_noms', 3600, function() {
            return Analyse::where('is_active', true)
                          ->whereNotNull('parent_id') // Seulement analyses finales
                          ->pluck('nom', 'id')
                          ->toArray();
        });
        
        $suggestions = [];
        $termeNormalise = strtolower($terme);
        
        foreach ($touteAnalyses as $id => $nom) {
            $nomNormalise = strtolower($nom);
            $distance = levenshtein($termeNormalise, $nomNormalise);
            
            // Suggérer si distance <= 3 et nom contient partie du terme
            if ($distance <= 3 && strlen($terme) >= 3) {
                if (str_contains($nomNormalise, $termeNormalise) || 
                    str_contains($termeNormalise, $nomNormalise)) {
                    $suggestions[] = [
                        'id' => $id,
                        'nom' => $nom,
                        'distance' => $distance
                    ];
                }
            }
        }
        
        // Trier par distance et retourner top 5
        usort($suggestions, fn($a, $b) => $a['distance'] <=> $b['distance']);
        return array_slice($suggestions, 0, 5);
    }

    private function getAnalysesSimilaires(string $terme): array
    {
        // Recherche dans synonymes et mots-clés
        return $this->analyseRepository->rechercherDansSynonymes($terme, 5);
    }

    /**
     * Vérification disponibilité analyse
     */
    public function estDisponible(Analyse $analyse): bool
    {
        // Vérifications multiples
        return $analyse->is_active && 
               $analyse->is_available &&
               $this->equipementDisponible($analyse) &&
               $this->reactifsDisponibles($analyse) &&
               !$this->enMaintenance($analyse);
    }

    private function equipementDisponible(Analyse $analyse): bool
    {
        if (!$analyse->equipement_requis) return true;
        
        // Vérifier status équipement dans système LIMS
        return Cache::remember("equipement_status_{$analyse->equipement_requis}", 300, function() use ($analyse) {
            // Simuler vérification équipement
            return rand(1, 100) > 5; // 95% de disponibilité
        });
    }

    private function reactifsDisponibles(Analyse $analyse): bool
    {
        if (!$analyse->reactifs_requis) return true;
        
        $reactifs = json_decode($analyse->reactifs_requis, true) ?? [];
        
        foreach ($reactifs as $reactif) {
            $stock = Cache::get("stock_reactif_{$reactif}", 0);
            if ($stock < 10) { // Stock minimum
                return false;
            }
        }
        
        return true;
    }

    private function enMaintenance(Analyse $analyse): bool
    {
        // Vérifier si analyse en maintenance planifiée
        return Cache::get("maintenance_analyse_{$analyse->id}", false);
    }

    /**
     * Vérification compatibilité entre analyses
     */
    public function verifierCompatibilite(int $nouvelleAnalyseId, array $analyseIds): array
    {
        if (empty($analyseIds)) return [];
        
        $nouvelleAnalyse = $this->getAnalyseAvecDetails($nouvelleAnalyseId);
        $analyses = Analyse::whereIn('id', $analyseIds)->get();
        
        $incompatibles = [];
        
        foreach ($analyses as $analyse) {
            // Vérifier incompatibilités connues
            if ($this->sontIncompatibles($nouvelleAnalyse, $analyse)) {
                $incompatibles[] = $analyse->nom;
            }
        }
        
        return $incompatibles;
    }

    private function sontIncompatibles(Analyse $analyse1, Analyse $analyse2): bool
    {
        // Règles d'incompatibilité métier
        
        // 1. Jeûne vs non-jeûne
        if ($analyse1->necessite_jeune && $analyse2->interdit_jeune) {
            return true;
        }
        
        // 2. Types de tubes incompatibles
        if ($analyse1->type_tube_requis && $analyse2->type_tube_requis &&
            $analyse1->type_tube_requis !== $analyse2->type_tube_requis &&
            !$this->tubesCompatibles($analyse1->type_tube_requis, $analyse2->type_tube_requis)) {
            return true;
        }
        
        // 3. Analyses mutuellement exclusives
        $exclusions = [
            'glucose' => ['hba1c_immediate'],
            'cortisol_matin' => ['cortisol_soir'],
        ];
        
        $code1 = strtolower($analyse1->code);
        $code2 = strtolower($analyse2->code);
        
        if (isset($exclusions[$code1]) && in_array($code2, $exclusions[$code1])) {
            return true;
        }
        
        return false;
    }

    private function tubesCompatibles(string $tube1, string $tube2): bool
    {
        $compatibilites = [
            'EDTA' => ['EDTA'],
            'HEPARINE' => ['HEPARINE'],
            'SEC' => ['SEC', 'GEL'],
            'GEL' => ['SEC', 'GEL'],
            'FLUORURE' => ['FLUORURE'],
        ];
        
        return isset($compatibilites[$tube1]) && in_array($tube2, $compatibilites[$tube1]);
    }

    /**
     * Calculs optimaux pour prélèvements
     */
    public function calculerNombreTubes(array $analyses): int
    {
        if (empty($analyses)) return 0;
        
        // Grouper par type de tube requis
        $parTypeTube = collect($analyses)->groupBy('type_tube_requis');
        
        $nombreTubes = 0;
        
        foreach ($parTypeTube as $typeTube => $analysesParType) {
            $volumeTotal = collect($analysesParType)->sum('volume_echantillon_ml');
            $capaciteTube = $this->getCapaciteTube($typeTube);
            
            // Calculer nombre de tubes nécessaires avec marge de sécurité
            $nombreTubes += max(1, ceil(($volumeTotal * 1.2) / $capaciteTube));
        }
        
        return $nombreTubes;
    }

    public function determinerTypeTube(array $analyses): string
    {
        if (empty($analyses)) return 'SEC';
        
        // Prioriser selon hiérarchie de contraintes
        $types = collect($analyses)->pluck('type_tube_requis')->filter()->unique();
        
        $priorite = ['EDTA', 'HEPARINE', 'FLUORURE', 'GEL', 'SEC'];
        
        foreach ($priorite as $type) {
            if ($types->contains($type)) {
                return $type;
            }
        }
        
        return 'SEC';
    }

    private function getCapaciteTube(string $typeTube): float
    {
        return match($typeTube) {
            'EDTA' => 4.0,
            'HEPARINE' => 4.0,
            'SEC' => 5.0,
            'GEL' => 5.0,
            'FLUORURE' => 2.0,
            default => 5.0
        };
    }

    /**
     * Analyses complémentaires suggérées
     */
    public function getAnalysesComplementaires(int $analyseId): array
    {
        $cacheKey = "complementaires_analyse_{$analyseId}";
        
        return Cache::remember($cacheKey, 1800, function() use ($analyseId) {
            // Logique basée sur associations fréquentes
            $associations = $this->analyseRepository->getAssociationsFrequentes($analyseId);
            
            return $associations->map(function($association) {
                return [
                    'id' => $association->id,
                    'nom' => $association->nom,
                    'prix' => $association->prix,
                    'frequence_association' => $association->frequence,
                    'raison' => $this->genererRaisonComplementaire($association->code)
                ];
            })->toArray();
        });
    }

    private function genererRaisonComplementaire(string $code): string
    {
        $raisons = [
            'glucose' => 'Souvent prescrit avec bilan lipidique',
            'cholesterol' => 'Complète le bilan cardiovasculaire',
            'creatinine' => 'Évaluation fonction rénale',
            'alat' => 'Bilan hépatique complet',
        ];
        
        return $raisons[strtolower($code)] ?? 'Fréquemment associé';
    }

    /**
     * Suggestions pour patient basées sur historique
     */
    public function getSuggestionsPatient(int $patientId): array
    {
        $cacheKey = "suggestions_patient_{$patientId}";
        
        return Cache::remember($cacheKey, 1800, function() use ($patientId) {
            $patient = Patient::find($patientId);
            if (!$patient) return [];
            
            // Analyses récurrentes
            $recurrentes = $this->getAnalysesRecurrentes($patientId);
            
            // Analyses manquantes dans suivi
            $suiviManquant = $this->detecterSuiviManquant($patientId);
            
            // Analyses saisonnières
            $saisonnieres = $this->getAnalysesSaisonnieres($patientId);
            
            return array_merge($recurrentes, $suiviManquant, $saisonnieres);
        });
    }

    private function getAnalysesRecurrentes(int $patientId): array
    {
        // Analyses faites régulièrement par ce patient
        $recurrentes = DB::table('prescriptions')
            ->join('prescription_analyse', 'prescriptions.id', '=', 'prescription_analyse.prescription_id')
            ->join('analyses', 'prescription_analyse.analyse_id', '=', 'analyses.id')
            ->where('prescriptions.patient_id', $patientId)
            ->where('prescriptions.created_at', '>=', now()->subMonths(12))
            ->selectRaw('
                analyses.id,
                analyses.nom,
                analyses.prix,
                COUNT(*) as frequence,
                MAX(prescriptions.created_at) as derniere_fois
            ')
            ->groupBy('analyses.id', 'analyses.nom', 'analyses.prix')
            ->having('frequence', '>=', 2)
            ->having('derniere_fois', '<', now()->subMonths(3))
            ->orderByDesc('frequence')
            ->limit(5)
            ->get();
        
        return $recurrentes->map(fn($r) => [
            'id' => $r->id,
            'nom' => $r->nom,
            'prix' => $r->prix,
            'type' => 'recurrente',
            'raison' => "Fait {$r->frequence} fois cette année",
            'priorite' => 'moyenne'
        ])->toArray();
    }

    private function detecterSuiviManquant(int $patientId): array
    {
        // Logique complexe de détection de suivi médical manquant
        // Basée sur l'âge, les antécédents, les analyses précédentes
        
        return []; // À implémenter selon protocoles médicaux
    }

    private function getAnalysesSaisonnieres(int $patientId): array
    {
        $mois = now()->month;
        $suggestions = [];
        
        // Automne/Hiver : vitamines et immunité
        if (in_array($mois, [10, 11, 12, 1, 2])) {
            $suggestions[] = [
                'id' => $this->getAnalyseIdByCode('vitamine_d'),
                'nom' => 'Vitamine D',
                'type' => 'saisonniere',
                'raison' => 'Période hivernale, exposition solaire réduite',
                'priorite' => 'faible'
            ];
        }
        
        // Printemps : allergies
        if (in_array($mois, [3, 4, 5])) {
            $suggestions[] = [
                'id' => $this->getAnalyseIdByCode('ige_totales'),
                'nom' => 'IgE totales',
                'type' => 'saisonniere',
                'raison' => 'Période des allergies saisonnières',
                'priorite' => 'faible'
            ];
        }
        
        return array_filter($suggestions, fn($s) => $s['id'] !== null);
    }

    /**
     * Packs d'analyses prédéfinis
     */
    public function getPackBilanBasic(): Collection
    {
        return $this->getAnalysesByCodes([
            'hemogramme', 'glycemie', 'uree', 'creatinine'
        ]);
    }

    public function getPackBilanComplet(): Collection
    {
        return $this->getAnalysesByCodes([
            'hemogramme', 'glycemie', 'uree', 'creatinine',
            'cholesterol_total', 'hdl', 'ldl', 'triglycerides',
            'alat', 'asat', 'bilirubine_totale'
        ]);
    }

    public function getPackBilanLipidique(): Collection
    {
        return $this->getAnalysesByCodes([
            'cholesterol_total', 'hdl', 'ldl', 'triglycerides'
        ]);
    }

    public function getPackBilanHepatique(): Collection
    {
        return $this->getAnalysesByCodes([
            'alat', 'asat', 'ggt', 'bilirubine_totale', 'bilirubine_directe'
        ]);
    }

    public function getPackBilanRenal(): Collection
    {
        return $this->getAnalysesByCodes([
            'uree', 'creatinine', 'acide_urique', 'clearance_creatinine'
        ]);
    }

    public function getPackBilanThyroide(): Collection
    {
        return $this->getAnalysesByCodes([
            'tsh', 't3', 't4'
        ]);
    }

    private function getAnalysesByCodes(array $codes): Collection
    {
        return Analyse::whereIn('code', $codes)
                     ->where('is_active', true)
                     ->get();
    }

    private function getAnalyseIdByCode(string $code): ?int
    {
        return Analyse::where('code', $code)
                     ->where('is_active', true)
                     ->value('id');
    }

    /**
     * Analyses nécessitant le jeûne
     */
    public function getAnalysesNecessitantJeune(Collection $analyseIds): Collection
    {
        return Analyse::whereIn('id', $analyseIds)
                     ->where('necessite_jeune', true)
                     ->get(['id', 'nom']);
    }

    public function getAnalysesInterdisantJeune(Collection $analyseIds): Collection
    {
        return Analyse::whereIn('id', $analyseIds)
                     ->where('interdit_jeune', true)
                     ->get(['id', 'nom']);
    }

    /**
     * Statistiques d'utilisation
     */
    public function incrementerCompteurUtilisation(int $analyseId): void
    {
        Analyse::where('id', $analyseId)->increment('compteur_utilisation');
        
        // Invalider cache
        Cache::forget("analyse_details_{$analyseId}");
    }

    public function getAnalysesPopulaires(int $limite = 20): Collection
    {
        return Cache::remember('analyses_populaires', 3600, function() use ($limite) {
            return Analyse::where('is_active', true)
                         ->whereNotNull('parent_id')
                         ->orderByDesc('compteur_utilisation')
                         ->limit($limite)
                         ->get(['id', 'nom', 'prix', 'compteur_utilisation']);
        });
    }

    /**
     * Prix et remises
     */
    public function calculerPrixAvecRemise(int $analyseId, float $tauxRemise): float
    {
        $analyse = $this->getAnalyseAvecDetails($analyseId);
        return $analyse->prix * (1 - $tauxRemise / 100);
    }

    public function getPrixGroupes(array $analyseIds): array
    {
        $analyses = Analyse::whereIn('id', $analyseIds)->get(['id', 'prix']);
        
        return $analyses->mapWithKeys(function($analyse) {
            return [$analyse->id => $analyse->prix];
        })->toArray();
    }
}