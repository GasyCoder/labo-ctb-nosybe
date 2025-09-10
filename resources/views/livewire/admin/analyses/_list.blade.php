{{-- Liste des analyses ultra-optimisée mobile --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="bg-gray-50 dark:bg-gray-700 px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-2 sm:space-y-0">
            <h6 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">
                @switch($selectedLevel)
                    @case('racines')
                        Analyses Racines
                        @break
                    @case('parents')
                        Panels Uniquement
                        @break
                    @case('normales')
                        Analyses Normales
                        @break
                    @case('enfants')
                        Sous-Analyses
                        @break
                    @case('tous')
                        Toutes les Analyses
                        @break
                    @default
                        Liste des Analyses
                @endswitch
                
                @if($selectedExamen && $examens->find($selectedExamen))
                    <span class="text-xs sm:text-sm font-normal text-gray-600 dark:text-gray-400">
                        - {{ $examens->find($selectedExamen)->abr }}
                    </span>
                @endif
                @if($search)
                    <span class="text-xs sm:text-sm font-normal text-gray-600 dark:text-gray-400 block sm:inline">
                        - "{{ Str::limit($search, 20) }}"
                    </span>
                @endif
            </h6>
            
            {{-- Légende - cachée sur mobile, visible sur tablet+ --}}
            <div class="hidden lg:flex items-center text-sm text-gray-600 dark:text-gray-400 space-x-4">
                @if($selectedLevel === 'tous' || $selectedLevel === 'racines')
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                        Parent
                    </div>
                @endif
                @if($selectedLevel === 'tous' || $selectedLevel === 'racines' || $selectedLevel === 'normales')
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                        Normal
                    </div>
                @endif
                @if($selectedLevel === 'tous' || $selectedLevel === 'enfants')
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-gray-400 rounded-full mr-2"></div>
                        Enfant
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    @if($this->analyses->count() > 0)
        {{-- Version Desktop : Tableau classique (inchangé) --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="w-40 px-6 py-4 text-left font-medium text-gray-900 dark:text-white">Code</th>
                        <th class="w-80 px-4 py-4 text-left font-medium text-gray-900 dark:text-white">Désignation</th>
                        @if($selectedLevel === 'enfants')
                            <th class="w-32 px-3 py-4 text-left font-medium text-gray-900 dark:text-white">Parent</th>
                        @endif
                        <th class="w-28 px-3 py-4 text-left font-medium text-gray-900 dark:text-white">Type</th>
                        <th class="w-28 px-3 py-4 text-left font-medium text-gray-900 dark:text-white">Examen</th>
                        <th class="w-24 px-3 py-4 text-right font-medium text-gray-900 dark:text-white">Prix</th>
                        @if($selectedLevel === 'parents')
                            <th class="w-24 px-3 py-4 text-center font-medium text-gray-900 dark:text-white">Sous-analyses</th>
                        @endif
                        <th class="w-24 px-3 py-4 text-center font-medium text-gray-900 dark:text-white">Statut</th>
                        <th class="w-32 px-3 py-4 text-center font-medium text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($this->analyses as $analyse)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="w-40 px-6 py-4">
                                <div class="flex items-center">
                                    @if($analyse->level === 'PARENT')
                                        <div class="w-3 h-3 bg-purple-500 rounded-full mr-3 flex-shrink-0"></div>
                                    @elseif($analyse->level === 'NORMAL')
                                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3 flex-shrink-0"></div>
                                    @else
                                        <div class="w-3 h-3 bg-gray-400 rounded-full mr-3 {{ $selectedLevel === 'enfants' ? '' : 'ml-6' }} flex-shrink-0"></div>
                                    @endif
                                    <span class="font-mono text-sm font-medium break-all
                                        {{ $analyse->is_bold ? 'font-bold' : '' }}
                                        {{ $analyse->level === 'PARENT' ? 'text-purple-800 dark:text-purple-300' : '' }}
                                        {{ $analyse->level === 'CHILD' ? 'text-gray-600 dark:text-gray-400 text-xs' : '' }}">
                                        {{ $analyse->code }}
                                    </span>
                                </div>
                            </td>
                            <td class="w-80 px-4 py-4">
                                <div class="{{ $analyse->is_bold ? 'font-bold' : '' }}
                                    {{ $analyse->level === 'PARENT' ? 'text-purple-900 dark:text-purple-200 font-semibold' : '' }}
                                    {{ $analyse->level === 'CHILD' && $selectedLevel !== 'enfants' ? 'text-gray-600 dark:text-gray-400 text-sm pl-4' : '' }} 
                                    break-words">
                                    {{ $analyse->designation }}
                                </div>
                                @if($analyse->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 break-words">{{ Str::limit($analyse->description, 80) }}</p>
                                @endif
                            </td>
                            @if($selectedLevel === 'enfants')
                                <td class="w-32 px-3 py-4">
                                    @if($analyse->parent)
                                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-2 py-1 rounded text-xs font-medium block text-center">
                                            {{ $analyse->parent->code }}
                                        </span>
                                    @endif
                                </td>
                            @endif
                            <td class="w-28 px-3 py-4">
                                @if($analyse->type)
                                    <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 py-1 rounded-full text-xs font-medium block text-center">
                                        {{ Str::limit($analyse->type->name, 8) }}
                                    </span>
                                @endif
                            </td>
                            <td class="w-28 px-3 py-4">
                                @if($analyse->examen)
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-md text-xs font-medium block text-center">
                                        {{ $analyse->examen->abr }}
                                    </span>
                                @endif
                            </td>
                            <td class="w-24 px-3 py-4 text-right">
                                @if($analyse->prix > 0)
                                    <span class="font-medium text-gray-900 dark:text-white text-sm whitespace-nowrap">
                                        {{ number_format($analyse->prix, 0, ',', ' ') }}
                                    </span>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Ar</div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 text-sm">Inclus</span>
                                @endif
                            </td>
                            @if($selectedLevel === 'parents')
                                <td class="w-24 px-3 py-4 text-center">
                                    @if($analyse->enfants && count($analyse->enfants) > 0)
                                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full text-xs font-medium">
                                            {{ count($analyse->enfants) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500 text-xs">0</span>
                                    @endif
                                </td>
                            @endif
                            <td class="w-24 px-3 py-4 text-center">
                                @if($analyse->status)
                                    <span class="inline-flex items-center justify-center bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 w-16 py-1 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 w-16 py-1 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Inactif
                                    </span>
                                @endif
                            </td>
                            <td class="w-32 px-3 py-4 text-center">
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
                                    <button wire:click="confirmDelete({{ $analyse->id }})" 
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

        {{-- Version Mobile et Tablet : Format simplifié et linéaire --}}
        <div class="lg:hidden">
            @foreach($this->analyses as $analyse)
                <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                    <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        {{-- Ligne 1: Code + Badge niveau + Prix --}}
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center min-w-0 flex-1">
                                {{-- Indicateur de niveau --}}
                                @if($analyse->level === 'PARENT')
                                    <div class="w-2.5 h-2.5 bg-purple-500 rounded-full mr-2 flex-shrink-0"></div>
                                @elseif($analyse->level === 'NORMAL')
                                    <div class="w-2.5 h-2.5 bg-blue-500 rounded-full mr-2 flex-shrink-0"></div>
                                @else
                                    <div class="w-2.5 h-2.5 bg-gray-400 rounded-full mr-2 flex-shrink-0"></div>
                                @endif
                                
                                {{-- Code --}}
                                <span class="font-mono text-sm font-medium mr-2 {{ $analyse->is_bold ? 'font-bold' : '' }}
                                    {{ $analyse->level === 'PARENT' ? 'text-purple-700 dark:text-purple-300' : '' }}
                                    {{ $analyse->level === 'CHILD' ? 'text-gray-600 dark:text-gray-400' : '' }}">
                                    {{ $analyse->code }}
                                </span>
                                
                                {{-- Badge niveau --}}
                                <span class="text-xs px-1.5 py-0.5 rounded
                                    {{ $analyse->level === 'PARENT' ? 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300' : '' }}
                                    {{ $analyse->level === 'NORMAL' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : '' }}
                                    {{ $analyse->level === 'CHILD' ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' : '' }}">
                                    {{ $analyse->level === 'PARENT' ? 'P' : ($analyse->level === 'NORMAL' ? 'N' : 'E') }}
                                </span>
                            </div>
                            
                            {{-- Prix --}}
                            <div class="text-right ml-2 flex-shrink-0">
                                @if($analyse->prix > 0)
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ number_format($analyse->prix, 0, ',', ' ') }} Ar
                                    </div>
                                @else
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Inclus</div>
                                @endif
                            </div>
                        </div>

                        {{-- Ligne 2: Désignation --}}
                        <div class="mb-2">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white leading-5 {{ $analyse->is_bold ? 'font-bold' : '' }}
                                {{ $analyse->level === 'PARENT' ? 'text-purple-900 dark:text-purple-200' : '' }}
                                {{ $analyse->level === 'CHILD' ? 'text-gray-700 dark:text-gray-300' : '' }}">
                                {{ $analyse->designation }}
                            </h4>
                            @if($analyse->description)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 leading-4">
                                    {{ Str::limit($analyse->description, 80) }}
                                </p>
                            @endif
                        </div>

                        {{-- Ligne 3: Métadonnées en ligne --}}
                        <div class="flex items-center text-xs space-x-3 mb-3">
                            {{-- Type --}}
                            @if($analyse->type)
                                <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded-full">
                                    {{ Str::limit($analyse->type->name, 10) }}
                                </span>
                            @endif

                            {{-- Examen --}}
                            @if($analyse->examen)
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded">
                                    {{ $analyse->examen->abr }}
                                </span>
                            @endif

                            {{-- Parent (pour les enfants) --}}
                            @if($selectedLevel === 'enfants' && $analyse->parent)
                                <span class="bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded">
                                    {{ $analyse->parent->code }}
                                </span>
                            @endif

                            {{-- Nombre d'enfants (pour les parents) --}}
                            @if($selectedLevel === 'parents' && $analyse->enfants && count($analyse->enfants) > 0)
                                <span class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded-full">
                                    {{ count($analyse->enfants) }} sous
                                </span>
                            @endif

                            {{-- Statut --}}
                            <span class="inline-flex items-center
                                {{ $analyse->status ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    @if($analyse->status)
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    @else
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    @endif
                                </svg>
                                {{ $analyse->status ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>

                        {{-- Ligne 4: Actions compactes --}}
                        <div class="flex space-x-1">
                            <button wire:click="show({{ $analyse->id }})" 
                                    class="flex-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 py-1.5 px-2 rounded text-xs font-medium transition-colors flex items-center justify-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Voir
                            </button>
                            <button wire:click="edit({{ $analyse->id }})" 
                                    class="flex-1 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 py-1.5 px-2 rounded text-xs font-medium transition-colors flex items-center justify-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Modifier
                            </button>
                            <button wire:click="confirmDelete({{ $analyse->id }})" 
                                    class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 py-1.5 px-2 rounded text-xs font-medium transition-colors flex items-center justify-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        @if($this->analyses->hasPages())
            <div class="px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200 dark:border-gray-600">
                {{ $this->analyses->links() }}
            </div>
        @endif
    @else
        {{-- État vide --}}
        <div class="text-center py-8 sm:py-12 px-4">
            <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h5 class="text-lg sm:text-xl font-medium text-gray-900 dark:text-white mb-2">Aucun résultat trouvé</h5>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-4 max-w-md mx-auto">
                @switch($selectedLevel)
                    @case('racines')
                        Aucune analyse racine ne correspond à vos critères.
                        @break
                    @case('parents')
                        Aucun panel ne correspond à vos critères.
                        @break
                    @case('normales')
                        Aucune analyse normale ne correspond à vos critères.
                        @break
                    @case('enfants')
                        Aucune sous-analyse ne correspond à vos critères.
                        @break
                    @default
                        Essayez de modifier vos critères de recherche ou vos filtres.
                @endswitch
            </p>
            @if($search || $selectedExamen || $selectedLevel !== 'racines')
                <button wire:click="resetFilters" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 sm:py-2 rounded-lg flex items-center mx-auto transition-colors text-sm sm:text-base">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Réinitialiser les filtres
                </button>
            @else
                <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 sm:py-2 rounded-lg flex items-center mx-auto transition-colors text-sm sm:text-base">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Créer une analyse
                </button>
            @endif
        </div>
    @endif
</div>