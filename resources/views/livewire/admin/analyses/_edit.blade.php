{{-- resources/views/livewire/admin/analyses/_edit.blade.php - Version Mobile Optimisée --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mx-2 sm:mx-0">
    <div class="bg-yellow-50 dark:bg-yellow-900/20 px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl">
        <h6 class="font-semibold text-sm sm:text-base text-yellow-900 dark:text-yellow-200 flex items-center">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span class="truncate">Modifier l'Analyse: {{ $analyse->code }}</span>
        </h6>
    </div>
    
    <div class="p-3 sm:p-6">
        <form wire:submit.prevent="update" class="space-y-4 sm:space-y-6">
            {{-- Section 1: Informations de base --}}
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 sm:p-6 bg-gray-50 dark:bg-gray-700/50">
                <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="truncate">Informations principales</span>
                </h3>

                {{-- Première ligne : Code, Niveau, Parent (mobile: stack, tablet+: grid) --}}
                <div class="space-y-4 sm:grid sm:grid-cols-2 lg:grid-cols-3 sm:gap-4 sm:space-y-0">
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('code') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
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
                                class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('level') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                            <option value="">Sélectionnez un niveau</option>
                            <option value="PARENT">PARENT (Panel)</option>
                            <option value="NORMAL">NORMAL</option>
                            <option value="CHILD">CHILD (Sous-analyse)</option>
                        </select>
                        @error('level')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2 lg:col-span-1">
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Parent (si applicable)
                        </label>
                        <select wire:model="parent_id" 
                                class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Aucun parent</option>
                            @if($analysesParents)
                                @foreach($analysesParents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->code }} - {{ Str::limit($parent->designation, 30) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                {{-- Deuxième ligne : Désignation et Prix --}}
                <div class="mt-4 space-y-4 sm:grid sm:grid-cols-1 lg:grid-cols-2 sm:gap-4 sm:space-y-0">
                    <div>
                        <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Désignation <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('designation') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
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
                               class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('prix') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                               id="prix" 
                               wire:model="prix"
                               placeholder="0.00">
                        @error('prix')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea wire:model="description" 
                              id="description"
                              rows="3"
                              class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white resize-none"
                              placeholder="Description optionnelle de l'analyse"></textarea>
                </div>
            </div>

            {{-- Section 2: Paramètres techniques --}}
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 sm:p-6 bg-blue-50 dark:bg-blue-900/20">
                <h3 class="text-base sm:text-lg font-medium text-blue-900 dark:text-blue-200 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="truncate">Paramètres techniques</span>
                </h3>

                {{-- Première ligne technique --}}
                <div class="space-y-4 sm:grid sm:grid-cols-2 sm:gap-4 sm:space-y-0">
                    <div>
                        <label for="examen_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Examen <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="examen_id" 
                                class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('examen_id') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                            <option value="">Sélectionnez un examen</option>
                            @if($examens)
                                @foreach($examens as $examen)
                                    <option value="{{ $examen->id }}">{{ $examen->abr }} - {{ Str::limit($examen->name, 25) }}</option>
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
                                class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('type_id') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
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
                </div>

                {{-- Deuxième ligne technique --}}
                <div class="mt-4 space-y-4 sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:gap-4 sm:space-y-0">
                    <div class="lg:col-span-2">
                        <label for="valeur_ref" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Valeurs de référence
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                               id="valeur_ref" 
                               wire:model="valeur_ref"
                               placeholder="Ex: 3.89 - 6.05">
                    </div>

                    <div>
                        <label for="unite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Unité
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                               id="unite" 
                               wire:model="unite"
                               placeholder="g/l, mmol/l">
                    </div>

                    <div>
                        <label for="ordre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Ordre
                        </label>
                        <input type="number" 
                               class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                               id="ordre" 
                               wire:model="ordre"
                               placeholder="99">
                    </div>
                </div>

                {{-- Suffixe --}}
                <div class="mt-4">
                    <label for="suffixe" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Suffixe
                    </label>
                    <input type="text" 
                           class="w-full px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                           id="suffixe" 
                           wire:model="suffixe"
                           placeholder="Suffixe optionnel">
                </div>
            </div>

            {{-- Section 3: Options d'affichage --}}
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 sm:p-6 bg-green-50 dark:bg-green-900/20">
                <h3 class="text-base sm:text-lg font-medium text-green-900 dark:text-green-200 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                    </svg>
                    Options et statut
                </h3>

                <div class="space-y-4 sm:flex sm:items-start sm:space-y-0 sm:space-x-8">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   id="is_bold" 
                                   wire:model="is_bold"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div class="ml-3">
                            <label for="is_bold" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="font-bold">Texte en gras</span>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Affiche en gras dans les résultats</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   id="status" 
                                   wire:model="status"
                                   class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div class="ml-3">
                            <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="text-green-700 dark:text-green-300">Analyse active</span>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Disponible dans les listes</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informations sur les enfants si applicable --}}
            @if($analyse->enfants && count($analyse->enfants) > 0)
                <div class="border border-amber-200 dark:border-amber-600 rounded-lg p-3 sm:p-6 bg-amber-50 dark:bg-amber-900/20">
                    <h3 class="text-base sm:text-lg font-medium text-amber-900 dark:text-amber-200 mb-3 sm:mb-4 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Sous-analyses ({{ count($analyse->enfants) }})
                    </h3>
                    <div class="text-sm text-amber-800 dark:text-amber-200 mb-3">
                        Cette analyse possède des sous-analyses. Modifiez-les individuellement via la liste principale.
                    </div>
                    <div class="space-y-2 sm:grid sm:grid-cols-1 md:grid-cols-2 sm:gap-3 sm:space-y-0">
                        @foreach($analyse->enfants as $enfant)
                            <div class="bg-white dark:bg-gray-800 border border-amber-200 dark:border-amber-600 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <span class="font-mono text-sm font-medium dark:text-white">{{ $enfant->code }}</span>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $enfant->designation }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded ml-2 flex-shrink-0">
                                        {{ number_format($enfant->prix, 0, ',', ' ') }} Ar
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Boutons d'action - Optimisés pour mobile --}}
            <div class="pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-600">
                {{-- Mobile: Boutons empilés, Desktop: En ligne --}}
                <div class="space-y-3 sm:flex sm:flex-wrap sm:items-center sm:space-y-0 sm:space-x-3">
                    <button type="submit" 
                            class="w-full sm:w-auto bg-yellow-600 hover:bg-yellow-700 text-white px-4 sm:px-6 py-3 rounded-lg flex items-center justify-center transition-colors font-medium text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Mettre à jour
                    </button>
                    
                    <button type="button" 
                            wire:click="backToList" 
                            class="w-full sm:w-auto bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-white px-4 sm:px-6 py-3 rounded-lg flex items-center justify-center transition-colors font-medium text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Annuler
                    </button>
                    
                    <button type="button" 
                            wire:click="show({{ $analyse->id }})" 
                            class="w-full sm:w-auto bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 text-indigo-700 dark:text-indigo-300 px-4 sm:px-6 py-3 rounded-lg flex items-center justify-center transition-colors font-medium text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="hidden sm:inline">Voir les détails</span>
                        <span class="sm:hidden">Détails</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>