{{-- Vue principale resources/views/livewire/secretaire/add-prescription.blade.php --}}
<div class="container mx-auto px-4 py-6">
    <div class="mb-4">
        <a href="{{ route('secretaire.prescriptions') }}"
           wire:navigate
           class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">
            <em class="ni ni-arrow-left mr-2"></em> Retour à la liste
        </a>
    </div>
    {{-- 🎯 HEADER WORKFLOW LABORATOIRE --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 mb-6 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-heading font-bold text-slate-800 dark:text-slate-100">
                    <em class="ni ni-dashlite text-primary-600"></em>
                    Nouvelle Prescription
                </h1>
                @if($patient)
                    <p class="text-slate-600 dark:text-slate-300 mt-1">
                        Patient: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $patient->nom }} {{ $patient->prenom }}</span>
                        <span class="text-slate-500 dark:text-slate-400">({{ $patient->reference }})</span>
                    </p>
                @endif
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <div class="text-sm text-slate-500 dark:text-slate-400">{{ now()->format('d/m/Y H:i') }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500">Secrétaire: {{ Auth::user()->name }}</div>
                </div>
            </div>
        </div>
        
        {{-- INDICATEUR PROGRESSION --}}
        <div class="flex items-center justify-between">
            @php
                $etapes = [
                    'patient' => ['icon' => 'user', 'label' => 'Patient', 'color' => 'primary'],
                    'clinique' => ['icon' => 'list-round', 'label' => 'Clinique', 'color' => 'cyan'],
                    'analyses' => ['icon' => 'filter', 'label' => 'Analyses', 'color' => 'green'],
                    'prelevements' => ['icon' => 'package', 'label' => 'Prélèvements', 'color' => 'yellow'],
                    'paiement' => ['icon' => 'coin-alt', 'label' => 'Paiement', 'color' => 'red'],
                    'tubes' => ['icon' => 'capsule', 'label' => 'Tubes', 'color' => 'slate'],
                    'confirmation' => ['icon' => 'check-circle', 'label' => 'Confirmé', 'color' => 'green']
                ];
                $etapeActuelleIndex = array_search($etape, array_keys($etapes));
            @endphp
            
            @foreach($etapes as $key => $config)
                @php
                    $index = array_search($key, array_keys($etapes));
                    $isActive = $etape === $key;
                    $isCompleted = $index < $etapeActuelleIndex;
                    $isAccessible = $index <= $etapeActuelleIndex;
                @endphp
                
                <div class="flex flex-col items-center">
                    <button wire:click="allerEtape('{{ $key }}')" 
                            {{ !$isAccessible ? 'disabled' : '' }}
                            class="w-12 h-12 rounded-full flex items-center justify-center mb-2 transition-all
                                {{ $isActive ? 'bg-'.$config['color'].'-600 text-white shadow-lg' : 
                                   ($isCompleted ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500') }}
                                {{ $isAccessible ? 'hover:shadow-md cursor-pointer' : 'cursor-not-allowed' }}">
                        <em class="ni ni-{{ $config['icon'] }} text-lg"></em>
                    </button>
                    <span class="text-xs font-medium text-center
                        {{ $isActive ? 'text-'.$config['color'].'-600' : 'text-slate-500' }}">
                        {{ $config['label'] }}
                    </span>
                </div>
                
                @if(!$loop->last)
                    <div class="flex-1 h-0.5 mx-2 mt-6 
                        {{ $isCompleted ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- ===== ÉTAPE 1: PATIENT ===== --}}
    @include('livewire.secretaire.prescription.partials.patient')

    {{-- ===== ÉTAPE 2: INFORMATIONS CLINIQUES ===== --}}
    @include('livewire.secretaire.prescription.partials.clinique')

    {{-- ===== ÉTAPE 3: SÉLECTION ANALYSES ===== --}}
    @include('livewire.secretaire.prescription.partials.analyses')    

    {{-- ===== ÉTAPE 4: PRÉLÈVEMENTS ===== --}}
    @include('livewire.secretaire.prescription.partials.prelevements')       

    {{-- ===== ÉTAPE 5: PAIEMENT ===== --}}
    @include('livewire.secretaire.prescription.partials.paiement')           

    {{-- ===== ÉTAPE 6: TUBES ET ÉTIQUETTES ===== --}}
    @include('livewire.secretaire.prescription.partials.tubes')              

    {{-- ===== ÉTAPE 7: CONFIRMATION ===== --}}
    @if($etape === 'confirmation')
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border border-green-200 p-8 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <em class="ni ni-check-circle text-3xl text-green-600"></em>
                </div>
                
                <h2 class="text-2xl font-heading font-bold text-green-900 mb-4">
                    Prescription Enregistrée avec Succès !
                </h2>
                
                <div class="text-slate-600 mb-6 space-y-1">
                    <p>Patient: <strong>{{ $patient->nom }} {{ $patient->prenom }}</strong></p>
                    <p>{{ count($analysesPanier) }} analyse(s) • {{ count($tubesGeneres) }} tube(s)</p>
                    <p>Montant payé: <strong class="text-green-600">{{ number_format($total, 0) }} Ar</strong></p>
                </div>
                
                <div class="flex justify-center space-x-4">
                    <button wire:click="nouveauPrescription" 
                            class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        <em class="ni ni-plus mr-2"></em>Nouvelle prescription
                    </button>
                    <button class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        <em class="ni ni-printer mr-2"></em>Imprimer reçu
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
