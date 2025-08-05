{{-- livewire.secretaire.prescription.partials.clinique --}}
@if($etape === 'clinique')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
        {{-- HEADER SECTION --}}
        <div class="bg-gradient-to-r from-cyan-50 to-blue-100 dark:from-slate-700 dark:to-slate-800 px-6 py-5 border-b border-gray-200 dark:border-slate-600">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-cyan-600 dark:bg-cyan-500 rounded-xl flex items-center justify-center shadow-lg">
                    <em class="ni ni-notes text-white text-xl"></em>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Informations Cliniques</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Renseignements médicaux et prescription</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-8">
            {{-- SECTION PRESCRIPTEUR ET TYPE --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- PRESCRIPTEUR --}}
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                        <em class="ni ni-user-md mr-2 text-cyan-500"></em>
                        Prescripteur <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <em class="ni ni-user-md text-slate-400 dark:text-slate-500"></em>
                        </div>
                        <select wire:model="prescripteurId" 
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl 
                                       bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                       focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 
                                       transition-all duration-200 shadow-sm
                                       hover:border-cyan-300 dark:hover:border-cyan-600
                                       @error('prescripteurId') border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500 @enderror">
                            <option value="" class="text-slate-400">Sélectionner un prescripteur...</option>
                            @foreach($prescripteurs as $prescripteur)
                                <option value="{{ $prescripteur->id }}" class="text-slate-900 dark:text-slate-100">
                                    {{ $prescripteur->nom }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <em class="ni ni-chevron-down text-slate-400 dark:text-slate-500"></em>
                        </div>
                    </div>
                    @error('prescripteurId') 
                        <p class="flex items-center text-red-600 dark:text-red-400 text-sm mt-1">
                            <em class="ni ni-alert-circle mr-1"></em>{{ $message }}
                        </p> 
                    @enderror
                </div>
                
                {{-- TYPE PATIENT --}}
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                        <em class="ni ni-building mr-2 text-blue-500"></em>
                        Type de patient
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <em class="ni ni-building text-slate-400 dark:text-slate-500"></em>
                        </div>
                        <select wire:model="patientType" 
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl 
                                       bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                       focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 
                                       transition-all duration-200 shadow-sm
                                       hover:border-cyan-300 dark:hover:border-cyan-600">
                            <option value="EXTERNE" class="text-slate-900 dark:text-slate-100">🏠 Externe</option>
                            <option value="HOSPITALISE" class="text-slate-900 dark:text-slate-100">🏥 Hospitalisé</option>
                            <option value="URGENCE-JOUR" class="text-slate-900 dark:text-slate-100">🚨 Urgence Jour</option>
                            <option value="URGENCE-NUIT" class="text-slate-900 dark:text-slate-100">🌙 Urgence Nuit</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <em class="ni ni-chevron-down text-slate-400 dark:text-slate-500"></em>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION ÂGE ET POIDS --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- ÂGE --}}
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                        <em class="ni ni-calendar mr-2 text-green-500"></em>
                        Âge du patient <span class="text-red-500">*</span>
                    </label>
                    <div class="flex space-x-3">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <em class="ni ni-hash text-slate-400 dark:text-slate-500"></em>
                            </div>
                            <input type="number" 
                                   wire:model="age" 
                                   min="0" 
                                   max="150"
                                   placeholder="Âge"
                                   class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl 
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 
                                          transition-all duration-200 shadow-sm
                                          hover:border-cyan-300 dark:hover:border-cyan-600
                                          @error('age') border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500 @enderror">
                        </div>
                        <div class="relative min-w-[100px]">
                            <select wire:model="uniteAge" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl 
                                           bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                           focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 
                                           transition-all duration-200 shadow-sm
                                           hover:border-cyan-300 dark:hover:border-cyan-600">
                                <option value="Ans">Ans</option>
                                <option value="Mois">Mois</option>
                                <option value="Jours">Jours</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <em class="ni ni-chevron-down text-slate-400 dark:text-slate-500 text-sm"></em>
                            </div>
                        </div>
                    </div>
                    @error('age') 
                        <p class="flex items-center text-red-600 dark:text-red-400 text-sm mt-1">
                            <em class="ni ni-alert-circle mr-1"></em>{{ $message }}
                        </p> 
                    @enderror
                </div>
                
                {{-- POIDS --}}
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                        <em class="ni ni-activity mr-2 text-orange-500"></em>
                        Poids du patient
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <em class="ni ni-activity text-slate-400 dark:text-slate-500"></em>
                        </div>
                        <input type="number" 
                               wire:model="poids" 
                               step="0.1" 
                               min="0"
                               placeholder="Ex: 65.5"
                               class="w-full pl-12 pr-16 py-3 border border-gray-300 dark:border-slate-600 rounded-xl 
                                      bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                      placeholder-slate-400 dark:placeholder-slate-500
                                      focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 
                                      transition-all duration-200 shadow-sm
                                      hover:border-cyan-300 dark:hover:border-cyan-600">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">kg</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center">
                        <em class="ni ni-info-circle mr-1"></em>
                        Optionnel - utile pour le calcul des doses
                    </p>
                </div>
            </div>
            
            {{-- RENSEIGNEMENTS CLINIQUES --}}
            <div class="space-y-3">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <em class="ni ni-clipboard mr-2 text-purple-500"></em>
                    Renseignements cliniques
                </label>
                <div class="relative">
                    <textarea wire:model="renseignementClinique" 
                              rows="5"
                              placeholder="Décrivez les symptômes, antécédents médicaux, indications spéciales, allergies connues..."
                              class="w-full px-4 py-4 border border-gray-300 dark:border-slate-600 rounded-xl 
                                     bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                     placeholder-slate-400 dark:placeholder-slate-500
                                     focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 
                                     transition-all duration-200 shadow-sm resize-none
                                     hover:border-cyan-300 dark:hover:border-cyan-600"></textarea>
                    <div class="absolute top-4 right-4 text-xs text-slate-400 dark:text-slate-500 pointer-events-none">
                        <em class="ni ni-edit"></em>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center">
                            <em class="ni ni-shield-check text-green-500 mr-1"></em>
                            Informations confidentielles
                        </span>
                        <span class="flex items-center">
                            <em class="ni ni-lock text-blue-500 mr-1"></em>
                            Données sécurisées
                        </span>
                    </div>
                    <span class="text-slate-400">{{ strlen($renseignementClinique ?? '') }} caractères</span>
                </div>
            </div>

            {{-- SECTION AIDE CONTEXTUELLE --}}
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 
                        border border-indigo-200 dark:border-indigo-800 rounded-xl p-5">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <em class="ni ni-bulb text-white"></em>
                    </div>
                    <div>
                        <h4 class="font-semibold text-indigo-800 dark:text-indigo-200 mb-2">
                            Conseils pour une prescription optimale
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-indigo-700 dark:text-indigo-300">
                            <div class="flex items-center">
                                <em class="ni ni-check-circle text-green-500 mr-2 flex-shrink-0"></em>
                                <span>Vérifiez les allergies connues</span>
                            </div>
                            <div class="flex items-center">
                                <em class="ni ni-check-circle text-green-500 mr-2 flex-shrink-0"></em>
                                <span>Notez les traitements en cours</span>
                            </div>
                            <div class="flex items-center">
                                <em class="ni ni-check-circle text-green-500 mr-2 flex-shrink-0"></em>
                                <span>Précisez la durée des symptômes</span>
                            </div>
                            <div class="flex items-center">
                                <em class="ni ni-check-circle text-green-500 mr-2 flex-shrink-0"></em>
                                <span>Indiquez l'intensité de la douleur</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- BOUTONS DE NAVIGATION --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-200 dark:border-slate-600">
                <button wire:click="allerEtape('patient')" 
                        class="w-full sm:w-auto inline-flex items-center px-6 py-3 bg-slate-100 dark:bg-slate-700 
                               text-slate-700 dark:text-slate-300 font-semibold rounded-xl 
                               hover:bg-slate-200 dark:hover:bg-slate-600 transition-all duration-200
                               focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                    <em class="ni ni-arrow-left mr-2"></em>
                    Retour Patient
                </button>
                
                <div class="flex items-center text-sm text-slate-500 dark:text-slate-400">
                    <div class="flex space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <div class="w-2 h-2 bg-cyan-500 rounded-full"></div>
                        <div class="w-2 h-2 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
                    </div>
                    <span class="ml-2">Étape 2/3</span>
                </div>
                
                <button wire:click="validerInformationsCliniques" 
                        class="w-full sm:w-auto inline-flex items-center px-8 py-3 bg-gradient-to-r from-cyan-600 to-blue-700 
                        hover:from-cyan-700 hover:to-blue-800 text-white font-semibold rounded-xl 
                        shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <em class="ni ni-flask mr-2"></em>
                    Continuer vers Analyses
                    <em class="ni ni-arrow-right ml-2"></em>
                </button>
            </div>
        </div>
    </div>
@endif