{{-- livewire.secretaire.prescription.partials.patient --}}
@if($etape === 'patient')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        {{-- HEADER SECTION ADAPTATIF --}}
        <div class="bg-gradient-to-r {{ $isEditMode ? 'from-orange-50 to-amber-50' : 'from-primary-50 to-primary-50' }} dark:from-slate-700 dark:to-slate-800 px-4 py-3 border-b border-gray-100 dark:border-slate-600">
            <div class="flex items-center">
                <div class="w-8 h-8 {{ $isEditMode ? 'bg-orange-500' : 'bg-primary-500' }} rounded-lg flex items-center justify-center">
                    <em class="ni ni-user text-white text-sm"></em>
                </div>
                <div class="ml-3">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">
                        {{ $isEditMode ? 'Modification Patient' : 'Informations Patient' }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $isEditMode ? 'Modifier les données du patient ou en sélectionner un autre' : 'Recherchez ou créez un nouveau patient' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-4">
            @if(!$nouveauPatient && (!$isEditMode || !$patient))
                {{-- RECHERCHE PATIENT EXISTANT --}}
                <div class="mb-6">
                    <div class="relative">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <em class="ni ni-search {{ $isEditMode ? 'text-orange-500' : 'text-primary-500' }} mr-1.5 text-xs"></em>
                            {{ $isEditMode ? 'Rechercher un autre patient' : 'Rechercher un patient existant' }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <em class="ni ni-search text-slate-400 dark:text-slate-500 text-sm"></em>
                            </div>
                            <input type="text" 
                                   wire:model.live="recherchePatient" 
                                   placeholder="Tapez le nom, prénom, référence ou téléphone..."
                                   class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 dark:border-slate-600 rounded-lg 
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 
                                          transition-all duration-200
                                          hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-600">
                            @if($recherchePatient)
                                <button wire:click="$set('recherchePatient', '')" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                                    <em class="ni ni-times text-sm"></em>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- RÉSULTATS RECHERCHE --}}
                @if($patientsResultats->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3 flex items-center">
                            <em class="ni ni-check-circle text-green-500 mr-1.5 text-xs"></em>
                            {{ $patientsResultats->count() }} patient(s) trouvé(s)
                        </h3>
                        <div class="space-y-2">
                            @foreach($patientsResultats as $patient_item)
                                <div wire:click="selectionnerPatient({{ $patient_item->id }})" 
                                     class="group p-3 border border-gray-200 dark:border-slate-600 rounded-lg 
                                            hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 
                                            hover:bg-{{ $isEditMode ? 'orange' : 'primary' }}-50/50 dark:hover:bg-slate-700/50
                                            cursor-pointer transition-all duration-200 hover:shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-{{ $isEditMode ? 'orange' : 'primary' }}-500 to-{{ $isEditMode ? 'orange' : 'primary' }}-600 rounded-lg flex items-center justify-center text-white">
                                                <em class="ni ni-user text-xs"></em>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800 dark:text-slate-100 text-sm">
                                                    {{ $patient_item->nom }} {{ $patient_item->prenom }}
                                                </div>
                                                <div class="flex items-center space-x-3 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                    <span class="flex items-center">
                                                        <em class="ni ni-id-badge mr-1 {{ $isEditMode ? 'text-orange-500' : 'text-primary-500' }} text-xs"></em>
                                                        {{ $patient_item->numero_dossier ?? $patient_item->reference }}
                                                    </span>
                                                    {{-- ✅ AFFICHAGE DE LA CIVILITÉ --}}
                                                    <span class="flex items-center">
                                                        @if($patient_item->civilite === 'Enfant garçon')
                                                            <span class="text-blue-500">👦</span>
                                                        @elseif($patient_item->civilite === 'Enfant fille')
                                                            <span class="text-pink-500">👧</span>
                                                        @elseif($patient_item->civilite === 'Madame')
                                                            <span class="text-purple-500">👩</span>
                                                        @elseif($patient_item->civilite === 'Monsieur')
                                                            <span class="text-blue-500">👨</span>
                                                        @else
                                                            <span class="text-green-500">👧</span>
                                                        @endif
                                                        <span class="ml-1">{{ $patient_item->civilite }}</span>
                                                    </span>
                                                    @if($patient_item->telephone)
                                                        <span class="flex items-center">
                                                            <em class="ni ni-call mr-1 text-green-500 text-xs"></em>
                                                            {{ $patient_item->telephone }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center text-{{ $isEditMode ? 'orange' : 'primary' }}-600 dark:text-{{ $isEditMode ? 'orange' : 'primary' }}-400 group-hover:translate-x-0.5 transition-transform">
                                            <span class="text-xs font-medium mr-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                {{ $isEditMode ? 'Changer' : 'Sélectionner' }}
                                            </span>
                                            <em class="ni ni-arrow-right text-sm"></em>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                {{-- BOUTON NOUVEAU PATIENT --}}
                <div class="text-center pt-4 border-t border-gray-100 dark:border-slate-600">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                        {{ $isEditMode ? 'Créer un nouveau patient ?' : 'Patient introuvable ?' }}
                    </p>
                    <button wire:click="creerNouveauPatient" 
                            class="inline-flex items-center px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors text-sm">
                        <em class="ni ni-plus mr-2 text-xs"></em>
                        Créer un nouveau patient
                    </button>
                </div>
            @else
                {{-- FORMULAIRE NOUVEAU PATIENT OU MODIFICATION --}}
                <div class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        {{-- NOM --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                <em class="ni ni-edit mr-1.5 text-slate-500 text-xs"></em>
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model="nom" 
                                   placeholder="Nom de famille"
                                   class="w-full px-3 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 
                                          transition-all duration-200
                                          hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-600
                                          @error('nom') border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500 @enderror">
                            @error('nom') 
                                <p class="flex items-center text-red-600 dark:text-red-400 text-xs mt-1">
                                    <em class="ni ni-alert-circle mr-1 text-xs"></em>{{ $message }}
                                </p> 
                            @enderror
                        </div>
                        
                        {{-- PRÉNOM --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                <em class="ni ni-edit mr-1.5 text-slate-500 text-xs"></em>
                                Prénom
                            </label>
                            <input type="text" 
                                   wire:model="prenom" 
                                   placeholder="Prénom(s)"
                                   class="w-full px-3 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 
                                          transition-all duration-200
                                          hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-600
                                          @error('prenom') border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500 @enderror">
                            @error('prenom') 
                                <p class="flex items-center text-red-600 dark:text-red-400 text-xs mt-1">
                                    <em class="ni ni-alert-circle mr-1 text-xs"></em>{{ $message }}
                                </p> 
                            @enderror
                        </div>
                    </div>
                    
                    {{-- ✅ CIVILITÉ MISE À JOUR AVEC LES NOUVELLES OPTIONS --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Civilité <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">
                            @foreach($this->civilitesDisponibles as $value => $config)
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                        wire:model="civilite" 
                                        value="{{ $value }}" 
                                        class="sr-only peer">
                                    <div @class([
                                        'w-full p-2.5 text-center border rounded-lg text-sm transition-all duration-200 min-h-[3rem] flex flex-col items-center justify-center',
                                        'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300',
                                        'peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700' => $isEditMode,
                                        'peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:text-primary-700' => !$isEditMode,
                                        'dark:peer-checked:bg-slate-700 dark:peer-checked:text-slate-100',
                                        'hover:border-gray-300 dark:hover:border-slate-500',
                                        'peer-checked:shadow-sm peer-checked:ring-1',
                                        'peer-checked:ring-orange-200' => $isEditMode,
                                        'peer-checked:ring-primary-200' => !$isEditMode
                                    ])>
                                        <div class="text-lg mb-0.5">
                                            @if($value === 'Enfant garçon')
                                            @elseif($value === 'Enfant fille')
                                            @elseif($value === 'Madame')
                                            @elseif($value === 'Monsieur')
                                            @else
                                            @endif
                                        </div>
                                        <div class="text-xs font-medium leading-tight">
                                            @if($value === 'Enfant garçon')
                                                Garçon
                                            @elseif($value === 'Enfant fille')
                                                Fille
                                            @else
                                                {{ $config['label'] }}
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('civilite') 
                            <p class="flex items-center text-red-600 dark:text-red-400 text-xs mt-1">
                                <em class="ni ni-alert-circle mr-1 text-xs"></em>{{ $message }}
                            </p> 
                        @enderror
                    </div>

                    {{-- TÉLÉPHONE ET EMAIL --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- TÉLÉPHONE --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Téléphone
                            </label>
                            <input type="tel" 
                                   wire:model="telephone" 
                                   placeholder="+261 34 12 345 67"
                                   class="w-full px-3 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500">
                        </div>
                        
                        {{-- EMAIL --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Email
                            </label>
                            <input type="email" 
                                   wire:model="email" 
                                   placeholder="email@exemple.com"
                                   class="w-full px-3 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500">
                        </div>
                    </div>
                    
                    {{-- ✅ ALERTE COHÉRENCE ÂGE-CIVILITÉ (si étape clinique déjà passée) --}}
                    @if(in_array($civilite, ['Enfant garçon', 'Enfant fille']) && $age > 18 && $age > 0)
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-yellow-500 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                    <em class="ni ni-alert-triangle text-white text-xs"></em>
                                </div>
                                <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                    <span class="font-medium">Attention :</span> 
                                    La civilité "{{ $civilite }}" est sélectionnée mais l'âge est de {{ $age }} ans. 
                                    Vérifiez la cohérence lors de l'étape clinique.
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    {{-- BOUTONS D'ACTION --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-4 border-t border-gray-100 dark:border-slate-600">
                        <button wire:click="$set('nouveauPatient', false)" 
                                class="w-full sm:w-auto inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-slate-700 
                                       text-gray-700 dark:text-slate-300 font-medium rounded-lg 
                                       hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors text-sm
                                       focus:ring-2 focus:ring-gray-500 focus:ring-offset-1 dark:focus:ring-offset-slate-800">
                            <em class="ni ni-arrow-left mr-1.5 text-xs"></em>
                            {{ $isEditMode ? 'Annuler modification' : 'Retour à la recherche' }}
                        </button>
                        <button wire:click="validerNouveauPatient" 
                                class="w-full sm:w-auto inline-flex items-center px-4 py-2 {{ $isEditMode ? 'bg-green-500 hover:bg-green-600 focus:ring-green-500' : 'bg-primary-500 hover:bg-primary-600 focus:ring-primary-500' }} 
                                       text-white font-medium rounded-lg transition-colors text-sm
                                       focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-slate-800
                                       disabled:opacity-50 disabled:cursor-not-allowed">
                            <em class="ni ni-{{ $isEditMode ? 'save' : 'check' }} mr-1.5 text-xs"></em>
                            {{ $isEditMode ? 'Enregistrer modifications' : 'Valider le patient' }}
                            <em class="ni ni-arrow-right ml-1.5 text-xs"></em>
                        </button>
                    </div>
                    
                    {{-- INFORMATIONS D'AIDE --}}
                    <div class="bg-blue-50/50 dark:bg-blue-900/10 border border-blue-200/50 dark:border-blue-800/50 rounded-lg p-3">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <em class="ni ni-info text-white text-xs"></em>
                            </div>
                            <div>
                                <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-1 text-sm">
                                    {{ $isEditMode ? 'Modification des données patient' : 'Informations importantes' }}
                                </h4>
                                <div class="space-y-1 text-xs text-blue-700 dark:text-blue-300">
                                    <div class="flex items-center">
                                        <em class="ni ni-check-circle text-green-500 mr-1.5 flex-shrink-0 text-xs"></em>
                                        <span>Les champs marqués d'un astérisque (*) sont obligatoires</span>
                                    </div>
                                    @if($isEditMode)
                                        <div class="flex items-center">
                                            <em class="ni ni-edit text-orange-500 mr-1.5 flex-shrink-0 text-xs"></em>
                                            <span>Les modifications seront appliquées à toutes les prescriptions futures</span>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <em class="ni ni-shield-check text-green-500 mr-1.5 flex-shrink-0 text-xs"></em>
                                            <span>Le système vérifiera automatiquement les doublons</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center">
                                        <em class="ni ni-tag text-green-500 mr-1.5 flex-shrink-0 text-xs"></em>
                                        <span>Une référence unique {{ $isEditMode ? 'est déjà assignée' : 'sera automatiquement générée' }}</span>
                                    </div>
                                    {{-- ✅ NOUVELLE INFO SUR LES CIVILITÉS ENFANTS --}}
                                    <div class="flex items-center">
                                        <em class="ni ni-users text-blue-500 mr-1.5 flex-shrink-0 text-xs"></em>
                                        <span>Pour les mineurs, sélectionnez "Garçon" ou "Fille" selon le genre</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif