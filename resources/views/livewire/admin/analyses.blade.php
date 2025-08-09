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
                    @if($selectedExamen || $search)
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

    {{-- Mode Liste --}}
    @if($mode === 'list')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h6 class="font-semibold text-gray-900 dark:text-white">
                        Liste des Analyses
                        @if($selectedExamen && $examens->find($selectedExamen))
                            <span class="text-sm font-normal text-gray-600 dark:text-gray-400">
                                - {{ $examens->find($selectedExamen)->abr }}
                            </span>
                        @endif
                        @if($search)
                            <span class="text-sm font-normal text-gray-600 dark:text-gray-400">
                                - Recherche: "{{ $search }}"
                            </span>
                        @endif
                    </h6>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 space-x-6">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                            Parent
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                            Normal
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-gray-400 rounded-full mr-2"></div>
                            Enfant
                        </div>
                    </div>
                </div>
            </div>
            
            @if($this->analyses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="w-32 px-6 py-4 text-left font-medium text-gray-900 dark:text-white">Code</th>
                                <th class="min-w-[200px] py-4 text-left font-medium text-gray-900 dark:text-white">Désignation</th>
                                <th class="w-24 py-4 text-left font-medium text-gray-900 dark:text-white">Type</th>
                                <th class="w-24 py-4 text-left font-medium text-gray-900 dark:text-white">Examen</th>
                                <th class="w-24 py-4 text-right font-medium text-gray-900 dark:text-white">Prix</th>
                                <th class="w-24 py-4 text-center font-medium text-gray-900 dark:text-white">Statut</th>
                                <th class="w-32 py-4 text-center font-medium text-gray-900 dark:text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->analyses as $analyse)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 w-32">
                                        <div class="flex items-center">
                                            @if($analyse->level === 'PARENT')
                                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                            @elseif($analyse->level === 'NORMAL')
                                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                            @else
                                                <div class="w-3 h-3 bg-gray-400 rounded-full mr-3 ml-6"></div>
                                            @endif
                                            <span class="font-mono text-sm font-medium 
                                                {{ $analyse->is_bold ? 'font-bold' : '' }}
                                                {{ $analyse->level === 'PARENT' ? 'text-purple-800 dark:text-purple-300' : '' }}
                                                {{ $analyse->level === 'CHILD' ? 'text-gray-600 dark:text-gray-400 text-xs' : '' }}">
                                                {{ $analyse->code }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 pr-4 min-w-[200px] max-w-[300px]">
                                        <div class="{{ $analyse->is_bold ? 'font-bold' : '' }}
                                            {{ $analyse->level === 'PARENT' ? 'text-purple-900 dark:text-purple-200 font-semibold' : '' }}
                                            {{ $analyse->level === 'CHILD' ? 'text-gray-600 dark:text-gray-400 text-sm pl-4' : '' }} truncate">
                                            {{ $analyse->designation }}
                                        </div>
                                        @if($analyse->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">{{ Str::limit($analyse->description, 60) }}</p>
                                        @endif
                                    </td>
                                    <td class="py-4 pr-4 w-24">
                                        @if($analyse->type)
                                            <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 py-1 rounded-full text-xs font-medium truncate block">
                                                {{ $analyse->type->name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 pr-4 w-24">
                                        @if($analyse->examen)
                                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-md text-xs font-medium truncate block">
                                                {{ $analyse->examen->abr }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-right w-24">
                                        @if($analyse->prix > 0)
                                            <span class="font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ number_format($analyse->prix, 0, ',', ' ') }} Ar
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-sm whitespace-nowrap">Inclus</span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center w-24">
                                        @if($analyse->status)
                                            <span class="inline-flex items-center bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Actif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Inactif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center w-32">
                                        <div class="flex justify-center space-x-2">
                                            <button wire:click="show({{ $analyse->id }})" 
                                                    class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800 p-2 rounded-lg transition-colors"
                                                    title="Voir les détails">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="edit({{ $analyse->id }})" 
                                                    class="bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-800 p-2 rounded-lg transition-colors"
                                                    title="Modifier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($this->analyses->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Affichage de <span class="font-medium">{{ $this->analyses->firstItem() }}</span>
                                à <span class="font-medium">{{ $this->analyses->lastItem() }}</span>
                                sur <span class="font-medium">{{ $this->analyses->total() }}</span> résultats
                            </div>
                            <div>
                                {{ $this->analyses->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    @if($search || $selectedExamen)
                        {{-- État vide avec filtres actifs --}}
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white mb-2">Aucun résultat trouvé</h5>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Essayez de modifier vos critères de recherche ou vos filtres.
                        </p>
                        <button wire:click="resetFilters" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Réinitialiser les filtres
                        </button>
                    @else
                        {{-- État vide global --}}
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white mb-2">Aucune analyse trouvée</h5>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Commencez par créer votre première analyse.</p>
                        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Créer une analyse
                        </button>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- Mode Création --}}
    @if($mode === 'create')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="bg-blue-50 dark:bg-blue-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl">
                <h6 class="font-semibold text-blue-900 dark:text-blue-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Créer une Nouvelle Analyse
                </h6>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="store" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('code') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="code" 
                                   wire:model="code"
                                   placeholder="Ex: GLY">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Niveau <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="level" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('level') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                <option value="">Sélectionnez un niveau</option>
                                <option value="PARENT">PARENT (Panel)</option>
                                <option value="NORMAL">NORMAL</option>
                                <option value="CHILD">CHILD (Sous-analyse)</option>
                            </select>
                            @error('level')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Parent (si applicable)
                            </label>
                            <select wire:model="parent_id" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Aucun parent</option>
                                @if($analysesParents)
                                    @foreach($analysesParents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->code }} - {{ $parent->designation }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Désignation <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('designation') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="designation" 
                                   wire:model="designation"
                                   placeholder="Ex: Glycémie">
                            @error('designation')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="prix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Prix (Ar) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('prix') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="prix" 
                                   wire:model="prix"
                                   placeholder="0.00">
                            @error('prix')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea wire:model="description" 
                                  id="description"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Description optionnelle de l'analyse"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="examen_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Examen <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="examen_id" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('examen_id') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                <option value="">Sélectionnez un examen</option>
                                @if($examens)
                                    @foreach($examens as $examen)
                                        <option value="{{ $examen->id }}">{{ $examen->abr }} - {{ $examen->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('examen_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="type_id" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('type_id') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                <option value="">Sélectionnez un type</option>
                                @if($types)
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('type_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Unité
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                                   id="unite" 
                                   wire:model="unite"
                                   placeholder="Ex: g/l, mmol/l">
                        </div>

                        <div>
                            <label for="ordre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Ordre d'affichage
                            </label>
                            <input type="number" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                                   id="ordre" 
                                   wire:model="ordre"
                                   placeholder="99">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="valeur_ref" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Valeurs de référence
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                                   id="valeur_ref" 
                                   wire:model="valeur_ref"
                                   placeholder="Ex: 3.89 - 6.05">
                        </div>

                        <div>
                            <label for="suffixe" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Suffixe
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                                   id="suffixe" 
                                   wire:model="suffixe"
                                   placeholder="Suffixe optionnel">
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_bold" 
                                   wire:model="is_bold"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="is_bold" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Texte en gras
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="status" 
                                   wire:model="status"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                   checked>
                            <label for="status" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Analyse active
                            </label>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Enregistrer
                        </button>
                        <button type="button" wire:click="backToList" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Mode Édition --}}
    @if($mode === 'edit' && $analyse)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl">
                <h6 class="font-semibold text-yellow-900 dark:text-yellow-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier l'Analyse: {{ $analyse->code }}
                </h6>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="update" class="space-y-6">
                    {{-- Même contenu que le mode création, mais avec les valeurs pré-remplies --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="edit_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white @error('code') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="edit_code" 
                                   wire:model="code">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="edit_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Niveau <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="level" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white @error('level') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                <option value="PARENT">PARENT (Panel)</option>
                                <option value="NORMAL">NORMAL</option>
                                <option value="CHILD">CHILD (Sous-analyse)</option>
                            </select>
                            @error('level')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="edit_parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Parent (si applicable)
                            </label>
                            <select wire:model="parent_id" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Aucun parent</option>
                                @if($analysesParents)
                                    @foreach($analysesParents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->code }} - {{ $parent->designation }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    {{-- Autres champs similaires au mode création... --}}

                    <div class="flex space-x-4">
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Mettre à jour
                        </button>
                        <button type="button" wire:click="backToList" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Mode Affichage --}}
    @if($mode === 'show' && $analyse)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="bg-indigo-50 dark:bg-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl">
                <h6 class="font-semibold text-indigo-900 dark:text-indigo-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Détails de l'Analyse: {{ $analyse->code }}
                </h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Code :</span>
                            <span class="font-mono bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-md text-sm">{{ $analyse->code }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Désignation :</span>
                            <span class="font-medium text-gray-900 dark:text-white {{ $analyse->is_bold ? 'font-bold' : '' }}">{{ $analyse->designation }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Niveau :</span>
                            <span class="
                                @if($analyse->level === 'PARENT') bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200
                                @elseif($analyse->level === 'NORMAL') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 @endif
                                px-2 py-1 rounded-full text-sm font-medium">
                                {{ $analyse->level }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Prix :</span>
                            <span class="font-bold text-green-600 dark:text-green-400">{{ number_format($analyse->prix, 0, ',', ' ') }} Ar</span>
                        </div>
                        @if($analyse->parent)
                            <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="font-medium text-gray-600 dark:text-gray-400">Parent :</span>
                                <span class="text-gray-900 dark:text-white">{{ $analyse->parent->code }} - {{ $analyse->parent->designation }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Examen :</span>
                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-md text-sm">
                                {{ $analyse->examen ? $analyse->examen->abr . ' - ' . $analyse->examen->name : 'N/A' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Type :</span>
                            <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 py-1 rounded-full text-sm">
                                {{ $analyse->type ? $analyse->type->name : 'N/A' }}
                            </span>
                        </div>
                        @if($analyse->valeur_ref)
                            <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="font-medium text-gray-600 dark:text-gray-400">Valeurs de référence :</span>
                                <span class="text-gray-900 dark:text-white">{{ $analyse->valeur_ref }} {{ $analyse->unite }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Statut :</span>
                            @if($analyse->status)
                                <span class="inline-flex items-center bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Inactif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($analyse->description)
                    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Description</h4>
                        <p class="text-gray-700 dark:text-gray-300">{{ $analyse->description }}</p>
                    </div>
                @endif

                @if($analyse->enfants && count($analyse->enfants) > 0)
                    <div class="mt-6">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-4">Analyses enfants ({{ count($analyse->enfants) }})</h4>
                        <div class="space-y-2">
                            @foreach($analyse->enfants as $enfant)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-mono text-sm font-medium dark:text-white">{{ $enfant->code }}</span>
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $enfant->designation }}</span>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($enfant->prix, 0, ',', ' ') }} Ar</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex space-x-4 mt-8">
                    <button wire:click="edit({{ $analyse->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </button>
                    <button wire:click="backToList" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Retour à la liste
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>