<?php

namespace App\Repositories;

use App\Models\Paiement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaiementRepository
{
    protected Paiement $model;

    public function __construct(Paiement $model)
    {
        $this->model = $model;
    }

    /**
     * Créer un nouvel enregistrement de paiement
     */
    public function create(array $data): Paiement
    {
        return $this->model->create($data);
    }

    /**
     * Trouver un paiement par ID
     */
    public function find(int $paiementId): ?Paiement
    {
        return $this->model->with([
            'prescription:id,reference,patient_id',
            'prescription.patient:id,nom,prenom'
        ])->find($paiementId);
    }

    /**
     * Obtenir le total des encaissements d'un utilisateur pour une date donnée
     */
    public function getEncaissementUtilisateurJour(int $utilisateurId, string $date): float
    {
        return $this->model
            ->where('recu_par', $utilisateurId)
            ->whereDate('created_at', $date)
            ->where('status', '!=', 'ANNULE')
            ->sum('montant');
    }

    /**
     * Obtenir les statistiques des paiements pour une journée
     */
    public function getStatistiquesJour(string $date): array
    {
        $debut = $date . ' 00:00:00';
        $fin = $date . ' 23:59:59';

        $stats = $this->model
            ->whereBetween('created_at', [$debut, $fin])
            ->where('status', '!=', 'ANNULE')
            ->selectRaw('
                COUNT(*) as nombre_paiements,
                SUM(montant) as total_encaisse,
                AVG(montant) as montant_moyen,
                COUNT(DISTINCT prescription_id) as prescriptions_payees
            ')
            ->first();

        $parMode = $this->model
            ->whereBetween('created_at', [$debut, $fin])
            ->where('status', '!=', 'ANNULE')
            ->selectRaw('mode_paiement, COUNT(*) as nombre, SUM(montant) as total')
            ->groupBy('mode_paiement')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->mode_paiement => [
                    'nombre' => $item->nombre,
                    'total' => $item->total
                ]];
            })->toArray();

        return [
            'nombre_paiements' => $stats->nombre_paiements ?? 0,
            'total_encaisse' => $stats->total_encaisse ?? 0,
            'montant_moyen' => $stats->montant_moyen ?? 0,
            'prescriptions_payees' => $stats->prescriptions_payees ?? 0,
            'par_mode_paiement' => $parMode
        ];
    }

    /**
     * Obtenir un rapport de caisse pour une période donnée
     */
    public function getRapportCaisse(string $dateDebut, string $dateFin): array
    {
        $debut = $dateDebut . ' 00:00:00';
        $fin = $dateFin . ' 23:59:59';

        $paiements = $this->model
            ->with([
                'prescription:id,reference,patient_id',
                'prescription.patient:id,nom,prenom'
            ])
            ->whereBetween('created_at', [$debut, $fin])
            ->where('status', '!=', 'ANNULE')
            ->select([
                'id', 'prescription_id', 'montant', 'mode_paiement',
                'numero_recu', 'created_at', 'recu_par'
            ])
            ->get();

        $totalParMode = $paiements->groupBy('mode_paiement')
            ->map(function ($group) {
                return [
                    'nombre' => $group->count(),
                    'total' => $group->sum('montant')
                ];
            })->toArray();

        return [
            'paiements' => $paiements->map(function ($paiement) {
                return [
                    'id' => $paiement->id,
                    'numero_recu' => $paiement->numero_recu,
                    'montant' => $paiement->montant,
                    'mode_paiement' => $paiement->mode_paiement,
                    'date' => $paiement->created_at,
                    'patient' => $paiement->prescription->patient->nom . ' ' . $paiement->prescription->patient->prenom,
                    'prescription_ref' => $paiement->prescription->reference,
                    'recu_par' => $paiement->recu_par
                ];
            })->toArray(),
            'total_par_mode' => $totalParMode,
            'total_general' => $paiements->sum('montant'),
            'nombre_total' => $paiements->count()
        ];
    }

    /**
     * Obtenir les encaissements d'un utilisateur pour une date donnée
     */
    public function getEncaissementsUtilisateur(int $utilisateurId, string $date): array
    {
        $debut = $date . ' 00:00:00';
        $fin = $date . ' 23:59:59';

        $paiements = $this->model
            ->with([
                'prescription:id,reference,patient_id',
                'prescription.patient:id,nom,prenom'
            ])
            ->where('recu_par', $utilisateurId)
            ->whereBetween('created_at', [$debut, $fin])
            ->where('status', '!=', 'ANNULE')
            ->select([
                'id', 'prescription_id', 'montant', 'mode_paiement',
                'numero_recu', 'created_at'
            ])
            ->get();

        return $paiements->map(function ($paiement) {
            return [
                'id' => $paiement->id,
                'numero_recu' => $paiement->numero_recu,
                'montant' => $paiement->montant,
                'mode_paiement' => $paiement->mode_paiement,
                'date' => $paiement->created_at,
                'patient' => $paiement->prescription->patient->nom . ' ' . $paiement->prescription->patient->prenom,
                'prescription_ref' => $paiement->prescription->reference
            ];
        })->toArray();
    }
}