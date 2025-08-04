<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'reference', 'nom', 'prenom', 'sexe', 'telephone', 'email', 'is_fidele'
    ];

    // Optionnel : un scope pour filtrer les fidèles
    public function scopeFideles($query)
    {
        return $query->where('is_fidele', true);
    }
}
