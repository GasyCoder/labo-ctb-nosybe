<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function show(Prescription $prescription)
    {
        // Charger les relations nécessaires
        $prescription->load(['patient', 'analyses', 'prelevements', 'tubes', 'prescripteur', 'paiements.paymentMethod']);
        
        return view('livewire.secretaire.prescription.facture-impression', [
            'prescription' => $prescription,
            'patient' => $prescription->patient,
            'analysesPanier' => $prescription->analyses->map(function($analyse) {
                return [
                    'id' => $analyse->id,
                    'designation' => $analyse->designation,
                    'code' => $analyse->code,
                    'prix_effectif' => $analyse->pivot->prix ?? $analyse->prix,
                    'parent_nom' => $analyse->parent?->designation ?? 'Analyse individuelle',
                    'is_parent' => $analyse->level === 'PARENT',
                ];
            })->toArray(),
            'prelevementsSelectionnes' => $prescription->prelevements->map(function($prelevement) {
                return [
                    'id' => $prelevement->id,
                    'nom' => $prelevement->nom,
                    'description' => $prelevement->description,
                    'prix' => $prelevement->pivot->prix_unitaire ?? $prelevement->prix,
                    'quantite' => $prelevement->pivot->quantite ?? 1,
                    'type_tube_requis' => $prelevement->pivot->type_tube_requis ?? 'SEC',
                ];
            })->toArray(),
            'tubesGeneres' => $prescription->tubes->map(function($tube) {
                return [
                    'numero_tube' => $tube->numero_tube,
                    'code_barre' => $tube->code_barre,
                ];
            })->toArray(),
            'total' => $this->calculerTotal($prescription),
            'remise' => $prescription->remise ?? 0,
            'reference' => $prescription->reference,
            'prescripteurId' => $prescription->prescripteur_id,
            'age' => $prescription->age,
            'uniteAge' => $prescription->unite_age ?? 'ans',
            'poids' => $prescription->poids,
            'renseignementClinique' => $prescription->renseignement_clinique,
            'patientType' => $prescription->patient_type,
            'modePaiement' => $prescription->paiements->first()?->paymentMethod->code ?? 'ESPECES',
        ]);
    }
    
    private function calculerTotal(Prescription $prescription)
    {
        $totalAnalyses = $prescription->analyses->sum(function($analyse) {
            return $analyse->pivot->prix ?? $analyse->prix;
        });
        
        $totalPrelevements = $prescription->prelevements->sum(function($prelevement) {
            return ($prelevement->pivot->prix_unitaire ?? $prelevement->prix) * 
                   ($prelevement->pivot->quantite ?? 1);
        });
        
        return $totalAnalyses + $totalPrelevements - ($prescription->remise ?? 0);
    }
}