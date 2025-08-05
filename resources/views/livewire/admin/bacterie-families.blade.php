<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-200">
    {{-- Toggle Dark/Light Mode --}}
    <div class="fixed top-4 right-4 z-50">
        <button wire:click="toggleDarkMode" 
                class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200">
            <svg x-show="!darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg x-show="darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <svg class="w-6 h-6 mr-3 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5m14 14H5m14-7H5"/>
                    </svg>
                    Familles de Bactéries
                @elseif($currentView === 'create')
                    <svg class="w-6 h-6 mr-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouvelle Famille
                @elseif($currentView === 'edit')
                    <svg class="w-6 h-6 mr-3 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier la Famille
                @elseif($currentView === 'show')
                    <svg class="w-6 h-6 mr-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Détails de la Famille
                @endif
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1 transition-colors duration-200">
                @if($currentView === 'list')
                    Gérez les familles de bactéries et leurs classifications
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
                <div class="flex items-center  space-x-2">
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search"
                               placeholder="Rechercher une famille..."
                               class="pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 w-64 bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-colors duration-200">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    @if($search)
                        <button wire:click="resetSearch" class="px-2 py- bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm transition-colors duration-200" title="Effacer la recherche">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
                
                <button wire:click="create" class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouvelle Famille
                </button>
            </div>
        @endif
    </div>

