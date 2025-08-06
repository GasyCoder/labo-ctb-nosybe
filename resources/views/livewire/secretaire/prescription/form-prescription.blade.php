{{-- resources/views/livewire/secretaire/prescription/form-prescription.blade.php --}}
<div class="container mx-auto px-4 py-6">
    {{-- NAVIGATION RETOUR --}}
    <div class="mb-4">
        <a href="{{ route('secretaire.prescriptions') }}"
           wire:navigate
           class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">
            <em class="ni ni-arrow-left mr-2"></em> Retour à la liste
        </a>
    </div>

    {{-- 🎯 HEADER WORKFLOW LABORATOIRE --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 mb-6 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-heading font-bold text-slate-800 dark:text-slate-100">
                    @if($isEditMode)
                        <em class="ni ni-edit text-orange-600 mr-2"></em>
                        Modifier Prescription
                    @else
                        <em class="ni ni-dashlite text-primary-600 mr-2"></em>
                        Nouvelle Prescription
                    @endif
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
                    <div class="text-xs text-slate-400 dark:text-slate-500">
                        {{ $isEditMode ? 'Modifié' : 'Créé' }} par: {{ Auth::user()->name }}
                    </div>
                </div>
                
                {{-- BOUTON RESET (seulement en mode création) --}}
                @if(!$isEditMode)
                    <button wire:click="nouveauPrescription" 
                            wire:confirm="Êtes-vous sûr de vouloir recommencer ? Toutes les données seront perdues."
                            class="px-3 py-2 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition-colors text-sm">
                        <em class="ni ni-refresh mr-1"></em>
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
                $progressColor = $isEditMode ? 'from-orange-500 to-green-500' : 'from-primary-500 to-green-500';
            @endphp
                    
            {{-- BARRE DE PROGRESSION --}}
            <div class="absolute top-6 left-6 right-6 h-0.5 bg-gray-200 dark:bg-slate-600 z-0">
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
                        $activeClass = $isEditMode ? 'bg-orange-600' : 'bg-primary-600';
                        $labelActiveClass = $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-primary-600 dark:text-primary-400';
                    @endphp
                    
                    <div class="flex flex-col items-center group" 
                        x-data="{ showTooltip: false }">
                        {{-- BOUTON ÉTAPE --}}
                        <button wire:click="allerEtape('{{ $key }}')" 
                                {{ !$finalAccessible ? 'disabled' : '' }}
                                @mouseenter="showTooltip = true"
                                @mouseleave="showTooltip = false"
                                class="relative w-12 h-12 rounded-full flex items-center justify-center mb-2 transition-all duration-300 transform
                                    @if($isActive)
                                        {{ $activeClass }} text-white shadow-lg scale-110
                                    @elseif($isCompleted)
                                        bg-green-500 text-white shadow-md
                                    @elseif($finalAccessible)
                                        bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600 hover:shadow-lg hover:scale-105 cursor-pointer
                                    @else
                                        bg-gray-200 dark:bg-slate-800 text-gray-400 dark:text-slate-600 cursor-not-allowed opacity-50
                                    @endif">
                            
                            @if($isCompleted)
                                <em class="ni ni-check text-lg"></em>
                            @else
                                <em class="ni ni-{{ $config['icon'] }} text-lg"></em>
                            @endif
                            
                            {{-- BADGE NOMBRE --}}
                            @if($key === 'analyses' && !empty($analysesPanier) && !$isActive)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-600 text-white text-xs rounded-full flex items-center justify-center">
                                    {{ count($analysesPanier) }}
                                </span>
                            @elseif($key === 'prelevements' && !empty($prelevementsSelectionnes) && !$isActive)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-600 text-white text-xs rounded-full flex items-center justify-center">
                                    {{ count($prelevementsSelectionnes) }}
                                </span>
                            @endif
                        </button>
                        
                        {{-- LABEL ÉTAPE --}}
                        <div class="text-center">
                            <span class="text-xs font-medium block
                                @if($isActive)
                                    {{ $labelActiveClass }}
                                @elseif($isCompleted)
                                    text-green-600 dark:text-green-400
                                @else
                                    text-slate-500 dark:text-slate-400
                                @endif">
                                {{ $config['label'] }}
                            </span>
                            @if($isActive)
                                <span class="text-xs text-slate-400 dark:text-slate-500 mt-1 block">
                                    {{ $config['description'] }}
                                </span>
                            @endif
                        </div>
                        
                        {{-- TOOLTIP --}}
                        <div x-show="showTooltip && !{{ $finalAccessible ? 'true' : 'false' }}"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute top-16 left-1/2 transform -translate-x-1/2 bg-red-600 text-white text-xs px-2 py-1 rounded shadow-lg whitespace-nowrap z-20">
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
            <div class="mt-4 text-center">
                <div class="inline-flex items-center px-4 py-2 
                            {{ $isEditMode ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800' : 'bg-primary-50 dark:bg-primary-900/20 border-primary-200 dark:border-primary-800' }} 
                            border rounded-lg shadow-sm">
                    <em class="ni ni-{{ $etapes[$etape]['icon'] }} {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-primary-600 dark:text-primary-400' }} mr-2"></em>
                    <span class="text-sm font-medium {{ $isEditMode ? 'text-orange-800 dark:text-orange-200' : 'text-primary-800 dark:text-primary-200' }}">
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
        <div class="max-w-8xl mx-auto">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border {{ $isEditMode ? 'border-orange-200 dark:border-orange-800' : 'border-green-200 dark:border-green-800' }} p-8 text-center">
                <div class="w-20 h-20 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900/30' : 'bg-green-100 dark:bg-green-900/30' }} rounded-full flex items-center justify-center mx-auto mb-6">
                    <em class="ni ni-{{ $isEditMode ? 'edit' : 'check-circle' }} text-3xl {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}"></em>
                </div>
                
                <h2 class="text-2xl font-heading font-bold {{ $isEditMode ? 'text-orange-900 dark:text-orange-100' : 'text-green-900 dark:text-green-100' }} mb-4">
                    @if($isEditMode)
                        ✏️ Prescription Modifiée avec Succès !
                    @else
                        🎉 Prescription Enregistrée avec Succès !
                    @endif
                </h2>
                
                <div class="text-slate-600 dark:text-slate-300 mb-6 space-y-2">
                    @if($isEditMode)
                        <p><strong>Prescription ID:</strong> #{{ $prescription->id }}</p>
                    @endif
                    <p><strong>Patient:</strong> {{ $patient->nom }} {{ $patient->prenom }}</p>
                    <p><strong>Analyses:</strong> {{ count($analysesPanier) }} sélectionnée(s)</p>
                    @if(!empty($prelevementsSelectionnes))
                        <p><strong>Prélèvements:</strong> {{ count($prelevementsSelectionnes) }}</p>
                    @endif
                    @if(!empty($tubesGeneres))
                        <p><strong>Tubes {{ $isEditMode ? 'régénérés' : 'générés' }}:</strong> {{ count($tubesGeneres) }}</p>
                    @endif
                    <p><strong>Montant {{ $isEditMode ? 'total' : 'payé' }}:</strong> 
                        <span class="{{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }} font-bold">
                            {{ number_format($total, 0) }} Ar
                        </span>
                    </p>
                </div>
                
                {{-- ACTIONS FINALES --}}
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button wire:click="nouveauPrescription" 
                            class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <em class="ni ni-plus mr-2"></em>Nouvelle prescription
                    </button>
                    <button class="px-6 py-3 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-xl font-semibold transition-colors">
                        <em class="ni ni-printer mr-2"></em>Facture
                    </button>
                    <a href="{{ route('secretaire.prescriptions') }}" 
                       wire:navigate
                       class="px-6 py-3 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800 rounded-xl font-semibold transition-colors">
                        <em class="ni ni-list mr-2"></em>Voir toutes les prescriptions
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