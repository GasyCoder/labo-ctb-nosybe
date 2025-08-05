{{-- livewire.secretaire.prescription.partials.patient --}}
{{-- ===== ÉTAPE 1: PATIENT ===== --}}
@if($etape === 'patient')
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex items-center mb-6">
            <em class="ni ni-user text-primary-600 dark:text-primary-400 text-xl mr-3"></em>
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
                              placeholder-slate-400 dark:placeholder-slate-400
                              focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            
            {{-- RÉSULTATS RECHERCHE --}}
            @if($patientsResultats->count() > 0)
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Patients trouvés :</h3>
                    <div class="space-y-2">
                        @foreach($patientsResultats as $patient)
                            <div wire:click="selectionnerPatient({{ $patient->id }})" 
                                 class="p-4 border border-gray-200 dark:border-slate-600 rounded-lg 
                                        hover:border-primary-300 dark:hover:border-primary-500 
                                        hover:bg-primary-50 dark:hover:bg-slate-700 
                                        cursor-pointer transition-all">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-slate-800 dark:text-slate-100">
                                            {{ $patient->nom }} {{ $patient->prenom }}
                                        </div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400">
                                            <em class="ni ni-id-badge mr-1"></em>{{ $patient->reference }}
                                            @if($patient->telephone)
                                                • <em class="ni ni-call mr-1"></em>{{ $patient->telephone }}
                                            @endif
                                        </div>
                                    </div>
                                    <em class="ni ni-arrow-right text-primary-600 dark:text-primary-400"></em>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif(strlen($recherchePatient) >= 2)
                <div class="text-center py-8 bg-gray-50 dark:bg-slate-700 rounded-lg mb-6">
                    <em class="ni ni-info text-4xl text-slate-400 dark:text-slate-500 mb-4"></em>
                    <p class="text-slate-600 dark:text-slate-300 mb-4">Aucun patient trouvé avec "{{ $recherchePatient }}"</p>
                </div>
            @endif
            
            {{-- BOUTON NOUVEAU PATIENT --}}
            <div class="text-center">
                <button wire:click="creerNouveauPatient" 
                        class="px-6 py-3 bg-green-600 dark:bg-green-700 text-white rounded-lg 
                               hover:bg-green-700 dark:hover:bg-green-600 transition-colors
                               focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                    <em class="ni ni-plus mr-2"></em>Créer nouveau patient
                </button>
            </div>
        @else
            {{-- FORMULAIRE NOUVEAU PATIENT --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Nom *
                    </label>
                    <input type="text" wire:model="nom" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                  bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                  placeholder-slate-400 dark:placeholder-slate-400
                                  focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('nom') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Prénom
                    </label>
                    <input type="text" wire:model="prenom" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                  bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                  placeholder-slate-400 dark:placeholder-slate-400
                                  focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('prenom') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- GENRE EN BOUTONS GROUPÉS - DESIGN COMPACT ET ÉLÉGANT --}}
                <div class="">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                        Genre du patient *
                    </label>
                    <div class="inline-flex rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 shadow-sm" 
                         role="group" 
                         aria-label="Genre du patient">
                        
                        {{-- MADAME --}}
                        <input type="radio" 
                               wire:model.defer="sexe" 
                               value="Madame" 
                               id="sexe_Madame"
                               name="sexe"
                               class="sr-only peer/madame" 
                               autocomplete="off">
                        <label for="sexe_Madame" 
                               class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 
                                      bg-white dark:bg-slate-700 border-r border-gray-300 dark:border-slate-600 first:rounded-l-lg 
                                      hover:bg-gray-50 dark:hover:bg-slate-600 hover:text-slate-900 dark:hover:text-slate-100
                                      peer-checked/madame:bg-primary-600 peer-checked/madame:text-white peer-checked/madame:border-primary-600
                                      peer-focus/madame:ring-2 peer-focus/madame:ring-primary-500 peer-focus/madame:ring-offset-1
                                      cursor-pointer transition-all duration-200 select-none">
                            <em class="ni ni-user-woman text-base mr-2"></em>
                            Madame
                        </label>

                        {{-- MONSIEUR --}}
                        <input type="radio" 
                               wire:model.defer="sexe" 
                               value="Monsieur" 
                               id="sexe_Monsieur"
                               name="sexe"
                               class="sr-only peer/monsieur" 
                               autocomplete="off">
                        <label for="sexe_Monsieur" 
                               class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 
                                      bg-white dark:bg-slate-700 border-r border-gray-300 dark:border-slate-600
                                      hover:bg-gray-50 dark:hover:bg-slate-600 hover:text-slate-900 dark:hover:text-slate-100
                                      peer-checked/monsieur:bg-primary-600 peer-checked/monsieur:text-white peer-checked/monsieur:border-primary-600
                                      peer-focus/monsieur:ring-2 peer-focus/monsieur:ring-primary-500 peer-focus/monsieur:ring-offset-1
                                      cursor-pointer transition-all duration-200 select-none">
                            <em class="ni ni-user text-base mr-2"></em>
                            Monsieur
                        </label>

                        {{-- MADEMOISELLE --}}
                        <input type="radio" 
                               wire:model.defer="sexe" 
                               value="Mademoiselle" 
                               id="sexe_Mademoiselle"
                               name="sexe"
                               class="sr-only peer/mademoiselle" 
                               autocomplete="off">
                        <label for="sexe_Mademoiselle" 
                               class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 
                                      bg-white dark:bg-slate-700 border-r border-gray-300 dark:border-slate-600
                                      hover:bg-gray-50 dark:hover:bg-slate-600 hover:text-slate-900 dark:hover:text-slate-100
                                      peer-checked/mademoiselle:bg-primary-600 peer-checked/mademoiselle:text-white peer-checked/mademoiselle:border-primary-600
                                      peer-focus/mademoiselle:ring-2 peer-focus/mademoiselle:ring-primary-500 peer-focus/mademoiselle:ring-offset-1
                                      cursor-pointer transition-all duration-200 select-none">
                            <em class="ni ni-user-star text-base mr-2"></em>
                            <span class="hidden sm:inline">Mademoiselle</span>
                            <span class="sm:hidden">Mlle</span>
                        </label>

                        {{-- ENFANT --}}
                        <input type="radio" 
                               wire:model.defer="sexe" 
                               value="Enfant" 
                               id="sexe_Enfant"
                               name="sexe"
                               class="sr-only peer/enfant" 
                               autocomplete="off">
                        <label for="sexe_Enfant" 
                               class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 
                                      bg-white dark:bg-slate-700 last:rounded-r-lg
                                      hover:bg-gray-50 dark:hover:bg-slate-600 hover:text-slate-900 dark:hover:text-slate-100
                                      peer-checked/enfant:bg-primary-600 peer-checked/enfant:text-white peer-checked/enfant:border-primary-600
                                      peer-focus/enfant:ring-2 peer-focus/enfant:ring-primary-500 peer-focus/enfant:ring-offset-1
                                      cursor-pointer transition-all duration-200 select-none">
                            <em class="ni ni-user-child text-base mr-2"></em>
                            Enfant
                        </label>
                    </div>
                    @error('sexe') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <em class="ni ni-call mr-1 text-slate-500 dark:text-slate-400"></em>Téléphone
                    </label>
                    <input type="tel" wire:model="telephone" 
                           placeholder="Ex: +261 34 12 345 67"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                  bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                  placeholder-slate-400 dark:placeholder-slate-400
                                  focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('telephone') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <em class="ni ni-emails mr-1 text-slate-500 dark:text-slate-400"></em>Email
                    </label>
                    <input type="email" wire:model="email" 
                           placeholder="exemple@email.com"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg 
                                  bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                  placeholder-slate-400 dark:placeholder-slate-400
                                  focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('email') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            {{-- BOUTONS D'ACTION --}}
            <div class="flex justify-between mt-6">
                <button wire:click="$set('nouveauPatient', false)" 
                        class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 
                               rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors
                               focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                    <em class="ni ni-arrow-left mr-2"></em>Retour recherche
                </button>
                <button wire:click="validerNouveauPatient" 
                        class="px-6 py-2 bg-primary-600 dark:bg-primary-600 text-white rounded-lg 
                               hover:bg-primary-700 dark:hover:bg-primary-500 transition-colors
                               focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    Valider patient<em class="ni ni-arrow-right ml-2"></em>
                </button>
            </div>
            
            {{-- INFORMATIONS D'AIDE --}}
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-start">
                    <em class="ni ni-info text-blue-600 dark:text-blue-400 text-lg mr-3 mt-0.5"></em>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">
                            Informations importantes
                        </h4>
                        <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                            <li>• Les champs marqués d'un astérisque (*) sont obligatoires</li>
                            <li>• Le système vérifiera automatiquement si un patient similaire existe déjà</li>
                            <li>• Une référence unique sera automatiquement générée</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif