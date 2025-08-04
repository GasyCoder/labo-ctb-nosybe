<?php

namespace App\Repositories;

use App\Models\Analyse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class AnalyseRepository
{
    protected Analyse $model;

    public function __construct(Analyse $model)
    {
        $this->model = $model;
    }

    /**
     * Récupérer une analyse avec ses relations détaillées
     */
    public function getAvecDetails(int $analyseId): Analyse
    {
        return $this->model->with([
            'parent:id,nom', // Parent de l'analyse (si hiérarchie existe)
            'synonymes:id,analyse_id,nom', // Synonymes si table existe
            'equipement:id,nom,statut', // Équipement requis
            'reactifs:id,nom,stock' // Réactifs requis
        ])->findOrFail($analyseId);
    }

    /**
     * Recherche d'analyses par terme
     */
    public function rechercher(string $terme, int $limite = 10): Collection
    {
        return $this->model->where('is_active', true)
            ->where(function (Builder $query) use ($terme) {
                $terme = strtolower($terme);
                $query->whereRaw('LOWER(nom) LIKE ?', ["%{$terme}%"])
                      ->orWhereRaw('LOWER(code) LIKE ?', ["%{$terme}%"]);
            })
            ->select(['id', 'nom', 'code', 'prix'])
            ->limit($limite)
            ->get();
    }

    /**
     * Recherche dans les synonymes d'analyses
     */
    public function rechercherDansSynonymes(string $terme, int $limite = 5): array
    {
        return $this->model->where('is_active', true)
            ->whereHas('synonymes', function (Builder $query) use ($terme) {
                $query->whereRaw('LOWER(nom) LIKE ?', ["%{$terme}%"]);
            })
            ->select(['id', 'nom', 'code', 'prix'])
            ->limit($limite)
            ->get()
            ->toArray();
    }

    /**
     * Récupérer les analyses fréquemment associées
     */
    public function getAssociationsFrequentes(int $analyseId): Collection
    {
        // Supposons une table pivot 'prescription_analyse' pour les associations
        $associations = DB::table('prescription_analyse as pa1')
            ->join('prescription_analyse as pa2', 'pa1.prescription_id', '=', 'pa2.prescription_id')
            ->join('analyses', 'pa2.analyse_id', '=', 'analyses.id')
            ->where('pa1.analyse_id', $analyseId)
            ->where('pa2.analyse_id', '!=', $analyseId)
            ->where('analyses.is_active', true)
            ->selectRaw('analyses.id, analyses.nom, analyses.prix, COUNT(*) as frequence')
            ->groupBy('analyses.id', 'analyses.nom', 'analyses.prix')
            ->orderByDesc('frequence')
            ->limit(5)
            ->get();

        return $associations->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'nom' => $item->nom,
                'prix' => $item->prix,
                'frequence' => $item->frequence,
                'code' => $item->nom // Supposons que le code est dérivé du nom pour simplifier
            ];
        });
    }

    /**
     * Mise à jour d'une analyse
     */
    public function update(int $analyseId, array $data): Analyse
    {
        $analyse = $this->model->findOrFail($analyseId);
        $analyse->update($data);
        return $analyse->fresh();
    }
}