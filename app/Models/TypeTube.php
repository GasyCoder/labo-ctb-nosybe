<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeTube extends Model
{
    protected $table = 'type_tubes';

    protected $fillable = [
        'code',
        'couleur',
    ];

    // RELATIONS

    /**
     * Relation avec les prélèvements qui recommandent ce type de tube
     */
    public function prelevementsRecommandes()
    {
        return $this->hasMany(Prelevement::class, 'type_tube_recommande_id');
    }

    /**
     * Relation avec les tubes de ce type
     */
    public function tubes()
    {
        return $this->hasMany(Tube::class, 'type_tube_id');
    }

    // MÉTHODES MÉTIER

    /**
     * Retourne la description du type de tube selon la logique laboratoire
     */
    public function getDescriptionAttribute()
    {
        return match($this->code) {
            'SEC' => 'Tube sec (sans anticoagulant) - Sérologie, biochimie',
            'EDTA' => 'Tube EDTA - Hématologie, numération',
            'HEPA' => 'Tube hépariné - Biochimie urgente',
            'CITR' => 'Tube citraté - Hémostase, coagulation',
            'FLACON' => 'Flacon stérile - Urine, selles, crachat, liquides',
            'ECOUVILLON' => 'Écouvillon stérile - Prélèvements bactériologiques',
            default => 'Type de tube spécialisé'
        };
    }

    /**
     * Retourne les analyses recommandées pour ce type de tube
     */
    public function getAnalysesRecommandeesAttribute()
    {
        return match($this->code) {
            'SEC' => ['Glycémie', 'Créatinine', 'Urée', 'Transaminases', 'Sérologie'],
            'EDTA' => ['Hémogramme', 'Numération', 'Frottis sanguin'],
            'HEPA' => ['Ionogramme', 'Gazométrie', 'Biochimie urgente'],
            'CITR' => ['TP', 'TCA', 'Fibrinogène', 'D-dimères'],
            'FLACON' => ['ECBU', 'Coproculture', 'Recherche parasites'],
            'ECOUVILLON' => ['Culture bactérienne', 'Antibiogramme', 'PCR'],
            default => []
        };
    }

    /**
     * Détermine si c'est un tube sanguin
     */
    public function estTubeSanguin()
    {
        return in_array($this->code, ['SEC', 'EDTA', 'HEPA', 'CITR']);
    }

    /**
     * Retourne l'icône du type de tube
     */
    public function getIconeAttribute()
    {
        return match($this->code) {
            'SEC', 'EDTA', 'HEPA', 'CITR' => '🧪',
            'FLACON' => '🫙',
            'ECOUVILLON' => '🦠',
            default => '⚗️'
        };
    }

    // SCOPES

    /**
     * Scope pour les tubes sanguins
     */
    public function scopeSanguins($query)
    {
        return $query->whereIn('code', ['SEC', 'EDTA', 'HEPA', 'CITR']);
    }

    /**
     * Scope pour les contenants non sanguins
     */
    public function scopeNonSanguins($query)
    {
        return $query->whereIn('code', ['FLACON', 'ECOUVILLON']);
    }

    // MÉTHODES STATIQUES

    /**
     * Retourne tous les types de tubes organisés par catégorie
     */
    public static function parCategorie()
    {
        return [
            'sanguins' => static::sanguins()->orderBy('code')->get(),
            'non_sanguins' => static::nonSanguins()->orderBy('code')->get(),
        ];
    }

    /**
     * Retourne le type de tube par code
     */
    public static function parCode($code)
    {
        return static::where('code', $code)->first();
    }
}