@if($currentView === 'list')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
        
        @if($this->familles->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 transition-colors duration-200">
                        <tr>
                            <th class="text-left px-6 py-4 font-medium text-gray-900 dark:text-white">Famille</th>
                            <th class="text-left px-2 py-4 font-medium text-gray-900 dark:text-white">Bactéries associées</th>
                            <th class="text-left px-2 py-4 font-medium text-gray-900 dark:text-white">Antibiotiques associés</th>
                            <th class="text-center px-2 py-4 font-medium text-gray-900 dark:text-white">Statut</th>
                            <th class="text-center px-2 py-4 font-medium text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600 transition-colors duration-200">
                        @foreach($this->familles as $famille)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <!-- Colonne Famille -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $famille->designation }}</div>
                                </td>
                                
                                <!-- Colonne Bactéries associées -->
                                <td class="px-2 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-md">
                                        @foreach($famille->bacteries->take(3) as $bacterie)
                                            <span @class([
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200' => $loop->parent->odd,
                                                'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200' => $loop->parent->even,
                                                'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' => $loop->iteration % 3 === 0
                                            ])>
                                                {{ $bacterie->designation }}
                                            </span>
                                        @endforeach
                                        
                                        @if($famille->bacteries->count() > 3)
                                            <x-dropdown>
                                                <x-slot name="trigger">
                                                    <button type="button" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                                                        +{{ $famille->bacteries->count() - 3 }} autres
                                                    </button>
                                                </x-slot>
                                                
                                                <x-slot name="content">
                                                    <div class="p-2 max-h-60 overflow-y-auto">
                                                        @foreach($famille->bacteries->slice(3) as $bacterie)
                                                            <div class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                                {{ $bacterie->designation }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </x-slot>
                                            </x-dropdown>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Colonne Antibiotiques associés -->
                                <td class="px-2 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-md">
                                        @foreach($famille->antibiotiques->take(3) as $antibiotique)
                                            <span @class([
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200' => $loop->parent->odd,
                                                'bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-200' => $loop->parent->even,
                                                'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-200' => $loop->iteration % 3 === 0
                                            ])>
                                                {{ $antibiotique->designation }}
                                            </span>
                                        @endforeach
                                        
                                        @if($famille->antibiotiques->count() > 3)
                                            <x-dropdown>
                                                <x-slot name="trigger">
                                                    <button type="button" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                                                        +{{ $famille->antibiotiques->count() - 3 }} autres
                                                    </button>
                                                </x-slot>
                                                
                                                <x-slot name="content">
                                                    <div class="p-2 max-h-60 overflow-y-auto">
                                                        @foreach($famille->antibiotiques->slice(3) as $antibiotique)
                                                            <div class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                                {{ $antibiotique->designation }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </x-slot>
                                            </x-dropdown>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Colonne Statut -->
                                <td class="px-2 py-4 whitespace-nowrap text-center">
                                    <button wire:click="toggleStatus({{ $famille->id }})" class="transition-colors duration-200">
                                        @if($famille->status)
                                            <span class="inline-flex items-center bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 px-2 py-1 rounded-full text-xs font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 px-2 py-1 rounded-full text-xs font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Inactive
                                            </span>
                                        @endif
                                    </button>
                                </td>
                                
                                <!-- Colonne Actions -->
                                <td class="px-2 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button wire:click="show({{ $famille->id }})" 
                                                class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-900/75 p-2 rounded-lg transition-colors duration-200"
                                                title="Voir les détails">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="edit({{ $famille->id }})" 
                                                class="bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-900/75 p-2 rounded-lg transition-colors duration-200"
                                                title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $famille->id }})" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette famille ?')"
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
            @if($this->familles->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600 transition-colors duration-200">
                    {{ $this->familles->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                @if($search)
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h5 class="text-xl font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-200">Aucune famille trouvée</h5>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 transition-colors duration-200">
                        Aucune famille ne correspond à votre recherche "{{ $search }}".
                    </p>
                    <button wire:click="resetSearch" class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors duration-200">
                        Effacer la recherche
                    </button>
                @else
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5m14 14H5"/>
                    </svg>
                    <h5 class="text-xl font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-200">Aucune famille trouvée</h5>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 transition-colors duration-200">Commencez par créer votre première famille de bactérie.</p>
                    <button wire:click="create" class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Créer une famille
                    </button>
                @endif
            </div>
        @endif
    </div>
@endif
    {{-- Vue Création --}}
    @if($currentView === 'create')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="bg-purple-50 dark:bg-purple-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl transition-colors duration-200">
                <h6 class="font-semibold text-purple-900 dark:text-purple-300 flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Créer une Nouvelle Famille de Bactérie
                </h6>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="store" class="space-y-6">
                    <div>
                        <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                            Famille <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('designation') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                               id="designation" 
                               wire:model="designation"
                               placeholder="Ex: ENTEROBACTERIACEAE">
                        @error('designation')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="status" 
                               wire:model="status"
                               class="w-4 h-4 text-purple-600 bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded focus:ring-purple-500 focus:ring-2 transition-colors duration-200"
                               checked>
                        <label for="status" class="ml-2 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            Famille active
                        </label>
                    </div>

                    <div class="flex space-x-4">
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
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

    {{-- Vue Édition --}}
    @if($currentView === 'edit' && $famille)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl transition-colors duration-200">
                <h6 class="font-semibold text-yellow-900 dark:text-yellow-300 flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier la Famille: {{ $famille->designation }}
                </h6>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="update" class="space-y-6">
                    <div>
                        <label for="edit_designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                            Famille <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('designation') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                               id="edit_designation" 
                               wire:model="designation">
                        @error('designation')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="edit_status" 
                               wire:model="status"
                               class="w-4 h-4 text-yellow-600 bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded focus:ring-yellow-500 focus:ring-2 transition-colors duration-200"
                               {{ $famille->status ? 'checked' : '' }}>
                        <label for="edit_status" class="ml-2 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            Famille active
                        </label>
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

    {{-- Vue Affichage --}}
    @if($currentView === 'show' && $famille)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="bg-indigo-50 dark:bg-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl transition-colors duration-200">
                <h6 class="font-semibold text-indigo-900 dark:text-indigo-300 flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Détails de la Famille: {{ $famille->designation }}
                </h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Famille :</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $famille->designation }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Nombre de bactéries :</span>
                            <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full text-sm font-medium">{{ $famille->bacteries->count() }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Statut :</span>
                            @if($famille->status)
                                <span class="inline-flex items-center bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 px-2 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 px-2 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Créée le :</span>
                            <span class="text-gray-900 dark:text-white">{{ $famille->created_at ? $famille->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Modifiée le :</span>
                            <span class="text-gray-900 dark:text-white">{{ $famille->updated_at ? $famille->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                @if($famille->bacteries->count() > 0)
                    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg transition-colors duration-200">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-4 transition-colors duration-200">Bactéries associées</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($famille->bacteries as $bacterie)
                                <span class="bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-300 px-3 py-1 rounded-full text-sm">
                                    {{ $bacterie->designation }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex space-x-4 mt-8">
                    <button wire:click="edit({{ $famille->id }})" class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
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