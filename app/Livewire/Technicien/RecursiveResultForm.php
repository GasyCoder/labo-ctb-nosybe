<?php

namespace App\Livewire\Technicien;

use App\Models\Analyse;
use Livewire\Component;
use App\Models\Resultat;
use Illuminate\Support\Arr;
use App\Models\Prescription;
use App\Models\Antibiogramme;
use App\Models\BacterieFamille;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ResultatAntibiotique;
use Illuminate\Support\Facades\Cache;

class RecursiveResultForm extends Component
{
    public Prescription $prescription;
    public ?int $parentId = null;
    protected $listeners = ['syncAntibiogrammes'];

    /** @var \Illuminate\Support\Collection|\App\Models\Analyse[] */
    public $roots;

    /** État du formulaire par analyse_id */
    public array $results = [];

    /** Familles et mapping familles → bactéries (pour l’UI) */
    public $familles = [];
    public array $bacteriesByFamille = [];

    /** Analyses modifiées et en attente de synchronisation antibiogrammes */
    public array $pendingSync = [];

    /* =======================
     |  Helpers Flash
     |=======================*/
    private function flashSuccess(string $message): void
    {
        if (\function_exists('flash')) {
            flash()->success($message);
        } else {
            session()->flash('message', $message);
        }
    }

    private function flashError(string $message): void
    {
        if (\function_exists('flash')) {
            flash()->error($message);
        } else {
            session()->flash('error', $message);
        }
    }

    private function flashInfo(string $message): void
    {
        if (\function_exists('flash')) {
            flash()->info($message);
        } else {
            session()->flash('info', $message);
        }
    }


    /**
     * Obtient la valeur d'interprétation avec défaut "NORMAL"
     */
    public function getInterpretation($analyseId): string
    {
        return $this->results[$analyseId]['interpretation'] ?? 'NORMAL';
    }

    /**
     * Initialise les valeurs par défaut pour une nouvelle analyse
     */
    private function initializeDefaultValues(int $analyseId): void
    {
        if (!isset($this->results[$analyseId]) || !is_array($this->results[$analyseId])) {
            $this->results[$analyseId] = [];
        }
        
        // Définir "NORMAL" comme défaut si aucune interprétation n'existe
        if (!isset($this->results[$analyseId]['interpretation']) || 
            empty($this->results[$analyseId]['interpretation'])) {
            $this->results[$analyseId]['interpretation'] = 'NORMAL';
        }
    }


    public function mount(int $prescriptionId, ?int $parentId = null): void
    {
        $this->prescription = Prescription::with(['patient'])->findOrFail($prescriptionId);
        $this->parentId     = $parentId;

        // Messages d'accueil existants...
        $nom    = $this->prescription->patient?->nom ?? null;
        $prenom = $this->prescription->patient?->prenom ?? null;

        if ($nom || $prenom) {
            $this->flashSuccess("Patient « {$nom} {$prenom} » sélectionné — vous pouvez modifier ses informations.");
        } else {
            $this->flashInfo("Prescription #{$this->prescription->id} chargée — vous pouvez saisir/modifier les résultats.");
        }

        $this->loadAnalysisTree();
        $this->loadBacteriaData();
        $this->hydrateExistingResults();
        
        // ✅ Nouveau: Initialiser les valeurs par défaut pour toutes les analyses
        $this->initializeDefaults();
    }

    /**
     * Initialise les valeurs par défaut pour toutes les analyses
     */
    private function initializeDefaults(): void
    {
        $allAnalyses = collect();
        
        // Collecter toutes les analyses (récursivement)
        foreach ($this->roots as $root) {
            $allAnalyses->push($root);
            if ($root->enfantsRecursive) {
                $allAnalyses = $allAnalyses->merge($this->collectAllChildren($root->enfantsRecursive));
            }
        }
        
        // Initialiser les valeurs par défaut pour chaque analyse
        foreach ($allAnalyses as $analyse) {
            $this->initializeDefaultValues($analyse->id);
        }
    }

    /**
     * Collecte récursivement tous les enfants
     */
    private function collectAllChildren($children)
    {
        $result = collect();
        foreach ($children as $child) {
            $result->push($child);
            if ($child->enfantsRecursive) {
                $result = $result->merge($this->collectAllChildren($child->enfantsRecursive));
            }
        }
        return $result;
    }

