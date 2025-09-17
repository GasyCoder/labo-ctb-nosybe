<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prelevement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'denomination',
        'prix',
        'quantite',
        'is_active',
        'type_tube_id',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'quantite' => 'integer',
        'is_active' => 'boolean',
    ];

    // RELATIONS

    /**
     * Relation avec le type de tube recommandé
     */
    public function typeTubeRecommande()
    {
        return $this->belongsTo(TypeTube::class, 'type_tube_id');
    }

    /**
     * Relation Many-to-Many avec Prescription via la table pivot
     */
    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, 'prelevement_prescription')
            ->withPivot('prix_unitaire', 'quantite', 'is_payer')
            ->withTimestamps();
    }

    /**
     * Relation avec les tubes générés pour ce prélèvement
     */
    public function tubes()
    {
        return $this->hasMany(Tube::class);
    }

    // SCOPES

    /**
     * Scope pour les prélèvements actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour rechercher par code ou dénomination
     */
    public function scopeRecherche($query, $terme)
    {
        return $query->where(function($q) use ($terme) {
            $q->where('code', 'like', "%{$terme}%")
              ->orWhere('denomination', 'like', "%{$terme}%");
        });
    }

    /**
     * Scope pour les prélèvements sanguins
     */
    public function scopeSanguins($query)
    {
        return $query->where('denomination', 'like', '%sanguin%')
                    ->orWhere('code', 'like', 'PL2%')
                    ->orWhere('code', 'like', 'PL3%')
                    ->orWhere('code', 'like', 'PL4%');
    }

    /**
     * Scope pour les prélèvements par écouvillon
     */
    public function scopeEcouvillons($query)
    {
        return $query->where('denomination', 'like', '%écouvillon%')
                    ->orWhere('code', 'PL1');
    }

    // MÉTHODES MÉTIER

    /**
     * Détermine si c'est un prélèvement sanguin
     */
    public function estSanguin()
    {
        return stripos($this->denomination, 'sanguin') !== false ||
               in_array($this->code, ['PL2', 'PL3', 'PL4']);
    }

    /**
     * Détermine si c'est un prélèvement par écouvillon
     */
    public function estEcouvillon()
    {
        return stripos($this->denomination, 'écouvillon') !== false ||
               $this->code === 'PL1';
    }

    /**
     * Retourne le type de tube recommandé (code couleur)
     */
    public function getTypeTubeRecommande()
    {
        if ($this->typeTubeRecommande) {
            return [
                'code' => $this->typeTubeRecommande->code,
                'couleur' => $this->typeTubeRecommande->couleur,
                'id' => $this->typeTubeRecommande->id,
            ];
        }

        // Logique de fallback selon le type de prélèvement
        return $this->getTypeTubeFallback();
    }

    /**
     * Logique de fallback pour déterminer le type de tube
     */
    private function getTypeTubeFallback()
    {
        if ($this->estEcouvillon()) {
            return ['code' => 'ECOUVILLON', 'couleur' => 'Blanc', 'id' => null];
        }

        if ($this->estSanguin()) {
            // Pour les analyses de glycémie (HGPO, G50), recommander SEC
            if (stripos($this->denomination, 'HGPO') !== false || 
                stripos($this->denomination, 'G50') !== false) {
                return ['code' => 'SEC', 'couleur' => 'Rouge', 'id' => null];
            }
            
            // Pour le sang standard, SEC par défaut
            return ['code' => 'SEC', 'couleur' => 'Rouge', 'id' => null];
        }

        // Défaut pour autres prélèvements
        return ['code' => 'FLACON', 'couleur' => 'Transparent', 'id' => null];
    }

    /**
     * Retourne tous les types de tubes possibles pour ce prélèvement
     */
    public function getTypesTubesPossibles()
    {
        if ($this->estSanguin()) {
            // Pour le sang, tous les tubes sanguins sont possibles
            return TypeTube::whereIn('code', ['SEC', 'EDTA', 'HEPA', 'CITR'])->get();
        }

        if ($this->estEcouvillon()) {
            return TypeTube::where('code', 'ECOUVILLON')->get();
        }

        // Pour autres prélèvements
        return TypeTube::where('code', 'FLACON')->get();
    }

    /**
     * Calcule le prix total pour une quantité donnée
     */
    public function calculerPrixTotal($quantite = 1)
    {
        return $this->prix * $quantite;
    }

    /**
     * Vérifie si le prélèvement est disponible
     */
    public function estDisponible()
    {
        return $this->is_active && $this->quantite > 0;
    }

    /**
     * Retourne le libellé complet du prélèvement
     */
    public function getLibelleCompletAttribute()
    {
        return "{$this->code} - {$this->denomination}";
    }

    /**
     * Retourne l'icône selon le type de prélèvement
     */
    public function getIconeAttribute()
    {
        if ($this->estSanguin()) {
            return '🩸'; // Goutte de sang
        }

        if ($this->estEcouvillon()) {
            return '🦠'; // Microbe
        }

        return '🧪'; // Tube à essai
    }

    /**
     * Retourne la couleur d'affichage selon le type
     */
    public function getCouleurAffichageAttribute()
    {
        if ($this->estSanguin()) {
            return 'red';
        }

        if ($this->estEcouvillon()) {
            return 'blue';
        }

        return 'green';
    }

    // MÉTHODES STATIQUES

    /**
     * Retourne les prélèvements les plus utilisés
     */
    public static function lesPlusUtilises($limite = 5)
    {
        return static::withCount('prescriptions')
                    ->actifs()
                    ->orderByDesc('prescriptions_count')
                    ->limit($limite)
                    ->get();
    }

    /**
     * Recherche de prélèvements avec suggestions
     */
    public static function rechercher($terme)
    {
        return static::actifs()
                    ->recherche($terme)
                    ->orderBy('denomination')
                    ->get();
    }

    /**
     * Retourne les prélèvements par catégorie
     */
    public static function parCategorie()
    {
        return [
            'sanguins' => static::actifs()->sanguins()->get(),
            'ecouvillons' => static::actifs()->ecouvillons()->get(),
            'autres' => static::actifs()
                            ->whereNotIn('code', ['PL1', 'PL2', 'PL3', 'PL4'])
                            ->get()
        ];
    }
}