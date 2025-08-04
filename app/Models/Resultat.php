<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resultat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prescription_id',
        'analyse_id',
        'resultats',
        'valeur',
        'interpretation',
        'conclusion',
        'status',
        'validated_by',
        'validated_at',
    ];

    // Relations
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function analyse()
    {
        return $this->belongsTo(Analyse::class);
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Scopes pour les statuts
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    public function scopeValides($query)
    {
        return $query->where('status', 'VALIDE');
    }
    public function scopeEnCours($query)
    {
        return $query->where('status', 'EN_COURS');
    }
}
