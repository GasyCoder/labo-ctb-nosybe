{{-- resources/views/livewire/admin/analyses.blade.php - Version Mobile Optimisée --}}
<div class="p-3 sm:p-6">
    {{-- Messages flash --}}
    @if (session()->has('message'))
        <div class="mb-4 sm:mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 sm:p-4 flex items-start">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-green-800 dark:text-green-200 font-medium text-sm sm:text-base">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 sm:mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 sm:p-4 flex items-start">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="text-red-800 dark:text-red-200 font-medium text-sm sm:text-base">{{ session('error') }}</span>
        </div>
    @endif

    {{-- En-tête responsive --}}
    <div class="mb-4 sm:mb-8">
        <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:justify-between sm:items-center">
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                    @if($mode === 'list')
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span class="truncate">Gestion des Analyses</span>
                    @elseif($mode === 'create')
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="truncate">Nouvelle Analyse</span>
                    @elseif($mode === 'edit')
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span class="truncate">Modifier l'Analyse</span>
                    @elseif($mode === 'show')
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="truncate">Détails de l'Analyse</span>
                    @endif
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm sm:text-base">
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
            
            {{-- Bouton Nouvelle Analyse - Mobile friendly --}}
            @if($mode === 'list')
                <div class="flex-shrink-0">
                    <button wire:click="create" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 sm:py-2 rounded-lg flex items-center justify-center transition-colors text-sm sm:text-base font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden sm:inline">Nouvelle Analyse</span>
                        <span class="sm:hidden">Nouvelle</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Filtres - Complètement réorganisés pour mobile --}}
        @if($mode === 'list')
            <div class="mt-4 sm:mt-6 space-y-3 sm:space-y-4">
                {{-- Première ligne mobile : Affichage et Recherche --}}
                <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                    {{-- Filtre par niveau d'affichage --}}
                    <div class="flex-1 sm:flex-none">
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
                    <div class="flex-1">
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
                </div>

                {{-- Deuxième ligne mobile : Autres filtres --}}
                <div class="flex flex-col sm:flex-row sm:items-end space-y-3 sm:space-y-0 sm:space-x-4">
                    {{-- Filtre par examen --}}
                    <div class="flex-1 sm:flex-none sm:w-40">
                        <label class="block text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-1">Examen:</label>
                        <select wire:model.live="selectedExamen" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Tous</option>
                            @if($examens)
                                @foreach($examens as $examen)
                                    <option value="{{ $examen->id }}">{{ $examen->abr }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Nombre par page --}}
                    <div class="flex-1 sm:flex-none sm:w-24">
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
                        <div class="flex-shrink-0">
                            <button wire:click="resetFilters" class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-lg text-sm transition-colors flex items-center justify-center" title="Réinitialiser tous les filtres">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Réinitialiser
                            </button>
                        </div>
                    @endif
                </div>
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

    {{-- Modal de confirmation de suppression - Optimisé mobile --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4 py-4">
            {{-- Arrière-plan --}}
            <div class="fixed inset-0 bg-black opacity-50" wire:click="closeDeleteModal"></div>
            
            {{-- Modal --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 max-w-sm sm:max-w-md w-full z-10 mx-3 sm:mx-0">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 6.5c-.77.833-.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-2">
                            Confirmer la suppression
                        </h3>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 sm:mb-6">
                            Êtes-vous sûr de vouloir supprimer l'analyse 
                            <strong class="break-words">"{{ $analyseToDelete ? $analyseToDelete->designation : '' }}"</strong> ?
                            <br><span class="text-red-600 dark:text-red-400">Cette action est irréversible.</span>
                        </p>
                    </div>
                </div>
                
                {{-- Boutons empilés sur mobile, côte à côte sur desktop --}}
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end space-y-reverse space-y-2 sm:space-y-0 sm:space-x-3">
                    <button wire:click="closeDeleteModal" 
                            class="w-full sm:w-auto px-4 py-2.5 sm:py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        Annuler
                    </button>
                    <button wire:click="delete" 
                            class="w-full sm:w-auto px-4 py-2.5 sm:py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors font-medium">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>