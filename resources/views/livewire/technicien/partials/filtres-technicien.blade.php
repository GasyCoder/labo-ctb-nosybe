{{-- livewire.technicien.partials.filtres-technicien --}}
<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mb-8">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            {{-- Recherche globale --}}
            <div class="md:col-span-3">
                <div class="relative">
                    <input type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Rechercher par patient, prescripteur..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:bg-gray-700">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Filtre par date --}}
            <div>
                <select wire:model.live="dateFilter"
                    class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:bg-gray-700">
                    <option value="">Toutes les dates</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="yesterday">Hier</option>
                    <option value="this_week">Cette semaine</option>
                    <option value="this_month">Ce mois</option>
                </select>
            </div>

            {{-- Filtre par prescripteur --}}
            <div>
                <select wire:model.live="prescripteurFilter"
                    class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:bg-gray-700">
                    <option value="">Tous prescripteurs</option>
                    @foreach($prescripteurs ?? [] as $prescripteur)
                        <option value="{{ $prescripteur->id }}">
                            Dr. {{ $prescripteur->nom }} {{ $prescripteur->prenom }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2">
                {{-- Bouton Filtres --}}
                <button wire:click="toggleAdvancedFilters" 
                    class="flex items-center gap-2 px-4 py-3 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    Filtres
                    @if($showAdvancedFilters)
                        <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    @else
                        <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    @endif
                </button>

                {{-- Bouton Export --}}
                <button wire:click="exportData" 
                    class="flex items-center gap-2 px-4 py-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>

        {{-- Filtres avancés --}}
        @if($showAdvancedFilters)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Filtre par type d'analyse --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Type d'analyse
                        </label>
                        <select wire:model.live="typeAnalyseFilter"
                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:bg-gray-700">
                            <option value="">Tous les types</option>
                            <option value="biochimie">Biochimie</option>
                            <option value="hematologie">Hématologie</option>
                            <option value="immunologie">Immunologie</option>
                            <option value="microbiologie">Microbiologie</option>
                        </select>
                    </div>

                    {{-- Filtre par priorité --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Priorité
                        </label>
                        <select wire:model.live="prioriteFilter"
                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:bg-gray-700">
                            <option value="">Toutes priorités</option>
                            <option value="normale">Normale</option>
                            <option value="urgente">Urgente</option>
                            <option value="stat">STAT</option>
                        </select>
                    </div>

                    {{-- Filtre par âge patient --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Âge patient
                        </label>
                        <select wire:model.live="ageFilter"
                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:bg-gray-700">
                            <option value="">Tous âges</option>
                            <option value="pediatrie">Pédiatrie (0-18 ans)</option>
                            <option value="adulte">Adulte (18-65 ans)</option>
                            <option value="senior">Senior (65+ ans)</option>
                        </select>
                    </div>

                    {{-- Bouton réinitialiser --}}
                    <div class="flex items-end">
                        <button wire:click="resetFilters"
                            class="w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Réinitialiser
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Indicateurs de filtres actifs --}}
        @if($search || $dateFilter || $prescripteurFilter || $typeAnalyseFilter || $prioriteFilter || $ageFilter)
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Filtres actifs:</span>
                
                @if($search)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        Recherche: {{ Str::limit($search, 20) }}
                        <button wire:click="$set('search', '')" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                    </span>
                @endif

                @if($dateFilter)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Date: {{ ucfirst(str_replace('_', ' ', $dateFilter)) }}
                        <button wire:click="$set('dateFilter', '')" class="ml-1 text-green-600 hover:text-green-800">×</button>
                    </span>
                @endif

                @if($prescripteurFilter)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        Prescripteur filtré
                        <button wire:click="$set('prescripteurFilter', '')" class="ml-1 text-purple-600 hover:text-purple-800">×</button>
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>