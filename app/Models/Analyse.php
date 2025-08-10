<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Analyse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'level',
        'parent_id',
        'designation',
        'description',
        'prix',
        'is_bold',
        'examen_id',
        'type_id',
        'valeur_ref',
        'unite',
        'suffixe',
        'valeurs_predefinies',
        'ordre',
        'status',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'is_bold' => 'boolean',
        'status' => 'boolean',
        'valeurs_predefinies' => 'array',
        'ordre' => 'integer',
    ];

    // Relations hiérarchie
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function enfants()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('ordre')->orderBy('id');
    }

    // Récursion profonde
    public function enfantsRecursive()
    {
        return $this->enfants()->with(['type','examen','enfantsRecursive']);
    }

    // Relations annexes
    public function examen()
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function resultats()
    {
        return $this->hasMany(Resultat::class);
    }

    // Scopes utiles
    public function scopeActives($q) { return $q->where('status', true); }
    public function scopeParents($q) { return $q->where('level', 'PARENT'); }
    public function scopeNormales($q){ return $q->where('level', 'NORMAL'); }
    public function scopeEnfants($q) { return $q->where('level', 'CHILD'); }
    public function scopeRacines($q) { return $q->whereNull('parent_id')->orWhere('level','PARENT'); }

    // Accessors
    public function getValeurCompleteAttribute()
    {
        if ($this->valeur_ref && $this->unite) {
            return $this->valeur_ref.' '.$this->unite;
        }
        return $this->valeur_ref;
    }

    public function getEstParentAttribute()
    {
        return $this->level === 'PARENT';
    }

    public function getADesEnfantsAttribute()
    {
        return $this->enfants()->exists();
    }

    // AJOUT IMPORTANT : Accesseur pour formatted_results
    public function getFormattedResultsAttribute()
    {
        if (!$this->valeurs_predefinies || !is_array($this->valeurs_predefinies)) {
            return [];
        }
        return $this->valeurs_predefinies;
    }

    // AJOUT : Accesseur pour result_disponible (compatibilité ancien code)
    public function getResultDisponibleAttribute()
    {
        return [
            'val_ref' => $this->valeur_ref,
            'unite' => $this->unite,
            'suffixe' => $this->suffixe,
        ];
    }

    // Méthodes utilitaires
    public function getPrixFormate() { return number_format($this->prix, 0, ',', ' ').' Ar'; }

    public function descendantsIds(): array
    {
        $ids = [];
        $stack = [$this->loadMissing('enfants')];
        while ($node = array_pop($stack)) {
            foreach ($node->enfants as $child) {
                $ids[] = $child->id;
                $stack[] = $child->loadMissing('enfants');
            }
        }
        return $ids;
    }
}