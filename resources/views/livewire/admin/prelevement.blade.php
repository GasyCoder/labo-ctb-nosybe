<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-200">
    {{-- Toggle Dark/Light  currentView --}}
    <div class="fixed top-4 right-4 z-50">
        <button wire:click="toggleDark currentView" 
                class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200">
            <svg x-show="!dark currentView" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg x-show="dark currentView" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </button>
    </div>

    {{-- Messages flash --}}
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 flex items-center transition-colors duration-200">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-green-800 dark:text-green-300 font-medium">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 flex items-center transition-colors duration-200">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="text-red-800 dark:text-red-300 font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center transition-colors duration-200">
                @if($currentView === 'list')
                    <svg class="w-6 h-6 mr-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    Gestion des Prélèvements
                @elseif($currentView === 'create')
                    <svg class="w-6 h-6 mr-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouveau Prélèvement
                @elseif($currentView === 'edit')
                    <svg class="w-6 h-6 mr-3 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier le Prélèvement
                @elseif($currentView === 'show')
                    <svg class="w-6 h-6 mr-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Détails du Prélèvement
                @endif
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1 transition-colors duration-200">
                @if($currentView === 'list')
                    Gérez les différents types de prélèvements médicaux
                @else
                    <button wire:click="backToList" class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </button>
                @endif
            </p>
        </div>

        @if($currentView === 'list')
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                {{-- Barre de recherche --}}
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" 
                               wire: currentViewl.live.debounce.300ms="search"
                               placeholder="Rechercher un prélèvement..."
                               class="pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-64 bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-colors duration-200">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    @if($search)
                        <button wire:click="resetSearch" class="px-2 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm transition-colors duration-200" title="Effacer la recherche">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
                
                <button wire:click="create" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouveau Prélèvement
                </button>
            </div>
        @endif
    </div>

    {{--  currentView Liste --}}
    @if($currentView === 'list')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl transition-colors duration-200">
                <div class="flex justify-between items-center">
                    <h6 class="font-semibold text-gray-900 dark:text-white transition-colors duration-200">
                        Liste des Prélèvements
                        @if($search)
                            <span class="text-sm font-normal text-gray-600 dark:text-gray-400">
                                - Recherche: "{{ $search }}"
                            </span>
                        @endif
                    </h6>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 space-x-6 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            Actif
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                            Inactif
                        </div>
                        @if($this->prelevements->total() > 0)
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $this->prelevements->total() }} prélèvement{{ $this->prelevements->total() > 1 ? 's' : '' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($this->prelevements->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 transition-colors duration-200">
                            <tr>
                                <th class="text-left px-6 py-4 font-medium text-gray-900 dark:text-white">Nom</th>
                                <th class="text-left py-4 font-medium text-gray-900 dark:text-white">Description</th>
                                <th class="text-right py-4 font-medium text-gray-900 dark:text-white">Prix</th>
                                <th class="text-center py-4 font-medium text-gray-900 dark:text-white">Quantité</th>
                                <th class="text-center py-4 font-medium text-gray-900 dark:text-white">Statut</th>
                                <th class="text-center py-4 font-medium text-gray-900 dark:text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600 transition-colors duration-200">
                            @foreach($this->prelevements as $prelevement)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $prelevement->nom }}</div>
                                    </td>
                                    <td class="py-4 pr-4">
                                        <div class="text-gray-600 dark:text-gray-300 text-sm">
                                            {{ Str::limit($prelevement->description, 80) }}
                                        </div>
                                    </td>
                                    <td class="py-4 text-right">
                                        <span class="font-medium text-emerald-600 dark:text-emerald-400">
                                            {{ number_format($prelevement->prix, 0, ',', ' ') }} Ar
                                        </span>
                                    </td>
                                    <td class="py-4 text-center">
                                        <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full text-sm font-medium">
                                            {{ $prelevement->quantite }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center">
                                        <button wire:click="toggleStatus({{ $prelevement->id }})" 
                                                class="transition-colors duration-200">
                                            @if($prelevement->is_active)
                                                <span class="inline-flex items-center bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 px-2 py-1 rounded-full text-xs font-medium">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Actif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 px-2 py-1 rounded-full text-xs font-medium">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Inactif
                                                </span>
                                            @endif
                                        </button>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <button wire:click="show({{ $prelevement->id }})" 
                                                    class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-900/75 p-2 rounded-lg transition-colors duration-200"
                                                    title="Voir les détails">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="edit({{ $prelevement->id }})" 
                                                    class="bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-900/75 p-2 rounded-lg transition-colors duration-200"
                                                    title="Modifier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="delete({{ $prelevement->id }})" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce prélèvement ?')"
                                                    class="bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/75 p-2 rounded-lg transition-colors duration-200"
                                                    title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                @if($this->prelevements->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300 transition-colors duration-200">
                                Affichage de <span class="font-medium">{{ $this->prelevements->firstItem() }}</span>
                                à <span class="font-medium">{{ $this->prelevements->lastItem() }}</span>
                                sur <span class="font-medium">{{ $this->prelevements->total() }}</span> résultats
                            </div>
                            <div>
                                {{ $this->prelevements->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    @if($search)
                        {{-- État vide avec recherche active --}}
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-200">Aucun prélèvement trouvé</h5>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 transition-colors duration-200">
                            Aucun prélèvement ne correspond à votre recherche "{{ $search }}".
                        </p>
                        <button wire:click="resetSearch" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Effacer la recherche
                        </button>
                    @else
                        {{-- État vide global --}}
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-200">Aucun prélèvement trouvé</h5>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 transition-colors duration-200">Commencez par créer votre premier prélèvement.</p>
                        <button wire:click="create" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Créer un prélèvement
                        </button>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{--  currentView Création --}}
    @if($currentView === 'create')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="bg-emerald-50 dark:bg-emerald-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl transition-colors duration-200">
                <h6 class="font-semibold text-emerald-900 dark:text-emerald-300 flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Créer un Nouveau Prélèvement
                </h6>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="store" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Nom du Prélèvement <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('nom') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="nom" 
                                   wire: currentViewl="nom"
                                   placeholder="Ex: Prélèvement sang">
                            @error('nom')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="prix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Prix (Ar) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('prix') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="prix" 
                                   wire: currentViewl="prix"
                                   placeholder="0.00">
                            @error('prix')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea wire: currentViewl="description" 
                                  id="description"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('description') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                  placeholder="Description détaillée du prélèvement"></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="quantite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Quantité par défaut <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('quantite') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="quantite" 
                                   wire: currentViewl="quantite"
                                   placeholder="1">
                            @error('quantite')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-end">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="is_active" 
                                       wire: currentViewl="is_active"
                                       class="w-4 h-4 text-emerald-600 bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded focus:ring-emerald-500 focus:ring-2 transition-colors duration-200"
                                       checked>
                                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-200">
                                    Prélèvement actif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Enregistrer
                        </button>
                        <button type="button" wire:click="backToList" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
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

    {{--  currentView Édition --}}
    @if($currentView === 'edit' && $prelevement)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl transition-colors duration-200">
                <h6 class="font-semibold text-yellow-900 dark:text-yellow-300 flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier le Prélèvement: {{ $prelevement->nom }}
                </h6>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="update" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="edit_nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Nom du Prélèvement <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('nom') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="edit_nom" 
                                   wire: currentViewl="nom">
                            @error('nom')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="edit_prix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Prix (Ar) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('prix') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="edit_prix" 
                                   wire: currentViewl="prix">
                            @error('prix')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea wire: currentViewl="description" 
                                  id="edit_description"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('description') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="edit_quantite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Quantité par défaut <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('quantite') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="edit_quantite" 
                                   wire: currentViewl="quantite">
                            @error('quantite')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-end">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="edit_is_active" 
                                       wire: currentViewl="is_active"
                                       class="w-4 h-4 text-yellow-600 bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded focus:ring-yellow-500 focus:ring-2 transition-colors duration-200"
                                       {{ $prelevement->is_active ? 'checked' : '' }}>
                                <label for="edit_is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-200">
                                    Prélèvement actif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 dark:bg-yellow-700 dark:hover:bg-yellow-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Mettre à jour
                        </button>
                        <button type="button" wire:click="backToList" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
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

    {{--  currentView Affichage --}}
    @if($currentView === 'show' && $prelevement)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="bg-indigo-50 dark:bg-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl transition-colors duration-200">
                <h6 class="font-semibold text-indigo-900 dark:text-indigo-300 flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Détails du Prélèvement: {{ $prelevement->nom }}
                </h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Nom :</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $prelevement->nom }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Prix :</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($prelevement->prix, 0, ',', ' ') }} Ar</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Quantité :</span>
                            <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full text-sm font-medium">{{ $prelevement->quantite }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Statut :</span>
                            @if($prelevement->is_active)
                                <span class="inline-flex items-center bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 px-2 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 px-2 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Inactif
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Créé le :</span>
                            <span class="text-gray-900 dark:text-white">{{ $prelevement->created_at ? $prelevement->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Modifié le :</span>
                            <span class="text-gray-900 dark:text-white">{{ $prelevement->updated_at ? $prelevement->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        @if($prelevement->deleted_at)
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Supprimé le :</span>
                            <span class="text-red-600 dark:text-red-400">{{ $prelevement->deleted_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($prelevement->description)
                    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg transition-colors duration-200">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-200">Description</h4>
                        <p class="text-gray-700 dark:text-gray-300 transition-colors duration-200">{{ $prelevement->description }}</p>
                    </div>
                @endif

                <div class="flex space-x-4 mt-8">
                    <button wire:click="edit({{ $prelevement->id }})" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </button>
                    <button wire:click="backToList" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
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

