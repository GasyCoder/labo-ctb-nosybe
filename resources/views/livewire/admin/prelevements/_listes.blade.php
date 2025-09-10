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
                                            <button wire:click="confirmDelete({{ $prelevement->id }})" 
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
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
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