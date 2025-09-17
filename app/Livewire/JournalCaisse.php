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
    public $totalGeneral; // Total de tous les paiements (sans filtre)

    public function mount()
    {
        // Initialiser avec une plage par défaut
        $this->dateDebut = Carbon::today()->subDays(7)->format('Y-m-d'); // 7 jours avant
        $this->dateFin = Carbon::today()->format('Y-m-d'); // aujourd'hui

        // Calculer le total général de tous les paiements (sans filtre)
        $this->totalGeneral = Paiement::sum('montant');
    }

    public function updated($propertyName)
    {
        // Recharger les données lorsque dateDebut ou dateFin change
        $this->render();
    }

    public function render()
    {
        $paiements = $this->getPaiements();
        $totauxParMethode = $this->getTotauxParMethode($paiements);
        $totalSemaine = $this->getTotalSemaine();

        return view('livewire.journal-caisse', [
            'paiements' => $paiements,
            'totauxParMethode' => $totauxParMethode,
            'totalGeneral' => $this->totalGeneral,
            'totalSemaine' => $totalSemaine['total'],
            'evolutionSemaine' => $totalSemaine['evolution'],
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

    private function getTotalSemaine()
    {
        // Calculer la période de la semaine pour la période filtrée
        $debutPeriode = Carbon::parse($this->dateDebut)->startOfDay();
        $finPeriode = Carbon::parse($this->dateFin)->endOfDay();

        // Calculer la semaine précédente
        $debutSemainePrecedente = $debutPeriode->copy()->subWeek()->startOfDay();
        $finSemainePrecedente = $finPeriode->copy()->subWeek()->endOfDay();

        // Total de la période filtrée
        $totalSemaine = Paiement::payés()
            ->whereBetween('created_at', [$debutPeriode, $finPeriode])
            ->sum('montant');

        // Total de la semaine précédente
        $totalSemainePrecedente = Paiement::payés()
            ->whereBetween('created_at', [$debutSemainePrecedente, $finSemainePrecedente])
            ->sum('montant');

        // Calculer l'évolution
        $evolution = $totalSemainePrecedente > 0
            ? (($totalSemaine - $totalSemainePrecedente) / $totalSemainePrecedente) * 100
            : 0;

        return [
            'total' => $totalSemaine,
            'evolution' => $evolution,
        ];
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

        $totalSemaine = $this->getTotalSemaine();

        dd([
            'dateDebut' => $this->dateDebut,
            'dateFin' => $this->dateFin,
            'total_paiements_periode' => $allPaiements->count(),
            'paiements_payes' => $allPaiements->where('status', true)->count(),
            'paiements_non_payes' => $allPaiements->where('status', false)->count(),
            'totalGeneral_sans_filtre' => $this->totalGeneral,
            'totalSemaine' => $totalSemaine['total'],
            'evolutionSemaine' => $totalSemaine['evolution'],
            'details' => $allPaiements->toArray(),
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
        $totalSemaine = $this->getTotalSemaine();

        $pdf = Pdf::loadView('factures.journal-caisse', [
            'paiements' => $paiements,
            'totauxParMethode' => $totauxParMethode,
            'totalGeneral' => $this->totalGeneral,
            'totalSemaine' => $totalSemaine['total'],
            'evolutionSemaine' => $totalSemaine['evolution'],
            'dateDebut' => $this->dateDebut,
            'dateFin' => $this->dateFin
        ]);

        $filename = 'journal-caisse-' . $this->dateDebut . '-' . $this->dateFin . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}