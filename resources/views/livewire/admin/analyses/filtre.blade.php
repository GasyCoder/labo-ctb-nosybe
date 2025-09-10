{{-- Filtres améliorés UI/UX responsive --}}
@if($mode === 'list')
    <div class="mt-4 sm:mt-6">
        {{-- Conteneur principal avec fond et bordures --}}
        <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-6">
            {{-- En-tête des filtres --}}
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
                    </svg>
                    Filtres de recherche
                </h3>
                
                {{-- Indicateur de filtres actifs --}}
                @php
                    $activeFilters = 0;
                    if($search) $activeFilters++;
                    if($selectedExamen) $activeFilters++;
                    if($selectedLevel !== 'racines') $activeFilters++;
                @endphp
                
                @if($activeFilters > 0)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                        {{ $activeFilters }} filtre{{ $activeFilters > 1 ? 's' : '' }} actif{{ $activeFilters > 1 ? 's' : '' }}
                    </span>
                @endif
            </div>

            {{-- Grille des filtres --}}
            <div class="space-y-4">
                {{-- Ligne 1: Filtres principaux --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    {{-- Filtre par niveau d'affichage --}}
                    <div class="sm:col-span-2 xl:col-span-1">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Type d'analyse
                        </label>
                        <select wire:model.live="selectedLevel" 
                                class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white transition-colors">
                            <option value="racines">🌿 Racines ({{ $this->getAnalysesCountByLevel()['racines'] }})</option>
                            <option value="parents">📋 Panels ({{ $this->getAnalysesCountByLevel()['parents'] }})</option>
                            <option value="normales">🔬 Normales ({{ $this->getAnalysesCountByLevel()['normales'] }})</option>
                            <option value="enfants">🔗 Sous-analyses ({{ $this->getAnalysesCountByLevel()['enfants'] }})</option>
                            <option value="tous">📊 Toutes ({{ $this->getAnalysesCountByLevel()['tous'] }})</option>
                        </select>
                    </div>

                    {{-- Barre de recherche améliorée --}}
                    <div class="sm:col-span-2 xl:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Recherche textuelle
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" 
                                wire:model.live.debounce.300ms="search"
                                placeholder="Rechercher par code ou désignation..."
                                class="w-full pl-10 pr-10 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-colors">
                            
                            {{-- Bouton clear pour la recherche --}}
                            @if($search)
                                <button wire:click="$set('search', '')" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        {{-- Suggestions de recherche --}}
                        @if(empty($search))
                            <div class="mt-1 flex flex-wrap gap-1">
                                <button wire:click="$set('search', 'GLY')" 
                                        class="inline-flex items-center px-2 py-1 rounded text-xs text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    GLY
                                </button>
                                <button wire:click="$set('search', 'ALAT')" 
                                        class="inline-flex items-center px-2 py-1 rounded text-xs text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    ALAT
                                </button>
                                <button wire:click="$set('search', 'HbA1c')" 
                                        class="inline-flex items-center px-2 py-1 rounded text-xs text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    HbA1c
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Filtre par examen --}}
                    <div class="xl:col-span-1">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Examen médical
                        </label>
                        <select wire:model.live="selectedExamen" 
                                class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white transition-colors">
                            <option value="">Tous les examens</option>
                            @if($examens)
                                @foreach($examens as $examen)
                                    <option value="{{ $examen->id }}">{{ $examen->abr }} - {{ Str::limit($examen->name, 20) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                {{-- Ligne 2: Contrôles secondaires --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                        {{-- Nombre par page --}}
                        <div class="flex items-center space-x-2">
                            <label class="text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Afficher:
                            </label>
                            <select wire:model.live="perPage" 
                                    class="px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white transition-colors">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">par page</span>
                        </div>

                        {{-- Résumé des résultats --}}
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            @if($this->analyses->total() > 0)
                                {{ number_format($this->analyses->total()) }} résultat{{ $this->analyses->total() > 1 ? 's' : '' }} trouvé{{ $this->analyses->total() > 1 ? 's' : '' }}
                                @if($this->analyses->hasPages())
                                    (page {{ $this->analyses->currentPage() }} sur {{ $this->analyses->lastPage() }})
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Actions de filtre --}}
                    <div class="flex items-center space-x-2 mt-3 sm:mt-0">
                        {{-- Bouton Reset avec style amélioré --}}
                        @if($selectedExamen || $search || $selectedLevel !== 'racines')
                            <button wire:click="resetFilters" 
                                    class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Réinitialiser
                            </button>
                        @endif

                        {{-- Bouton d'export (optionnel) --}}
                        @if($this->analyses->count() > 0)
                            <button class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="hidden sm:inline">Exporter</span>
                                <span class="sm:hidden">Export</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres actifs sous forme de badges (optionnel) --}}
        @if($selectedExamen || $search || $selectedLevel !== 'racines')
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Filtres actifs:</span>
                
                @if($selectedLevel !== 'racines')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                        {{ ucfirst($selectedLevel) }}
                        <button wire:click="$set('selectedLevel', 'racines')" class="ml-1 hover:bg-purple-200 dark:hover:bg-purple-800 rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                @endif

                @if($search)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                        "{{ Str::limit($search, 15) }}"
                        <button wire:click="$set('search', '')" class="ml-1 hover:bg-blue-200 dark:hover:bg-blue-800 rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                @endif

                @if($selectedExamen && $examens->find($selectedExamen))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                        {{ $examens->find($selectedExamen)->abr }}
                        <button wire:click="$set('selectedExamen', '')" class="ml-1 hover:bg-green-200 dark:hover:bg-green-800 rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                @endif
            </div>
        @endif
    </div>
@endif