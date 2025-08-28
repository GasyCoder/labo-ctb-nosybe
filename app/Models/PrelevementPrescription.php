<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrelevementPrescription extends Model
{
    protected $table = 'prelevement_prescription';

    protected $fillable = [
        'prescription_id',
        'prelevement_id',
        'prix_unitaire',
        'quantite',
        'is_payer',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function prelevement()
    {
        return $this->belongsTo(Prelevement::class);
    }

    // Accès au patient via la prescription
    public function patient()
    {
        return $this->prescription->patient();
    }
}
