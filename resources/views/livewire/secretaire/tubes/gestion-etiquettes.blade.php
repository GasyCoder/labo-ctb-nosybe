{{-- UN SEUL DIV RACINE POUR LIVEWIRE --}}
<div>
    <div class="p-6">
        {{-- HEADER --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                        Gestion des Étiquettes
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Sélectionnez les tubes à imprimer
                    </p>
                </div>
                
                {{-- Compteur sélection --}}
                @if(count($tubesSelectionnes) > 0)
                    <div class="text-right">
                        <div class="bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-lg">
                            <span class="text-blue-700 dark:text-blue-300 font-medium">
                                {{ count($tubesSelectionnes) }} tube(s) sélectionné(s)
                            </span>
                        </div>
                        
                        {{-- Résumé de la sélection --}}
                        @if($this->selectionSummary)
                            <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                @foreach($this->selectionSummary['par_type'] as $type => $count)
                                    <span class="mr-2">{{ $type }}: {{ $count }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- STATISTIQUES COMPLÈTES --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            {{-- Total tubes --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total tubes</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ number_format($statistiques['total'] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Non réceptionnés --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Non réceptionnés</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ number_format($statistiques['non_receptionnes'] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Réceptionnés --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Réceptionnés</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ number_format($statistiques['receptionnes'] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Aujourd'hui --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4.001M3 21h18l-1-8H4l-1 8z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Aujourd'hui</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ number_format($statistiques['aujourd_hui'] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTRES ET CONFIGURATION --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            {{-- Filtres de recherche --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Recherche
                    </label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="recherche" 
                           placeholder="Code-barre, référence, patient..." 
                           class="w-full p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Statut
                    </label>
                    <select wire:model.live="filtreStatut" 
                            class="w-full p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="tous">Tous les statuts</option>
                        <option value="non_receptionnes">Non réceptionnés</option>
                        <option value="receptionnes">Réceptionnés</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Période
                    </label>
                    <select wire:model.live="filtreDate" 
                            class="w-full p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="aujourd_hui">Aujourd'hui</option>
                        <option value="hier">Hier</option>
                        <option value="cette_semaine">Cette semaine</option>
                        <option value="ce_mois">Ce mois</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button wire:click="reinitialiserFiltres" 
                            class="w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Réinitialiser
                    </button>
                </div>
            </div>

            {{-- Configuration impression --}}
            <div class="border-t pt-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Colonnes PDF
                        </label>
                        <input type="number" 
                               wire:model.live="nombreColonnes" 
                               min="2" max="4"
                               class="w-20 p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="flex items-center mt-6">
                        <label class="flex items-center text-gray-700 dark:text-gray-300">
                            <input type="checkbox" 
                                   wire:model.live="inclurePatient" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2">Inclure infos patient</span>
                        </label>
                    </div>
                    
                    <div class="ml-auto">
                        <button wire:click="imprimerEtiquettes" 
                                wire:loading.attr="disabled"
                                @disabled(empty($tubesSelectionnes))
                                class="px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors">
                            <span wire:loading.remove wire:target="imprimerEtiquettes">
                                Générer PDF ({{ count($tubesSelectionnes) }})
                            </span>
                            <span wire:loading wire:target="imprimerEtiquettes" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Génération...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- LISTE DES TUBES --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            {{-- EN-TÊTE LISTE --}}
            <div class="p-4 border-b dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-gray-700 dark:text-gray-300">
                        <input type="checkbox" 
                               wire:model.live="toutSelectionner" 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 font-medium">Tout sélectionner</span>
                    </label>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $this->tubes->count() }} sur {{ $this->tubes->total() }} tube(s)
                        </span>
                        
                        @if(count($tubesSelectionnes) > 0)
                            <button wire:click="viderSelection" 
                                    class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                Vider la sélection
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- LISTE DES TUBES --}}
            <div class="divide-y dark:divide-gray-700">
                @forelse($this->tubes as $tube)
                    <div class="p-4 flex items-center justify-between transition-colors duration-150 {{ in_array($tube->id, $tubesSelectionnes) ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                        <div class="flex items-center space-x-4 flex-1">
                            {{-- CHECKBOX --}}
                            <input type="checkbox" 
                                   wire:model.live="tubesSelectionnes"
                                   value="{{ $tube->id }}"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            
                            {{-- ICÔNE TYPE PRÉLÈVEMENT --}}
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                                {{ $tube->icone ?? 'T' }}
                            </div>
                            
                            {{-- INFORMATIONS PRINCIPALES --}}
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-1">
                                    <span class="font-bold text-gray-900 dark:text-white">
                                        {{ $tube->code_barre }}
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded-full font-medium {{ $tube->estReceptionne() ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' }}">
                                        {{ $tube->statut }}
                                    </span>
                                </div>
                                
                                @if(isset($tube->prescription->patient))
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $tube->prescription->patient->civilite }}
                                    {{ $tube->prescription->patient->nom }}
                                    {{ $tube->prescription->patient->prenom }}
                                </div>
                                @endif
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span class="flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $tube->prescription->reference ?? 'N/A' }}
                                    </span>
                                    @if(isset($tube->prelevement))
                                    <span class="flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                        {{ $tube->prelevement->denomination }}
                                    </span>
                                    @endif
                                    <span class="flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $tube->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- PRESCRIPTEUR --}}
                            <div class="text-right text-sm text-gray-600 dark:text-gray-400">
                                @if(isset($tube->prescription->prescripteur))
                                    <div>Dr. {{ $tube->prescription->prescripteur->nom }}</div>
                                @endif
                                <div class="text-xs">{{ $tube->numero_tube ?? 'N/A' }}</div>
                            </div>
                        </div>
                        
                        {{-- ACTIONS --}}
                        <div class="flex items-center space-x-2 ml-4">
                            @if(!$tube->estReceptionne())
                                <button wire:click="marquerReceptionne({{ $tube->id }})"
                                        class="p-2 text-green-600 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors"
                                        title="Marquer comme réceptionné">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            @else
                                <div class="p-2 text-green-600" title="Réceptionné">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- AUCUN RÉSULTAT --}}
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m2 0h4m4 0h4m-4-8a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            Aucun tube trouvé
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            Aucun tube ne correspond aux critères de recherche actuels.
                        </p>
                        <button wire:click="reinitialiserFiltres" 
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                            Réinitialiser les filtres
                        </button>
                    </div>
                @endforelse
            </div>
            
            {{-- PAGINATION --}}
            @if($this->tubes->hasPages())
                <div class="p-4 border-t dark:border-gray-700">
                    {{ $this->tubes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>