{{-- livewire.secretaire.prescription.partials.tubes - VERSION UNIFIÉE CRÉATION/ÉDITION --}}
@if($etape === 'tubes')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        {{-- HEADER SECTION ADAPTATIF --}}
        <div class="bg-gradient-to-r {{ $isEditMode ? 'from-orange-50 to-amber-50' : 'from-slate-50 to-gray-50' }} dark:from-slate-700 dark:to-slate-800 px-4 py-3 border-b border-gray-100 dark:border-slate-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 {{ $isEditMode ? 'bg-orange-500' : 'bg-slate-600' }} rounded-lg flex items-center justify-center">
                        <em class="ni ni-printer text-white text-sm"></em>
                    </div>
                    <div class="ml-3">
                        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">
                            {{ $isEditMode ? 'Régénération Tubes et Étiquettes' : 'Tubes et Étiquettes' }}
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $isEditMode ? 'Régénération et impression des nouveaux codes-barres' : 'Génération et impression des codes-barres' }}
                        </p>
                    </div>
                </div>
                
                @if(count($tubesGeneres) > 0)
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-700 text-orange-700 dark:text-orange-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200' }} rounded-full text-xs font-medium">
                            {{ count($tubesGeneres) }} tube(s) {{ $isEditMode ? 'régénéré(s)' : '' }}
                        </span>
                        <div class="w-2 h-2 {{ $isEditMode ? 'bg-orange-500' : 'bg-green-500' }} rounded-full animate-pulse"></div>
                    </div>
                @endif
            </div>
        </div>

        <div class="p-4">
            {{-- ALERTE MODE ÉDITION --}}
            @if($isEditMode)
                <div class="mb-4 p-3 bg-orange-50/50 dark:bg-orange-900/10 border border-orange-200/50 dark:border-orange-800/50 rounded-lg">
                    <div class="flex items-center">
                        <em class="ni ni-refresh text-orange-500 mr-2 text-sm"></em>
                        <div>
                            <h4 class="font-medium text-orange-800 dark:text-orange-200 text-sm">Régénération des tubes</h4>
                            <p class="text-xs text-orange-600 dark:text-orange-300 mt-0.5">
                                Les anciens tubes ont été supprimés et de nouveaux tubes ont été générés avec des codes-barres mis à jour.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if(count($tubesGeneres) > 0)
                {{-- SUCCESS MESSAGE --}}
                <div class="bg-{{ $isEditMode ? 'orange' : 'green' }}-50/50 dark:bg-{{ $isEditMode ? 'orange' : 'green' }}-900/10 
                            border border-{{ $isEditMode ? 'orange' : 'green' }}-200/50 dark:border-{{ $isEditMode ? 'orange' : 'green' }}-800/50 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $isEditMode ? 'bg-orange-500' : 'bg-green-500' }} rounded flex items-center justify-center mr-3 flex-shrink-0">
                            <em class="ni ni-{{ $isEditMode ? 'refresh' : 'check-circle' }} text-white text-xs"></em>
                        </div>
                        <div>
                            <h3 class="font-medium {{ $isEditMode ? 'text-orange-800 dark:text-orange-200' : 'text-green-800 dark:text-green-200' }} mb-0.5 text-sm">
                                @if($isEditMode)
                                    🔄 Tubes régénérés avec succès !
                                @else
                                    🎉 Tubes générés avec succès !
                                @endif
                            </h3>
                            <p class="text-xs {{ $isEditMode ? 'text-orange-700 dark:text-orange-300' : 'text-green-700 dark:text-green-300' }}">
                                {{ count($tubesGeneres) }} tube(s) {{ $isEditMode ? 'régénéré(s) et prêt(s)' : 'prêt(s)' }} pour l'impression des étiquettes codes-barres
                            </p>
                        </div>
                    </div>
                </div>
                
                {{-- TUBES GRID --}}
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-slate-800 dark:text-slate-100 mb-3 flex items-center">
                        <em class="ni ni-package mr-1.5 text-slate-500 dark:text-slate-400 text-xs"></em>
                        {{ $isEditMode ? 'Liste des nouveaux tubes générés' : 'Liste des tubes générés' }}
                    </h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-3">
                        @foreach($tubesGeneres as $index => $tube)
                            <div class="group bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-lg p-3 
                                        hover:border-slate-300 dark:hover:border-slate-500 hover:bg-white dark:hover:bg-slate-700
                                        transition-colors hover:shadow-sm
                                        {{ $isEditMode ? 'ring-1 ring-orange-200 dark:ring-orange-800' : '' }}">
                                
                                {{-- TUBE HEADER --}}
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-gradient-to-br from-{{ $isEditMode ? 'orange' : 'slate' }}-500 to-{{ $isEditMode ? 'orange' : 'slate' }}-600 rounded flex items-center justify-center text-white">
                                            <em class="ni ni-capsule text-xs"></em>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-slate-800 dark:text-slate-100 text-xs">
                                                {{ $tube['numero_tube'] ?? 'Tube #'.$tube['id'] }}
                                            </h4>
                                            <p class="text-xxs text-slate-500 dark:text-slate-400">
                                                {{ $isEditMode ? 'Nouveau tube' : 'Tube' }} {{ $index + 1 }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <span class="px-1.5 py-0.5 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-200' : 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' }} rounded-full text-xxs font-medium">
                                        {{ $tube['statut'] ?? 'GÉNÉRÉ' }}
                                    </span>
                                </div>
                                
                                {{-- TUBE DETAILS --}}
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xxs text-slate-600 dark:text-slate-400 flex items-center">
                                            <em class="ni ni-barcode mr-1 text-slate-500 dark:text-slate-500 text-xs"></em>
                                            Code-barre
                                        </span>
                                        <code class="text-xxs font-mono bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-1.5 py-0.5 rounded">
                                            {{ $tube['code_barre'] ?? 'En cours...' }}
                                        </code>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-xxs text-slate-600 dark:text-slate-400 flex items-center">
                                            <em class="ni ni-flask mr-1 text-slate-500 dark:text-slate-500 text-xs"></em>
                                            Type
                                        </span>
                                        <span class="text-xxs font-medium text-slate-800 dark:text-slate-200">
                                            {{ $tube['type_tube'] ?? 'Standard' }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-xxs text-slate-600 dark:text-slate-400 flex items-center">
                                            <em class="ni ni-activity mr-1 text-slate-500 dark:text-slate-500 text-xs"></em>
                                            Volume
                                        </span>
                                        <span class="text-xxs font-medium text-slate-800 dark:text-slate-200">
                                            {{ $tube['volume_ml'] ?? 5 }} ml
                                        </span>
                                    </div>
                                </div>
                                
                                {{-- BARCODE VISUALIZATION --}}
                                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-600">
                                    <div class="text-center">
                                        <div class="bg-white dark:bg-slate-800 rounded-lg p-2 border border-slate-200 dark:border-slate-600">
                                            {{-- Simulation d'un code-barre --}}
                                            <div class="flex justify-center space-x-0.5 mb-1">
                                                @for($i = 0; $i < 12; $i++)
                                                    <div class="w-0.5 bg-slate-800 dark:bg-slate-200" 
                                                         style="height: {{ rand(10, 16) }}px"></div>
                                                @endfor
                                            </div>
                                            <p class="text-xxs font-mono text-slate-600 dark:text-slate-400">
                                                {{ $tube['code_barre'] ?? 'Code en cours...' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- BADGE NOUVEAUTÉ EN MODE ÉDITION --}}
                                @if($isEditMode)
                                    <div class="mt-2 text-center">
                                        <span class="inline-flex items-center px-1.5 py-0.5 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-200 rounded-full text-xxs font-medium">
                                            <em class="ni ni-refresh mr-0.5 text-xs"></em>
                                            Régénéré
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                
                {{-- STATISTIQUES RAPIDES --}}
                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 mb-4">
                    <h4 class="font-medium text-slate-800 dark:text-slate-100 mb-2 flex items-center text-sm">
                        <em class="ni ni-bar-chart mr-1.5 text-slate-500 dark:text-slate-400 text-xs"></em>
                        {{ $isEditMode ? 'Résumé de régénération' : 'Résumé de génération' }}
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="text-center">
                            <div class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ count($tubesGeneres) }}</div>
                            <div class="text-xxs text-slate-600 dark:text-slate-400">Tubes {{ $isEditMode ? 'régénérés' : 'générés' }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ count(array_filter($tubesGeneres, fn($t) => ($t['statut'] ?? '') === 'GENERE')) }}
                            </div>
                            <div class="text-xxs text-slate-600 dark:text-slate-400">Prêts</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ count(array_unique(array_column($tubesGeneres, 'type_tube'))) }}</div>
                            <div class="text-xxs text-slate-600 dark:text-slate-400">Types différents</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ array_sum(array_column($tubesGeneres, 'volume_ml')) }}</div>
                            <div class="text-xxs text-slate-600 dark:text-slate-400">Volume total (ml)</div>
                        </div>
                    </div>
                </div>

                {{-- COMPARAISON AVANT/APRÈS EN MODE ÉDITION --}}
                @if($isEditMode && isset($prescription))
                    <div class="bg-orange-50/50 dark:bg-orange-900/10 border border-orange-200/50 dark:border-orange-800/50 rounded-lg p-3 mb-4">
                        <h4 class="font-medium text-orange-800 dark:text-orange-200 mb-2 flex items-center text-sm">
                            <em class="ni ni-exchange mr-1.5 text-xs"></em>
                            Modifications apportées
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                            <div class="p-2 bg-red-50/50 dark:bg-red-900/10 border border-red-200/50 dark:border-red-800/50 rounded">
                                <h5 class="font-medium text-red-800 dark:text-red-200 mb-1 text-xs">❌ Anciens tubes supprimés</h5>
                                <p class="text-red-600 dark:text-red-300 text-xxs">Les tubes précédents ont été invalidés</p>
                            </div>
                            <div class="p-2 bg-green-50/50 dark:bg-green-900/10 border border-green-200/50 dark:border-green-800/50 rounded">
                                <h5 class="font-medium text-green-800 dark:text-green-200 mb-1 text-xs">✅ Nouveaux tubes créés</h5>
                                <p class="text-green-600 dark:text-green-300 text-xxs">{{ count($tubesGeneres) }} nouveaux tubes avec codes mis à jour</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                {{-- ACTIONS BUTTONS --}}
                <div class="flex justify-center">
                    <button wire:click="terminerPrescription"
                            class="w-full sm:w-auto inline-flex items-center px-6 py-3 
                                {{ $isEditMode ? 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500' }} 
                                text-white font-medium rounded-lg text-sm
                                transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg
                                focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-slate-800
                                disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <em class="ni ni-check-circle mr-2 text-sm"></em>
                        {{ $isEditMode ? 'Mettre à jour la prescription' : 'Terminer la prescription' }}
                        <em class="ni ni-arrow-right ml-2 text-xs"></em>
                    </button>
                </div>
                                
                {{-- HELP SECTION --}}
                <div class="mt-4 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-200/50 dark:border-blue-800/50 rounded-lg p-3">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center mr-3 flex-shrink-0">
                            <em class="ni ni-info text-white text-xs"></em>
                        </div>
                        <div>
                            <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-1.5 text-sm">
                                {{ $isEditMode ? 'Instructions de réimpression' : 'Instructions d\'impression' }}
                            </h4>
                            <div class="text-xs text-blue-700 dark:text-blue-300 space-y-0.5">
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
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full mb-4">
                        <em class="ni ni-alert-circle text-2xl text-slate-400 dark:text-slate-500"></em>
                    </div>
                    <h3 class="text-base font-medium text-slate-600 dark:text-slate-300 mb-2">
                        {{ $isEditMode ? 'Aucun tube à régénérer' : 'Aucun tube généré' }}
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-4 max-w-md mx-auto text-sm">
                        @if($isEditMode)
                            Il semble qu'aucun nouveau tube n'ait été généré pour cette modification. 
                            Cela peut arriver si aucun prélèvement n'a été modifié.
                        @else
                            Il semble qu'aucun tube n'ait été généré pour cette prescription. 
                            Cela peut arriver si aucun prélèvement n'a été sélectionné.
                        @endif
                    </p>
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <button wire:click="allerEtape('prelevements')" 
                                class="px-4 py-2 {{ $isEditMode ? 'bg-orange-500 hover:bg-orange-600' : 'bg-yellow-500 hover:bg-yellow-600' }} text-white rounded-lg font-medium transition-colors text-sm">
                            <em class="ni ni-arrow-left mr-1.5 text-xs"></em>
                            Retour aux prélèvements
                        </button>
                        <button wire:click="allerEtape('confirmation')" 
                                class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg font-medium transition-colors text-sm">
                            {{ $isEditMode ? 'Terminer modification' : 'Continuer sans tubes' }}
                            <em class="ni ni-arrow-right ml-1.5 text-xs"></em>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif