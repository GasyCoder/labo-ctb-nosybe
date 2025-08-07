<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Paiement;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class RecalculerCommissions extends Command
{
    protected $signature = 'commissions:recalculer 
                            {--confirm : Confirmer le recalcul sans demander}
                            {--dry-run : Simuler sans sauvegarder}';

    protected $description = 'Recalculer toutes les commissions avec le nouveau pourcentage des settings';

    public function handle()
    {
        $this->info('🔄 Démarrage du recalcul des commissions...');
        $this->newLine();
        
        $nouveauPourcentage = Setting::getCommissionPourcentage();
        
        $this->info("📊 Nouveau pourcentage: {$nouveauPourcentage}%");
        
        // Récupérer tous les paiements avec prescripteurs
        $paiements = Paiement::with('prescription.prescripteur')->get();
        
        if ($paiements->isEmpty()) {
            $this->info('❌ Aucun paiement trouvé.');
            return 0;
        }
        
        $totalPaiements = $paiements->count();
        $paiementsModifies = 0;
        $anciensTotal = 0;
        $nouveauxTotal = 0;
        $biologieSolidaire = 0;
        
        $this->info("📝 Analyse de {$totalPaiements} paiements...");
        $this->newLine();
        
        // Mode simulation
        if ($this->option('dry-run')) {
            $this->warn('🔍 MODE SIMULATION (aucune modification)');
            $this->newLine();
        }
        
        // Demander confirmation si pas en dry-run et pas --confirm
        if (!$this->option('dry-run') && !$this->option('confirm')) {
            if (!$this->confirm('⚠️  Voulez-vous vraiment recalculer TOUTES les commissions?')) {
                $this->info('❌ Opération annulée.');
                return 0;
            }
            $this->newLine();
        }
        
        $progressBar = $this->output->createProgressBar($totalPaiements);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');
        $progressBar->setMessage('Analyse en cours...');
        $progressBar->start();
        
        DB::beginTransaction();
        
        try {
            foreach ($paiements as $paiement) {
                $ancienneCommission = $paiement->commission_prescripteur;
                $nouvelleCommission = 0;
                
                // Calculer nouvelle commission
                if ($paiement->prescription && $paiement->prescription->prescripteur) {
                    $prescripteur = $paiement->prescription->prescripteur;
                    
                    // Si BiologieSolidaire, commission = 0
                    if ($prescripteur->status === 'BiologieSolidaire') {
                        $nouvelleCommission = 0;
                        $biologieSolidaire++;
                        $progressBar->setMessage("BiologieSolidaire: {$prescripteur->nom}");
                    } else {
                        // Sinon, appliquer le nouveau pourcentage
                        $nouvelleCommission = $paiement->montant * ($nouveauPourcentage / 100);
                        $progressBar->setMessage("Médecin: {$prescripteur->nom}");
                    }
                }
                
                // Si commission différente
                if (round($ancienneCommission, 2) != round($nouvelleCommission, 2)) {
                    $paiementsModifies++;
                    $anciensTotal += $ancienneCommission;
                    $nouveauxTotal += $nouvelleCommission;
                    
                    // Sauvegarder seulement si pas en dry-run
                    if (!$this->option('dry-run')) {
                        $paiement->commission_prescripteur = $nouvelleCommission;
                        $paiement->save();
                    }
                }
                
                $progressBar->advance();
            }
            
            if (!$this->option('dry-run')) {
                DB::commit();
            } else {
                DB::rollBack();
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $progressBar->finish();
            $this->newLine(2);
            $this->error("❌ Erreur: " . $e->getMessage());
            return 1;
        }
        
        $progressBar->setMessage('Terminé!');
        $progressBar->finish();
        $this->newLine(2);
        
        // Résultats
        $this->info("✅ RÉSULTATS");
        $this->line("────────────────────────────────────────");
        $this->info("📊 Paiements analysés: {$totalPaiements}");
        $this->info("🔄 Paiements modifiés: {$paiementsModifies}");
        $this->info("🧪 BiologieSolidaire (0%): {$biologieSolidaire}");
        $this->newLine();
        
        $this->info("💰 IMPACT FINANCIER");
        $this->line("────────────────────────────────────────");
        $this->info("Ancien total: " . number_format($anciensTotal, 0, ',', ' ') . " Ar");
        $this->info("Nouveau total: " . number_format($nouveauxTotal, 0, ',', ' ') . " Ar");
        
        $difference = $nouveauxTotal - $anciensTotal;
        if ($difference > 0) {
            $this->error("📈 Augmentation: +" . number_format($difference, 0, ',', ' ') . " Ar");
        } elseif ($difference < 0) {
            $this->info("📉 Économie: " . number_format(abs($difference), 0, ',', ' ') . " Ar");
        } else {
            $this->info("➡️  Aucun changement financier");
        }
        
        $this->newLine();
        
        if ($this->option('dry-run')) {
            $this->warn('🔍 SIMULATION - Aucune modification sauvegardée');
            $this->info('Pour appliquer: php artisan commissions:recalculer --confirm');
        } else {
            $this->info('🎉 Recalcul terminé avec succès!');
        }
        
        return 0;
    }
}