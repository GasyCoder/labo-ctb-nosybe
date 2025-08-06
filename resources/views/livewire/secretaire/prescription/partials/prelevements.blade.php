{{-- livewire.secretaire.prescription.partials.prelevements --}}
@if($etape === 'prelevements')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
        {{-- HEADER SECTION ADAPTATIF --}}
        <div class="bg-gradient-to-r {{ $isEditMode ? 'from-orange-50 to-amber-100' : 'from-yellow-50 to-orange-100' }} dark:from-slate-700 dark:to-slate-800 px-6 py-5 border-b border-gray-200 dark:border-slate-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 {{ $isEditMode ? 'bg-orange-600' : 'bg-yellow-600' }} dark:bg-yellow-500 rounded-xl flex items-center justify-center shadow-lg">
                        <em class="ni ni-package text-white text-xl"></em>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                            {{ $isEditMode ? 'Modification Prélèvements' : 'Prélèvements' }}
                        </h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $isEditMode ? 'Modifier les prélèvements requis' : 'Sélection optionnelle des prélèvements requis' }}
                        </p>
                    </div>
                </div>
                @if(count($prelevementsSelectionnes) > 0)
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1.5 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }} rounded-full text-sm font-semibold">
                            {{ count($prelevementsSelectionnes) }} sélectionnés
                        </span>
                        <button wire:click="$set('prelevementsSelectionnes', [])" 
                                wire:confirm="{{ $isEditMode ? 'Supprimer tous les prélèvements de cette prescription ?' : 'Vider la sélection de prélèvements ?' }}"
                                class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all">
                            <em class="ni ni-trash text-lg"></em>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="p-6">
            {{-- ALERTE MODE ÉDITION --}}
            @if($isEditMode)
                <div class="mb-6 p-4 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 border border-orange-200 dark:border-orange-800 rounded-xl">
                    <div class="flex items-center">
                        <em class="ni ni-edit text-orange-600 mr-3"></em>
                        <div>
                            <h4 class="font-semibold text-orange-800 dark:text-orange-200">Modification des prélèvements</h4>
                            <p class="text-sm text-orange-600 dark:text-orange-300 mt-1">
                                Modifiez les prélèvements existants ou ajoutez-en de nouveaux. Les tubes seront régénérés automatiquement.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                {{-- SÉLECTION PRÉLÈVEMENTS --}}
                <div class="xl:col-span-2 space-y-6">
                    {{-- RECHERCHE PRÉLÈVEMENTS --}}
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                            <em class="ni ni-search mr-2 {{ $isEditMode ? 'text-orange-500' : 'text-yellow-500' }}"></em>
                            {{ $isEditMode ? 'Rechercher/modifier un prélèvement' : 'Rechercher un prélèvement' }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <em class="ni ni-search text-slate-400 dark:text-slate-500"></em>
                            </div>
                            <input type="text" 
                                   wire:model.live="recherchePrelevement" 
                                   placeholder="Tapez le nom du prélèvement..."
                                   class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl 
                                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'yellow' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'yellow' }}-500 
                                          transition-all duration-200 shadow-sm
                                          hover:border-{{ $isEditMode ? 'orange' : 'yellow' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'yellow' }}-600">
                            @if($recherchePrelevement)
                                <button wire:click="$set('recherchePrelevement', '')" 
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                                    <em class="ni ni-times text-lg"></em>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    {{-- RÉSULTATS RECHERCHE PRÉLÈVEMENTS --}}
                    @if($prelevementsRecherche->count() > 0)
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center">
                                <em class="ni ni-check-circle text-green-500 mr-2"></em>
                                {{ $prelevementsRecherche->count() }} résultat(s) trouvé(s)
                            </h3>
                            <div class="space-y-3">
                                @foreach($prelevementsRecherche as $prelevement)
                                    <div class="group p-4 border border-gray-200 dark:border-slate-600 rounded-xl 
                                               hover:border-{{ $isEditMode ? 'orange' : 'yellow' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'yellow' }}-500 
                                               hover:bg-gradient-to-r hover:from-{{ $isEditMode ? 'orange' : 'yellow' }}-50 hover:to-transparent 
                                               dark:hover:from-slate-700 dark:hover:to-transparent
                                               transition-all duration-300 transform hover:scale-[1.01] hover:shadow-md
                                               {{ isset($prelevementsSelectionnes[$prelevement->id]) ? ($isEditMode ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-300 dark:border-orange-600' : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-600') : '' }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4 flex-1">
                                                <div class="w-10 h-10 bg-gradient-to-br from-{{ $isEditMode ? 'orange' : 'yellow' }}-500 to-{{ $isEditMode ? 'amber' : 'orange' }}-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                                                    <em class="ni ni-flask"></em>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-slate-800 dark:text-slate-100">{{ $prelevement->nom }}</h4>
                                                    @if($prelevement->description)
                                                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ $prelevement->description }}</p>
                                                    @endif
                                                    <div class="flex items-center mt-2 space-x-4">
                                                        <span class="inline-flex items-center px-2 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }} rounded-full text-xs font-medium">
                                                            <em class="ni ni-money mr-1"></em>
                                                            {{ number_format($prelevement->prix, 0) }} Ar
                                                        </span>
                                                        @if(isset($prelevementsSelectionnes[$prelevement->id]))
                                                            <span class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-medium">
                                                                <em class="ni ni-check mr-1"></em>
                                                                Qté: {{ $prelevementsSelectionnes[$prelevement->id]['quantite'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <button wire:click="ajouterPrelevement({{ $prelevement->id }})" 
                                                    class="px-4 py-2 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105
                                                    {{ isset($prelevementsSelectionnes[$prelevement->id]) 
                                                       ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 cursor-not-allowed' 
                                                       : ($isEditMode ? 'bg-orange-600 hover:bg-orange-700' : 'bg-yellow-600 hover:bg-yellow-700') . ' text-white shadow-lg hover:shadow-xl' }}"
                                                    {{ isset($prelevementsSelectionnes[$prelevement->id]) ? 'disabled' : '' }}>
                                                <em class="ni ni-{{ isset($prelevementsSelectionnes[$prelevement->id]) ? 'check' : 'plus' }} mr-2"></em>
                                                {{ isset($prelevementsSelectionnes[$prelevement->id]) ? 'Ajouté' : ($isEditMode ? 'Modifier' : 'Ajouter') }}
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- TOUS LES PRÉLÈVEMENTS DISPONIBLES --}}
                    @if(strlen($recherchePrelevement) < 2)
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center">
                                <em class="ni ni-package mr-2 {{ $isEditMode ? 'text-orange-500' : 'text-yellow-500' }}"></em>
                                {{ $isEditMode ? 'Prélèvements disponibles pour modification' : 'Prélèvements disponibles' }}
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                @foreach($prelevementsDisponibles as $prelevement)
                                    <div class="group p-4 border border-gray-200 dark:border-slate-600 rounded-xl 
                                               hover:border-{{ $isEditMode ? 'orange' : 'yellow' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'yellow' }}-500 
                                               hover:bg-gradient-to-r hover:from-{{ $isEditMode ? 'orange' : 'yellow' }}-50 hover:to-transparent 
                                               dark:hover:from-slate-700 dark:hover:to-transparent
                                               transition-all duration-300 transform hover:scale-[1.01] hover:shadow-md
                                               {{ isset($prelevementsSelectionnes[$prelevement->id]) ? ($isEditMode ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-300 dark:border-orange-600' : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-600') : '' }}">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start space-x-3 flex-1">
                                                <div class="w-8 h-8 bg-gradient-to-br from-{{ $isEditMode ? 'orange' : 'yellow' }}-500 to-{{ $isEditMode ? 'amber' : 'orange' }}-600 rounded-lg flex items-center justify-center text-white shadow-md flex-shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v6l-4.5 8A2 2 0 006.5 21h11a2 2 0 001.5-4l-4.5-8V3m-6 0h6" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-slate-800 dark:text-slate-100 text-sm">{{ $prelevement->nom }}</h4>
                                                    @if($prelevement->description)
                                                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 line-clamp-2">{{ $prelevement->description }}</p>
                                                    @endif
                                                    <div class="flex items-center justify-between mt-2">
                                                        <div class="inline-flex items-center px-2 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }} rounded-full text-xs font-medium">
                                                            <em class="ni ni-money mr-1"></em>
                                                            {{ number_format($prelevement->prix, 0) }} Ar
                                                        </div>
                                                        @if(isset($prelevementsSelectionnes[$prelevement->id]))
                                                            <span class="text-xs text-green-600 dark:text-green-400 font-medium">
                                                                ✓ Qté: {{ $prelevementsSelectionnes[$prelevement->id]['quantite'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <button wire:click="ajouterPrelevement({{ $prelevement->id }})" 
                                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105 ml-3
                                                    {{ isset($prelevementsSelectionnes[$prelevement->id]) 
                                                       ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 cursor-not-allowed' 
                                                       : ($isEditMode ? 'bg-orange-600 hover:bg-orange-700' : 'bg-yellow-600 hover:bg-yellow-700') . ' text-white shadow-md hover:shadow-lg' }}"
                                                    {{ isset($prelevementsSelectionnes[$prelevement->id]) ? 'disabled' : '' }}>
                                                <em class="ni ni-{{ isset($prelevementsSelectionnes[$prelevement->id]) ? 'check' : 'plus' }}"></em>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                {{-- PRÉLÈVEMENTS SÉLECTIONNÉS --}}
                <div class="space-y-6">
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-slate-800 dark:text-slate-100 flex items-center">
                                <em class="ni ni-package mr-2 {{ $isEditMode ? 'text-orange-500' : 'text-yellow-500' }}"></em>
                                {{ $isEditMode ? 'Prélèvements modifiés' : 'Sélectionnés' }}
                            </h3>
                        </div>
                        
                        @if(count($prelevementsSelectionnes) > 0)
                            <div class="space-y-3">
                                @foreach($prelevementsSelectionnes as $prelevement)
                                    <div class="p-4 bg-white dark:bg-slate-800 border {{ $isEditMode ? 'border-orange-200 dark:border-orange-700' : 'border-yellow-200 dark:border-yellow-700' }} rounded-xl shadow-sm">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <div class="font-semibold text-slate-800 dark:text-slate-100 text-sm">{{ $prelevement['nom'] }}</div>
                                                @if($prelevement['description'])
                                                    <div class="text-slate-500 dark:text-slate-400 text-xs mt-1">{{ $prelevement['description'] }}</div>
                                                @endif
                                            </div>
                                            <button wire:click="retirerPrelevement({{ $prelevement['id'] }})" 
                                                    wire:confirm="{{ $isEditMode ? 'Retirer ce prélèvement de la prescription ?' : 'Retirer ce prélèvement ?' }}"
                                                    class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all">
                                                <em class="ni ni-cross text-sm"></em>
                                            </button>
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-slate-600 dark:text-slate-400 text-xs">Quantité:</span>
                                                <input type="number" 
                                                       wire:change="modifierQuantitePrelevement({{ $prelevement['id'] }}, $event.target.value)"
                                                       value="{{ $prelevement['quantite'] }}" 
                                                       min="1" max="10"
                                                       class="w-16 px-2 py-1 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm
                                                              bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                                              focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'yellow' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'yellow' }}-500">
                                            </div>
                                            <div class="font-semibold {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-yellow-600 dark:text-yellow-400' }} text-sm">
                                                {{ number_format($prelevement['prix'] * $prelevement['quantite'], 0) }} Ar
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            {{-- TOTAL PRÉLÈVEMENTS --}}
                            <div class="border-t border-gray-200 dark:border-slate-600 pt-4 mt-4">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-800 dark:text-slate-100">Total prélèvements:</span>
                                    <span class="font-bold {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-yellow-600 dark:text-yellow-400' }} text-lg">
                                        {{ number_format(collect($prelevementsSelectionnes)->sum(fn($p) => $p['prix'] * $p['quantite']), 0) }} Ar
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-slate-500 dark:text-slate-400">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <em class="ni ni-package text-2xl"></em>
                                </div>
                                <p class="text-sm font-medium mb-1">
                                    {{ $isEditMode ? 'Aucun prélèvement dans cette prescription' : 'Aucun prélèvement sélectionné' }}
                                </p>
                                <p class="text-xs">
                                    {{ $isEditMode ? 'Utilisez la recherche pour en ajouter' : 'Les prélèvements sont optionnels' }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- AIDE CONTEXTUELLE MODE ÉDITION --}}
                    @if($isEditMode)
                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 
                                    border border-orange-200 dark:border-orange-800 rounded-xl p-4">
                            <h4 class="font-semibold text-orange-800 dark:text-orange-200 mb-2 flex items-center">
                                <em class="ni ni-info mr-2"></em>
                                Modification des prélèvements
                            </h4>
                            <div class="text-sm text-orange-700 dark:text-orange-300 space-y-1">
                                <p>• Les tubes existants seront supprimés</p>
                                <p>• De nouveaux tubes seront générés</p>
                                <p>• Les quantités peuvent être ajustées</p>
                                <p>• Le coût total sera recalculé</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- BOUTONS DE NAVIGATION --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-200 dark:border-slate-600 mt-8">
                <button wire:click="allerEtape('analyses')" 
                        class="w-full sm:w-auto inline-flex items-center px-6 py-3 bg-slate-100 dark:bg-slate-700 
                               text-slate-700 dark:text-slate-300 font-semibold rounded-xl 
                               hover:bg-slate-200 dark:hover:bg-slate-600 transition-all duration-200
                               focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                    <em class="ni ni-arrow-left mr-2"></em>
                    Retour Analyses
                </button>
                
                <div class="flex items-center text-sm text-slate-500 dark:text-slate-400">
                    <div class="flex space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <div class="w-2 h-2 bg-cyan-500 rounded-full"></div>
                        <div class="w-2 h-2 {{ $isEditMode ? 'bg-orange-500' : 'bg-yellow-500' }} rounded-full"></div>
                        <div class="w-2 h-2 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
                    </div>
                    <span class="ml-2">Étape 4/7</span>
                </div>
                
                <button wire:click="validerPrelevements" 
                    class="w-full sm:w-auto inline-flex items-center px-8 py-3 bg-{{ $isEditMode ? 'green' : 'blue' }}-600 text-white font-semibold rounded-lg">
                <em class="ni ni-sign-cc-alt2 mr-2"></em>
                @if(count($prelevementsSelectionnes) > 0)
                    {{ $isEditMode ? 'Modifier avec ' . count($prelevementsSelectionnes) . ' prélèvement(s)' : 'Ajouter ' . count($prelevementsSelectionnes) . ' prélèvement(s)' }}
                @else
                    {{ $isEditMode ? 'Mettre à jour sans prélèvement' : 'Continuer sans prélèvement' }}
                @endif
                <em class="ni ni-arrow-right ml-2"></em>
            </button>
            </div>
        </div>
    </div>
@endif