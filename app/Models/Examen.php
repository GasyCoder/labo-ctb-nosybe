<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examen extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'abr',
        'status',
    ];

    // Scope pour les examens actifs
    public function scopeActifs($query)
    {
        return $query->where('status', true);
    }

    // Relation éventuelle : un examen a plusieurs analyses
    public function analyses()
    {
        return $this->hasMany(Analyse::class);
    }
}
