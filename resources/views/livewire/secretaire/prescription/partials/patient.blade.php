{{-- livewire.secretaire.prescription.partials.patient --}}
@if($etape === 'patient')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
        {{-- HEADER SECTION ADAPTATIF --}}
        <div class="bg-gradient-to-r {{ $isEditMode ? 'from-orange-50 to-amber-100' : 'from-primary-50 to-primary-100' }} dark:from-slate-700 dark:to-slate-800 px-6 py-5 border-b border-gray-200 dark:border-slate-600">
            <div class="flex items-center">
                <div class="w-12 h-12 {{ $isEditMode ? 'bg-orange-600' : 'bg-primary-600' }} dark:bg-primary-500 rounded-xl flex items-center justify-center shadow-lg">
                    <em class="ni ni-user text-white text-xl"></em>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                        {{ $isEditMode ? 'Modification Patient' : 'Informations Patient' }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        {{ $isEditMode ? 'Modifier les données du patient ou en sélectionner un autre' : 'Recherchez ou créez un nouveau patient' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if(!$nouveauPatient && (!$isEditMode || !$patient))
                {{-- RECHERCHE PATIENT EXISTANT --}}
                <div class="mb-8">
                    <div class="relative">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                            <em class="ni ni-search {{ $isEditMode ? 'text-orange-500' : 'text-primary-500' }} mr-2"></em>
                            {{ $isEditMode ? 'Rechercher un autre patient' : 'Rechercher un patient existant' }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <em class="ni ni-search text-slate-400 dark:text-slate-500"></em>
                            </div>
                            <input type="text" 
                                   wire:model.live="recherchePatient" 
                                   placeholder="Tapez le nom, prénom, référence ou téléphone..."
                                   class="w-full pl-12 pr-4 py-4 text-base border border-gray-300 dark:border-slate-600 rounded-xl 
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 
                                          transition-all duration-200 shadow-sm
                                          hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-600">
                            @if($recherchePatient)
                                <button wire:click="$set('recherchePatient', '')" 
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                                    <em class="ni ni-times text-lg"></em>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- RÉSULTATS RECHERCHE --}}
                @if($patientsResultats->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center">
                            <em class="ni ni-check-circle text-green-500 mr-2"></em>
                            {{ $patientsResultats->count() }} patient(s) trouvé(s)
                        </h3>
                        <div class="space-y-3">
                            @foreach($patientsResultats as $patient_item)
                                <div wire:click="selectionnerPatient({{ $patient_item->id }})" 
                                     class="group p-5 border border-gray-200 dark:border-slate-600 rounded-xl 
                                            hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 
                                            hover:bg-gradient-to-r hover:from-{{ $isEditMode ? 'orange' : 'primary' }}-50 hover:to-transparent 
                                            dark:hover:from-slate-700 dark:hover:to-transparent
                                            cursor-pointer transition-all duration-300 transform hover:scale-[1.02] hover:shadow-md">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-{{ $isEditMode ? 'orange' : 'primary' }}-500 to-{{ $isEditMode ? 'orange' : 'primary' }}-600 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:shadow-xl transition-shadow">
                                                <em class="ni ni-user text-lg"></em>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-slate-800 dark:text-slate-100 text-lg">
                                                    {{ $patient_item->nom }} {{ $patient_item->prenom }}
                                                </div>
                                                <div class="flex items-center space-x-4 text-sm text-slate-500 dark:text-slate-400 mt-1">
                                                    <span class="flex items-center">
                                                        <em class="ni ni-id-badge mr-1.5 {{ $isEditMode ? 'text-orange-500' : 'text-primary-500' }}"></em>
                                                        {{ $patient_item->reference }}
                                                    </span>
                                                    @if($patient_item->telephone)
                                                        <span class="flex items-center">
                                                            <em class="ni ni-call mr-1.5 text-green-500"></em>
                                                            {{ $patient_item->telephone }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center text-{{ $isEditMode ? 'orange' : 'primary' }}-600 dark:text-{{ $isEditMode ? 'orange' : 'primary' }}-400 group-hover:translate-x-1 transition-transform">
                                            <span class="text-sm font-medium mr-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                {{ $isEditMode ? 'Changer' : 'Sélectionner' }}
                                            </span>
                                            <em class="ni ni-arrow-right text-xl"></em>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif(strlen($recherchePatient) >= 2)
                    <div class="text-center py-12 mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full mb-4">
                            <em class="ni ni-search text-3xl text-slate-400 dark:text-slate-500"></em>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-600 dark:text-slate-300 mb-2">Aucun patient trouvé</h3>
                        <p class="text-slate-500 dark:text-slate-400 mb-6">
                            Aucun résultat pour "<span class="font-medium text-slate-700 dark:text-slate-300">{{ $recherchePatient }}</span>"
                        </p>
                        <button wire:click="creerNouveauPatient" 
                                class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <em class="ni ni-plus mr-2"></em>
                            Créer ce patient
                        </button>
                    </div>
                @endif
                
                {{-- BOUTON NOUVEAU PATIENT --}}
                <div class="text-center pt-6 border-t border-gray-200 dark:border-slate-600">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                        {{ $isEditMode ? 'Créer un nouveau patient ?' : 'Patient introuvable ?' }}
                    </p>
                    <button wire:click="creerNouveauPatient" 
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 
                                   hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl">
                        <em class="ni ni-plus mr-3 text-lg"></em>
                        Créer un nouveau patient
                    </button>
                </div>
            @else
                {{-- FORMULAIRE NOUVEAU PATIENT OU MODIFICATION --}}
                <div class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- NOM --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <em class="ni ni-edit mr-2 text-slate-500"></em>
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model="nom" 
                                   placeholder="Nom de famille"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl 
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 
                                          transition-all duration-200 shadow-sm
                                          hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-600
                                          @error('nom') border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500 @enderror">
                            @error('nom') 
                                <p class="flex items-center text-red-600 dark:text-red-400 text-sm mt-1">
                                    <em class="ni ni-alert-circle mr-1"></em>{{ $message }}
                                </p> 
                            @enderror
                        </div>
                        
                        {{-- PRÉNOM --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <em class="ni ni-edit mr-2 text-slate-500"></em>
                                Prénom
                            </label>
                            <input type="text" 
                                   wire:model="prenom" 
                                   placeholder="Prénom(s)"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl 
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 
                                          transition-all duration-200 shadow-sm
                                          hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-600
                                          @error('prenom') border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500 @enderror">
                            @error('prenom') 
                                <p class="flex items-center text-red-600 dark:text-red-400 text-sm mt-1">
                                    <em class="ni ni-alert-circle mr-1"></em>{{ $message }}
                                </p> 
                            @enderror
                        </div>
                    </div>
                    
                    {{-- GENRE - VERSION AMÉLIORÉE --}}
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <em class="ni ni-users mr-2 text-slate-500"></em>
                            Civilité <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                            {{-- MADAME --}}
                            <div class="relative">
                                <input type="radio" 
                                       wire:model.live="sexe" 
                                       value="Madame" 
                                       id="sexe_Madame"
                                       name="sexe"
                                       class="sr-only peer" 
                                       autocomplete="off">
                                <label for="sexe_Madame" 
                                       class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 dark:border-slate-600 rounded-xl 
                                              bg-white dark:bg-slate-700 cursor-pointer transition-all duration-200
                                              hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 hover:bg-{{ $isEditMode ? 'orange' : 'primary' }}-50 dark:hover:bg-slate-600
                                              peer-checked:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 peer-checked:bg-{{ $isEditMode ? 'orange' : 'primary' }}-50 dark:peer-checked:bg-{{ $isEditMode ? 'orange' : 'primary' }}-900/30 
                                              peer-checked:shadow-md peer-focus:ring-2 peer-focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 peer-focus:ring-offset-2
                                              min-h-[80px]">
                                    <div class="text-2xl mb-1">👩</div>
                                    <span class="font-medium text-sm text-slate-700 dark:text-slate-300">Madame</span>
                                </label>
                            </div>

                            {{-- MONSIEUR --}}
                            <div class="relative">
                                <input type="radio" 
                                       wire:model.live="sexe" 
                                       value="Monsieur" 
                                       id="sexe_Monsieur"
                                       name="sexe"
                                       class="sr-only peer" 
                                       autocomplete="off">
                                <label for="sexe_Monsieur" 
                                       class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 dark:border-slate-600 rounded-xl 
                                              bg-white dark:bg-slate-700 cursor-pointer transition-all duration-200
                                              hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 hover:bg-{{ $isEditMode ? 'orange' : 'primary' }}-50 dark:hover:bg-slate-600
                                              peer-checked:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 peer-checked:bg-{{ $isEditMode ? 'orange' : 'primary' }}-50 dark:peer-checked:bg-{{ $isEditMode ? 'orange' : 'primary' }}-900/30 
                                              peer-checked:shadow-md peer-focus:ring-2 peer-focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 peer-focus:ring-offset-2
                                              min-h-[80px]">
                                    <div class="text-2xl mb-1">👨</div>
                                    <span class="font-medium text-sm text-slate-700 dark:text-slate-300">Monsieur</span>
                                </label>
                            </div>

                            {{-- MADEMOISELLE --}}
                            <div class="relative">
                                <input type="radio" 
                                       wire:model.live="sexe" 
                                       value="Mademoiselle" 
                                       id="sexe_Mademoiselle"
                                       name="sexe"
                                       class="sr-only peer" 
                                       autocomplete="off">
                                <label for="sexe_Mademoiselle" 
                                       class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 dark:border-slate-600 rounded-xl 
                                              bg-white dark:bg-slate-700 cursor-pointer transition-all duration-200
                                              hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 hover:bg-{{ $isEditMode ? 'orange' : 'primary' }}-50 dark:hover:bg-slate-600
                                              peer-checked:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 peer-checked:bg-{{ $isEditMode ? 'orange' : 'primary' }}-50 dark:peer-checked:bg-{{ $isEditMode ? 'orange' : 'primary' }}-900/30 
                                              peer-checked:shadow-md peer-focus:ring-2 peer-focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 peer-focus:ring-offset-2
                                              min-h-[80px]">
                                    <div class="text-2xl mb-1">⭐</div>
                                    <span class="font-medium text-sm text-slate-700 dark:text-slate-300">
                                        <span class="hidden sm:inline">Mademoiselle</span>
                                        <span class="sm:hidden">Mlle</span>
                                    </span>
                                </label>
                            </div>

                            {{-- ENFANT --}}
                            <div class="relative">
                                <input type="radio" 
                                       wire:model.live="sexe" 
                                       value="Enfant" 
                                       id="sexe_Enfant"
                                       name="sexe"
                                       class="sr-only peer" 
                                       autocomplete="off">
                                <label for="sexe_Enfant" 
                                       class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 dark:border-slate-600 rounded-xl 
                                              bg-white dark:bg-slate-700 cursor-pointer transition-all duration-200
                                              hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 hover:bg-{{ $isEditMode ? 'orange' : 'primary' }}-50 dark:hover:bg-slate-600
                                              peer-checked:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 peer-checked:bg-{{ $isEditMode ? 'orange' : 'primary' }}-50 dark:peer-checked:bg-{{ $isEditMode ? 'orange' : 'primary' }}-900/30 
                                              peer-checked:shadow-md peer-focus:ring-2 peer-focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 peer-focus:ring-offset-2
                                              min-h-[80px]">
                                    <div class="text-2xl mb-1">👶</div>
                                    <span class="font-medium text-sm text-slate-700 dark:text-slate-300">Enfant</span>
                                </label>
                            </div>
                        </div>
                        @error('sexe') 
                            <p class="flex items-center text-red-600 dark:text-red-400 text-sm mt-2">
                                <em class="ni ni-alert-circle mr-1"></em>{{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- TÉLÉPHONE --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <em class="ni ni-call mr-2 text-slate-500"></em>
                                Téléphone
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <em class="ni ni-call text-slate-400 dark:text-slate-500"></em>
                                </div>
                                <input type="tel" 
                                       wire:model="telephone" 
                                       placeholder="+261 34 12 345 67"
                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl 
                                              bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                              placeholder-slate-400 dark:placeholder-slate-500
                                              focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 
                                              transition-all duration-200 shadow-sm
                                              hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-600
                                              @error('telephone') border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500 @enderror">
                            </div>
                            @error('telephone') 
                                <p class="flex items-center text-red-600 dark:text-red-400 text-sm mt-1">
                                    <em class="ni ni-alert-circle mr-1"></em>{{ $message }}
                                </p> 
                            @enderror
                        </div>
                        
                        {{-- EMAIL --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <em class="ni ni-emails mr-2 text-slate-500"></em>
                                Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <em class="ni ni-emails text-slate-400 dark:text-slate-500"></em>
                                </div>
                                <input type="email" 
                                       wire:model="email" 
                                       placeholder="exemple@email.com"
                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl 
                                              bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                              placeholder-slate-400 dark:placeholder-slate-500
                                              focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'primary' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'primary' }}-500 
                                              transition-all duration-200 shadow-sm
                                              hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'primary' }}-600
                                              @error('email') border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500 @enderror">
                            </div>
                            @error('email') 
                                <p class="flex items-center text-red-600 dark:text-red-400 text-sm mt-1">
                                    <em class="ni ni-alert-circle mr-1"></em>{{ $message }}
                                </p> 
                            @enderror
                        </div>
                    </div>
                    
                    {{-- BOUTONS D'ACTION --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-200 dark:border-slate-600">
                        <button wire:click="$set('nouveauPatient', false)" 
                                class="w-full sm:w-auto inline-flex items-center px-6 py-3 bg-slate-100 dark:bg-slate-700 
                                       text-slate-700 dark:text-slate-300 font-semibold rounded-xl 
                                       hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors
                                       focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                            <em class="ni ni-arrow-left mr-2"></em>
                            {{ $isEditMode ? 'Annuler modification' : 'Retour à la recherche' }}
                        </button>
                        <button wire:click="validerNouveauPatient" 
                                class="w-full sm:w-auto inline-flex items-center px-8 py-3 {{ $isEditMode ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500' }} 
                                       text-white font-semibold rounded-xl transition-colors
                                       focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-800
                                       disabled:opacity-50 disabled:cursor-not-allowed">
                            <em class="ni ni-{{ $isEditMode ? 'save' : 'check' }} mr-2"></em>
                            {{ $isEditMode ? 'Enregistrer modifications' : 'Valider le patient' }}
                            <em class="ni ni-arrow-right ml-2"></em>
                        </button>
                    </div>
                    
                    {{-- INFORMATIONS D'AIDE --}}
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 
                                border border-blue-200 dark:border-blue-800 rounded-xl p-5">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <em class="ni ni-info text-white"></em>
                            </div>
                            <div>
                                <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                    {{ $isEditMode ? 'Modification des données patient' : 'Informations importantes' }}
                                </h4>
                                <div class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
                                    <div class="flex items-center">
                                        <em class="ni ni-check-circle text-green-500 mr-2 flex-shrink-0"></em>
                                        <span>Les champs marqués d'un astérisque (*) sont obligatoires</span>
                                    </div>
                                    @if($isEditMode)
                                        <div class="flex items-center">
                                            <em class="ni ni-edit text-orange-500 mr-2 flex-shrink-0"></em>
                                            <span>Les modifications seront appliquées à toutes les prescriptions futures</span>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <em class="ni ni-shield-check text-green-500 mr-2 flex-shrink-0"></em>
                                            <span>Le système vérifiera automatiquement les doublons</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center">
                                        <em class="ni ni-tag text-green-500 mr-2 flex-shrink-0"></em>
                                        <span>Une référence unique {{ $isEditMode ? 'est déjà assignée' : 'sera automatiquement générée' }}</span>
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