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
        'valeur_reference',
        'ordre',
        'status',
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

    // Relation avec examen (si tu veux retrouver l’examen lié)
    public function examen()
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    // Relation avec type (si tu veux retrouver le type d’analyse)
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    // Optionnel : scope pour les analyses actives
    public function scopeActives($query)
    {
        return $query->where('status', true);
    }
}
