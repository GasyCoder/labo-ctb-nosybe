<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Paiement;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JournalCaisse extends Component
{
    public $dateDebut;
    public $dateFin;
    
    public function mount()
    {
        // Initialiser avec une plage plus large pour voir tous les paiements
        $this->dateDebut = Carbon::today()->subDays(7)->format('Y-m-d'); // 7 jours avant
        $this->dateFin = Carbon::today()->format('Y-m-d'); // aujourd'hui
    }
    
    public function render()
    {
        $paiements = $this->getPaiements();
        $totauxParMethode = $this->getTotauxParMethode($paiements);
        $totalGeneral = $paiements->sum('montant');
        
        return view('livewire.journal-caisse', [
            'paiements' => $paiements,
            'totauxParMethode' => $totauxParMethode,
            'totalGeneral' => $totalGeneral
        ]);
    }
    
    private function getPaiements()
    {
        return Paiement::with([
            'prescription.patient',
            'paymentMethod',
            'utilisateur'
        ])
        ->whereBetween('created_at', [
            Carbon::parse($this->dateDebut)->startOfDay(),
            Carbon::parse($this->dateFin)->endOfDay()
        ])
        ->payés() // Seulement les paiements avec status = 1
        ->orderBy('created_at')
        ->orderBy('payment_method_id')
        ->get();
    }
    
    // 🔧 Méthode de débogage - à supprimer après test
    public function debug()
    {
        $allPaiements = Paiement::with(['prescription.patient', 'paymentMethod'])
            ->whereBetween('created_at', [
                Carbon::parse($this->dateDebut)->startOfDay(),
                Carbon::parse($this->dateFin)->endOfDay()
            ])
            ->get();
            
        dd([
            'dateDebut' => $this->dateDebut,
            'dateFin' => $this->dateFin,
            'total_paiements_periode' => $allPaiements->count(),
            'paiements_payes' => $allPaiements->where('status', true)->count(),
            'paiements_non_payes' => $allPaiements->where('status', false)->count(),
            'details' => $allPaiements->toArray()
        ]);
    }
    
    private function getTotauxParMethode($paiements)
    {
        return $paiements->groupBy('paymentMethod.label')->map(function ($group) {
            return [
                'total' => $group->sum('montant'),
                'count' => $group->count()
            ];
        });
    }
    
    public function exportPdf()
    {
        $paiements = $this->getPaiements();
        $totauxParMethode = $this->getTotauxParMethode($paiements);
        $totalGeneral = $paiements->sum('montant');
        
        $pdf = Pdf::loadView('factures.journal-caisse', [
            'paiements' => $paiements,
            'totauxParMethode' => $totauxParMethode,
            'totalGeneral' => $totalGeneral,
            'dateDebut' => $this->dateDebut,
            'dateFin' => $this->dateFin
        ]);
        
        $filename = 'journal-caisse-' . $this->dateDebut . '-' . $this->dateFin . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}