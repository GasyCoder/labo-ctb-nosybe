<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescripteur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'specialite',
        'telephone',
        'email',
        'is_active',
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
        return $this->hasMany(Prescription::class);
    }

    // MÉTHODES POUR CALCULER LES COMMISSIONS

    /**
     * Calculer le total des commissions pour ce prescripteur
     * @param string|null $dateDebut
     * @param string|null $dateFin
     * @return float
     */
    public function calculerCommissionsTotal($dateDebut = null, $dateFin = null)
    {
        $query = $this->prescriptions()
                     ->payees()
                     ->terminees();

        if ($dateDebut && $dateFin) {
            $query->parPeriode($dateDebut, $dateFin);
        }

        return $query->get()->sum('commission_prescripteur');
    }

    /**
     * Calculer le montant total des analyses prescrites (payées et terminées)
     */
    public function getMontantAnalysesTotalAttribute()
    {
        return $this->prescriptions()
                   ->payees()
                   ->terminees()
                   ->get()
                   ->sum('montant_analyses');
    }

    /**
     * Calculer le nombre de prescriptions réalisées
     */
    public function getNombrePrescriptionsRealeesAttribute()
    {
        return $this->prescriptions()
                   ->payees()
                   ->terminees()
                   ->count();
    }

    /**
     * Obtenir les commissions détaillées par mois
     */
    public function getCommissionsParMois($annee = null)
    {
        $annee = $annee ?: date('Y');

        return $this->prescriptions()
                   ->payees()
                   ->terminees()
                   ->whereYear('created_at', $annee)
                   ->selectRaw('
                       MONTH(created_at) as mois,
                       COUNT(*) as nombre_prescriptions,
                       SUM(montant_analyses) as montant_analyses,
                       SUM(montant_analyses * 0.10) as commission
                   ')
                   ->groupBy('mois')
                   ->orderBy('mois')
                   ->get();
    }

    /**
     * Commission en attente (prescriptions payées mais pas encore terminées)
     */
    public function getCommissionEnAttenteAttribute()
    {
        return $this->prescriptions()
                   ->payees()
                   ->whereNotIn('status', ['ANALYSE_TERMINEE', 'TERMINE', 'ARCHIVE'])
                   ->get()
                   ->sum('part_prescripteur');
    }

    /**
     * Commission disponible (prescriptions terminées)
     */
    public function getCommissionDisponibleAttribute()
    {
        return $this->prescriptions()
                   ->payees()
                   ->terminees()
                   ->get()
                   ->sum('part_prescripteur');
    }

    // Accesseur pour nom complet
    public function getNomCompletAttribute()
    {
        return trim($this->prenom . ' ' . $this->nom);
    }
}
