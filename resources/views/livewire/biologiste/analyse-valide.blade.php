<!-- Interface principale - analyse-valide.blade.php -->
<div class="min-h-screen transition-colors duration-200">
    <!-- Header -->
    <div class="dark:border-gray-700 shadow-sm ">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <i class="fas fa-microscope text-blue-600 dark:text-blue-400 text-xl mr-3"></i>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Gestion des Analyses</h1>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                 
                    <!-- Date actuelle -->
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ now()->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Statistiques -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6 max-w-md lg:max-w-none">
            <!-- Terminé -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 px-3 py-2 rounded-xl border border-orange-200 dark:border-orange-700">
                <div class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wide">Terminé</div>
                <div class="text-xl font-bold text-orange-800 dark:text-orange-300">{{ $analyseTermines->total() }}</div>
                <div class="flex justify-end mt-1">
                    <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-sm"></i>
                </div>
            </div>
            
            <!-- Validé -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 px-3 py-2 rounded-xl border border-green-200 dark:border-green-700">
                <div class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Validé</div>
                <div class="text-xl font-bold text-green-800 dark:text-green-300">{{ $analyseValides->total() }}</div>
                <div class="flex justify-end mt-1">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-sm"></i>
                </div>
            </div>
            
            <!-- Urgences -->
            <div class="bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 px-3 py-2 rounded-xl border border-red-200 dark:border-red-700">
                <div class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide">Urgences</div>
                <div class="text-xl font-bold text-red-800 dark:text-red-300">{{ $stats['urgences_nuit'] + $stats['urgences_jour'] }}</div>
                <div class="flex justify-end mt-1">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-sm"></i>
                </div>
            </div>
            
            <!-- Total -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 px-3 py-2 rounded-xl border border-blue-200 dark:border-blue-700">
                <div class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Total</div>
                <div class="text-xl font-bold text-blue-800 dark:text-blue-300">{{ $analyseTermines->total() + $analyseValides->total() }}</div>
                <div class="flex justify-end mt-1">
                    <i class="fas fa-chart-bar text-blue-600 dark:text-blue-400 text-sm"></i>
                </div>
            </div>
        </div>
        <!-- Contenu principal -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <!-- Barre de recherche et filtres -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <!-- Recherche -->
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               wire:model.debounce.300ms="search" 
                               class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                               placeholder="Rechercher par patient, prescripteur...">
                        @if($search)
                            <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-times text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-3">
                        <!-- Filtres avancés -->
                        <button wire:click="toggleFilters" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-filter mr-2"></i>
                            Filtres
                        </button>
                        
                        <!-- Export -->
                        <button class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                    </div>
                </div>

                <!-- Filtres avancés -->
                @if($showFilters)
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prescripteur</label>
                            <select wire:model="filterPrescripteur" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white">
                                <option value="">Tous les prescripteurs</option>
                                @foreach($prescripteurs as $prescripteur)
                                    <option value="{{ $prescripteur->id }}">Dr. {{ $prescripteur->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type d'urgence</label>
                            <select wire:model="filterUrgence" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white">
                                <option value="">Tous les types</option>
                                <option value="URGENCE-NUIT">Urgence Nuit</option>
                                <option value="URGENCE-JOUR">Urgence Jour</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button wire:click="resetFilters" class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Onglets -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex">
                    <button wire:click="$set('tab', 'termine')"
                            class="px-6 py-3 border-b-2 font-medium text-sm transition-all duration-200 {{ $tab === 'termine' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        <i class="fas fa-clock mr-2"></i>
                        Terminé
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $tab === 'termine' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                            {{ $analyseTermines->total() }}
                        </span>
                    </button>
                    <button wire:click="$set('tab', 'valide')"
                            class="px-6 py-3 border-b-2 font-medium text-sm transition-all duration-200 {{ $tab === 'valide' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        <i class="fas fa-check-circle mr-2"></i>
                        Validé
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $tab === 'valide' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                            {{ $analyseValides->total() }}
                        </span>
                    </button>
                </nav>
            </div>

            <!-- Tableau -->
            <div class="overflow-x-auto">
                @if($tab === 'termine')
                    @include('livewire.biologiste.partials.analyse-card', [
                        'prescriptions' => $analyseTermines,
                        'statusLabel' => 'Terminé',
                        'statusClass' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300',
                        'statusIcon' => 'fas fa-clock'
                    ])
                @elseif($tab === 'valide')
                    @include('livewire.biologiste.partials.analyse-card', [
                        'prescriptions' => $analyseValides,
                        'statusLabel' => 'Validé',
                        'statusClass' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                        'statusIcon' => 'fas fa-check-circle'
                    ])
                @endif
            </div>
        </div>
    </div>
</div>
