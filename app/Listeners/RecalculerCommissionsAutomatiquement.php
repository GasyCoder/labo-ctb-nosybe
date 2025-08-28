<?php

namespace App\Listeners;

use App\Models\Paiement;
use Illuminate\Support\Facades\Log;
use App\Jobs\RecalculerCommissionsJob;
use App\Events\CommissionPourcentageChanged;

class RecalculerCommissionsAutomatiquement
{
    public function handle(CommissionPourcentageChanged $event)
    {
        Log::info("🚀 Déclenchement du recalcul automatique des commissions", [
            'ancien_pourcentage' => $event->ancienPourcentage,
            'nouveau_pourcentage' => $event->nouveauPourcentage
        ]);

        // Option 1: Exécution immédiate (synchrone)
        $this->recalculerImmediatement($event);

        // Option 2: Exécution en arrière-plan (asynchrone) - RECOMMANDÉ
        // RecalculerCommissionsJob::dispatch($event->ancienPourcentage, $event->nouveauPourcentage);
        
        Log::info("✅ Job de recalcul des commissions mis en queue");
    }

    // Méthode alternative pour exécution immédiate
    private function recalculerImmediatement(CommissionPourcentageChanged $event)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            $paiements = Paiement::with('prescription.prescripteur')->get();
            $paiementsModifies = 0;
            
            foreach ($paiements as $paiement) {
                $ancienneCommission = $paiement->commission_prescripteur;
                $nouvelleCommission = 0;
                
                if ($paiement->prescription && $paiement->prescription->prescripteur) {
                    $prescripteur = $paiement->prescription->prescripteur;
                    
                    if ($prescripteur->status === 'BiologieSolidaire') {
                        $nouvelleCommission = 0;
                    } else {
                        $nouvelleCommission = $paiement->montant * ($event->nouveauPourcentage / 100);
                    }
                }
                
                if (round($ancienneCommission, 2) != round($nouvelleCommission, 2)) {
                    $paiementsModifies++;
                    $paiement->commission_prescripteur = $nouvelleCommission;
                    $paiement->save();
                }
            }
            
            \Illuminate\Support\Facades\DB::commit();
            
            Log::info("✅ Recalcul immédiat terminé", [
                'paiements_modifies' => $paiementsModifies
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error("❌ Erreur recalcul immédiat: " . $e->getMessage());
        }
    }
}