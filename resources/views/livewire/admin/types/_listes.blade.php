 <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h6 class="font-semibold text-gray-900 dark:text-white">
                        Liste des Types d'Analyse
                        @if($search)
                            <span class="text-sm font-normal text-gray-600 dark:text-gray-400">
                                - Recherche: "{{ $search }}"
                            </span>
                        @endif
                    </h6>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 space-x-6">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            Actif
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                            Inactif
                        </div>
                        @if($this->types->total() > 0)
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $this->types->total() }} type{{ $this->types->total() > 1 ? 's' : '' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($this->types->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="w-20 px-6 py-4 text-left font-medium text-gray-900 dark:text-white">ID</th>
                                <th class="min-w-[180px] py-4 text-left font-medium text-gray-900 dark:text-white">Nom</th>
                                <th class="min-w-[200px] py-4 text-left font-medium text-gray-900 dark:text-white">Libellé</th>
                                <th class="w-32 py-4 text-center font-medium text-gray-900 dark:text-white">Nb Analyses</th>
                                <th class="w-28 py-4 text-center font-medium text-gray-900 dark:text-white">Statut</th>
                                <th class="w-40 py-4 text-center font-medium text-gray-900 dark:text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($this->types as $type)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 w-20">
                                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-md text-sm font-medium">{{ $type->id }}</span>
                                    </td>
                                    <td class="py-4 pr-4 min-w-[180px]">
                                        <div class="font-medium text-gray-900 dark:text-white truncate">{{ $type->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-mono truncate">{{ strtolower($type->name) }}</div>
                                    </td>
                                    <td class="py-4 pr-4 min-w-[200px]">
                                        <div class="text-gray-900 dark:text-white truncate">{{ $type->libelle }}</div>
                                    </td>
                                    <td class="py-4 text-center w-32">
                                        @if($type->analyses_count > 0)
                                            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full text-sm font-medium whitespace-nowrap">
                                                {{ $type->analyses_count }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-sm whitespace-nowrap">0</span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center w-28">
                                        <button wire:click="toggleStatus({{ $type->id }})" 
                                                class="transition-colors duration-200 {{ $type->status ? 'text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300' : 'text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300' }}">
                                            @if($type->status)
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
                                        </button>
                                    </td>
                                    <td class="py-4 text-center w-40">
                                        <div class="flex justify-center space-x-2">
                                            <button wire:click="show({{ $type->id }})" 
                                                    class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200 hover:bg-indigo-200 dark:hover:bg-indigo-800 p-2 rounded-lg transition-colors"
                                                    title="Voir les détails">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="edit({{ $type->id }})" 
                                                    class="bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200 hover:bg-yellow-200 dark:hover:bg-yellow-800 p-2 rounded-lg transition-colors"
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
                @if($this->types->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Affichage de <span class="font-medium">{{ $this->types->firstItem() }}</span>
                                à <span class="font-medium">{{ $this->types->lastItem() }}</span>
                                sur <span class="font-medium">{{ $this->types->total() }}</span> résultats
                            </div>
                            <div>
                                {{ $this->types->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    @if($search)
                        {{-- État vide avec recherche active --}}
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white mb-2">Aucun type trouvé</h5>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Aucun type ne correspond à votre recherche "{{ $search }}".
                        </p>
                        <button wire:click="resetSearch" class="bg-purple-600 dark:bg-purple-500 hover:bg-purple-700 dark:hover:bg-purple-600 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Effacer la recherche
                        </button>
                    @else
                        {{-- État vide global --}}
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white mb-2">Aucun type d'analyse trouvé</h5>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Commencez par créer votre premier type d'analyse.</p>
                        <button wire:click="create" class="bg-purple-600 dark:bg-purple-500 hover:bg-purple-700 dark:hover:bg-purple-600 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Créer un type
                        </button>
                    @endif
                </div>
            @endif
        </div>