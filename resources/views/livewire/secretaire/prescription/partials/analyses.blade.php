{{-- livewire.secretaire.prescription.partials.analyses --}}
@if($etape === 'analyses')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- RECHERCHE ANALYSES --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <em class="ni ni-test-tube {{ $isEditMode ? 'text-orange-500' : 'text-green-500' }} text-sm mr-2"></em>
                        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">
                            {{ $isEditMode ? 'Modification Analyses' : 'Recherche Analyses' }}
                        </h2>
                    </div>
                    @if(count($analysesPanier) > 0)
                        <span class="px-2 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-200' : 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200' }} rounded-full text-xs font-medium">
                            {{ count($analysesPanier) }} sélectionnées
                        </span>
                    @endif
                </div>

                {{-- ALERTE MODE ÉDITION --}}
                @if($isEditMode)
                    <div class="mb-4 p-3 bg-orange-50/50 dark:bg-orange-900/10 border border-orange-200/50 dark:border-orange-800/50 rounded-lg">
                        <div class="flex items-center">
                            <em class="ni ni-edit text-orange-500 mr-2 text-sm"></em>
                            <div>
                                <h4 class="font-medium text-orange-800 dark:text-orange-200 text-sm">Modification des analyses</h4>
                                <p class="text-xs text-orange-600 dark:text-orange-300 mt-0.5">
                                    Ajoutez, retirez ou modifiez les analyses sélectionnées. Les changements remplaceront la sélection actuelle.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                {{-- RECHERCHE OBLIGATOIRE --}}
                <div class="mb-4">
                    <div class="relative">
                        <em class="ni ni-search absolute left-3 top-2.5 text-slate-400 text-sm"></em>
                        <input type="text" wire:model.live="rechercheAnalyse" 
                            placeholder="Rechercher par CODE (ex: NFS, GLY, URE) ou DESIGNATION (ex: HÉMOGRAMME, GLYCÉMIE)..."
                            class="w-full pl-9 pr-3 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                    bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                    focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'green' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'green' }}-500
                                    hover:border-{{ $isEditMode ? 'orange' : 'green' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'green' }}-600 transition-colors">
                    </div>
                    @if(strlen($rechercheAnalyse) > 0 && strlen($rechercheAnalyse) < 2)
                        <p class="text-yellow-600 dark:text-yellow-400 text-xs mt-1">
                            <em class="ni ni-info mr-1 text-xs"></em>Tapez au moins 2 caractères pour commencer la recherche
                        </p>
                    @endif
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span class="px-2 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-200' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-200' }} rounded-full text-xxs">
                            💡 Exemples: NFS, GLY, URE, HÉMOGRAMME, GLYCÉMIE
                        </span>
                    </div>
                </div>
                
                {{-- RÉSULTATS RECHERCHE --}}
                @if($analysesRecherche->count() > 0)
                    <div class="space-y-2">
                        <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <em class="ni ni-search mr-1 text-xs"></em>{{ $analysesRecherche->count() }} résultat(s) trouvé(s)
                            @if($parentRecherche)
                                <span class="text-xxs text-gray-500">pour "{{ $parentRecherche->designation }} ({{ $parentRecherche->code }})"</span>
                            @endif
                        </h3>

                        {{-- Grouper les résultats par type --}}
                        @php
                            $analysesDirectes = $analysesRecherche->where('recherche_directe', true);
                            $autresAnalyses = $analysesRecherche->where('recherche_directe', '!=', true);
                        @endphp

                        {{-- ANALYSES TROUVÉES DIRECTEMENT (priorité) --}}
                        @if($analysesDirectes->count() > 0)
                            <div class="mb-3">
                                <h4 class="text-xs font-medium text-green-700 dark:text-green-300 mb-2 flex items-center">
                                    <em class="ni ni-target mr-1 text-xs"></em>
                                    Correspondances directes ({{ $analysesDirectes->count() }})
                                    <span class="ml-2 px-1.5 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-200 rounded-full text-xxs">
                                        Recommandé
                                    </span>
                                </h4>
                                
                                @foreach($analysesDirectes as $analyse)
                                    <div class="flex justify-between items-center p-2.5 
                                                border-l-4 border-green-400 
                                                bg-green-50/50 dark:bg-green-900/10 
                                                border border-green-200 dark:border-green-800 
                                                rounded-lg mb-2 hover:bg-green-100/50 dark:hover:bg-green-900/20 transition-colors">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-1.5 py-0.5 bg-green-500 text-white rounded font-mono text-xs font-bold">
                                                    {{ $analyse->code }}
                                                </span>
                                                <div>
                                                    <span class="font-medium text-slate-800 dark:text-slate-100 text-sm">
                                                        {{ $analyse->designation }}
                                                    </span>
                                                    <div class="text-slate-500 dark:text-slate-400 text-xs flex items-center">
                                                        @if($analyse->parent)
                                                            <em class="ni ni-hierarchy mr-1 text-xs"></em>
                                                            {{ $analyse->parent->designation }}
                                                            @if($analyse->parent->prix > 0)
                                                                <span class="ml-1 px-1 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-200 rounded text-xxs">
                                                                    Panel disponible
                                                                </span>
                                                            @endif
                                                        @else
                                                            <em class="ni ni-single mr-1 text-xs"></em>
                                                            Analyse individuelle
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-slate-700 dark:text-slate-300 text-sm">
                                                {{ $analyse->getPrixFormate() }}
                                            </span>
                                            <button wire:click="ajouterAnalyse({{ $analyse->id }})" 
                                                    class="px-2 py-1 text-xs rounded transition-colors
                                                    {{ isset($analysesPanier[$analyse->id]) 
                                                    ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200' 
                                                    : ($isEditMode ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600') . ' text-white' }}"
                                                    {{ isset($analysesPanier[$analyse->id]) ? 'disabled' : '' }}>
                                                <em class="ni ni-{{ isset($analysesPanier[$analyse->id]) ? 'check' : 'plus' }} text-xs"></em>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                            {{-- AUTRES ANALYSES (panels, etc.) --}}
                                @if($autresAnalyses->count() > 0)
                                    <div>
                                        @if($analysesDirectes->count() > 0)
                                            <h4 class="text-xs font-medium text-slate-600 dark:text-slate-400 mb-2 flex items-center">
                                                <em class="ni ni-package mr-1 text-xs"></em>
                                                Autres options ({{ $autresAnalyses->count() }})
                                            </h4>
                                        @endif
                                        
                                        @foreach($autresAnalyses as $analyse)
                                            <div class="flex justify-between items-center p-2.5 border border-gray-200 dark:border-slate-600 rounded-lg 
                                                    hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors mb-2">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="px-1.5 py-0.5 
                                                            {{ $analyse->level === 'PARENT' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-200' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200' }} 
                                                            rounded font-mono text-xs font-bold">
                                                            {{ $analyse->code }}
                                                        </span>
                                                        <div>
                                                            <span class="font-medium text-slate-800 dark:text-slate-100 text-sm flex items-center">
                                                                {{ $analyse->designation }}
                                                                @if($analyse->level === 'PARENT' && isset($analyse->enfants_inclus))
                                                                    <span class="ml-2 px-1.5 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-200 rounded-full text-xxs">
                                                                        Panel complet
                                                                    </span>
                                                                @endif
                                                            </span>
                                                            <div class="text-slate-500 dark:text-slate-400 text-xs">
                                                                @if($analyse->level === 'PARENT')
                                                                    <em class="ni ni-package mr-1 text-xs"></em>
                                                                    Panel - Inclut plusieurs analyses
                                                                @else
                                                                    {{ $analyse->parent?->designation ? $analyse->parent->designation . ($analyse->parent->prix > 0 ? ' (inclus dans panel)' : '') : 'Analyse individuelle' }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium text-slate-700 dark:text-slate-300 text-sm">
                                                        {{ $analyse->parent && $analyse->parent->prix > 0 && $analyse->level !== 'PARENT' ? 'Inclus' : $analyse->getPrixFormate() }}
                                                    </span>
                                                    <button wire:click="ajouterAnalyse({{ $analyse->id }})" 
                                                            class="px-2 py-1 text-xs rounded transition-colors
                                                            {{ isset($analysesPanier[$analyse->id]) 
                                                            ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200' 
                                                            : ($isEditMode ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600') . ' text-white' }}"
                                                            {{ isset($analysesPanier[$analyse->id]) ? 'disabled' : '' }}>
                                                        <em class="ni ni-{{ isset($analysesPanier[$analyse->id]) ? 'check' : 'plus' }} text-xs"></em>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                                @elseif(strlen($rechercheAnalyse) >= 2)
                                    <div class="text-center py-6 bg-gray-50 dark:bg-slate-700 rounded-lg">
                                        <em class="ni ni-info text-2xl text-slate-400 mb-2"></em>
                                        <p class="text-slate-600 dark:text-slate-300 text-sm">Aucune analyse trouvée avec "{{ $rechercheAnalyse }}"</p>
                                        <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                            <p>💡 Essayez :</p>
                                            <p>• Le code exact (ex: NFS, GLY, URE)</p>
                                            <p>• Une partie du nom (ex: HÉMOGRAMME, GLYCÉMIE)</p>
                                            <p>• Les analyses individuelles sont maintenant trouvables directement !</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-8 bg-gray-50 dark:bg-slate-700 rounded-lg">
                                        <em class="ni ni-search text-2xl text-slate-400 mb-2"></em>
                                        <p class="text-base text-slate-600 dark:text-slate-300 mb-1">
                                            {{ $isEditMode ? 'Modification des analyses' : 'Recherche d\'analyses' }}
                                        </p>
                                        <p class="text-slate-500 dark:text-slate-400 text-sm">
                                            {{ $isEditMode ? 'Recherchez pour ajouter/modifier des analyses' : 'Tapez dans le champ ci-dessus pour rechercher des analyses' }}
                                        </p>
                                        <div class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                                            <p>✨ <strong>Nouveau :</strong> Trouvez directement toute analyse CHILD par son code !</p>
                                            <p>Exemple : Recherchez "GB" pour trouver directement "Globules Blancs" même s'il fait partie du panel "NFS"</p>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="flex justify-between mt-4">
                                    <button wire:click="allerEtape('clinique')" 
                                            class="px-3 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors text-sm">
                                        <em class="ni ni-arrow-left mr-1.5 text-xs"></em>Clinique
                                    </button>
                                    <button wire:click="validerAnalyses" 
                                            class="px-4 py-2 {{ $isEditMode ? 'bg-green-500 hover:bg-green-600' : 'bg-primary-500 hover:bg-primary-600' }} text-white rounded-lg transition-colors text-sm">
                                        {{ $isEditMode ? 'Modifier prélèvements' : 'Prélèvements' }}<em class="ni ni-arrow-right ml-1.5 text-xs"></em>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        {{-- PANIER ANALYSES --}}
                        <div>
                            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-medium text-slate-800 dark:text-slate-100 text-sm">
                                        <em class="ni ni-bag mr-1.5 text-xs"></em>Analyses sélectionnées
                                    </h3>
                                    @if(count($analysesPanier) > 0)
                                        <button wire:click="$set('analysesPanier', [])" 
                                                wire:confirm="{{ $isEditMode ? 'Voulez-vous vraiment supprimer toutes les analyses ?' : 'Voulez-vous vider le panier ?' }}"
                                                class="text-red-500 hover:text-red-600 text-xs transition-colors">
                                            <em class="ni ni-trash text-xs"></em>
                                        </button>
                                    @endif
                                </div>

                                {{-- ANCIEN PANIER EN MODE ÉDITION --}}
                                @if($isEditMode && isset($prescription) && count($analysesPanier) > 0)
                                    <div class="mb-3 p-2 bg-orange-50/50 dark:bg-orange-900/20 border border-orange-200/50 dark:border-orange-800/50 rounded-lg">
                                        <h4 class="text-xs font-medium text-orange-800 dark:text-orange-200 mb-1">
                                            <em class="ni ni-info mr-1 text-xs"></em>Sélection actuelle
                                        </h4>
                                        <p class="text-xxs text-orange-600 dark:text-orange-300">
                                            {{ count($analysesPanier) }} analyse(s) dans cette prescription
                                        </p>
                                    </div>
                                @endif
                                
                                @if(count($analysesPanier) > 0)
                                    <div class="space-y-2 mb-3">
                                        @foreach($analysesPanier as $analyse)
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-1.5 mb-0.5">
                                                        <span class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 rounded font-mono text-xxs font-bold">
                                                            {{ $analyse['code'] }}
                                                        </span>
                                                        <div class="font-medium text-xs text-slate-800 dark:text-slate-100">{{ $analyse['designation'] }}</div>
                                                    </div>
                                                    <div class="text-slate-500 dark:text-slate-400 text-xxs">{{ $analyse['parent_nom'] }}</div>
                                                    
                                                    {{-- BADGE MODE ÉDITION --}}
                                                    @if($isEditMode && isset($analyse['is_parent']) && $analyse['is_parent'])
                                                        <span class="inline-block mt-0.5 px-1.5 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-200 rounded-full text-xxs">
                                                            Panel complet
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-right ml-2">
                                                    <div class="font-medium text-slate-700 dark:text-slate-300 text-xs">
                                                        {{ $analyse['prix_effectif'] > 0 ? number_format($analyse['prix_effectif'], 0) . ' Ar' : 'Inclus' }}
                                                    </div>
                                                    <button wire:click="retirerAnalyse({{ $analyse['id'] }})" 
                                                            wire:confirm="{{ $isEditMode ? 'Retirer cette analyse de la prescription ?' : 'Retirer du panier ?' }}"
                                                            class="text-red-500 hover:text-red-600 text-xxs transition-colors">
                                                        <em class="ni ni-cross text-xs"></em>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="border-t border-gray-100 dark:border-slate-600 pt-2">
                                        <div class="flex justify-between font-semibold text-base">
                                            <span class="text-slate-800 dark:text-slate-100">Total:</span>
                                            <span class="{{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                                                {{ number_format($total, 0) }} Ar
                                            </span>
                                        </div>
                                        
                                        {{-- COMPARAISON EN MODE ÉDITION --}}
                                        @if($isEditMode && isset($prescription))
                                            <div class="mt-1 text-xxs text-slate-500 dark:text-slate-400">
                                                <em class="ni ni-info mr-1 text-xxs"></em>
                                                Vous êtes en train de modifier les analyses de cette prescription
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center py-4 text-slate-500 dark:text-slate-400">
                                        <em class="ni ni-bag text-lg mb-1"></em>
                                        <p class="text-xs">
                                            {{ $isEditMode ? 'Aucune analyse sélectionnée pour cette prescription' : 'Aucune analyse sélectionnée' }}
                                        </p>
                                        @if($isEditMode)
                                            <p class="text-xxs mt-0.5">Utilisez la recherche pour ajouter des analyses</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- AIDE CONTEXTUELLE MODE ÉDITION --}}
                            @if($isEditMode)
                                <div class="mt-3 bg-orange-50/50 dark:bg-orange-900/10 border border-orange-200/50 dark:border-orange-800/50 rounded-lg p-3">
                                    <h4 class="font-medium text-orange-800 dark:text-orange-200 mb-2 flex items-center text-sm">
                                        <em class="ni ni-edit mr-1.5 text-xs"></em>
                                        Conseils modification
                                    </h4>
                                    <div class="text-xs text-orange-700 dark:text-orange-300 space-y-0.5">
                                        <p>• Les analyses retirées ne seront plus facturées</p>
                                        <p>• Les nouvelles analyses s'ajoutent au total</p>
                                        <p>• Les tubes seront régénérés si nécessaire</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif