<?php

namespace App\Services;

use App\Models\Paiement;
use App\Models\Prescription;
use App\Models\User;
use App\Repositories\PaiementRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class PaiementService
{
    protected PaiementRepository $paiementRepository;

    public function __construct(PaiementRepository $paiementRepository)
    {
        $this->paiementRepository = $paiementRepository;
    }

    /**
     * Enregistrer un paiement avec validation complète
     */
    public function enregistrerPaiement(Prescription $prescription, array $donneesPaiement): Paiement
    {
        // Validations préalables
        $this->validerDonneesPaiement($donneesPaiement);
        $this->validerAutorisationsUtilisateur($donneesPaiement);
        $this->validerCoherenceMontants($prescription, $donneesPaiement);
        
        DB::beginTransaction();
        
        try {
            // Créer l'enregistrement paiement
            $paiement = $this->paiementRepository->create([
                'prescription_id' => $prescription->id,
                'montant' => $donneesPaiement['montant_final'],
                'mode_paiement' => $donneesPaiement['mode_paiement'],
                'recu_par' => $donneesPaiement['recu_par'],
                'details_paiement' => $this->construireDetailsPaiement($donneesPaiement),
                'numero_recu' => $this->genererNumeroRecu(),
                'metadata' => [
                    'montant_paye' => $donneesPaiement['montant_paye'],
                    'monnaie_rendue' => $donneesPaiement['monnaie_rendue'] ?? 0,
                    'taux_remise' => $donneesPaiement['taux_remise'] ?? 0,
                    'montant_remise' => $donneesPaiement['montant_remise'] ?? 0,
                    'motif_remise' => $donneesPaiement['motif_remise'] ?? null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            ]);
            
            // Marquer prélèvements comme payés
            $this->marquerPrelevementsPayes($prescription);
            
            // Mettre à jour statut prescription
            $prescription->update(['status' => 'PAYE']);
            
            // Enregistrer dans journal de caisse
            $this->enregistrerMouvementCaisse($paiement);
            
            // Historique audit
            $this->enregistrerAuditPaiement($paiement, 'CREATION');
            
            DB::commit();
            
            // Invalider caches pertinents
            $this->invaliderCaches($prescription->patient_id);
            
            return $paiement;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Erreur enregistrement paiement : " . $e->getMessage());
        }
    }

    private function validerDonneesPaiement(array $donnees): void
    {
        $requises = ['mode_paiement', 'montant_final', 'montant_paye', 'recu_par'];
        
        foreach ($requises as $champ) {
            if (!isset($donnees[$champ]) || empty($donnees[$champ])) {
                throw new \InvalidArgumentException("Champ requis manquant : {$champ}");
            }
        }
        
        if (!in_array($donnees['mode_paiement'], ['ESPECES', 'CARTE', 'CHEQUE'])) {
            throw new \InvalidArgumentException("Mode de paiement invalide");
        }
        
        if ($donnees['montant_paye'] < $donnees['montant_final']) {
            throw new \InvalidArgumentException("Montant payé insuffisant");
        }
    }

    private function validerAutorisationsUtilisateur(array $donnees): void
    {
        $utilisateur = User::find($donnees['recu_par']);
        
        if (!$utilisateur) {
            throw new \InvalidArgumentException("Utilisateur introuvable");
        }
        
        // Vérifier autorisation encaissement
        if (!$this->peutEncaisser($utilisateur, $donnees['montant_final'])) {
            throw new \InvalidArgumentException("Utilisateur non autorisé pour ce montant");
        }
        
        // Vérifier autorisation remise
        if (isset($donnees['taux_remise']) && $donnees['taux_remise'] > 0) {
            if (!$this->autoriseRemise($utilisateur, $donnees['taux_remise'])) {
                throw new \InvalidArgumentException("Remise non autorisée");
            }
        }
        
        // Vérifier limite quotidienne
        if (!$this->verifierLimiteQuotidienne($utilisateur, $donnees['montant_final'])) {
            throw new \InvalidArgumentException("Limite quotidienne d'encaissement dépassée");
        }
    }

    private function validerCoherenceMontants(Prescription $prescription, array $donnees): void
    {
        // Calculer montant théorique prescription
        $montantTheorique = $prescription->analyses()->sum('analyses.prix');
        $remiseAppliquee = $donnees['montant_remise'] ?? 0;
        $montantAttendu = $montantTheorique - $remiseAppliquee;
        
        // Tolérance de 1% pour arrondis
        $tolerance = $montantAttendu * 0.01;
        
        if (abs($donnees['montant_final'] - $montantAttendu) > $tolerance) {
            throw new \InvalidArgumentException("Incohérence dans les montants calculés");
        }
    }

    private function construireDetailsPaiement(array $donnees): array
    {
        $details = [
            'mode' => $donnees['mode_paiement'],
            'timestamp' => now()->toISOString(),
        ];
        
        match($donnees['mode_paiement']) {
            'CARTE' => $details = array_merge($details, [
                'numero_transaction' => $donnees['numero_transaction'] ?? null,
                'numero_autorisation' => $donnees['numero_autorisation'] ?? null,
                'type_carte' => 'BANCAIRE', // À adapter selon besoins
            ]),
            'CHEQUE' => $details = array_merge($details, [
                'numero_cheque' => $donnees['numero_cheque'] ?? null,
                'banque' => $donnees['banque_cheque'] ?? null,
                'date_emission' => $donnees['date_cheque'] ?? now()->toDateString(),
            ]),
            'ESPECES' => $details = array_merge($details, [
                'montant_recu' => $donnees['montant_paye'],
                'monnaie_rendue' => $donnees['monnaie_rendue'] ?? 0,
            ]),
        };
        
        return $details;
    }

    private function genererNumeroRecu(): string
    {
        $prefixe = 'RC' . date('Y');
        $compteur = Cache::increment("compteur_recu_" . date('Y'), 1);
        
        if ($compteur === 1) {
            Cache::put("compteur_recu_" . date('Y'), 1, now()->endOfYear());
        }
        
        return $prefixe . str_pad($compteur, 6, '0', STR_PAD_LEFT);
    }

    private function marquerPrelevementsPayes(Prescription $prescription): void
    {
        $prescription->prelevements()->updateExistingPivot(
            $prescription->prelevements->pluck('id'),
            ['is_payer' => 'PAYE']
        );
    }

    private function enregistrerMouvementCaisse(Paiement $paiement): void
    {
        DB::table('mouvements_caisse')->insert([
            'paiement_id' => $paiement->id,
            'type_mouvement' => 'ENTREE',
            'mode_paiement' => $paiement->mode_paiement,
            'montant' => $paiement->montant,
            'utilisateur_id' => $paiement->recu_par,
            'date_mouvement' => now(),
            'description' => "Paiement prescription #{$paiement->prescription->reference}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function enregistrerAuditPaiement(Paiement $paiement, string $action): void
    {
        DB::table('audit_paiements')->insert([
            'paiement_id' => $paiement->id,
            'action' => $action,
            'utilisateur_id' => Auth::id(),
            'donnees_avant' => null,
            'donnees_apres' => json_encode($paiement->toArray()),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Validations métier utilisateur
     */
    public function peutEncaisser(User $utilisateur, float $montant): bool
    {
        // Vérifications par rôle et montant
        $limites = [
            'secretaire' => 500000, // 500k Ar
            'admin' => 2000000,     // 2M Ar
            'biologiste' => 1000000, // 1M Ar
        ];
        
        $roleUtilisateur = $utilisateur->roles->first()?->name ?? 'secretaire';
        $limiteAutorisee = $limites[$roleUtilisateur] ?? $limites['secretaire'];
        
        return $montant <= $limiteAutorisee;
    }

    public function autoriseRemise(User $utilisateur, float $tauxRemise): bool
    {
        // Autorisations remise par rôle
        $autorisations = [
            'secretaire' => 5,    // Max 5%
            'admin' => 25,        // Max 25%
            'biologiste' => 15,   // Max 15%
        ];
        
        $roleUtilisateur = $utilisateur->roles->first()?->name ?? 'secretaire';
        $tauxMaxAutorise = $autorisations[$roleUtilisateur] ?? $autorisations['secretaire'];
        
        return $tauxRemise <= $tauxMaxAutorise;
    }

    private function verifierLimiteQuotidienne(User $utilisateur, float $montant): bool
    {
        $encaissementAujourdhui = $this->paiementRepository->getEncaissementUtilisateurJour(
            $utilisateur->id,
            now()->toDateString()
        );
        
        $limiteQuotidienne = match($utilisateur->roles->first()?->name ?? 'secretaire') {
            'secretaire' => 2000000,  // 2M Ar/jour
            'admin' => 10000000,      // 10M Ar/jour
            'biologiste' => 5000000,  // 5M Ar/jour
            default => 2000000
        };
        
        return ($encaissementAujourdhui + $montant) <= $limiteQuotidienne;
    }

    /**
     * Codes comptables
     */
    public function getCodeComptable(string $modePaiement): string
    {
        return match($modePaiement) {
            'ESPECES' => '531000', // Caisse
            'CARTE' => '512100',   // Banque - Cartes
            'CHEQUE' => '512000',  // Banque - Chèques
            default => '531000'
        };
    }

    /**
     * Annulation paiement (avec autorisation)
     */
    public function annulerPaiement(int $paiementId, string $motif, User $utilisateur): void
    {
        if (!$this->peutAnnulerPaiement($utilisateur)) {
            throw new \InvalidArgumentException("Non autorisé à annuler des paiements");
        }
        
        DB::beginTransaction();
        
        try {
            $paiement = $this->paiementRepository->find($paiementId);
            
            if (!$paiement) {
                throw new \InvalidArgumentException("Paiement introuvable");
            }
            
            if ($paiement->status === 'ANNULE') {
                throw new \InvalidArgumentException("Paiement déjà annulé");
            }
            
            // Vérifier délai d'annulation (24h)
            if ($paiement->created_at->diffInHours(now()) > 24) {
                throw new \InvalidArgumentException("Délai d'annulation dépassé");
            }
            
            // Marquer comme annulé
            $paiement->update([
                'status' => 'ANNULE',
                'annule_par' => $utilisateur->id,
                'annule_at' => now(),
                'motif_annulation' => $motif,
            ]);
            
            // Mouvement inverse en caisse
            DB::table('mouvements_caisse')->insert([
                'paiement_id' => $paiement->id,
                'type_mouvement' => 'SORTIE',
                'mode_paiement' => $paiement->mode_paiement,
                'montant' => $paiement->montant,
                'utilisateur_id' => $utilisateur->id,
                'date_mouvement' => now(),
                'description' => "Annulation paiement #{$paiement->numero_recu} - {$motif}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Remettre prescription en attente paiement
            $paiement->prescription->update(['status' => 'EN_ATTENTE']);
            
            // Audit
            $this->enregistrerAuditPaiement($paiement, 'ANNULATION');
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Erreur annulation paiement : " . $e->getMessage());
        }
    }

    private function peutAnnulerPaiement(User $utilisateur): bool
    {
        return $utilisateur->hasRole(['admin', 'biologiste']);
    }

    /**
     * Statistiques et rapports
     */
    public function getStatistiquesJour(?string $date = null): array
    {
        $date = $date ?? now()->toDateString();
        
        return Cache::remember("stats_paiements_{$date}", 1800, function() use ($date) {
            return $this->paiementRepository->getStatistiquesJour($date);
        });
    }

    public function getRapportCaisse(string $dateDebut, string $dateFin): array
    {
        return $this->paiementRepository->getRapportCaisse($dateDebut, $dateFin);
    }

    public function getEncaissementsUtilisateur(int $utilisateurId, string $date): array
    {
        return $this->paiementRepository->getEncaissementsUtilisateur($utilisateurId, $date);
    }

    /**
     * Remboursements
     */
    public function creerRemboursement(int $paiementId, float $montant, string $motif, User $utilisateur): void
    {
        if (!$this->peutCreerRemboursement($utilisateur)) {
            throw new \InvalidArgumentException("Non autorisé à créer des remboursements");
        }
        
        DB::beginTransaction();
        
        try {
            $paiement = $this->paiementRepository->find($paiementId);
            
            if ($montant > $paiement->montant) {
                throw new \InvalidArgumentException("Montant remboursement supérieur au paiement");
            }
            
            // Créer enregistrement remboursement
            DB::table('remboursements')->insert([
                'paiement_id' => $paiementId,
                'montant' => $montant,
                'motif' => $motif,
                'cree_par' => $utilisateur->id,
                'status' => 'EN_ATTENTE',
                'numero_remboursement' => $this->genererNumeroRemboursement(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Erreur création remboursement : " . $e->getMessage());
        }
    }

    private function peutCreerRemboursement(User $utilisateur): bool
    {
        return $utilisateur->hasRole(['admin', 'biologiste']);
    }

    private function genererNumeroRemboursement(): string
    {
        $prefixe = 'RMB' . date('Y');
        $compteur = Cache::increment("compteur_remboursement_" . date('Y'), 1);
        
        return $prefixe . str_pad($compteur, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Validation paiements en attente
     */
    public function validerPaiementCheque(int $paiementId, User $utilisateur): void
    {
        $paiement = $this->paiementRepository->find($paiementId);
        
        if ($paiement->mode_paiement !== 'CHEQUE') {
            throw new \InvalidArgumentException("Seuls les chèques peuvent être validés");
        }
        
        if ($paiement->status_cheque === 'VALIDE') {
            throw new \InvalidArgumentException("Chèque déjà validé");
        }
        
        $paiement->update([
            'status_cheque' => 'VALIDE',
            'valide_par' => $utilisateur->id,
            'valide_at' => now(),
        ]);
        
        $this->enregistrerAuditPaiement($paiement, 'VALIDATION_CHEQUE');
    }

    public function rejeterPaiementCheque(int $paiementId, string $motif, User $utilisateur): void
    {
        $paiement = $this->paiementRepository->find($paiementId);
        
        $paiement->update([
            'status_cheque' => 'REJETE',
            'rejete_par' => $utilisateur->id,
            'rejete_at' => now(),
            'motif_rejet' => $motif,
        ]);
        
        // Créer mouvement de correction en caisse
        DB::table('mouvements_caisse')->insert([
            'paiement_id' => $paiement->id,
            'type_mouvement' => 'CORRECTION',
            'mode_paiement' => 'CHEQUE',
            'montant' => -$paiement->montant,
            'utilisateur_id' => $utilisateur->id,
            'date_mouvement' => now(),
            'description' => "Rejet chèque #{$paiement->numero_recu} - {$motif}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->enregistrerAuditPaiement($paiement, 'REJET_CHEQUE');
    }

    private function invaliderCaches(int $patientId): void
    {
        Cache::forget("patient_historique_{$patientId}");
        Cache::forget("patient_statistiques_{$patientId}");
        
        $dateAujourdhui = now()->toDateString();
        Cache::forget("stats_paiements_{$dateAujourdhui}");
    }
}