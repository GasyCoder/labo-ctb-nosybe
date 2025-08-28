<?php

namespace App\Jobs;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalculerCommissionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ancienPourcentage;
    public $nouveauPourcentage;

    public function __construct($ancienPourcentage, $nouveauPourcentage)
    {
        $this->ancienPourcentage = $ancienPourcentage;
        $this->nouveauPourcentage = $nouveauPourcentage;
    }

    public function handle()
    {
        Log::info("🔄 Job de recalcul des commissions démarré", [
            'ancien_pourcentage' => $this->ancienPourcentage,
            'nouveau_pourcentage' => $this->nouveauPourcentage
        ]);

        try {
            DB::beginTransaction();
            
            $paiements = Paiement::with('prescription.prescripteur')->get();
            $paiementsModifies = 0;
            $ancienTotal = 0;
            $nouveauTotal = 0;
            
            foreach ($paiements as $paiement) {
                $ancienneCommission = $paiement->commission_prescripteur;
                $nouvelleCommission = 0;
                
                if ($paiement->prescription && $paiement->prescription->prescripteur) {
                    $prescripteur = $paiement->prescription->prescripteur;
                    
                    if ($prescripteur->status === 'BiologieSolidaire') {
                        $nouvelleCommission = 0;
                    } else {
                        $nouvelleCommission = $paiement->montant * ($this->nouveauPourcentage / 100);
                    }
                }
                
                if (round($ancienneCommission, 2) != round($nouvelleCommission, 2)) {
                    $paiementsModifies++;
                    $ancienTotal += $ancienneCommission;
                    $nouveauTotal += $nouvelleCommission;
                    
                    $paiement->commission_prescripteur = $nouvelleCommission;
                    $paiement->save();
                }
            }
            
            DB::commit();
            
            Log::info("✅ Job de recalcul terminé avec succès", [
                'paiements_modifies' => $paiementsModifies,
                'ancien_total' => $ancienTotal,
                'nouveau_total' => $nouveauTotal,
                'difference' => $nouveauTotal - $ancienTotal
            ]);
            
            // Optionnel: Notifier l'utilisateur (mail, notification, etc.)
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("❌ Erreur dans le Job de recalcul", [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            throw $e; // Pour déclencher un retry automatique
        }
    }
}