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
        'status',
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

    public function scopeCommissionnables($query)
    {
        return $query->where('status', '!=', 'BiologieSolidaire');
    }

    // Relations
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'prescripteur_id');
    }

    // Statistiques commissions (AVEC EXCLUSION BiologieSolidaire)
    public function getStatistiquesCommissions($dateDebut = null, $dateFin = null)
    {
        // Si c'est BiologieSolidaire, retourner zéro
        if ($this->status === 'BiologieSolidaire') {
            return [
                'total_prescriptions' => 0,
                'montant_total_analyses' => 0,
                'montant_total_paye' => 0,
                'total_commission' => 0,
                'commission_moyenne' => 0
            ];
        }

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
        // Si c'est BiologieSolidaire, retourner collection vide
        if ($this->status === 'BiologieSolidaire') {
            return collect([]);
        }

        $query = $this->prescriptions()->whereHas('paiements');
        
        if ($dateDebut && $dateFin && $dateDebut !== '' && $dateFin !== '') {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($dateDebut)->startOfDay(),
                \Carbon\Carbon::parse($dateFin)->endOfDay()
            ]);
        } elseif ($annee) {
            $query->whereYear('created_at', $annee);
        }

        // Charger la relation avec le patient
        $prescriptions = $query->with(['paiements', 'patient'])->get();
        
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
                // Ajouter les détails des prescriptions avec les informations du patient
                'prescriptions' => $prescriptionsDuMois->map(function($prescription) {
                    return (object)[
                        'id' => $prescription->id,
                        'patient_nom_complet' => $prescription->patient ? $prescription->patient->nom_complet : 'Patient inconnu',
                        'patient_numero_dossier' => $prescription->patient ? $prescription->patient->numero_dossier : 'N/A',
                        'montant_analyses' => $prescription->getMontantAnalysesCalcule(),
                        'montant_paye' => $prescription->paiements->sum('montant'),
                        'commission' => $prescription->paiements->sum('commission_prescripteur'),
                        'date' => $prescription->created_at->format('d/m/Y'),
                    ];
                }),
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

    public function getEstCommissionnableAttribute()
    {
        return $this->status !== 'BiologieSolidaire';
    }

    // Méthodes statiques
    public static function getGradesDisponibles()
    {
        return [
            'Dr' => 'Docteur',
            'Pr' => 'Professeur',
            'Infirmier(e)' => 'Infirmier(e)',
            'Sage-femme' => 'Sage-femme'
        ];
    }

    public static function getStatusDisponibles()
    {
        return [
            'Medecin' => 'Médecin',
            'BiologieSolidaire' => 'Biologie Solidaire',
        ];
    }
}