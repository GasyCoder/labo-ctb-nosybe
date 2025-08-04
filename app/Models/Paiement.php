<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paiement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prescription_id',
        'montant',
        'mode_paiement',
        'recu_par',
    ];

    // Relations

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function utilisateur()
    {
        // Le user qui a encaissé le paiement
        return $this->belongsTo(User::class, 'recu_par');
    }
}
