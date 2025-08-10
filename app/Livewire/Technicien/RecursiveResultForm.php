<?php

namespace App\Livewire\Technicien;

use App\Models\Analyse;
use App\Models\Prescription;
use App\Models\Resultat;
use App\Models\BacterieFamille;
use App\Models\Antibiogramme;
use App\Models\ResultatAntibiotique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Livewire\Component;

class RecursiveResultForm extends Component
{
    public Prescription $prescription;
    public ?int $parentId = null;

    /** @var \Illuminate\Support\Collection<Analyse> */
    public $roots; // racines à afficher (parents / panels)

    /** Tableau des saisies indexé par analyse_id */
    public array $results = [
        // 123 => ['valeur'=>..., 'resultats'=>..., 'interpretation'=>..., 'conclusion'=>..., 'famille_id'=>..., 'bacterie_id'=>...]
    ];

    /** familles + bactéries pour GERME/CULTURE */
    public $familles = [];
    public array $bacteriesByFamille = [];

    public function mount(int $prescriptionId, ?int $parentId = null)
    {
        $this->prescription = Prescription::findOrFail($prescriptionId);
        $this->parentId = $parentId;

        // Si parentId fourni, charger seulement ce parent et ses enfants
        if ($this->parentId) {
            $this->roots = collect([
                Analyse::with(['type', 'examen', 'enfantsRecursive.type', 'enfantsRecursive.examen'])
                    ->findOrFail($this->parentId)
            ]);
        } else {
            // Charger l'arbre complet (mode original)
            $all = $this->prescription->analyses()
                ->with(['type', 'examen', 'enfantsRecursive.type', 'enfantsRecursive.examen'])
                ->orderBy('ordre')->orderBy('id')
                ->get();

            $ids = $all->pluck('id')->all();
            $parentIdsInSet = $all->pluck('parent_id')->filter()->unique();

            $this->roots = $all->filter(function ($a) use ($ids, $parentIdsInSet) {
                return is_null($a->parent_id) || !$parentIdsInSet->contains($a->id) || !in_array($a->parent_id, $ids);
            })->values();
        }

        // Précharger résultats existants
        $this->hydrateExistingResults();

        // Cache familles/bactéries pour bactério
        $familles = BacterieFamille::actives()->with('bacteries:id,famille_id,designation,status')->get();
        $this->familles = $familles;
        foreach ($familles as $f) {
            $this->bacteriesByFamille[$f->id] = $f->bacteries->where('status', true)->pluck('designation', 'id')->toArray();
        }
    }

    protected function hydrateExistingResults(): void
    {
        $existing = Resultat::where('prescription_id', $this->prescription->id)->get();
        foreach ($existing as $r) {
            $payload = [
                'valeur'         => $r->valeur,
                'resultats'      => $r->resultats,
                'interpretation' => $r->interpretation,
                'conclusion'     => $r->conclusion,
                'famille_id'     => $r->famille_id,
                'bacterie_id'    => $r->bacterie_id,
            ];

            // JSON -> array pour SELECT_MULTIPLE / LEUCOCYTES etc.
            if (is_string($payload['resultats']) && $this->looksLikeJson($payload['resultats'])) {
                $payload['resultats'] = json_decode($payload['resultats'], true);
            }
            if (is_string($payload['valeur']) && $this->looksLikeJson($payload['valeur'])) {
                $payload['valeur'] = json_decode($payload['valeur'], true);
            }

            $this->results[$r->analyse_id] = array_filter($payload, fn($v) => !is_null($v));
        }
    }

