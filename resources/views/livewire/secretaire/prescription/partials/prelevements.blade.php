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