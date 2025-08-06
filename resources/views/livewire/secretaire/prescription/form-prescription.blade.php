{{-- resources/views/livewire/secretaire/prescription/form-prescription.blade.php --}}
<div class="px-3 py-3">
    {{-- NAVIGATION RETOUR --}}
    <div class="mb-3">
        <a href="{{ route('secretaire.prescription.index') }}"
           wire:navigate
           class="inline-flex items-center px-3 py-1.5 bg-gray-50 dark:bg-slate-700 text-gray-600 dark:text-slate-300 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors text-sm">
            <em class="ni ni-arrow-left mr-1.5 text-xs"></em> Retour à la liste
        </a>
    </div>

    {{-- 🎯 HEADER WORKFLOW LABORATOIRE --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 mb-4 p-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-base font-semibold text-slate-800 dark:text-slate-100 flex items-center">
                    @if($isEditMode)
                        <em class="ni ni-edit text-orange-500 mr-2 text-sm"></em>
                        Modifier Prescription
                    @else
                        <em class="ni ni-dashlite text-primary-500 mr-2 text-sm"></em>
                        Nouvelle Prescription
                    @endif
                </h1>
                
                @if($patient)
                    <p class="text-slate-500 dark:text-slate-400 mt-1 text-xs">
                        Patient: <span class="font-medium text-slate-700 dark:text-slate-200">{{ $patient->nom }} {{ $patient->prenom }}</span>
                        <span class="text-slate-400 dark:text-slate-500">({{ $patient->reference }})</span>
                    </p>
                @endif
            </div>
            
            <div class="flex items-center space-x-2">
                <div class="text-right">
                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ now()->format('d/m/Y H:i') }}</div>
                    <div class="text-xxs text-slate-400 dark:text-slate-500">
                        {{ $isEditMode ? 'Modifié' : 'Créé' }} par: {{ Auth::user()->name }}
                    </div>
                </div>
                
                {{-- BOUTON RESET (seulement en mode création) --}}
                @if(!$isEditMode)
                    <button wire:click="nouveauPrescription" 
                            wire:confirm="Êtes-vous sûr de vouloir recommencer ? Toutes les données seront perdues."
                            class="px-2.5 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors text-xs">
                        <em class="ni ni-refresh mr-1 text-xs"></em>
                        Reset
                    </button>
                @endif
            </div>
        </div>
        
        {{-- INDICATEUR PROGRESSION ADAPTATIF --}}
        <div class="relative">
            @php
                $etapes = [
                    'patient' => [
                        'icon' => 'user', 
                        'label' => 'Patient', 
                        'color' => $isEditMode ? 'orange' : 'primary', 
                        'description' => $isEditMode ? 'Modification patient' : 'Sélection du patient'
                    ],
                    'clinique' => [
                        'icon' => 'list-round', 
                        'label' => 'Clinique', 
                        'color' => 'cyan', 
                        'description' => $isEditMode ? 'Modification infos médicales' : 'Informations médicales'
                    ],
                    'analyses' => [
                        'icon' => 'filter', 
                        'label' => 'Analyses', 
                        'color' => 'green', 
                        'description' => $isEditMode ? 'Modification tests' : 'Sélection des tests'
                    ],
                    'prelevements' => [
                        'icon' => 'package', 
                        'label' => 'Prélèvements', 
                        'color' => 'yellow', 
                        'description' => $isEditMode ? 'Modification échantillons' : 'Échantillons requis'
                    ],
                    'paiement' => [
                        'icon' => 'coin-alt', 
                        'label' => 'Paiement', 
                        'color' => 'red', 
                        'description' => $isEditMode ? 'Modification facturation' : 'Facturation'
                    ],
                    'tubes' => [
                        'icon' => 'capsule', 
                        'label' => 'Tubes', 
                        'color' => 'slate', 
                        'description' => $isEditMode ? 'Régénération codes-barres' : 'Génération codes-barres'
                    ],
                    'confirmation' => [
                        'icon' => 'check-circle', 
                        'label' => $isEditMode ? 'Modifié' : 'Confirmé', 
                        'color' => 'green', 
                        'description' => $isEditMode ? 'Modification terminée' : 'Prescription terminée'
                    ]
                ];
                $etapeActuelleIndex = array_search($etape, array_keys($etapes));
                $progressColor = $isEditMode ? 'from-orange-400 to-green-400' : 'from-primary-400 to-green-400';
            @endphp
                    
            {{-- BARRE DE PROGRESSION --}}
            <div class="absolute top-4 left-4 right-4 h-0.5 bg-gray-100 dark:bg-slate-600 z-0">
                <div class="h-full bg-gradient-to-r {{ $progressColor }} transition-all duration-300" 
                    style="width: {{ ($etapeActuelleIndex / (count($etapes) - 1)) * 100 }}%"></div>
            </div>
            
            {{-- ÉTAPES NAVIGATION --}}
            <div class="flex items-center justify-between relative z-10">
                @foreach($etapes as $key => $config)
                    @php
                        $index = array_search($key, array_keys($etapes));
                        $isActive = $etape === $key;
                        $isCompleted = $index < $etapeActuelleIndex;
                        $isAccessible = $index <= $etapeActuelleIndex;
                        
                        // Validation spécifique pour chaque étape
                        $canAccess = true;
                        switch($key) {
                            case 'clinique':
                                $canAccess = $patient !== null;
                                break;
                            case 'analyses':
                                $canAccess = $patient !== null && $prescripteurId !== null;
                                break;
                            case 'prelevements':
                                $canAccess = !empty($analysesPanier);
                                break;
                            case 'paiement':
                                $canAccess = !empty($analysesPanier);
                                break;
                            case 'tubes':
                                $canAccess = $total > 0;
                                break;
                            case 'confirmation':
                                $canAccess = $isEditMode ? true : (!empty($tubesGeneres) || $etape === 'confirmation');
                                break;
                        }
                        
                        $finalAccessible = $canAccess && $isAccessible;
                        
                        // Classes dynamiques selon le mode
                        $activeClass = $isEditMode ? 'bg-orange-500' : 'bg-primary-500';
                        $labelActiveClass = $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-primary-600 dark:text-primary-400';
                    @endphp
                    
                    <div class="flex flex-col items-center group" 
                        x-data="{ showTooltip: false }">
                        {{-- BOUTON ÉTAPE --}}
                        <button wire:click="allerEtape('{{ $key }}')" 
                                {{ !$finalAccessible ? 'disabled' : '' }}
                                @mouseenter="showTooltip = true"
                                @mouseleave="showTooltip = false"
                                class="relative w-8 h-8 rounded-full flex items-center justify-center mb-1.5 transition-all duration-200 transform
                                    @if($isActive)
                                        {{ $activeClass }} text-white shadow-md scale-110
                                    @elseif($isCompleted)
                                        bg-green-500 text-white shadow-sm
                                    @elseif($finalAccessible)
                                        bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600 hover:scale-105 cursor-pointer
                                    @else
                                        bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-slate-600 cursor-not-allowed opacity-50
                                    @endif">
                            
                            @if($isCompleted)
                                <em class="ni ni-check text-xs"></em>
                            @else
                                <em class="ni ni-{{ $config['icon'] }} text-xs"></em>
                            @endif
                            
                            {{-- BADGE NOMBRE --}}
                            @if($key === 'analyses' && !empty($analysesPanier) && !$isActive)
                                <span class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-red-500 text-white text-xxs rounded-full flex items-center justify-center">
                                    {{ count($analysesPanier) }}
                                </span>
                            @elseif($key === 'prelevements' && !empty($prelevementsSelectionnes) && !$isActive)
                                <span class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-red-500 text-white text-xxs rounded-full flex items-center justify-center">
                                    {{ count($prelevementsSelectionnes) }}
                                </span>
                            @endif
                        </button>
                        
                        {{-- LABEL ÉTAPE --}}
                        <div class="text-center">
                            <span class="text-xxs font-medium block
                                @if($isActive)
                                    {{ $labelActiveClass }}
                                @elseif($isCompleted)
                                    text-green-600 dark:text-green-400
                                @else
                                    text-slate-500 dark:text-slate-400
                                @endif">
                                {{ $config['label'] }}
                            </span>
                        </div>
                        
                        {{-- TOOLTIP --}}
                        <div x-show="showTooltip && !{{ $finalAccessible ? 'true' : 'false' }}"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute top-12 left-1/2 transform -translate-x-1/2 bg-red-500 text-white text-xxs px-2 py-1 rounded shadow-lg whitespace-nowrap z-20">
                            @switch($key)
                                @case('clinique')
                                    Sélectionnez d'abord un patient
                                    @break
                                @case('analyses')
                                    Complétez les informations cliniques
                                    @break
                                @case('prelevements')
                                    Sélectionnez au moins une analyse
                                    @break
                                @case('paiement')
                                    Sélectionnez au moins une analyse
                                    @break
                                @case('tubes')
                                    Validez le paiement
                                    @break
                                @case('confirmation')
                                    Terminez le processus
                                    @break
                            @endswitch
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- INFORMATIONS ÉTAPE ACTUELLE --}}
            <div class="mt-3 text-center">
                <div class="inline-flex items-center px-3 py-1.5 
                            {{ $isEditMode ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800' : 'bg-primary-50 dark:bg-primary-900/20 border-primary-200 dark:border-primary-800' }} 
                            border rounded-lg">
                    <em class="ni ni-{{ $etapes[$etape]['icon'] }} {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-primary-600 dark:text-primary-400' }} mr-1.5 text-xs"></em>
                    <span class="text-xs font-medium {{ $isEditMode ? 'text-orange-800 dark:text-orange-200' : 'text-primary-800 dark:text-primary-200' }}">
                        Étape {{ $etapeActuelleIndex + 1 }}/{{ count($etapes) }} : {{ $etapes[$etape]['description'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ÉTAPES CONTENUS (RÉUTILISABLES) ===== --}}
    
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
        <div class="mx-auto">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border {{ $isEditMode ? 'border-orange-200 dark:border-orange-800' : 'border-green-200 dark:border-green-800' }} p-5 text-center">
                <div class="w-12 h-12 {{ $isEditMode ? 'bg-orange-50 dark:bg-orange-900/20' : 'bg-green-50 dark:bg-green-900/20' }} rounded-full flex items-center justify-center mx-auto mb-3">
                    <em class="ni ni-{{ $isEditMode ? 'edit' : 'check-circle' }} text-xl {{ $isEditMode ? 'text-orange-500 dark:text-orange-400' : 'text-green-500 dark:text-green-400' }}"></em>
                </div>
                
                <h2 class="text-lg font-semibold {{ $isEditMode ? 'text-orange-900 dark:text-orange-100' : 'text-green-900 dark:text-green-100' }} mb-3">
                    @if($isEditMode)
                        ✏️ Prescription Modifiée avec Succès !
                    @else
                        🎉 Prescription Enregistrée avec Succès !
                    @endif
                </h2>
                
             <div class="text-slate-600 dark:text-slate-300 mb-4 space-y-0.75 text-sm">
                @if($isEditMode && isset($prescription))
                    <p><span class="font-medium">Prescription ID:</span> #{{ $prescription->id }}</p>
                @endif
                
                <p>
                    <span class="font-medium">Patient:</span> 
                    @isset($patient)
                        {{ $patient->nom }} {{ $patient->prenom }}
                        @if(!empty($patient->date_naissance))
                            ({{ \Carbon\Carbon::parse($patient->date_naissance)->age }} ans)
                        @endif
                    @else
                        <span class="text-red-500 dark:text-red-400">Veuillez sélectionner un patient</span>
                    @endisset
                </p>

                <p><span class="font-medium">Analyses:</span> {{ count($analysesPanier) }} sélectionnée(s)</p>
                
                @if(!empty($prelevementsSelectionnes))
                    <p><span class="font-medium">Prélèvements:</span> {{ count($prelevementsSelectionnes) }}</p>
                @endif
                
                @if(!empty($tubesGeneres))
                    <p>
                        <span class="font-medium">Tubes {{ $isEditMode ? 'régénérés' : 'générés' }}:</span> 
                        {{ count($tubesGeneres) }}
                    </p>
                @endif
                
                <p>
                    <span class="font-medium">Montant {{ $isEditMode ? 'total' : 'payé' }}:</span> 
                    <span class="{{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }} font-semibold">
                        {{ number_format($total, 0) }} Ar
                    </span>
                </p>
            </div>
                
                {{-- ACTIONS FINALES --}}
                <div class="flex flex-col sm:flex-row justify-center gap-2">
                    <button wire:click="nouveauPrescription" 
                            class="px-3 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg font-medium transition-colors text-sm">
                        <em class="ni ni-plus mr-1.5 text-xs"></em>Nouvelle prescription
                    </button>
                    <button class="px-3 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg font-medium transition-colors text-sm">
                        <em class="ni ni-printer mr-1.5 text-xs"></em>Facture patient
                    </button>
                    <a href="{{ route('secretaire.prescription.index') }}" 
                       wire:navigate
                       class="px-3 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg font-medium transition-colors text-sm">
                        <em class="ni ni-list mr-1.5 text-xs"></em>Voir toutes les prescriptions
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- NAVIGATION CLAVIER (Shortcuts) --}}
    <div x-data="{}" 
         @keydown.window="
            if (event.ctrlKey || event.metaKey) {
                switch(event.key) {
                    case '1': event.preventDefault(); $wire.allerEtape('patient'); break;
                    case '2': event.preventDefault(); $wire.allerEtape('clinique'); break;
                    case '3': event.preventDefault(); $wire.allerEtape('analyses'); break;
                    case '4': event.preventDefault(); $wire.allerEtape('prelevements'); break;
                    case '5': event.preventDefault(); $wire.allerEtape('paiement'); break;
                }
            }
         "
         class="hidden">
    </div>
</div>

{{-- SCRIPT POUR AMÉLIORER LA NAVIGATION --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mise à jour du titre de la page selon l'étape et le mode
    const etapesCreation = {
        'patient': 'Sélection Patient',
        'clinique': 'Informations Cliniques', 
        'analyses': 'Sélection Analyses',
        'prelevements': 'Prélèvements',
        'paiement': 'Paiement',
        'tubes': 'Génération Tubes',
        'confirmation': 'Confirmation'
    };
    
    const etapesEdition = {
        'patient': 'Modification Patient',
        'clinique': 'Modification Clinique', 
        'analyses': 'Modification Analyses',
        'prelevements': 'Modification Prélèvements',
        'paiement': 'Modification Paiement',
        'tubes': 'Régénération Tubes',
        'confirmation': 'Modification terminée'
    };
    
    // Observer les changements d'étape via Livewire
    Livewire.on('navigateToStep', (step) => {
        const isEditMode = @json($isEditMode ?? false);
        const etapes = isEditMode ? etapesEdition : etapesCreation;
        const prefix = isEditMode ? 'Modification' : 'Prescription';
        document.title = `${etapes[step] || prefix} - Laboratoire`;
    });
    
    // Smooth scroll vers le contenu quand on change d'étape
    window.addEventListener('livewire:navigated', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>
@endpush