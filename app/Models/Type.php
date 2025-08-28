<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Type extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'libelle',
        'status',
    ];
    protected $casts = [
        'status' => 'boolean',
    ];


    // Optionnel : scope pour les types actifs
    public function scopeActifs($query)
    {
        return $query->where('status', true);
    }
    // Relation avec les analyses
    public function analyses()
    {
        return $this->hasMany(Analyse::class);
    }
}
