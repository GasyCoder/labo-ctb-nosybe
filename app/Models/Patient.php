<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'nom', 'prenom', 'civilite', 'date_naissance', 'telephone', 'email', 'statut'
    ];

    // Relations
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    // Optionnel : des scopes pour filtrer par statut
    public function scopeFideles($query)
    {
        return $query->where('statut', 'FIDELE');
    }

    public function scopeVip($query)
    {
        return $query->where('statut', 'VIP');
    }

    public function scopeNouveaux($query)
    {
        return $query->where('statut', 'NOUVEAU');
    }
}