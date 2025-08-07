<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prescripteur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'grade',
        'specialite',
        'status', // ← NOUVEAU CHAMP
        'telephone',
        'email',
        'is_active',
        'adresse',
        'ville',
        'code_postal',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActifs($query)
    {
        return $query->where('is_active', true);
    }

    // Relations
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'prescripteur_id');
    }

    // Statistiques commissions (NOUVELLE VERSION SIMPLE)
    public function getStatistiquesCommissions($dateDebut = null, $dateFin = null)
    {
        $query = $this->prescriptions()->whereHas('paiements');

        if ($dateDebut && $dateFin && $dateDebut !== '' && $dateFin !== '') {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($dateDebut)->startOfDay(),
                \Carbon\Carbon::parse($dateFin)->endOfDay()
            ]);
        }

        $prescriptions = $query->with('paiements')->get();

        return [
            'total_prescriptions' => $prescriptions->count(),
            'montant_total_analyses' => $prescriptions->sum(function($p) { return $p->getMontantAnalysesCalcule(); }),
            'montant_total_paye' => $prescriptions->sum(function($p) { return $p->paiements->sum('montant'); }),
            'total_commission' => $prescriptions->sum(function($p) { return $p->paiements->sum('commission_prescripteur'); }),
            'commission_moyenne' => $prescriptions->count() > 0 ? $prescriptions->sum(function($p) { return $p->paiements->sum('commission_prescripteur'); }) / $prescriptions->count() : 0
        ];
    }

    public function getCommissionsParMois($annee = null, $dateDebut = null, $dateFin = null)
    {
        $query = $this->prescriptions()->whereHas('paiements');
        
        if ($dateDebut && $dateFin && $dateDebut !== '' && $dateFin !== '') {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($dateDebut)->startOfDay(),
                \Carbon\Carbon::parse($dateFin)->endOfDay()
            ]);
        } elseif ($annee) {
            $query->whereYear('created_at', $annee);
        }

        $prescriptions = $query->with('paiements')->get();
        
        if ($prescriptions->isEmpty()) {
            return collect([]);
        }

        $prescriptionsParMois = $prescriptions->groupBy(function($prescription) {
            return $prescription->created_at->month;
        });

        $results = collect();
        foreach ($prescriptionsParMois as $mois => $prescriptionsDuMois) {
            $results->push((object)[
                'mois' => $mois,
                'nombre_prescriptions' => $prescriptionsDuMois->count(),
                'montant_analyses' => $prescriptionsDuMois->sum(function($p) { return $p->getMontantAnalysesCalcule(); }),
                'montant_paye' => $prescriptionsDuMois->sum(function($p) { return $p->paiements->sum('montant'); }),
                'commission' => $prescriptionsDuMois->sum(function($p) { return $p->paiements->sum('commission_prescripteur'); }),
            ]);
        }

        return $results->sortBy('mois')->values();
    }

    // Accesseurs
    public function getNomCompletAttribute()
    {
        $grade = $this->grade ? $this->grade . ' ' : '';
        $prenom = $this->prenom ? $this->prenom . ' ' : '';
        return trim($grade . $prenom . $this->nom);
    }

    public function getNomSimpleAttribute()
    {
        return trim(($this->prenom ? $this->prenom . ' ' : '') . $this->nom);
    }

    // Méthodes statiques
    public static function getGradesDisponibles()
    {
        return [
            'Dr' => 'Docteur',
        ];
    }

    // NOUVEAU : Méthode pour les statuts
    public static function getStatusDisponibles()
    {
        return [
            'Medecin' => 'Médecin',
            'BiologieSolidaire' => 'Biologie Solidaire',
        ];
    }
}