    private function looksLikeJson(?string $s): bool
    {
        if (!$s) return false;
        $s = trim($s);
        if (!str_starts_with($s, '{') && !str_starts_with($s, '[')) return false;
        json_decode($s);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * ✅ MÉTHODE CORRIGÉE : Gestion des mises à jour de résultats
     */
    public function updatedResults($value, $name)
    {
        Log::info('updatedResults:', ['name' => $name, 'value' => $value]);
        
        // ✅ NOUVELLE LOGIQUE : Gestion spéciale pour GERME/CULTURE
        if (preg_match('/^results\.(\d+)\.selectedOptions/', $name, $matches) || 
            preg_match('/^results\.(\d+)$/', $name, $matches)) {
            
            $analyseId = (int) $matches[1];
            
            // Récupérer les options actuelles après la mise à jour
            $selectedOptions = $this->results[$analyseId]['selectedOptions'] ?? [];
            
            Log::info('Options sélectionnées pour analyse ' . $analyseId, ['options' => $selectedOptions]);
            
            // Options standards qui n'ont pas besoin d'antibiogrammes
            $standardOptions = ['non-rechercher', 'en-cours', 'culture-sterile', 'absence-germe-pathogene', 'Autre'];
            
            // Vérifier si TOUTES les options sélectionnées sont des options standards
            $hasOnlyStandardOptions = !empty($selectedOptions) && 
                                    empty(array_diff($selectedOptions, $standardOptions));
            
            // Vérifier s'il y a des bactéries sélectionnées (qui commencent par "bacterie-")
            $hasBacteries = !empty(array_filter($selectedOptions, function($option) {
                return str_starts_with($option, 'bacterie-');
            }));
            
            Log::info('Analyse des options', [
                'analyse_id' => $analyseId,
                'hasOnlyStandardOptions' => $hasOnlyStandardOptions,
                'hasBacteries' => $hasBacteries,
                'selectedOptions' => $selectedOptions
            ]);
            
            // ✅ Si on a seulement des options standards OU si on n'a plus de bactéries
            if ($hasOnlyStandardOptions || (!$hasBacteries && !empty($selectedOptions))) {
                Log::info('Nettoyage des antibiogrammes pour analyse ' . $analyseId);
                $this->cleanupAntibiogrammes($analyseId);
            }
        }
        
        // Logique existante pour famille_id
        if (preg_match('/^results\.(\d+)\.(famille_id)$/', $name, $m)) {
            $analyseId = (int) $m[1];
            $this->results[$analyseId]['bacterie_id'] = null;
        }
    }

    /**
     * ✅ MÉTHODE CORRIGÉE : Suppression définitive des antibiogrammes
     */
    private function cleanupAntibiogrammes($analyseId)
    {
        try {
            Log::info('Début nettoyage antibiogrammes', ['analyse_id' => $analyseId]);
            
            // ✅ Trouver TOUS les antibiogrammes (actifs + soft-deleted)
            $antibiogrammes = Antibiogramme::withTrashed()
                ->where('prescription_id', $this->prescription->id)
                ->where('analyse_id', $analyseId)
                ->get();
            
            Log::info('Antibiogrammes trouvés', ['count' => $antibiogrammes->count()]);
            
            foreach ($antibiogrammes as $antibiogramme) {
                // ✅ Supprimer définitivement les résultats d'antibiotiques
                $deletedAntibiotiques = ResultatAntibiotique::where('antibiogramme_id', $antibiogramme->id)
                    ->delete(); // ou ->forceDelete() si ResultatAntibiotique utilise aussi SoftDeletes
                
                Log::info('Résultats antibiotiques supprimés', [
                    'antibiogramme_id' => $antibiogramme->id,
                    'deleted_count' => $deletedAntibiotiques
                ]);
                
                // ✅ SUPPRESSION DÉFINITIVE de l'antibiogramme (forceDelete au lieu de delete)
                $antibiogramme->forceDelete();
                Log::info('Antibiogramme définitivement supprimé', ['id' => $antibiogramme->id]);
            }
            
            Log::info('Nettoyage terminé avec succès', ['analyse_id' => $analyseId]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du nettoyage des antibiogrammes', [
                'analyse_id' => $analyseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * ✅ MÉTHODE DE DEBUG : Vérifier l'état des antibiogrammes
     */
    public function debugAntibiogrammeState($analyseId = null)
    {
        if (!$analyseId) {
            $analyseId = 194; // Valeur par défaut pour vos tests
        }
        
        // Compter les différents états
        $actifs = Antibiogramme::where('prescription_id', $this->prescription->id)
            ->where('analyse_id', $analyseId)
            ->count();
        
        $softDeleted = Antibiogramme::onlyTrashed()
            ->where('prescription_id', $this->prescription->id)
            ->where('analyse_id', $analyseId)
            ->count();
        
        $total = Antibiogramme::withTrashed()
            ->where('prescription_id', $this->prescription->id)
            ->where('analyse_id', $analyseId)
            ->count();
        
        // Détails des enregistrements
        $details = Antibiogramme::withTrashed()
            ->where('prescription_id', $this->prescription->id)
            ->where('analyse_id', $analyseId)
            ->get(['id', 'bacterie_id', 'created_at', 'updated_at', 'deleted_at'])
            ->toArray();
        
        Log::info('État complet des antibiogrammes', [
            'analyse_id' => $analyseId,
            'actifs' => $actifs,
            'soft_deleted' => $softDeleted,
            'total_en_base' => $total,
            'details' => $details
        ]);
        
        return [
            'actifs' => $actifs,
            'soft_deleted' => $softDeleted,
            'total' => $total,
            'details' => $details
        ];
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Vérifier et nettoyer avant la sauvegarde
     */
    private function cleanupBeforeSave()
    {
        foreach ($this->results as $analyseId => $data) {
            $analyse = Analyse::with('type')->find($analyseId);
            if (!$analyse) continue;

            $type = strtoupper($analyse->type->name ?? '');
            
            if (in_array($type, ['GERME', 'CULTURE'])) {
                $selectedOptions = Arr::get($data, 'selectedOptions', []);
                $standardOptions = ['non-rechercher', 'en-cours', 'culture-sterile', 'absence-germe-pathogene', 'Autre'];
                
                // Si on a seulement des options standards, nettoyer les antibiogrammes
                $hasOnlyStandardOptions = !empty($selectedOptions) && 
                                        empty(array_diff($selectedOptions, $standardOptions));
                
                if ($hasOnlyStandardOptions) {
                    $this->cleanupAntibiogrammes($analyseId);
                }
            }
        }
    }

    public function saveAll()
    {
        DB::beginTransaction();
        try {
            // ✅ Nettoyer avant de sauvegarder
            $this->cleanupBeforeSave();
            
            foreach ($this->results as $analyseId => $data) {
                $analyse = Analyse::with('type')->find($analyseId);
                if (!$analyse) continue;

                $type = strtoupper($analyse->type->name ?? '');

                // normalisation d'entrées
                $valeur         = Arr::get($data, 'valeur');
                $resultats      = Arr::get($data, 'resultats');
                $interpretation = Arr::get($data, 'interpretation');
                $conclusion     = Arr::get($data, 'conclusion');
                $famille_id     = Arr::get($data, 'famille_id');
                $bacterie_id    = Arr::get($data, 'bacterie_id');

                // SELECT_MULTIPLE → stocker en JSON
                if (in_array($type, ['SELECT_MULTIPLE']) && is_array($resultats)) {
                    $resultats = json_encode(array_values($resultats), JSON_UNESCAPED_UNICODE);
                }

                // LEUCOCYTES → on stocke "valeur" en JSON { polynucleaires, lymphocytes, valeur? }
                if ($type === 'LEUCOCYTES') {
                    $valeur = json_encode([
                        'polynucleaires' => Arr::get($data, 'polynucleaires', Arr::get($valeur, 'polynucleaires')),
                        'lymphocytes'    => Arr::get($data, 'lymphocytes', Arr::get($valeur, 'lymphocytes')),
                        'valeur'         => Arr::get($data, 'valeur', Arr::get($valeur, 'valeur')),
                    ], JSON_UNESCAPED_UNICODE);
                }

                if (in_array($type, ['GERME', 'CULTURE'])) {
                    $selectedOptions = Arr::get($data, 'selectedOptions', []);
                    
                    // Séparer options standards et bactéries
                    $optionsStandards = array_intersect($selectedOptions, ['non-rechercher', 'en-cours', 'culture-sterile', 'absence-germe-pathogene', 'Autre']);
                    
                    // ✅ Sauvegarder dans resultats avec statut EN_COURS
                    Resultat::updateOrCreate(
                        ['prescription_id' => $this->prescription->id, 'analyse_id' => $analyseId],
                        [
                            'resultats' => !empty($optionsStandards) ? json_encode($optionsStandards, JSON_UNESCAPED_UNICODE) : null,
                            'valeur' => Arr::get($data, 'autreValeur') ?: null,
                            'status' => 'EN_COURS', // ✅ Marquer comme EN_COURS au lieu de TERMINE
                        ]
                    );
                    continue;
                }

                Resultat::updateOrCreate(
                    [
                        'prescription_id' => $this->prescription->id,
                        'analyse_id'      => $analyseId,
                    ],
                    [
                        'valeur'         => is_array($valeur) ? json_encode($valeur, JSON_UNESCAPED_UNICODE) : $valeur,
                        'resultats'      => is_array($resultats) ? json_encode($resultats, JSON_UNESCAPED_UNICODE) : $resultats,
                        'interpretation' => $interpretation ?: null,
                        'conclusion'     => $conclusion ?: null,
                        'status'         => 'EN_COURS', // ✅ Marquer comme EN_COURS au lieu de TERMINE
                        'famille_id'     => $famille_id ?: null,
                        'bacterie_id'    => $bacterie_id ?: null,
                    ]
                );
            }

            // ✅ Mettre à jour le statut de la prescription seulement en EN_COURS
            if ($this->prescription->status === 'EN_ATTENTE') {
                $this->prescription->update(['status' => 'EN_COURS']);
            }

            DB::commit();
            
            // ✅ Message de succès différent
            session()->flash('message', 'Résultats sauvegardés avec succès. Utilisez les boutons "Terminer" pour finaliser.');
            
            // ✅ Rafraîchir la sidebar pour mettre à jour les statuts
            $this->dispatch('refreshSidebar');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('saveAll error', ['e' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Erreur lors de l\'enregistrement : '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.technicien.recursive-result-form');
    }
}