    {{-- livewire.secretaire.prescription.partials.analyses --}}
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