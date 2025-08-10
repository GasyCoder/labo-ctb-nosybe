<?php

namespace App\Livewire;

use App\Models\Prescription;
use App\Models\Analyse;
use App\Models\Resultat;
use App\Models\BacterieFamille;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ResultForm extends Component
{
    public $prescriptionId;
    public $analyseId;

    public Prescription $prescription;
    public Analyse $analyse;
    public ?Resultat $resultat = null;

    // Champs génériques
    public $resultats = '';
    public $valeur = '';
    public $interpretation = 'NORMAL';
    public $conclusion = '';

    // Leuco
    public $polynucleaires = '';
    public $lymphocytes = '';

    // Bactério
    public $famille_id = null;
    public $bacterie_id = null;
    public $familles = [];
    public $bacteries = [];

    protected $rules = [
        'resultats'      => 'nullable|string|max:1000',
        'valeur'         => 'nullable|string|max:500',
        'interpretation' => 'required|in:NORMAL,PATHOLOGIQUE',
        'conclusion'     => 'nullable|string|max:2000',
        'famille_id'     => 'nullable|exists:bacterie_familles,id',
        'bacterie_id'    => 'nullable|exists:bacteries,id',
    ];

    public function mount($prescriptionId, $analyseId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->analyseId = $analyseId;

        // Eager loading
        $this->prescription = Prescription::with(['tubes'])->findOrFail($prescriptionId);

        $this->analyse = Analyse::with([
            'type','examen',
            'enfantsRecursive.type',
            'enfantsRecursive.examen',
        ])->findOrFail($analyseId);

        // Résultat existant
        $this->resultat = Resultat::where([
            'prescription_id' => $prescriptionId,
            'analyse_id'      => $analyseId,
        ])->first();

        if ($this->resultat) {
            $this->resultats      = $this->resultat->resultats ?? '';
            $this->valeur         = $this->resultat->valeur ?? '';
            $this->interpretation = $this->resultat->interpretation ?? 'NORMAL';
            $this->conclusion     = $this->resultat->conclusion ?? '';
            $this->famille_id     = $this->resultat->famille_id;
            $this->bacterie_id    = $this->resultat->bacterie_id;

            // si LEUCOCYTES stockés en JSON
            if (($this->analyse->type->name ?? null) === 'LEUCOCYTES' && $this->valeur) {
                $json = json_decode($this->valeur, true);
                if (is_array($json)) {
                    $this->polynucleaires = $json['polynucleaires'] ?? '';
                    $this->lymphocytes    = $json['lymphocytes'] ?? '';
                }
            }
        }

        // Cache familles si CULTURE/GERME
        $typeName = $this->analyse->type->name ?? null;
        if (in_array($typeName, ['CULTURE','GERME'])) {
            $this->familles = cache()->remember('bacterie_familles_actives', 3600, function() {
                return BacterieFamille::actives()->with('bacteries')->get();
            });
            if ($this->famille_id) {
                $famille = $this->familles->find($this->famille_id);
                $this->bacteries = $famille ? $famille->bacteries : collect();
            }
        }
    }

    public function updatedFamilleId()
    {
        $this->bacterie_id = null;
        if ($this->famille_id) {
            $famille = collect($this->familles)->firstWhere('id', (int)$this->famille_id);
            $this->bacteries = $famille ? $famille->bacteries : collect();
        } else {
            $this->bacteries = collect();
        }
    }

    public function save()
    {
        try {
            $this->validate();

            $this->validateByType();

            // Prépare valeur pour types spéciaux
            $typeName = $this->analyse->type->name ?? null;
            $valeurAEnregistrer = $this->valeur;

            if ($typeName === 'LEUCOCYTES') {
                $valeurAEnregistrer = json_encode([
                    'polynucleaires' => $this->polynucleaires,
                    'lymphocytes'    => $this->lymphocytes,
                ]);
            }

            $tubeId = $this->prescription->tubes()->first()?->id;

            $data = [
                'prescription_id' => $this->prescriptionId,
                'analyse_id'      => $this->analyseId,
                'resultats'       => $this->resultats ?: null,
                'valeur'          => $valeurAEnregistrer ?: null,
                'tube_id'         => $tubeId,
                'interpretation'  => $this->interpretation,
                'conclusion'      => $this->conclusion ?: null,
                'status'          => $this->resultat ? $this->resultat->status : 'TERMINE',
                // bacterio
                'famille_id'      => $this->famille_id ?: null,
                'bacterie_id'     => $this->bacterie_id ?: null,
            ];

            \DB::transaction(function() use ($data) {
                $this->resultat = Resultat::updateOrCreate(
                    [
                        'prescription_id' => $this->prescriptionId,
                        'analyse_id'      => $this->analyseId,
                    ],
                    $data
                );
                $this->updatePrescriptionStatus();
            });

            Log::info('Résultat saisi', [
                'prescription_id' => $this->prescriptionId,
                'analyse_id'      => $this->analyseId,
                'user_id'         => Auth::id()
            ]);

            session()->flash('message', 'Résultat enregistré avec succès.');
            $this->dispatch('refreshSidebar');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur sauvegarde résultat', [
                'prescription_id' => $this->prescriptionId,
                'analyse_id'      => $this->analyseId,
                'error'           => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de l\'enregistrement. Veuillez réessayer.');
        }
    }

    public function markIncomplete()
    {
        if ($this->resultat) {
            $this->resultat->delete();
            $this->resultat = null;
            $this->reset(['resultats','valeur','conclusion','famille_id','bacterie_id']);
            $this->polynucleaires = '';
            $this->lymphocytes = '';
            $this->interpretation = 'NORMAL';

            session()->flash('message', 'Résultat marqué comme incomplet.');
            $this->dispatch('refreshSidebar');
        }
    }

    public function resetForm()
    {
        $this->reset(['resultats','valeur','conclusion','famille_id','bacterie_id']);
        $this->polynucleaires = '';
        $this->lymphocytes = '';
        $this->interpretation = 'NORMAL';
    }

    private function validateByType()
    {
        $typeName = $this->analyse->type->name ?? null;

        $rules = match($typeName) {
            'INPUT' => [
                // si unité renseignée on force numeric
                'valeur' => $this->analyse->unite ? 'required|numeric' : 'required|string|max:500'
            ],
            'DOSAGE', 'COMPTAGE' => [
                'valeur' => 'required|numeric'
            ],
            'LEUCOCYTES' => [
                'polynucleaires' => 'required|numeric|min:0|max:100',
                'lymphocytes'    => 'required|numeric|min:0|max:100'
            ],
            'SELECT', 'SELECT_MULTIPLE' => [
                'valeur'    => 'required_without:resultats',
                'resultats' => 'required_without:valeur'
            ],
            'NEGATIF_POSITIF_1' => [
                'valeur' => 'required|in:NEGATIF,POSITIF'
            ],
            'NEGATIF_POSITIF_2' => [
                'resultats' => 'required|in:NEGATIF,POSITIF',
                'valeur'    => 'nullable|string|max:500'
            ],
            'ABSENCE_PRESENCE_2' => [
                'valeur' => 'required|in:ABSENCE,PRESENCE'
            ],
            'GERME','CULTURE' => [
                'famille_id'  => 'required_with:bacterie_id|nullable|exists:bacterie_familles,id',
                'bacterie_id' => 'nullable|exists:bacteries,id'
            ],
            default => []
        };

        $this->validate($rules);

        // Alerte métier soft
        if (in_array($typeName, ['INPUT','DOSAGE','COMPTAGE']) && $this->analyse->valeur_ref && is_numeric($this->valeur)) {
            $valeur = (float) $this->valeur;
            if (preg_match('/(\d+\.?\d*)\s*-\s*(\d+\.?\d*)/', $this->analyse->valeur_ref, $m)) {
                $min = (float) $m[1]; $max = (float) $m[2];
                if ($valeur < ($min*0.1) || $valeur > ($max*10)) {
                    session()->flash('warning', 'Valeur très éloignée de la référence. Veuillez vérifier.');
                }
            }
        }
    }

    private function updatePrescriptionStatus()
    {
        if ($this->prescription->status === 'ARCHIVE') return;

        if ($this->prescription->status === 'EN_ATTENTE') {
            $this->prescription->update(['status' => 'EN_COURS']);
        }

        $totalAnalyses = $this->prescription->analyses()->count();
        $analysesAvecResultat = $this->prescription->resultats()->count();

        if ($totalAnalyses > 0 && $totalAnalyses === $analysesAvecResultat && $this->prescription->status === 'EN_COURS') {
            $this->prescription->update(['status' => 'TERMINE']);
        }
    }

    public function render()
    {
        return view('livewire.technicien.result-form');
    }
}
