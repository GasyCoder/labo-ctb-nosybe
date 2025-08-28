{{-- livewire.secretaire.prescription.partials.clinique --}}
@if($etape === 'clinique')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        {{-- HEADER SECTION ADAPTATIF --}}
        <div class="bg-gradient-to-r {{ $isEditMode ? 'from-orange-50 to-amber-50' : 'from-cyan-50 to-blue-50' }} dark:from-slate-700 dark:to-slate-800 px-4 py-3 border-b border-gray-100 dark:border-slate-600">
            <div class="flex items-center">
                <div class="w-8 h-8 {{ $isEditMode ? 'bg-orange-500' : 'bg-cyan-500' }} rounded-lg flex items-center justify-center">
                    <em class="ni ni-notes text-white text-sm"></em>
                </div>
                <div class="ml-3">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">
                        {{ $isEditMode ? 'Modification Informations Cliniques' : 'Informations Cliniques' }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $isEditMode ? 'Modifier les renseignements médicaux et prescription' : 'Renseignements médicaux et prescription' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-4 space-y-4">
            {{-- SECTION PRESCRIPTEUR ET TYPE --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- PRESCRIPTEUR --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                        <em class="ni ni-user-md mr-1.5 {{ $isEditMode ? 'text-orange-500' : 'text-cyan-500' }} text-xs"></em>
                        Prescripteur <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <em class="ni ni-user-md text-slate-400 dark:text-slate-500 text-sm"></em>
                        </div>
                        <select wire:model="prescripteurId" 
                                class="w-full pl-9 pr-8 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                       bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                       focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'cyan' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'cyan' }}-500 
                                       transition-all duration-200
                                       hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-600
                                       @error('prescripteurId') border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500 @enderror">
                            <option value="" class="text-slate-400">Sélectionner un prescripteur...</option>
                            @foreach($prescripteurs as $prescripteur)
                                <option value="{{ $prescripteur->id }}" class="text-slate-900 dark:text-slate-100">
                                    {{ $prescripteur->nom }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <em class="ni ni-chevron-down text-slate-400 dark:text-slate-500 text-xs"></em>
                        </div>
                    </div>
                    @error('prescripteurId') 
                        <p class="flex items-center text-red-600 dark:text-red-400 text-xs mt-1">
                            <em class="ni ni-alert-circle mr-1 text-xs"></em>{{ $message }}
                        </p> 
                    @enderror
                </div>
                
                {{-- TYPE PATIENT --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                        <em class="ni ni-building mr-1.5 text-blue-500 text-xs"></em>
                        Type de patient
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <em class="ni ni-building text-slate-400 dark:text-slate-500 text-sm"></em>
                        </div>
                        <select wire:model="patientType" 
                                class="w-full pl-9 pr-8 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                       bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                       focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'cyan' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'cyan' }}-500 
                                       transition-all duration-200
                                       hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-600">
                            <option value="EXTERNE" class="text-slate-900 dark:text-slate-100">🏠 Externe</option>
                            <option value="HOSPITALISE" class="text-slate-900 dark:text-slate-100">🏥 Hospitalisé</option>
                            <option value="URGENCE-JOUR" class="text-slate-900 dark:text-slate-100">🚨 Urgence Jour</option>
                            <option value="URGENCE-NUIT" class="text-slate-900 dark:text-slate-100">🌙 Urgence Nuit</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <em class="ni ni-chevron-down text-slate-400 dark:text-slate-500 text-xs"></em>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION ÂGE ET POIDS --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- ÂGE --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                        <em class="ni ni-calendar mr-1.5 text-green-500 text-xs"></em>
                        Âge du patient <span class="text-red-500">*</span>
                    </label>
                    <div class="flex space-x-2">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <em class="ni ni-hash text-slate-400 dark:text-slate-500 text-sm"></em>
                            </div>
                            <input type="number" 
                                   wire:model="age" 
                                   min="0" 
                                   max="150"
                                   placeholder="Âge"
                                   class="w-full pl-9 pr-3 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'cyan' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'cyan' }}-500 
                                          transition-all duration-200
                                          hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-600
                                          @error('age') border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500 @enderror">
                        </div>
                        <div class="relative w-20">
                            <select wire:model="uniteAge" 
                                    class="w-full px-2 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-xs
                                           bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                           focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'cyan' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'cyan' }}-500 
                                           transition-all duration-200
                                           hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-600">
                                <option value="Ans">Ans</option>
                                <option value="Mois">Mois</option>
                                <option value="Jours">Jours</option>
                            </select>
                        </div>
                    </div>
                    @error('age') 
                        <p class="flex items-center text-red-600 dark:text-red-400 text-xs mt-1">
                            <em class="ni ni-alert-circle mr-1 text-xs"></em>{{ $message }}
                        </p> 
                    @enderror
                </div>
                
                {{-- POIDS --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                        <em class="ni ni-activity mr-1.5 text-orange-500 text-xs"></em>
                        Poids du patient
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <em class="ni ni-activity text-slate-400 dark:text-slate-500 text-sm"></em>
                        </div>
                        <input type="number" 
                               wire:model="poids" 
                               step="0.1" 
                               min="0"
                               placeholder="Ex: 65.5"
                               class="w-full pl-9 pr-12 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                      bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                      placeholder-slate-400 dark:placeholder-slate-500
                                      focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'cyan' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'cyan' }}-500 
                                      transition-all duration-200
                                      hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-600">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">kg</span>
                        </div>
                    </div>
                    <p class="text-xxs text-slate-500 dark:text-slate-400 flex items-center">
                        <em class="ni ni-info-circle mr-1 text-xs"></em>
                        Optionnel - utile pour le calcul des doses
                    </p>
                </div>
            </div>
            
            {{-- RENSEIGNEMENTS CLINIQUES --}}
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    <em class="ni ni-clipboard mr-1.5 text-purple-500 text-xs"></em>
                    Renseignements cliniques
                </label>
                <div class="relative">
                    <textarea wire:model="renseignementClinique" 
                              rows="4"
                              placeholder="Décrivez les symptômes, antécédents médicaux, indications spéciales, allergies connues..."
                              class="w-full px-3 py-3 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                     bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                     placeholder-slate-400 dark:placeholder-slate-500
                                     focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'cyan' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'cyan' }}-500 
                                     transition-all duration-200 resize-none
                                     hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'cyan' }}-600"></textarea>
                    <div class="absolute top-3 right-3 text-xs text-slate-400 dark:text-slate-500 pointer-events-none">
                        <em class="ni ni-edit text-xs"></em>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xxs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center space-x-3">
                        <span class="flex items-center">
                            <em class="ni ni-shield-check text-green-500 mr-1 text-xs"></em>
                            Informations confidentielles
                        </span>
                        <span class="flex items-center">
                            <em class="ni ni-lock text-blue-500 mr-1 text-xs"></em>
                            Données sécurisées
                        </span>
                    </div>
                    <span class="text-slate-400">{{ strlen($renseignementClinique ?? '') }} caractères</span>
                </div>
            </div>

            {{-- SECTION AIDE CONTEXTUELLE --}}
            <div class="bg-indigo-50/50 dark:bg-indigo-900/10 border border-indigo-200/50 dark:border-indigo-800/50 rounded-lg p-3">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                        <em class="ni ni-bulb text-white text-xs"></em>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-indigo-800 dark:text-indigo-200 mb-2 text-sm">
                            {{ $isEditMode ? 'Conseils pour une modification optimale' : 'Conseils pour une prescription optimale' }}
                        </h4>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-indigo-700 dark:text-indigo-300">
                            <div class="flex items-center">
                                <em class="ni ni-check-circle text-green-500 mr-1.5 text-xs"></em>
                                <span>{{ $isEditMode ? 'Vérifiez les nouvelles allergies' : 'Vérifiez les allergies connues' }}</span>
                            </div>
                            <div class="w-px h-3 bg-indigo-300 dark:bg-indigo-600 hidden sm:block"></div>
                            <div class="flex items-center">
                                <em class="ni ni-check-circle text-green-500 mr-1.5 text-xs"></em>
                                <span>{{ $isEditMode ? 'Mettez à jour les traitements' : 'Notez les traitements en cours' }}</span>
                            </div>
                            <div class="w-px h-3 bg-indigo-300 dark:bg-indigo-600 hidden sm:block"></div>
                            <div class="flex items-center">
                                <em class="ni ni-check-circle text-green-500 mr-1.5 text-xs"></em>
                                <span>{{ $isEditMode ? 'Adaptez l\'évolution clinique' : 'Précisez la durée des symptômes' }}</span>
                            </div>
                            <div class="w-px h-3 bg-indigo-300 dark:bg-indigo-600 hidden sm:block"></div>
                            <div class="flex items-center">
                                <em class="ni ni-check-circle text-green-500 mr-1.5 text-xs"></em>
                                <span>{{ $isEditMode ? 'Réajustez si nécessaire' : 'Indiquez l\'intensité de la douleur' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOUTONS DE NAVIGATION --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-4 border-t border-gray-100 dark:border-slate-600">
                <button wire:click="allerEtape('patient')" 
                        class="w-full sm:w-auto inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-slate-700 
                               text-gray-700 dark:text-slate-300 font-medium rounded-lg 
                               hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors text-sm
                               focus:ring-2 focus:ring-gray-500 focus:ring-offset-1 dark:focus:ring-offset-slate-800">
                    <em class="ni ni-arrow-left mr-1.5 text-xs"></em>
                    Retour Patient
                </button>
                
                <div class="flex items-center text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex space-x-1">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                        <div class="w-1.5 h-1.5 {{ $isEditMode ? 'bg-orange-500' : 'bg-cyan-500' }} rounded-full"></div>
                        <div class="w-1.5 h-1.5 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
                    </div>
                    <span class="ml-2">Étape 2/7</span>
                </div>
                
                <button wire:click="validerInformationsCliniques" 
                        class="w-full sm:w-auto inline-flex items-center px-4 py-2 {{ $isEditMode ? 'bg-green-500 hover:bg-green-600 focus:ring-green-500' : 'bg-primary-500 hover:bg-primary-600 focus:ring-primary-500' }} 
                               text-white font-medium rounded-lg transition-colors text-sm
                               focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-slate-800
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ $isEditMode ? 'Modifier les Analyses' : 'Continuer vers Analyses' }}
                    <em class="ni ni-arrow-right ml-1.5 text-xs"></em>
                </button>
            </div>
        </div>
    </div>
@endif