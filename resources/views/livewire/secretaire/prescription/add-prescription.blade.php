{{-- resources/views/livewire/secretaire/add-prescription.blade.php --}}
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
    @if($etape === 'patient')
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="flex items-center mb-6">
                <em class="ni ni-user text-primary-600 text-xl mr-3"></em>
                <h2 class="text-xl font-heading font-semibold text-slate-800 dark:text-slate-100">Informations Patient</h2>
            </div>
            
            @if(!$nouveauPatient)
                {{-- RECHERCHE PATIENT EXISTANT --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <em class="ni ni-search text-slate-500 dark:text-slate-400 mr-1"></em>
                        Rechercher un patient existant
                    </label>
                    <input type="text" wire:model.live="recherchePatient" 
                           placeholder="Nom, prénom, référence ou téléphone..."
                           class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-lg 
                                  bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                  focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                {{-- RÉSULTATS RECHERCHE --}}
                @if($patientsResultats->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-slate-700 mb-3">Patients trouvés :</h3>
                        <div class="space-y-2">
                            @foreach($patientsResultats as $patient)
                                <div wire:click="selectionnerPatient({{ $patient->id }})" 
                                     class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 cursor-pointer transition-all">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium text-slate-800">
                                                {{ $patient->nom }} {{ $patient->prenom }}
                                            </div>
                                            <div class="text-sm text-slate-500">
                                                <em class="ni ni-id-badge mr-1"></em>{{ $patient->reference }}
                                                @if($patient->telephone)
                                                    • <em class="ni ni-call mr-1"></em>{{ $patient->telephone }}
                                                @endif
                                            </div>
                                        </div>
                                        <em class="ni ni-arrow-right text-primary-600"></em>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif(strlen($recherchePatient) >= 2)
                    <div class="text-center py-8 bg-gray-50 rounded-lg mb-6">
                        <em class="ni ni-info text-4xl text-slate-400 mb-4"></em>
                        <p class="text-slate-600 mb-4">Aucun patient trouvé avec "{{ $recherchePatient }}"</p>
                    </div>
                @endif
                
                {{-- BOUTON NOUVEAU PATIENT --}}
                <div class="text-center">
                    <button wire:click="creerNouveauPatient" 
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <em class="ni ni-plus mr-2"></em>Créer nouveau patient
                    </button>
                </div>
            @else
                {{-- FORMULAIRE NOUVEAU PATIENT --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Nom *
                        </label>
                        <input type="text" wire:model="nom" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('nom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Prénom
                        </label>
                        <input type="text" wire:model="prenom" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Sexe *
                        </label>
                        <select wire:model="sexe" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            <em class="ni ni-call mr-1"></em>Téléphone
                        </label>
                        <input type="tel" wire:model="telephone" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            <em class="ni ni-emails mr-1"></em>Email
                        </label>
                        <input type="email" wire:model="email" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                
                <div class="flex justify-between mt-6">
                    <button wire:click="$set('nouveauPatient', false)" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        <em class="ni ni-arrow-left mr-2"></em>Retour recherche
                    </button>
                    <button wire:click="validerNouveauPatient" 
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Valider patient<em class="ni ni-arrow-right ml-2"></em>
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- ===== ÉTAPE 2: INFORMATIONS CLINIQUES ===== --}}
    @if($etape === 'clinique')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <em class="ni ni-notes text-cyan-600 text-xl mr-3"></em>
                <h2 class="text-xl font-heading font-semibold text-slate-800">Informations Cliniques</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- PRESCRIPTEUR --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <em class="ni ni-user-md mr-1"></em>Prescripteur *
                    </label>
                    <select wire:model="prescripteurId" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="">Sélectionner un prescripteur...</option>
                        @foreach($prescripteurs as $prescripteur)
                            <option value="{{ $prescripteur->id }}">Dr {{ $prescripteur->nom }}</option>
                        @endforeach
                    </select>
                    @error('prescripteurId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- TYPE PATIENT --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <em class="ni ni-building mr-1"></em>Type de patient
                    </label>
                    <select wire:model="patientType" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="EXTERNE">Externe</option>
                        <option value="HOSPITALISE">Hospitalisé</option>
                        <option value="URGENCE-JOUR">Urgence Jour</option>
                        <option value="URGENCE-NUIT">Urgence Nuit</option>
                    </select>
                </div>
                
                {{-- ÂGE --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <em class="ni ni-calendar mr-1"></em>Âge *
                    </label>
                    <div class="flex space-x-2">
                        <input type="number" wire:model="age" min="0" max="150"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <select wire:model="uniteAge" 
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                            <option value="Ans">Ans</option>
                            <option value="Mois">Mois</option>
                            <option value="Jours">Jours</option>
                        </select>
                    </div>
                    @error('age') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- POIDS --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <em class="ni ni-activity mr-1"></em>Poids (kg)
                    </label>
                    <input type="number" wire:model="poids" step="0.1" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                           placeholder="Optionnel">
                </div>
            </div>
            
            {{-- RENSEIGNEMENTS CLINIQUES --}}
            <div class="mt-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    <em class="ni ni-clipboard mr-1"></em>Renseignements cliniques
                </label>
                <textarea wire:model="renseignementClinique" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                          placeholder="Symptômes, antécédents, indications médicales..."></textarea>
            </div>
            
            <div class="flex justify-between mt-6">
                <button wire:click="allerEtape('patient')" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <em class="ni ni-arrow-left mr-2"></em>Patient
                </button>
                <button wire:click="validerInformationsCliniques" 
                        class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
                    Analyses<em class="ni ni-arrow-right ml-2"></em>
                </button>
            </div>
        </div>
    @endif

    {{-- ===== ÉTAPE 3: SÉLECTION ANALYSES ===== --}}
    @if($etape === 'analyses')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- RECHERCHE ANALYSES --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <em class="ni ni-test-tube text-green-600 text-xl mr-3"></em>
                            <h2 class="text-xl font-heading font-semibold text-slate-800 dark:text-slate-100">Recherche Analyses</h2>
                        </div>
                        @if(count($analysesPanier) > 0)
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm font-medium">
                                {{ count($analysesPanier) }} sélectionnées
                            </span>
                        @endif
                    </div>
                    
                    {{-- RECHERCHE OBLIGATOIRE --}}
                    <div class="mb-6">
                        <div class="relative">
                            <em class="ni ni-search absolute left-3 top-3 text-slate-400"></em>
                            <input type="text" wire:model.live="rechercheAnalyse" 
                                placeholder="Rechercher par CODE (ex: NFS, GLY, URE) ou DESIGNATION (ex: HÉMOGRAMME, GLYCÉMIE)..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-lg 
                                        bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                        focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        @if(strlen($rechercheAnalyse) > 0 && strlen($rechercheAnalyse) < 2)
                            <p class="text-yellow-600 dark:text-yellow-400 text-sm mt-2">
                                <em class="ni ni-info mr-1"></em>Tapez au moins 2 caractères pour commencer la recherche
                            </p>
                        @endif
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-full text-xs">
                                💡 Exemples: NFS, GLY, URE, HÉMOGRAMME, GLYCÉMIE
                            </span>
                        </div>
                    </div>
                    
                    {{-- RÉSULTATS RECHERCHE --}}
                    @if($analysesRecherche->count() > 0)
                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                <em class="ni ni-search mr-1"></em>{{ $analysesRecherche->count() }} résultat(s) trouvé(s)
                                @if($parentRecherche)
                                    <span class="text-xs text-gray-500">pour "{{ $parentRecherche->designation }} ({{ $parentRecherche->code }})"</span>
                                @endif
                            </h3>
                            @foreach($analysesRecherche as $analyse)
                                <div class="flex justify-between items-center p-3 border border-gray-200 dark:border-slate-600 rounded-lg 
                                        hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded font-mono text-sm font-bold">
                                                {{ $analyse->code }}
                                            </span>
                                            <div>
                                                <span class="font-medium text-slate-800 dark:text-slate-100">{{ $analyse->designation }}</span>
                                                <div class="text-slate-500 dark:text-slate-400 text-sm">
                                                    {{ $analyse->parent?->designation ? $analyse->parent->designation . ($analyse->parent->prix > 0 ? ' (inclus)' : '') : 'Divers' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="font-medium text-slate-700 dark:text-slate-300">
                                            {{ $analyse->parent && $analyse->parent->prix > 0 ? 'Inclus' : $analyse->getPrixFormate() }}
                                        </span>
                                        <button wire:click="ajouterAnalyse({{ $analyse->id }})" 
                                                class="px-3 py-1 text-sm rounded transition-colors
                                                {{ isset($analysesPanier[$analyse->id]) 
                                                ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' 
                                                : 'bg-green-600 hover:bg-green-700 text-white' }}"
                                                {{ isset($analysesPanier[$analyse->id]) ? 'disabled' : '' }}>
                                            <em class="ni ni-{{ isset($analysesPanier[$analyse->id]) ? 'check' : 'plus' }}"></em>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif(strlen($rechercheAnalyse) >= 2)
                        <div class="text-center py-8 bg-gray-50 dark:bg-slate-700 rounded-lg">
                            <em class="ni ni-info text-4xl text-slate-400 mb-4"></em>
                            <p class="text-slate-600 dark:text-slate-300">Aucune analyse trouvée avec "{{ $rechercheAnalyse }}"</p>
                            @if(session('suggestions'))
                                <p class="text-yellow-600 dark:text-yellow-400 text-sm mt-2">{{ session('suggestions') }}</p>
                            @else
                                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Essayez avec d'autres mots-clés</p>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 dark:bg-slate-700 rounded-lg">
                            <em class="ni ni-search text-4xl text-slate-400 mb-4"></em>
                            <p class="text-lg text-slate-600 dark:text-slate-300 mb-2">Recherche d'analyses</p>
                            <p class="text-slate-500 dark:text-slate-400">Tapez dans le champ ci-dessus pour rechercher des analyses</p>
                        </div>
                    @endif
                    
                    <div class="flex justify-between mt-6">
                        <button wire:click="allerEtape('clinique')" 
                                class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600">
                            <em class="ni ni-arrow-left mr-2"></em>Clinique
                        </button>
                        <button wire:click="validerAnalyses" 
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Prélèvements<em class="ni ni-arrow-right ml-2"></em>
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- PANIER ANALYSES --}}
            <div>
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-medium text-slate-800 dark:text-slate-100">
                            <em class="ni ni-bag mr-2"></em>Analyses sélectionnées
                        </h3>
                        @if(count($analysesPanier) > 0)
                            <button wire:click="$set('analysesPanier', [])" 
                                    class="text-red-500 hover:text-red-700 text-sm">
                                <em class="ni ni-trash"></em>
                            </button>
                        @endif
                    </div>
                    
                    @if(count($analysesPanier) > 0)
                        <div class="space-y-3 mb-4">
                            @foreach($analysesPanier as $analyse)
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded font-mono text-xs font-bold">
                                                {{ $analyse['code'] }}
                                            </span>
                                            <div class="font-medium text-sm text-slate-800 dark:text-slate-100">{{ $analyse['designation'] }}</div>
                                        </div>
                                        <div class="text-slate-500 dark:text-slate-400 text-xs">{{ $analyse['parent_nom'] }}</div>
                                    </div>
                                    <div class="text-right ml-2">
                                        <div class="font-medium text-slate-700 dark:text-slate-300">
                                            {{ $analyse['prix_effectif'] > 0 ? number_format($analyse['prix_effectif'], 0) . ' Ar' : 'Inclus' }}
                                        </div>
                                        <button wire:click="retirerAnalyse({{ $analyse['id'] }})" 
                                                class="text-red-500 hover:text-red-700 text-xs">
                                            <em class="ni ni-cross"></em>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-slate-600 pt-3">
                            <div class="flex justify-between font-bold text-lg">
                                <span class="text-slate-800 dark:text-slate-100">Total:</span>
                                <span class="text-green-600 dark:text-green-400">{{ number_format($total, 0) }} Ar</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6 text-slate-500 dark:text-slate-400">
                            <em class="ni ni-bag text-2xl mb-2"></em>
                            <p class="text-sm">Aucune analyse sélectionnée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ===== ÉTAPE 4: PRÉLÈVEMENTS ===== --}}
    @if($etape === 'prelevements')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- SÉLECTION PRÉLÈVEMENTS --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <em class="ni ni-package text-yellow-600 text-xl mr-3"></em>
                            <h2 class="text-xl font-heading font-semibold text-slate-800 dark:text-slate-100">Sélection Prélèvements</h2>
                        </div>
                        @if(count($prelevementsSelectionnes) > 0)
                            <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-full text-sm font-medium">
                                {{ count($prelevementsSelectionnes) }} sélectionnés
                            </span>
                        @endif
                    </div>
                    
                    {{-- RECHERCHE PRÉLÈVEMENTS --}}
                    <div class="mb-6">
                        <div class="relative">
                            <em class="ni ni-search absolute left-3 top-3 text-slate-400"></em>
                            <input type="text" wire:model.live="recherchePrelevement" 
                                   placeholder="Rechercher un prélèvement..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        </div>
                    </div>
                    
                    {{-- RÉSULTATS RECHERCHE PRÉLÈVEMENTS --}}
                    @if($prelevementsRecherche->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                <em class="ni ni-search mr-1"></em>Résultats de recherche
                            </h3>
                            <div class="space-y-2">
                                @foreach($prelevementsRecherche as $prelevement)
                                    <div class="flex justify-between items-center p-3 border border-gray-200 dark:border-slate-600 rounded-lg 
                                               hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                        <div class="flex-1">
                                            <span class="font-medium text-slate-800 dark:text-slate-100">{{ $prelevement->nom }}</span>
                                            @if($prelevement->description)
                                                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ $prelevement->description }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($prelevement->prix, 0) }} Ar</span>
                                            <button wire:click="ajouterPrelevement({{ $prelevement->id }})" 
                                                    class="px-3 py-1 text-sm rounded transition-colors
                                                    {{ isset($prelevementsSelectionnes[$prelevement->id]) 
                                                       ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' 
                                                       : 'bg-yellow-600 hover:bg-yellow-700 text-white' }}"
                                                    {{ isset($prelevementsSelectionnes[$prelevement->id]) ? 'disabled' : '' }}>
                                                <em class="ni ni-{{ isset($prelevementsSelectionnes[$prelevement->id]) ? 'check' : 'plus' }}"></em>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- TOUS LES PRÉLÈVEMENTS DISPONIBLES --}}
                    <div>
                        <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                            <em class="ni ni-package mr-1"></em>Prélèvements disponibles
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($prelevementsDisponibles as $prelevement)
                                <div class="p-3 border border-gray-200 dark:border-slate-600 rounded-lg 
                                           hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors
                                           {{ isset($prelevementsSelectionnes[$prelevement->id]) ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-700' : '' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-slate-800 dark:text-slate-100">{{ $prelevement->nom }}</h4>
                                            @if($prelevement->description)
                                                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">{{ $prelevement->description }}</p>
                                            @endif
                                            <div class="text-yellow-600 dark:text-yellow-400 font-medium mt-1">{{ number_format($prelevement->prix, 0) }} Ar</div>
                                        </div>
                                        <button wire:click="ajouterPrelevement({{ $prelevement->id }})" 
                                                class="px-3 py-1 text-sm rounded transition-colors ml-2
                                                {{ isset($prelevementsSelectionnes[$prelevement->id]) 
                                                   ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' 
                                                   : 'bg-yellow-600 hover:bg-yellow-700 text-white' }}"
                                                {{ isset($prelevementsSelectionnes[$prelevement->id]) ? 'disabled' : '' }}>
                                            <em class="ni ni-{{ isset($prelevementsSelectionnes[$prelevement->id]) ? 'check' : 'plus' }}"></em>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-6">
                        <button wire:click="allerEtape('analyses')" 
                                class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600">
                            <em class="ni ni-arrow-left mr-2"></em>Analyses
                        </button>
                        <button wire:click="validerPrelevements" 
                                class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            Paiement<em class="ni ni-arrow-right ml-2"></em>
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- PRÉLÈVEMENTS SÉLECTIONNÉS --}}
            <div>
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-medium text-slate-800 dark:text-slate-100">
                            <em class="ni ni-package mr-2"></em>Prélèvements sélectionnés
                        </h3>
                        @if(count($prelevementsSelectionnes) > 0)
                            <button wire:click="$set('prelevementsSelectionnes', [])" 
                                    class="text-red-500 hover:text-red-700 text-sm">
                                <em class="ni ni-trash"></em>
                            </button>
                        @endif
                    </div>
                    
                    @if(count($prelevementsSelectionnes) > 0)
                        <div class="space-y-3">
                            @foreach($prelevementsSelectionnes as $prelevement)
                                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <div class="font-medium text-slate-800 dark:text-slate-100">{{ $prelevement['nom'] }}</div>
                                            @if($prelevement['description'])
                                                <div class="text-slate-500 dark:text-slate-400 text-xs">{{ $prelevement['description'] }}</div>
                                            @endif
                                        </div>
                                        <button wire:click="retirerPrelevement({{ $prelevement['id'] }})" 
                                                class="text-red-500 hover:text-red-700 text-xs">
                                            <em class="ni ni-cross"></em>
                                        </button>
                                    </div>
                                    
                                    <div class="flex justify-between items-center text-sm">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-slate-600 dark:text-slate-400">Quantité:</span>
                                            <input type="number" 
                                                   wire:change="modifierQuantitePrelevement({{ $prelevement['id'] }}, $event.target.value)"
                                                   value="{{ $prelevement['quantite'] }}" 
                                                   min="1" max="10"
                                                   class="w-16 px-2 py-1 border border-gray-300 dark:border-slate-600 rounded text-center 
                                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
                                        </div>
                                        <div class="font-medium text-yellow-600 dark:text-yellow-400">
                                            {{ number_format($prelevement['prix'] * $prelevement['quantite'], 0) }} Ar
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-slate-600 pt-3 mt-4">
                            <div class="flex justify-between font-bold">
                                <span class="text-slate-800 dark:text-slate-100">Total prélèvements:</span>
                                <span class="text-yellow-600 dark:text-yellow-400">
                                    {{ number_format(collect($prelevementsSelectionnes)->sum(fn($p) => $p['prix'] * $p['quantite']), 0) }} Ar
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6 text-slate-500 dark:text-slate-400">
                            <em class="ni ni-package text-2xl mb-2"></em>
                            <p class="text-sm">Aucun prélèvement sélectionné</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ===== ÉTAPE 5: PAIEMENT ===== --}}
    @if($etape === 'paiement')
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <div class="flex items-center mb-6">
                    <em class="ni ni-coin text-red-600 text-xl mr-3"></em>
                    <h2 class="text-xl font-heading font-semibold text-slate-800 dark:text-slate-100">Paiement</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- RÉCAPITULATIF --}}
                    <div>
                        <h3 class="font-medium text-slate-800 mb-4">
                            <em class="ni ni-file-text mr-2"></em>Récapitulatif de la commande
                        </h3>
                        
                        {{-- ANALYSES --}}
                        <div class="space-y-2 mb-4">
                            <h4 class="font-medium text-slate-700 dark:text-slate-300">Analyses :</h4>
                            @foreach($analysesPanier as $analyse)
                                <div class="flex justify-between py-1 border-b border-gray-100 dark:border-slate-600">
                                    <div class="flex items-center space-x-2">
                                        <span class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded font-mono text-xs font-bold">
                                            {{ $analyse['code'] }}
                                        </span>
                                        <span class="text-slate-700 dark:text-slate-300">{{ $analyse['designation'] }}</span>
                                    </div>
                                    <span class="font-medium">{{ number_format($analyse['prix'], 0) }} Ar</span>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- PRÉLÈVEMENTS --}}
                        @if(count($prelevementsSelectionnes) > 0)
                            <div class="space-y-2 mb-4">
                                <h4 class="font-medium text-slate-700 dark:text-slate-300">Prélèvements :</h4>
                                @foreach($prelevementsSelectionnes as $prelevement)
                                    <div class="flex justify-between py-1 border-b border-gray-100 dark:border-slate-600">
                                        <span class="text-slate-700 dark:text-slate-300">{{ $prelevement['nom'] }} (x{{ $prelevement['quantite'] }})</span>
                                        <span class="font-medium">{{ number_format($prelevement['prix'] * $prelevement['quantite'], 0) }} Ar</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        {{-- TOTAUX --}}
                        <div class="bg-gray-50 dark:bg-slate-700 p-4 rounded-lg">
                            <div class="flex justify-between mb-2">
                                <span>Sous-total analyses:</span>
                                <span>{{ number_format(collect($analysesPanier)->sum('prix'), 0) }} Ar</span>
                            </div>
                            @if(count($prelevementsSelectionnes) > 0)
                                <div class="flex justify-between mb-2">
                                    <span>Sous-total prélèvements:</span>
                                    <span>{{ number_format(collect($prelevementsSelectionnes)->sum(fn($p) => $p['prix'] * $p['quantite']), 0) }} Ar</span>
                                </div>
                            @endif
                            @if($remise > 0)
                                <div class="flex justify-between mb-2 text-red-600 dark:text-red-400">
                                    <span>Remise:</span>
                                    <span>-{{ number_format($remise, 0) }} Ar</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-bold text-lg border-t pt-2">
                                <span>Total à payer:</span>
                                <span class="text-red-600 dark:text-red-400">{{ number_format($total, 0) }} Ar</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- PAIEMENT --}}
                    <div>
                        <h3 class="font-medium text-slate-800 mb-4">
                            <em class="ni ni-wallet mr-2"></em>Détails du paiement
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mode de paiement</label>
                                <select wire:model="modePaiement" 
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                               bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                               focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <option value="ESPECES">💵 Espèces</option>
                                    <option value="CARTE">💳 Carte bancaire</option>
                                    <option value="CHEQUE">📄 Chèque</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Remise (Ar)</label>
                                <input type="number" wire:model.live="remise" min="0" step="100" 
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                              bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                              focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Montant payé</label>
                                <input type="number" wire:model.live="montantPaye" min="0" step="100" 
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                              bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                              focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                            
                            @if($monnaieRendue > 0)
                                <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                                    <div class="flex items-center">
                                        <em class="ni ni-money text-green-600 dark:text-green-400 mr-2"></em>
                                        <span class="font-medium text-green-800 dark:text-green-200">
                                            Monnaie à rendre : {{ number_format($monnaieRendue, 0) }} Ar
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-between mt-8">
                    <button wire:click="allerEtape('prelevements')" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        <em class="ni ni-arrow-left mr-2"></em>Prélèvements
                    </button>
                    <button wire:click="validerPaiement" 
                            class="px-8 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                        <em class="ni ni-check mr-2"></em>Valider le paiement
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== ÉTAPE 6: TUBES ET ÉTIQUETTES ===== --}}
    @if($etape === 'tubes')
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-6">
                    <em class="ni ni-printer text-slate-600 text-xl mr-3"></em>
                    <h2 class="text-xl font-heading font-semibold text-slate-800">Tubes et Étiquettes</h2>
                </div>
                
                @if(count($tubesGeneres) > 0)
                    <div class="mb-6">
                        <h3 class="font-medium text-slate-800 mb-4">
                            {{ count($tubesGeneres) }} tube(s) généré(s) avec succès
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($tubesGeneres as $tube)
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-slate-800">{{ $tube['numero_tube'] ?? 'Tube #'.$tube['id'] }}</span>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ $tube['statut'] }}</span>
                                    </div>
                                    <div class="text-sm text-slate-600">
                                        <div>Code-barre: {{ $tube['code_barre'] ?? 'En cours...' }}</div>
                                        <div>Type: {{ $tube['type_tube'] ?? 'Standard' }}</div>
                                        <div>Volume: {{ $tube['volume_ml'] ?? 5 }} ml</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex justify-center space-x-4">
                        <button wire:click="imprimerEtiquettes" 
                                class="px-6 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700">
                            <em class="ni ni-printer mr-2"></em>Imprimer étiquettes
                        </button>
                        <button wire:click="ignorerEtiquettes" 
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Ignorer l'impression
                        </button>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-500">
                        <em class="ni ni-alert-circle text-4xl mb-4"></em>
                        <p>Aucun tube généré</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

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