    private function loadAnalysisTree(): void
    {
        if ($this->parentId) {
            $this->roots = collect([
                Analyse::with(['type', 'examen', 'enfantsRecursive.type', 'enfantsRecursive.examen'])
                    ->findOrFail($this->parentId)
            ]);
            return;
        }

        $all = $this->prescription->analyses()
            ->with(['type', 'examen', 'enfantsRecursive.type', 'enfantsRecursive.examen'])
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        $ids             = $all->pluck('id')->all();
        $parentIdsInSet  = $all->pluck('parent_id')->filter()->unique();

        $this->roots = $all->filter(function ($a) use ($ids, $parentIdsInSet) {
            return is_null($a->parent_id)
                || !$parentIdsInSet->contains($a->id)
                || !in_array($a->parent_id, $ids, true);
        })->values();
    }

    private function loadBacteriaData(): void
    {
        $familles = BacterieFamille::actives()
            ->with(['bacteries' => function ($q) {
                $q->where('status', true)->orderBy('designation');
            }])
            ->orderBy('designation')
            ->get();

        $this->familles = $familles;

        foreach ($familles as $famille) {
            $this->bacteriesByFamille[$famille->id] = $famille->bacteries
                ->pluck('designation', 'id')
                ->toArray();
        }
    }

    protected function hydrateExistingResults(): void
    {
        $existing = Resultat::where('prescription_id', $this->prescription->id)
            ->with(['analyse.type'])
            ->get();

        foreach ($existing as $r) {
            $analyseId   = $r->analyse_id;
            $typeAnalyse = strtoupper($r->analyse?->type?->name ?? '');

            $payload = [
                'valeur'         => $r->valeur,
                'resultats'      => $r->resultats,
                'interpretation' => $r->interpretation,
                'conclusion'     => $r->conclusion,
                'famille_id'     => $r->famille_id,
                'bacterie_id'    => $r->bacterie_id,
            ];

            if (in_array($typeAnalyse, ['GERME', 'CULTURE'], true)) {
                $selectedOptions = [];
                $autreValeur     = null;

                // ✅ FIX: $r->resultats est déjà décodé par l'accessor du modèle
                if (!empty($r->resultats) && is_array($r->resultats)) {
                    $selectedOptions = $r->resultats;
                }

                // Ajouter les bactéries depuis antibiogrammes
                $antibiogrammes = Antibiogramme::where([
                    'prescription_id' => $this->prescription->id,
                    'analyse_id'      => $analyseId,
                ])->get();

                foreach ($antibiogrammes as $abg) {
                    $selectedOptions[] = 'bacterie-' . $abg->bacterie_id;
                }

                // Traiter "Autre"
                if (!empty($r->valeur)) {
                    $autreValeur = $r->valeur;
                    if (!in_array('Autre', $selectedOptions, true)) {
                        $selectedOptions[] = 'Autre';
                    }
                }

                $payload['selectedOptions'] = $selectedOptions;
                $payload['autreValeur']     = $autreValeur;
            }

            $this->results[$analyseId] = $payload;
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

    /** Helpers lecture */
    public function getSelectedOptions($analyseId): array
    {
        return $this->results[$analyseId]['selectedOptions'] ?? [];
    }

    public function getAutreValeur($analyseId): ?string
    {
        return $this->results[$analyseId]['autreValeur'] ?? null;
    }

    public function isBacterieSelected($analyseId, $bacterieId): bool
    {
        return in_array('bacterie-' . $bacterieId, $this->getSelectedOptions($analyseId), true);
    }

    public function markDirty(int $analyseId): void
    {
        $this->pendingSync[$analyseId] = true;
    }

    public function ensureResultsContainer(int $analyseId): void
    {
        if (!isset($this->results[$analyseId]) || !is_array($this->results[$analyseId])) {
            $this->results[$analyseId] = [];
        }
        if (!isset($this->results[$analyseId]['selectedOptions']) || !is_array($this->results[$analyseId]['selectedOptions'])) {
            $this->results[$analyseId]['selectedOptions'] = [];
        }
    }

    private function normalizeSelection(int $analyseId): void
    {
        $std = ['non-rechercher','en-cours','culture-sterile','absence-germe-pathogene','Autre'];
        $sel = $this->results[$analyseId]['selectedOptions'] ?? [];
        $hasStandard = !empty(array_intersect($std, $sel));

        if ($hasStandard) {
            // si standard présent → on supprime toutes les “bacterie-*”
            $sel = array_values(array_filter($sel, fn($v) => !str_starts_with($v, 'bacterie-')));
        } else {
            // sinon on garde seulement les bacterie-* (propreté)
            $sel = array_values(array_filter($sel, fn($v) => str_starts_with($v, 'bacterie-')));
        }

        // dédoublonnage
        $sel = array_values(array_unique($sel));
        $this->results[$analyseId]['selectedOptions'] = $sel;
    }

    public function toggleStandardOption(int $analyseId, string $path, string $option): void
    {
        $this->ensureResultsContainer($analyseId);

        $std = ['non-rechercher','en-cours','culture-sterile','absence-germe-pathogene','Autre'];
        if (!in_array($option, $std, true)) return;

        $sel = $this->results[$analyseId]['selectedOptions'] ?? [];
        $active = in_array($option, $sel, true);

        // activer un standard => garder seulement celui-ci
        if (!$active) {
            $sel = [$option];
        } else {
            // le retirer
            $sel = array_values(array_filter($sel, fn($v) => $v !== $option));
        }

        $this->results[$analyseId]['selectedOptions'] = $sel;

        // si "Autre" n'est plus présent, on efface la valeur
        if (!in_array('Autre', $sel, true)) {
            $this->results[$analyseId]['autreValeur'] = null;
        }

        $this->normalizeSelection($analyseId);
        $this->markDirty($analyseId);
    }

    private function hasStandardSelected(int $analyseId): bool
    {
        $sel = $this->results[$analyseId]['selectedOptions'] ?? [];
        if (!is_array($sel)) return false;

        $std = ['non-rechercher','en-cours','culture-sterile','absence-germe-pathogene','Autre'];
        return !empty(array_intersect($std, $sel));
    }

    public function toggleBacterieOption(int $analyseId, string $path, int $bacterieId): void
    {
        $this->ensureResultsContainer($analyseId);

        // ⛔ si une option standard est active, on ignore (sécurité back)
        if ($this->hasStandardSelected($analyseId)) {
            return;
        }

        // État courant (ne garder que les bacterie-*)
        $sel = $this->results[$analyseId]['selectedOptions'] ?? [];
        $sel = array_values(array_filter($sel, fn ($v) => str_starts_with((string)$v, 'bacterie-')));

        // Toggle
        $key = 'bacterie-' . $bacterieId;
        if (in_array($key, $sel, true)) {
            $sel = array_values(array_filter($sel, fn ($v) => $v !== $key));
            $action = 'removed';
        } else {
            $sel[] = $key;
            $sel = array_values(array_unique($sel));
            $action = 'added';
        }

        // Appliquer & dirty
        $this->results[$analyseId]['selectedOptions'] = $sel;
        $this->markDirty($analyseId);

    }

    public function clearGermeSelection(int $analyseId, string $path): void
    {
        $this->ensureResultsContainer($analyseId);

        $this->results[$analyseId]['selectedOptions'] = [];
        $this->results[$analyseId]['autreValeur'] = null;

        // marquer pour sync (les ABG seront nettoyés via le bouton "Synchroniser" ou saveAll)
        $this->markDirty($analyseId);

        // ✅ petite notif
        $this->flashInfo('Sélection des germes réinitialisée. Cliquez sur « Synchroniser » pour mettre à jour les antibiogrammes.');
    }


    /** NOUVEAU : savoir si l’analyse a des modifs non synchronisées */
    public function isAnalyseDirty(int $analyseId): bool
    {
        return !empty($this->pendingSync[$analyseId]);
    }


    /**
     * ⚠️ IMPORTANT : ne plus effectuer d’IO DB ici.
     * On marque seulement l’analyse comme “dirty” (pending sync).
     */
    public function updatedResults($value, $name): void
    {

        // Sélection GERME/CULTURE
        if (preg_match('/^(\d+)\.selectedOptions$/', $name, $m)) {
            $analyseId = (int) $m[1];
            $this->pendingSync[$analyseId] = true;
            return;
        }

        // Changement famille → reset bacterie_id + dirty
        if (preg_match('/^(\d+)\.(famille_id)$/', $name, $m)) {
            $analyseId = (int) $m[1];
            $this->results[$analyseId]['bacterie_id'] = null;
            $this->pendingSync[$analyseId] = true;
        }
    }

    /** Sauvegarde ciblée d’une analyse (sans sync auto) */
    public function saveAnalyseImmediate(int $analyseId): void
    {
        DB::beginTransaction();
        try {
            $data = $this->results[$analyseId] ?? [];
            if (!$data) return;

            $analyse = Analyse::with('type')->find($analyseId);
            if (!$analyse) return;

            $type = strtoupper($analyse->type->name ?? '');

            if (in_array($type, ['GERME', 'CULTURE'], true)) {
                // On ne touche pas aux antibiogrammes ici (sync bouton dédié)
                $selectedOptions = Arr::get($data, 'selectedOptions', []);
                $optionsStandards = array_intersect($selectedOptions, [
                    'non-rechercher', 'en-cours', 'culture-sterile', 'absence-germe-pathogene', 'Autre'
                ]);

                Resultat::updateOrCreate(
                    ['prescription_id' => $this->prescription->id, 'analyse_id' => $analyseId],
                    [
                        'resultats' => $optionsStandards ? json_encode(array_values($optionsStandards), JSON_UNESCAPED_UNICODE) : null,
                        'valeur'    => Arr::get($data, 'autreValeur') ?: null,
                        'status'    => 'EN_COURS',
                    ]
                );

                DB::commit();
                $this->flashSuccess('Analyse sauvegardée (hors antibiogrammes).');
                return;
            }

            $valeur         = Arr::get($data, 'valeur');
            $resultats      = Arr::get($data, 'resultats');
            $interpretation = Arr::get($data, 'interpretation');
            $conclusion     = Arr::get($data, 'conclusion');
            $famille_id     = Arr::get($data, 'famille_id');
            $bacterie_id    = Arr::get($data, 'bacterie_id');

            Resultat::updateOrCreate(
                ['prescription_id' => $this->prescription->id, 'analyse_id' => $analyseId],
                [
                    'valeur'         => is_array($valeur) ? json_encode($valeur, JSON_UNESCAPED_UNICODE) : $valeur,
                    'resultats'      => is_array($resultats) ? json_encode($resultats, JSON_UNESCAPED_UNICODE) : $resultats,
                    'interpretation' => $interpretation ?: null,
                    'conclusion'     => $conclusion ?: null,
                    'status'         => 'EN_COURS',
                    'famille_id'     => $famille_id ?: null,
                    'bacterie_id'    => $bacterie_id ?: null,
                ]
            );

            DB::commit();
            $this->flashSuccess('Analyse sauvegardée.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur saveAnalyseImmediate', ['e' => $e->getMessage()]);
            $this->flashError('Erreur : ' . $e->getMessage());
        }
    }

    /** Recharger une analyse depuis la DB (utile après sync) */
    public function reloadAnalyse(int $analyseId): void
    {
        unset($this->results[$analyseId]);

        $r = Resultat::where([
            'prescription_id' => $this->prescription->id,
            'analyse_id'      => $analyseId,
        ])->with('analyse.type')->first();

        if (!$r) {
            $this->pendingSync[$analyseId] = false;
            return;
        }

        $type = strtoupper($r->analyse?->type?->name ?? '');
        $selectedOptions = [];
        $autreValeur = null;

        if (in_array($type, ['GERME', 'CULTURE'], true)) {
            // ✅ CORRECTION : Récupérer les options standards depuis 'resultats'
            if ($r->resultats && $this->looksLikeJson($r->resultats)) {
                $optionsStandards = json_decode($r->resultats, true);
                if (is_array($optionsStandards)) {
                    $selectedOptions = array_merge($selectedOptions, array_values($optionsStandards));
                }
            }

            // Récupérer les bactéries depuis les antibiogrammes
            $abg = Antibiogramme::where([
                'prescription_id' => $this->prescription->id,
                'analyse_id'      => $analyseId,
            ])->get();

            foreach ($abg as $row) {
                $opt = 'bacterie-' . $row->bacterie_id;
                if (!in_array($opt, $selectedOptions, true)) {
                    $selectedOptions[] = $opt;
                }
            }

            // Gérer la valeur "Autre"
            if ($r->valeur && !$this->looksLikeJson($r->valeur)) {
                $autreValeur = $r->valeur;
                if (!in_array('Autre', $selectedOptions, true)) {
                    $selectedOptions[] = 'Autre';
                }
            }
        }

        $this->results[$analyseId] = array_filter([
            'selectedOptions' => $selectedOptions ?: null,
            'autreValeur'     => $autreValeur,
            'valeur'          => $r->valeur,
            'resultats'       => $r->resultats,
            'interpretation'  => $r->interpretation,
            'conclusion'      => $r->conclusion,
        ], static fn($v) => !is_null($v));

        $this->pendingSync[$analyseId] = false;
    }

    /** Supprime tous les antibiogrammes (et résultats liés) d’une analyse */
    private function cleanupAntibiogrammes(int $analyseId): void
    {
        $abg = Antibiogramme::where([
            'prescription_id' => $this->prescription->id,
            'analyse_id'      => $analyseId,
        ])->get();

        foreach ($abg as $row) {
            ResultatAntibiotique::where('antibiogramme_id', $row->id)->delete();
            $row->delete();
        }
    }

    /** Avant saveAll : si uniquement options standards → cleanup */
    private function cleanupBeforeSave(): void
    {
        foreach ($this->results as $analyseId => $data) {
            $analyse = Analyse::with('type')->find($analyseId);
            if (!$analyse) continue;

            $type = strtoupper($analyse->type->name ?? '');
            if (!in_array($type, ['GERME', 'CULTURE'], true)) continue;

            $selectedOptions = Arr::get($data, 'selectedOptions', []);
            $standard = ['non-rechercher', 'en-cours', 'culture-sterile', 'absence-germe-pathogene', 'Autre'];

            $hasOnlyStandard = !empty($selectedOptions) && empty(array_diff($selectedOptions, $standard));
            if ($hasOnlyStandard) {
                $this->cleanupAntibiogrammes($analyseId);
            }
        }
    }

    public function saveAll()
    {
        DB::beginTransaction();
        try {
            // 1) Appliquer d'abord toutes les sync en attente
            foreach (array_keys($this->pendingSync) as $analyseIdDirty) {
                if (!empty($this->pendingSync[$analyseIdDirty])) {
                    $this->syncAntibiogrammes($analyseIdDirty);
                }
            }

            // 2) Nettoyage logique GERME (si standard only)
            $this->cleanupBeforeSave();

            // 3) Récupérer les analyses principales pour la table pivot
            $principalAnalyseIds = DB::table('prescription_analyse')
                ->where('prescription_id', $this->prescription->id)
                ->pluck('analyse_id')
                ->toArray();

            // 4) Persister tous les Resultat (hors création/suppression ABG)
            $updatedAnalyseIds = [];
            
            foreach ($this->results as $analyseId => $data) {
                $analyse = Analyse::with('type')->find($analyseId);
                if (!$analyse) continue;

                $type = strtoupper($analyse->type->name ?? '');

                if (in_array($type, ['GERME', 'CULTURE'], true)) {
                    $selectedOptions  = Arr::get($data, 'selectedOptions', []);
                    $optionsStandards = array_intersect($selectedOptions, [
                        'non-rechercher', 'en-cours', 'culture-sterile', 'absence-germe-pathogene', 'Autre'
                    ]);

                    Resultat::updateOrCreate(
                        ['prescription_id' => $this->prescription->id, 'analyse_id' => $analyseId],
                        [
                            'resultats' => $optionsStandards ? json_encode(array_values($optionsStandards), JSON_UNESCAPED_UNICODE) : null,
                            'valeur'    => Arr::get($data, 'autreValeur') ?: null,
                            'status'    => 'EN_COURS',
                        ]
                    );
                    $updatedAnalyseIds[] = $analyseId;
                    continue;
                }

                $valeur         = Arr::get($data, 'valeur');
                $resultats      = Arr::get($data, 'resultats');
                $interpretation = Arr::get($data, 'interpretation');
                $conclusion     = Arr::get($data, 'conclusion');
                $famille_id     = Arr::get($data, 'famille_id');
                $bacterie_id    = Arr::get($data, 'bacterie_id');

                Resultat::updateOrCreate(
                    ['prescription_id' => $this->prescription->id, 'analyse_id' => $analyseId],
                    [
                        'valeur'         => is_array($valeur) ? json_encode($valeur, JSON_UNESCAPED_UNICODE) : $valeur,
                        'resultats'      => is_array($resultats) ? json_encode($resultats, JSON_UNESCAPED_UNICODE) : $resultats,
                        'interpretation' => $interpretation ?: null,
                        'conclusion'     => $conclusion ?: null,
                        'status'         => 'EN_COURS',
                        'famille_id'     => $famille_id ?: null,
                        'bacterie_id'    => $bacterie_id ?: null,
                    ]
                );
                $updatedAnalyseIds[] = $analyseId;
            }

            // ✅ NOUVEAU : Mettre à jour la table pivot pour les analyses principales qui ont été modifiées
            if (!empty($principalAnalyseIds) && !empty($updatedAnalyseIds)) {
                $pivotToUpdate = array_intersect($principalAnalyseIds, $updatedAnalyseIds);
                
                if (!empty($pivotToUpdate)) {
                    $pivotUpdatedCount = DB::table('prescription_analyse')
                        ->where('prescription_id', $this->prescription->id)
                        ->whereIn('analyse_id', $pivotToUpdate)
                        ->update([
                            'status' => \App\Models\AnalysePrescription::STATUS_EN_COURS,
                            'updated_at' => now()
                        ]);

                    Log::info('Statuts pivot mis à jour lors de la saisie', [
                        'prescription_id' => $this->prescription->id,
                        'pivot_updated_count' => $pivotUpdatedCount,
                        'principal_analyse_ids' => $pivotToUpdate
                    ]);
                }
            }

            // ✅ NOUVEAU : Mettre à jour le statut de la prescription si nécessaire
            if ($this->prescription->status === 'EN_ATTENTE') {
                $this->prescription->update(['status' => 'EN_COURS']);
            }

            DB::commit();

            // toast/flash (Laracasts ou fallback)
            $this->flashSuccess('Résultats sauvegardés avec mise à jour des statuts.');

            // au cas où d'autres composants Livewire doivent se rafraîchir avant le redirect
            $this->dispatch('refreshSidebar');

            // 🔁 rafraîchissement global : redirige vers l'URL courante
            return $this->redirectRoute(
                'technicien.prescription.show',
                ['prescription' => $this->prescription->id],
                navigate: true
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('saveAll error', ['e' => $e->getMessage()]);
            $this->flashError('Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    /**
     * Synchroniser antibiogrammes ⇄ options sélectionnées (appelée au clic)
     * - crée les ABG manquants pour les bactéries sélectionnées
     * - supprime les ABG des bactéries dé-sélectionnées
     */
    public function syncAntibiogrammes(int $analyseId): void
    {
        // 🔒 Anti double-clic / re-entrance via lock distribué
        $lockKey = "sync_abg_{$this->prescription->id}_{$analyseId}";
        $lock = Cache::lock($lockKey, 5);

        if (!$lock->get()) {
            return;
        }

        try {
            $data = $this->results[$analyseId] ?? [];
            $selected = \Illuminate\Support\Arr::get($data, 'selectedOptions', []);
            $selected = is_array($selected) ? $selected : [];

            $chosen = [];
            foreach ($selected as $opt) {
                if (is_string($opt) && str_starts_with($opt, 'bacterie-')) {
                    $id = (int) substr($opt, 9);
                    if ($id > 0) $chosen[] = $id;
                }
            }
            $chosen = array_values(array_unique($chosen));

            DB::beginTransaction();

            $existing = Antibiogramme::where('prescription_id', $this->prescription->id)
                ->where('analyse_id', $analyseId)
                ->get();

            $have = $existing->pluck('bacterie_id')->all();

            $toDelete = array_diff($have, $chosen);
            $toCreate = array_diff($chosen, $have);

            foreach ($toDelete as $bid) {
                $abg = $existing->firstWhere('bacterie_id', $bid);
                if ($abg) {
                    ResultatAntibiotique::where('antibiogramme_id', $abg->id)->delete();
                    $abg->delete();
                }
            }

            foreach ($toCreate as $bid) {
                $abg = Antibiogramme::firstOrCreate([
                    'prescription_id' => $this->prescription->id,
                    'analyse_id'      => $analyseId,
                    'bacterie_id'     => $bid,
                ]);
            }

            DB::commit();

            unset($this->pendingSync[$analyseId]);

            $msg = "Synchronisation réussie : ".count($chosen)." actif(s)";
            if (count($toCreate))  $msg .= ", ".count($toCreate)." créé(s)";
            if (count($toDelete))  $msg .= ", ".count($toDelete)." supprimé(s)";
            $this->flashSuccess($msg);

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->flashError('Erreur sync : ' . $e->getMessage());
        } finally {
            optional($lock)->release();
        }
    }

    public function render()
    {
        return view('livewire.technicien.recursive-result-form');
    }
}