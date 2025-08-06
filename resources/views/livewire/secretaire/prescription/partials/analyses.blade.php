{{-- livewire.secretaire.prescription.partials.analyses --}}
@if($etape === 'analyses')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- RECHERCHE ANALYSES --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <em class="ni ni-test-tube {{ $isEditMode ? 'text-orange-600' : 'text-green-600' }} text-xl mr-3"></em>
                        <h2 class="text-xl font-heading font-semibold text-slate-800 dark:text-slate-100">
                            {{ $isEditMode ? 'Modification Analyses' : 'Recherche Analyses' }}
                        </h2>
                    </div>
                    @if(count($analysesPanier) > 0)
                        <span class="px-3 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' }} rounded-full text-sm font-medium">
                            {{ count($analysesPanier) }} sélectionnées
                        </span>
                    @endif
                </div>

                {{-- ALERTE MODE ÉDITION --}}
                @if($isEditMode)
                    <div class="mb-6 p-4 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 border border-orange-200 dark:border-orange-800 rounded-xl">
                        <div class="flex items-center">
                            <em class="ni ni-edit text-orange-600 mr-3"></em>
                            <div>
                                <h4 class="font-semibold text-orange-800 dark:text-orange-200">Modification des analyses</h4>
                                <p class="text-sm text-orange-600 dark:text-orange-300 mt-1">
                                    Ajoutez, retirez ou modifiez les analyses sélectionnées. Les changements remplaceront la sélection actuelle.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                {{-- RECHERCHE OBLIGATOIRE --}}
                <div class="mb-6">
                    <div class="relative">
                        <em class="ni ni-search absolute left-3 top-3 text-slate-400"></em>
                        <input type="text" wire:model.live="rechercheAnalyse" 
                            placeholder="Rechercher par CODE (ex: NFS, GLY, URE) ou DESIGNATION (ex: HÉMOGRAMME, GLYCÉMIE)..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-lg 
                                    bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                    focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'green' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'green' }}-500
                                    hover:border-{{ $isEditMode ? 'orange' : 'green' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'green' }}-600 transition-all duration-200">
                    </div>
                    @if(strlen($rechercheAnalyse) > 0 && strlen($rechercheAnalyse) < 2)
                        <p class="text-yellow-600 dark:text-yellow-400 text-sm mt-2">
                            <em class="ni ni-info mr-1"></em>Tapez au moins 2 caractères pour commencer la recherche
                        </p>
                    @endif
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="px-3 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' }} rounded-full text-xs">
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
                                            : ($isEditMode ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700') . ' text-white' }}"
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
                        <p class="text-lg text-slate-600 dark:text-slate-300 mb-2">
                            {{ $isEditMode ? 'Modification des analyses' : 'Recherche d\'analyses' }}
                        </p>
                        <p class="text-slate-500 dark:text-slate-400">
                            {{ $isEditMode ? 'Recherchez pour ajouter/modifier des analyses' : 'Tapez dans le champ ci-dessus pour rechercher des analyses' }}
                        </p>
                    </div>
                @endif
                
                <div class="flex justify-between mt-6">
                 <button wire:click="allerEtape('clinique')" 
                            class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">
                        <em class="ni ni-arrow-left mr-2"></em>Clinique
                    </button>
                    <button wire:click="validerAnalyses" 
                            class="px-6 py-2 {{ $isEditMode ? 'bg-green-600 hover:bg-green-700' : 'bg-primary-600 hover:bg-primary-700' }} text-white rounded-lg transition-colors">
                        {{ $isEditMode ? 'Modifier prélèvements' : 'Prélèvements' }}<em class="ni ni-arrow-right ml-2"></em>
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
                                wire:confirm="{{ $isEditMode ? 'Voulez-vous vraiment supprimer toutes les analyses ?' : 'Voulez-vous vider le panier ?' }}"
                                class="text-red-500 hover:text-red-700 text-sm transition-colors">
                            <em class="ni ni-trash"></em>
                        </button>
                    @endif
                </div>

                {{-- ANCIEN PANIER EN MODE ÉDITION --}}
                @if($isEditMode && isset($prescription) && count($analysesPanier) > 0)
                    <div class="mb-4 p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                        <h4 class="text-sm font-semibold text-orange-800 dark:text-orange-200 mb-2">
                            <em class="ni ni-info mr-1"></em>Sélection actuelle
                        </h4>
                        <p class="text-xs text-orange-600 dark:text-orange-300">
                            {{ count($analysesPanier) }} analyse(s) dans cette prescription
                        </p>
                    </div>
                @endif
                
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
                                    
                                    {{-- BADGE MODE ÉDITION --}}
                                    @if($isEditMode && isset($analyse['is_parent']) && $analyse['is_parent'])
                                        <span class="inline-block mt-1 px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 rounded-full text-xs">
                                            Panel complet
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right ml-2">
                                    <div class="font-medium text-slate-700 dark:text-slate-300">
                                        {{ $analyse['prix_effectif'] > 0 ? number_format($analyse['prix_effectif'], 0) . ' Ar' : 'Inclus' }}
                                    </div>
                                    <button wire:click="retirerAnalyse({{ $analyse['id'] }})" 
                                            wire:confirm="{{ $isEditMode ? 'Retirer cette analyse de la prescription ?' : 'Retirer du panier ?' }}"
                                            class="text-red-500 hover:text-red-700 text-xs transition-colors">
                                        <em class="ni ni-cross"></em>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="border-t border-gray-200 dark:border-slate-600 pt-3">
                        <div class="flex justify-between font-bold text-lg">
                            <span class="text-slate-800 dark:text-slate-100">Total:</span>
                            <span class="{{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ number_format($total, 0) }} Ar
                            </span>
                        </div>
                        
                        {{-- COMPARAISON EN MODE ÉDITION --}}
                        @if($isEditMode && isset($prescription))
                            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                <em class="ni ni-info mr-1"></em>
                                Vous êtes en train de modifier les analyses de cette prescription
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-6 text-slate-500 dark:text-slate-400">
                        <em class="ni ni-bag text-2xl mb-2"></em>
                        <p class="text-sm">
                            {{ $isEditMode ? 'Aucune analyse sélectionnée pour cette prescription' : 'Aucune analyse sélectionnée' }}
                        </p>
                        @if($isEditMode)
                            <p class="text-xs mt-1">Utilisez la recherche pour ajouter des analyses</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- AIDE CONTEXTUELLE MODE ÉDITION --}}
            @if($isEditMode)
                <div class="mt-4 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 
                            border border-orange-200 dark:border-orange-800 rounded-xl p-4">
                    <h4 class="font-semibold text-orange-800 dark:text-orange-200 mb-2 flex items-center">
                        <em class="ni ni-edit mr-2"></em>
                        Conseils modification
                    </h4>
                    <div class="text-sm text-orange-700 dark:text-orange-300 space-y-1">
                        <p>• Les analyses retirées ne seront plus facturées</p>
                        <p>• Les nouvelles analyses s'ajoutent au total</p>
                        <p>• Les tubes seront régénérés si nécessaire</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif