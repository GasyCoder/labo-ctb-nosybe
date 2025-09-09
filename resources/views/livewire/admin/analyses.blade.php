{{-- resources/views/livewire/admin/analyses.blade.php --}}
<div class="p-6">
    {{-- Messages flash --}}
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 flex items-center">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-green-800 dark:text-green-200 font-medium">{{ session('message') }}</span>
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                @if($mode === 'list')
                    <svg class="w-6 h-6 mr-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Gestion des Analyses
                @elseif($mode === 'create')
                    <svg class="w-6 h-6 mr-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouvelle Analyse
                @elseif($mode === 'edit')
                    <svg class="w-6 h-6 mr-3 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier l'Analyse
                @elseif($mode === 'show')
                    <svg class="w-6 h-6 mr-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Détails de l'Analyse
                @endif
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                @if($mode === 'list')
                    Gérez les analyses médicales et leurs paramètres
                @else
                    <button wire:click="backToList" class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </button>
                @endif
            </p>
        </div>
        
    @if($mode === 'list')
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-3 lg:space-y-0 lg:space-x-3">
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 order-2 lg:order-1">
                {{-- NOUVEAU: Filtre par niveau d'affichage --}}
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Affichage:</label>
                    <select wire:model.live="selectedLevel" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="racines">Analyses racines ({{ $this->getAnalysesCountByLevel()['racines'] }})</option>
                        <option value="parents">Panels uniquement ({{ $this->getAnalysesCountByLevel()['parents'] }})</option>
                        <option value="normales">Analyses normales ({{ $this->getAnalysesCountByLevel()['normales'] }})</option>
                        <option value="enfants">Sous-analyses ({{ $this->getAnalysesCountByLevel()['enfants'] }})</option>
                        <option value="tous">Toutes les analyses ({{ $this->getAnalysesCountByLevel()['tous'] }})</option>
                    </select>
                </div>

                {{-- Barre de recherche --}}
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Rechercher:</label>
                    <div class="relative">
                        <input type="text" 
                            wire:model.live.debounce.300ms="search"
                            placeholder="Code, désignation..."
                            class="pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white w-48">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Filtre par examen --}}
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Examen:</label>
                    <select wire:model.live="selectedExamen" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Tous</option>
                        @if($examens)
                            @foreach($examens as $examen)
                                <option value="{{ $examen->id }}">{{ $examen->abr }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Nombre par page --}}
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Par page:</label>
                    <select wire:model.live="perPage" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                {{-- Boutons d'action pour les filtres --}}
                @if($selectedExamen || $search || $selectedLevel !== 'racines')
                    <button wire:click="resetFilters" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-lg text-sm transition-colors flex items-center" title="Réinitialiser tous les filtres">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>
                @endif
            </div>

            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors order-1 lg:order-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouvelle Analyse
            </button>
        </div>
    @endif

    </div>

    {{-- Contenu principal selon le mode --}}
    @switch($mode)
        @case('list')
            @include('livewire.admin.analyses._list')
            @break
        @case('create')
            @include('livewire.admin.analyses._create')
            @break
        @case('edit')
            @include('livewire.admin.analyses._edit')
            @break
        @case('show')
            @include('livewire.admin.analyses._show')
            @break
    @endswitch
</div>