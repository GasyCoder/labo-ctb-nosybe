{{-- livewire.secretaire.prescription.partials.tubes - VERSION UNIFIÉE CRÉATION/ÉDITION --}}
@if($etape === 'tubes')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
        {{-- HEADER SECTION ADAPTATIF --}}
        <div class="bg-gradient-to-r {{ $isEditMode ? 'from-orange-50 to-amber-100' : 'from-slate-50 to-gray-100' }} dark:from-slate-700 dark:to-slate-800 px-6 py-5 border-b border-gray-200 dark:border-slate-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 {{ $isEditMode ? 'bg-orange-600' : 'bg-slate-600' }} dark:bg-slate-500 rounded-xl flex items-center justify-center shadow-lg">
                        <em class="ni ni-printer text-white text-xl"></em>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                            {{ $isEditMode ? 'Régénération Tubes et Étiquettes' : 'Tubes et Étiquettes' }}
                        </h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $isEditMode ? 'Régénération et impression des nouveaux codes-barres' : 'Génération et impression des codes-barres' }}
                        </p>
                    </div>
                </div>
                
                @if(count($tubesGeneres) > 0)
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1.5 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-700 text-orange-800 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200' }} rounded-full text-sm font-semibold">
                            {{ count($tubesGeneres) }} tube(s) {{ $isEditMode ? 'régénéré(s)' : '' }}
                        </span>
                        <div class="w-3 h-3 {{ $isEditMode ? 'bg-orange-500' : 'bg-green-500' }} rounded-full animate-pulse"></div>
                    </div>
                @endif
            </div>
        </div>

        <div class="p-6">
            {{-- ALERTE MODE ÉDITION --}}
            @if($isEditMode)
                <div class="mb-6 p-4 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 border border-orange-200 dark:border-orange-800 rounded-xl">
                    <div class="flex items-center">
                        <em class="ni ni-refresh text-orange-600 mr-3"></em>
                        <div>
                            <h4 class="font-semibold text-orange-800 dark:text-orange-200">Régénération des tubes</h4>
                            <p class="text-sm text-orange-600 dark:text-orange-300 mt-1">
                                Les anciens tubes ont été supprimés et de nouveaux tubes ont été générés avec des codes-barres mis à jour.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if(count($tubesGeneres) > 0)
                {{-- SUCCESS MESSAGE --}}
                <div class="bg-gradient-to-r {{ $isEditMode ? 'from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20' : 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20' }} 
                            border {{ $isEditMode ? 'border-orange-200 dark:border-orange-800' : 'border-green-200 dark:border-green-800' }} rounded-xl p-5 mb-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 {{ $isEditMode ? 'bg-orange-600' : 'bg-green-600' }} rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <em class="ni ni-{{ $isEditMode ? 'refresh' : 'check-circle' }} text-white"></em>
                        </div>
                        <div>
                            <h3 class="font-semibold {{ $isEditMode ? 'text-orange-800 dark:text-orange-200' : 'text-green-800 dark:text-green-200' }} mb-1">
                                @if($isEditMode)
                                    🔄 Tubes régénérés avec succès !
                                @else
                                    🎉 Tubes générés avec succès !
                                @endif
                            </h3>
                            <p class="text-sm {{ $isEditMode ? 'text-orange-700 dark:text-orange-300' : 'text-green-700 dark:text-green-300' }}">
                                {{ count($tubesGeneres) }} tube(s) {{ $isEditMode ? 'régénéré(s) et prêt(s)' : 'prêt(s)' }} pour l'impression des étiquettes codes-barres
                            </p>
                        </div>
                    </div>
                </div>
                
                {{-- TUBES GRID --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 flex items-center">
                        <em class="ni ni-package mr-2 text-slate-500 dark:text-slate-400"></em>
                        {{ $isEditMode ? 'Liste des nouveaux tubes générés' : 'Liste des tubes générés' }}
                    </h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($tubesGeneres as $index => $tube)
                            <div class="group bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl p-5 
                                        hover:border-slate-300 dark:hover:border-slate-500 hover:bg-white dark:hover:bg-slate-700
                                        transition-all duration-300 transform hover:scale-[1.02] hover:shadow-md
                                        {{ $isEditMode ? 'ring-2 ring-orange-200 dark:ring-orange-800' : '' }}">
                                
                                {{-- TUBE HEADER --}}
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-{{ $isEditMode ? 'orange' : 'slate' }}-500 to-{{ $isEditMode ? 'orange' : 'slate' }}-600 rounded-lg flex items-center justify-center text-white shadow-lg group-hover:shadow-xl transition-shadow">
                                            <em class="ni ni-capsule text-lg"></em>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-slate-800 dark:text-slate-100 text-sm">
                                                {{ $tube['numero_tube'] ?? 'Tube #'.$tube['id'] }}
                                            </h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $isEditMode ? 'Nouveau tube' : 'Tube' }} {{ $index + 1 }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <span class="px-2 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' }} rounded-full text-xs font-medium">
                                        {{ $tube['statut'] ?? 'GÉNÉRÉ' }}
                                    </span>
                                </div>
                                
                                {{-- TUBE DETAILS --}}
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-600 dark:text-slate-400 flex items-center">
                                            <em class="ni ni-barcode mr-1.5 text-slate-500 dark:text-slate-500"></em>
                                            Code-barre
                                        </span>
                                        <code class="text-xs font-mono bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-2 py-1 rounded">
                                            {{ $tube['code_barre'] ?? 'En cours...' }}
                                        </code>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-600 dark:text-slate-400 flex items-center">
                                            <em class="ni ni-flask mr-1.5 text-slate-500 dark:text-slate-500"></em>
                                            Type
                                        </span>
                                        <span class="text-xs font-medium text-slate-800 dark:text-slate-200">
                                            {{ $tube['type_tube'] ?? 'Standard' }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-600 dark:text-slate-400 flex items-center">
                                            <em class="ni ni-activity mr-1.5 text-slate-500 dark:text-slate-500"></em>
                                            Volume
                                        </span>
                                        <span class="text-xs font-medium text-slate-800 dark:text-slate-200">
                                            {{ $tube['volume_ml'] ?? 5 }} ml
                                        </span>
                                    </div>
                                </div>
                                
                                {{-- BARCODE VISUALIZATION --}}
                                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-600">
                                    <div class="text-center">
                                        <div class="bg-white dark:bg-slate-800 rounded-lg p-3 border border-slate-200 dark:border-slate-600">
                                            {{-- Simulation d'un code-barre --}}
                                            <div class="flex justify-center space-x-1 mb-2">
                                                @for($i = 0; $i < 12; $i++)
                                                    <div class="w-1 bg-slate-800 dark:bg-slate-200" 
                                                         style="height: {{ rand(15, 25) }}px"></div>
                                                @endfor
                                            </div>
                                            <p class="text-xs font-mono text-slate-600 dark:text-slate-400">
                                                {{ $tube['code_barre'] ?? 'Code en cours...' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- BADGE NOUVEAUTÉ EN MODE ÉDITION --}}
                                @if($isEditMode)
                                    <div class="mt-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200 rounded-full text-xs font-medium">
                                            <em class="ni ni-refresh mr-1"></em>
                                            Régénéré
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                
                {{-- STATISTIQUES RAPIDES --}}
                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-5 mb-6">
                    <h4 class="font-semibold text-slate-800 dark:text-slate-100 mb-3 flex items-center">
                        <em class="ni ni-bar-chart mr-2 text-slate-500 dark:text-slate-400"></em>
                        {{ $isEditMode ? 'Résumé de régénération' : 'Résumé de génération' }}
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ count($tubesGeneres) }}</div>
                            <div class="text-xs text-slate-600 dark:text-slate-400">Tubes {{ $isEditMode ? 'régénérés' : 'générés' }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ count(array_filter($tubesGeneres, fn($t) => ($t['statut'] ?? '') === 'GENERE')) }}
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400">Prêts</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ count(array_unique(array_column($tubesGeneres, 'type_tube'))) }}</div>
                            <div class="text-xs text-slate-600 dark:text-slate-400">Types différents</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ array_sum(array_column($tubesGeneres, 'volume_ml')) }}</div>
                            <div class="text-xs text-slate-600 dark:text-slate-400">Volume total (ml)</div>
                        </div>
                    </div>
                </div>

                {{-- COMPARAISON AVANT/APRÈS EN MODE ÉDITION --}}
                @if($isEditMode && isset($prescription))
                    <div class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 
                                border border-orange-200 dark:border-orange-800 rounded-xl p-5 mb-6">
                        <h4 class="font-semibold text-orange-800 dark:text-orange-200 mb-3 flex items-center">
                            <em class="ni ni-exchange mr-2"></em>
                            Modifications apportées
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <h5 class="font-medium text-red-800 dark:text-red-200 mb-2">❌ Anciens tubes supprimés</h5>
                                <p class="text-red-600 dark:text-red-300 text-xs">Les tubes précédents ont été invalidés</p>
                            </div>
                            <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <h5 class="font-medium text-green-800 dark:text-green-200 mb-2">✅ Nouveaux tubes créés</h5>
                                <p class="text-green-600 dark:text-green-300 text-xs">{{ count($tubesGeneres) }} nouveaux tubes avec codes mis à jour</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                {{-- ACTIONS BUTTONS --}}
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <button wire:click="imprimerEtiquettes" 
                            class="w-full sm:w-auto inline-flex items-center px-8 py-4 bg-gradient-to-r from-{{ $isEditMode ? 'orange' : 'slate' }}-600 to-{{ $isEditMode ? 'orange' : 'slate' }}-700 
                                   hover:from-{{ $isEditMode ? 'orange' : 'slate' }}-700 hover:to-{{ $isEditMode ? 'orange' : 'slate' }}-800 text-white font-semibold rounded-xl 
                                   transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl
                                   focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'slate' }}-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                        <em class="ni ni-printer mr-3 text-lg"></em>
                        {{ $isEditMode ? 'Imprimer nouveaux codes' : 'Imprimer' }} {{ count($tubesGeneres) }} étiquette(s)
                        <em class="ni ni-arrow-right ml-3"></em>
                    </button>
                    
                    <button wire:click="ignorerEtiquettes" 
                            class="w-full sm:w-auto inline-flex items-center px-6 py-4 bg-slate-100 dark:bg-slate-700 
                                   text-slate-700 dark:text-slate-300 font-semibold rounded-xl 
                                   hover:bg-slate-200 dark:hover:bg-slate-600 transition-all duration-200
                                   focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                        <em class="ni ni-skip-forward mr-2"></em>
                        {{ $isEditMode ? 'Ignorer réimpression' : 'Ignorer l\'impression' }}
                    </button>
                </div>
                
                {{-- HELP SECTION --}}
                <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 
                            border border-blue-200 dark:border-blue-800 rounded-xl p-5">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <em class="ni ni-info text-white"></em>
                        </div>
                        <div>
                            <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                {{ $isEditMode ? 'Instructions de réimpression' : 'Instructions d\'impression' }}
                            </h4>
                            <div class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                                <p>• Assurez-vous que l'imprimante d'étiquettes est connectée et prête</p>
                                <p>• Utilisez des étiquettes standard de laboratoire (format recommandé)</p>
                                @if($isEditMode)
                                    <p>• Les nouveaux codes-barres remplacent les anciens pour la traçabilité</p>
                                    <p>• Détruisez les anciennes étiquettes pour éviter toute confusion</p>
                                @else
                                    <p>• Chaque tube aura son code-barre unique pour la traçabilité</p>
                                @endif
                                <p>• Vous pouvez ignorer l'impression et continuer vers la confirmation</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            @else
                {{-- EMPTY STATE --}}
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full mb-6">
                        <em class="ni ni-alert-circle text-3xl text-slate-400 dark:text-slate-500"></em>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-600 dark:text-slate-300 mb-2">
                        {{ $isEditMode ? 'Aucun tube à régénérer' : 'Aucun tube généré' }}
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6 max-w-md mx-auto">
                        @if($isEditMode)
                            Il semble qu'aucun nouveau tube n'ait été généré pour cette modification. 
                            Cela peut arriver si aucun prélèvement n'a été modifié.
                        @else
                            Il semble qu'aucun tube n'ait été généré pour cette prescription. 
                            Cela peut arriver si aucun prélèvement n'a été sélectionné.
                        @endif
                    </p>
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <button wire:click="allerEtape('prelevements')" 
                                class="px-6 py-3 {{ $isEditMode ? 'bg-orange-600 hover:bg-orange-700' : 'bg-yellow-600 hover:bg-yellow-700' }} text-white rounded-xl font-semibold transition-colors">
                            <em class="ni ni-arrow-left mr-2"></em>
                            Retour aux prélèvements
                        </button>
                        <button wire:click="allerEtape('confirmation')" 
                                class="px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-xl font-semibold transition-colors">
                            {{ $isEditMode ? 'Terminer modification' : 'Continuer sans tubes' }}
                            <em class="ni ni-arrow-right ml-2"></em>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif