{{-- livewire.technicien.partials.filtres-technicien --}}
<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mb-8">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filtres et recherche</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Recherche globale --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Recherche globale
                </label>
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Référence, nom du patient, prescripteur..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:bg-gray-700">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            {{-- Filtre par statut --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Statut
                </label>
                <select wire:model.live="statusFilter" 
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:bg-gray-700">
                    <option value="">Tous les statuts</option>
                    <option value="EN_ATTENTE">En attente</option>
                    <option value="EN_COURS">En cours</option>
                    <option value="TERMINE">Terminé</option>
                </select>
            </div>
            
            {{-- Bouton réinitialiser --}}
            <div class="flex items-end">
                <button wire:click="$set('search', '')" 
                        wire:click="$set('statusFilter', '')"
                        class="w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>
</div>