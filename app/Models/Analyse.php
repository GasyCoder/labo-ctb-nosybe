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

    // CASTS pour les types de données
    protected $casts = [
        'prix' => 'decimal:2',
        'is_bold' => 'boolean',
        'status' => 'boolean',
        'valeurs_predefinies' => 'array', // JSON automatiquement converti en array
        'ordre' => 'integer',
    ];

    // Relation parent : cette analyse appartient à un panel (si parent_id existe)
    public function parent()
    {
        return $this->belongsTo(Analyse::class, 'parent_id');
    }

    // Relation enfants : ce panel a plusieurs analyses enfants
    public function enfants()
    {
        return $this->hasMany(Analyse::class, 'parent_id');
    }

    // Relation avec examen
    public function examen()
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    // Relation avec type
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    // Relation avec les résultats
    public function resultats()
    {
        return $this->hasMany(Resultat::class);
    }

    // SCOPES utiles
    public function scopeActives($query)
    {
        return $query->where('status', true);
    }

    public function scopeParents($query)
    {
        return $query->where('level', 'PARENT');
    }

    public function scopeNormales($query)
    {
        return $query->where('level', 'NORMAL');
    }

    public function scopeEnfants($query)
    {
        return $query->where('level', 'CHILD');
    }

    // ACCESSEURS et MUTATEURS
    public function getValeurCompleteAttribute()
    {
        if ($this->valeur_ref && $this->unite) {
            return $this->valeur_ref . ' ' . $this->unite;
        }
        return $this->valeur_ref;
    }

    public function getEstParentAttribute()
    {
        return $this->level === 'PARENT';
    }

    public function getADesEnfantsAttribute()
    {
        return $this->enfants()->count() > 0;
    }

    // MÉTHODES utiles
    public function getPrixFormate()
    {
        return number_format($this->prix, 0, ',', ' ') . ' Ar';
    }
}