{{-- Filtres réorganisés pour mobile --}}
@if($mode === 'list')
    <div class="mt-4 sm:mt-6 space-y-3">
        {{-- Première ligne : Affichage et Recherche --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            {{-- Filtre par niveau d'affichage --}}
            <div>
                <label class="block text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1">Affichage:</label>
                <select wire:model.live="selectedLevel" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="racines">Racines ({{ $this->getAnalysesCountByLevel()['racines'] }})</option>
                    <option value="parents">Panels ({{ $this->getAnalysesCountByLevel()['parents'] }})</option>
                    <option value="normales">Normales ({{ $this->getAnalysesCountByLevel()['normales'] }})</option>
                    <option value="enfants">Sous-analyses ({{ $this->getAnalysesCountByLevel()['enfants'] }})</option>
                    <option value="tous">Toutes ({{ $this->getAnalysesCountByLevel()['tous'] }})</option>
                </select>
            </div>

            {{-- Barre de recherche --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="block text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1">Rechercher:</label>
                <div class="relative">
                    <input type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Code, désignation..."
                        class="w-full pl-9 pr-3 py-2.5 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Filler pour la grille lg --}}
            <div class="hidden lg:block"></div>
        </div>

        {{-- Deuxième ligne : Autres filtres et actions --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 items-end">
            {{-- Filtre par examen --}}
            <div class="col-span-2 sm:col-span-2 lg:col-span-2">
                <label class="block text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1">Examen:</label>
                <select wire:model.live="selectedExamen" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Tous les examens</option>
                    @if($examens)
                        @foreach($examens as $examen)
                            <option value="{{ $examen->id }}">{{ $examen->abr }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Nombre par page --}}
            <div>
                <label class="block text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1">Par page:</label>
                <select wire:model.live="perPage" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            {{-- Bouton Reset --}}
            @if($selectedExamen || $search || $selectedLevel !== 'racines')
                <div class="col-span-2 sm:col-span-1 lg:col-span-1">
                    <button wire:click="resetFilters" 
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-lg text-sm transition-colors flex items-center justify-center" 
                            title="Réinitialiser tous les filtres">
                        <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="hidden sm:inline">Réinitialiser</span>
                        <span class="sm:hidden">Reset</span>
                    </button>
                </div>
            @else
                {{-- Espace vide quand pas de bouton reset --}}
                <div class="col-span-2 sm:col-span-1 lg:col-span-1"></div>
            @endif

            {{-- Filler pour équilibrer la grille --}}
            <div class="hidden lg:block lg:col-span-2"></div>
        </div>
    </div>
@endif