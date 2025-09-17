<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="bg-blue-50 dark:bg-blue-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl">
        <h6 class="font-semibold text-blue-900 dark:text-blue-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Créer une Nouvelle Analyse
            @if($createWithChildren)
                <span class="ml-2 text-sm bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-2 py-1 rounded-full">
                    avec sous-analyses
                </span>
            @endif
        </h6>
    </div>
    <div class="p-6">
        <form wire:submit.prevent="store" class="space-y-8">
            {{-- SECTION 1: Informations principales --}}
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6 bg-gray-50 dark:bg-gray-700/50">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Informations principales
                </h3>
                
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
                        <select wire:model.live="level" 
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

                    @if(!$createWithChildren)
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
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
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

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea wire:model="description" 
                              id="description"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Description optionnelle de l'analyse"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
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

                <div class="flex items-center space-x-6 mt-6">
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
            </div>

            {{-- SECTION 2: Sous-analyses (si PARENT) --}}
            @if($level === 'PARENT' || $createWithChildren)
                <div class="border border-purple-200 dark:border-purple-600 rounded-lg p-6 bg-purple-50 dark:bg-purple-900/20">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-purple-900 dark:text-purple-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Sous-analyses
                            @if(count($sousAnalyses) > 0)
                                <span class="ml-2 bg-purple-200 dark:bg-purple-800 text-purple-800 dark:text-purple-200 px-2 py-1 rounded-full text-sm">
                                    {{ count($sousAnalyses) }}
                                </span>
                            @endif
                        </h3>
                        <button type="button" 
                                wire:click="addSousAnalyse" 
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Ajouter sous-analyse
                        </button>
                    </div>

                    @if(count($sousAnalyses) > 0)
                        <div class="space-y-4">
                            @foreach($sousAnalyses as $index => $sousAnalyse)
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                            Sous-analyse #{{ $index + 1 }}
                                        </h4>
                                        <div class="flex space-x-2">
                                            @if($index > 0)
                                                <button type="button" 
                                                        wire:click="moveSousAnalyseUp({{ $index }})"
                                                        class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 p-1 rounded"
                                                        title="Monter">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                    </svg>
                                                </button>
                                            @endif
                                            @if($index < count($sousAnalyses) - 1)
                                                <button type="button" 
                                                        wire:click="moveSousAnalyseDown({{ $index }})"
                                                        class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 p-1 rounded"
                                                        title="Descendre">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                            @endif
                                            <button type="button" 
                                                    wire:click="removeSousAnalyse({{ $index }})"
                                                    class="bg-red-100 dark:bg-red-900 hover:bg-red-200 dark:hover:bg-red-800 text-red-600 dark:text-red-300 p-1 rounded"
                                                    title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Code <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   wire:model="sousAnalyses.{{ $index }}.code"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white text-sm @error('sousAnalyses.'.$index.'.code') border-red-500 @enderror"
                                                   placeholder="Code">
                                            @error('sousAnalyses.'.$index.'.code')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Désignation <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   wire:model="sousAnalyses.{{ $index }}.designation"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white text-sm @error('sousAnalyses.'.$index.'.designation') border-red-500 @enderror"
                                                   placeholder="Désignation">
                                            @error('sousAnalyses.'.$index.'.designation')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Prix (Ar) <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" 
                                                   step="0.01"
                                                   wire:model="sousAnalyses.{{ $index }}.prix"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white text-sm @error('sousAnalyses.'.$index.'.prix') border-red-500 @enderror"
                                                   placeholder="0.00">
                                            @error('sousAnalyses.'.$index.'.prix')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Niveau <span class="text-red-500">*</span>
                                            </label>
                                            <select wire:model.live="sousAnalyses.{{ $index }}.level"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white text-sm @error('sousAnalyses.'.$index.'.level') border-red-500 @enderror">
                                                <option value="CHILD">CHILD</option>
                                                <option value="NORMAL">NORMAL</option>
                                                <option value="PARENT">PARENT (Panel)</option>
                                            </select>
                                            @error('sousAnalyses.'.$index.'.level')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Valeurs de référence
                                            </label>
                                            <input type="text" 
                                                   wire:model="sousAnalyses.{{ $index }}.valeur_ref"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white text-sm"
                                                   placeholder="Ex: 3.89 - 6.05">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Unité
                                            </label>
                                            <input type="text" 
                                                   wire:model="sousAnalyses.{{ $index }}.unite"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white text-sm"
                                                   placeholder="Ex: g/l">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Ordre
                                            </label>
                                            <input type="number" 
                                                   wire:model="sousAnalyses.{{ $index }}.ordre"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white text-sm"
                                                   readonly>
                                        </div>
                                    </div>

                                    {{-- Sous-section pour les sous-sous-analyses si cette sous-analyse est PARENT --}}
                                    @if(isset($sousAnalyses[$index]['level']) && $sousAnalyses[$index]['level'] === 'PARENT')
                                        <div class="mt-6 pt-4 border-t border-purple-200 dark:border-purple-600">
                                            <div class="flex justify-between items-center mb-4">
                                                <h5 class="text-md font-medium text-purple-800 dark:text-purple-300 flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                    </svg>
                                                    Sous-sous-analyses
                                                    @if(isset($sousAnalyses[$index]['children']) && count($sousAnalyses[$index]['children']) > 0)
                                                        <span class="ml-1 bg-purple-200 dark:bg-purple-800 text-purple-800 dark:text-purple-200 px-1 py-0.5 rounded-full text-xs">
                                                            {{ count($sousAnalyses[$index]['children']) }}
                                                        </span>
                                                    @endif
                                                </h5>
                                                <button type="button" 
                                                        wire:click="addChildToSousAnalyse({{ $index }})" 
                                                        class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded-md flex items-center transition-colors text-sm">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                    </svg>
                                                    Ajouter
                                                </button>
                                            </div>

                                            @if(isset($sousAnalyses[$index]['children']) && count($sousAnalyses[$index]['children']) > 0)
                                                <div class="space-y-3 ml-4">
                                                    @foreach($sousAnalyses[$index]['children'] as $cindex => $child)
                                                        <div class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md p-3">
                                                            <div class="flex justify-between items-center mb-3">
                                                                <h6 class="font-medium text-gray-800 dark:text-gray-200 text-xs">
                                                                    Sous-sous #{{ $cindex + 1 }}
                                                                </h6>
                                                                <div class="flex space-x-1">
                                                                    @if($cindex > 0)
                                                                        <button type="button" 
                                                                                wire:click="moveChildUp({{ $index }}, {{ $cindex }})"
                                                                                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 p-0.5 rounded text-xs"
                                                                                title="Monter">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                                            </svg>
                                                                        </button>
                                                                    @endif
                                                                    @if($cindex < count($sousAnalyses[$index]['children']) - 1)
                                                                        <button type="button" 
                                                                                wire:click="moveChildDown({{ $index }}, {{ $cindex }})"
                                                                                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 p-0.5 rounded text-xs"
                                                                                title="Descendre">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                                            </svg>
                                                                        </button>
                                                                    @endif
                                                                    <button type="button" 
                                                                            wire:click="removeChildFromSous({{ $index }}, {{ $cindex }})"
                                                                            class="bg-red-100 dark:bg-red-900 hover:bg-red-200 dark:hover:bg-red-800 text-red-600 dark:text-red-300 p-0.5 rounded text-xs"
                                                                            title="Supprimer">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                        Code <span class="text-red-500">*</span>
                                                                    </label>
                                                                    <input type="text" 
                                                                           wire:model="sousAnalyses.{{ $index }}.children.{{ $cindex }}.code"
                                                                           class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-400 focus:border-purple-400 dark:bg-gray-700 dark:text-white text-xs @error('sousAnalyses.'.$index.'.children.'.$cindex.'.code') border-red-500 @enderror"
                                                                           placeholder="Code">
                                                                    @error('sousAnalyses.'.$index.'.children.'.$cindex.'.code')
                                                                        <p class="mt-0.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                                    @enderror
                                                                </div>

                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                        Désignation <span class="text-red-500">*</span>
                                                                    </label>
                                                                    <input type="text" 
                                                                           wire:model="sousAnalyses.{{ $index }}.children.{{ $cindex }}.designation"
                                                                           class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-400 focus:border-purple-400 dark:bg-gray-700 dark:text-white text-xs @error('sousAnalyses.'.$index.'.children.'.$cindex.'.designation') border-red-500 @enderror"
                                                                           placeholder="Désignation">
                                                                    @error('sousAnalyses.'.$index.'.children.'.$cindex.'.designation')
                                                                        <p class="mt-0.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                                    @enderror
                                                                </div>

                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                        Prix (Ar) <span class="text-red-500">*</span>
                                                                    </label>
                                                                    <input type="number" 
                                                                           step="0.01"
                                                                           wire:model="sousAnalyses.{{ $index }}.children.{{ $cindex }}.prix"
                                                                           class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-400 focus:border-purple-400 dark:bg-gray-700 dark:text-white text-xs @error('sousAnalyses.'.$index.'.children.'.$cindex.'.prix') border-red-500 @enderror"
                                                                           placeholder="0.00">
                                                                    @error('sousAnalyses.'.$index.'.children.'.$cindex.'.prix')
                                                                        <p class="mt-0.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                                    @enderror
                                                                </div>

                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                        Niveau <span class="text-red-500">*</span>
                                                                    </label>
                                                                    <select wire:model="sousAnalyses.{{ $index }}.children.{{ $cindex }}.level"
                                                                            class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-400 focus:border-purple-400 dark:bg-gray-700 dark:text-white text-xs @error('sousAnalyses.'.$index.'.children.'.$cindex.'.level') border-red-500 @enderror">
                                                                        <option value="CHILD">CHILD</option>
                                                                        <option value="NORMAL">NORMAL</option>
                                                                    </select>
                                                                    @error('sousAnalyses.'.$index.'.children.'.$cindex.'.level')
                                                                        <p class="mt-0.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-2">
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                        Valeurs de référence
                                                                    </label>
                                                                    <input type="text" 
                                                                           wire:model="sousAnalyses.{{ $index }}.children.{{ $cindex }}.valeur_ref"
                                                                           class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-400 focus:border-purple-400 dark:bg-gray-700 dark:text-white text-xs"
                                                                           placeholder="Ex: 3.89 - 6.05">
                                                                </div>

                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                        Unité
                                                                    </label>
                                                                    <input type="text" 
                                                                           wire:model="sousAnalyses.{{ $index }}.children.{{ $cindex }}.unite"
                                                                           class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-400 focus:border-purple-400 dark:bg-gray-700 dark:text-white text-xs"
                                                                           placeholder="Ex: g/l">
                                                                </div>

                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                        Ordre
                                                                    </label>
                                                                    <input type="number" 
                                                                           wire:model="sousAnalyses.{{ $index }}.children.{{ $cindex }}.ordre"
                                                                           class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-400 focus:border-purple-400 dark:bg-gray-700 dark:text-white text-xs"
                                                                           readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm ml-4">
                                                    <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                    </svg>
                                                    <p>Aucune sous-sous-analyse ajoutée</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p>Aucune sous-analyse ajoutée</p>
                            <p class="text-sm">Cliquez sur "Ajouter sous-analyse" pour commencer</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Boutons d'action --}}
            <div class="flex space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    @if(($level === 'PARENT' || $createWithChildren) && count($sousAnalyses) > 0)
                        Enregistrer analyse + {{ count($sousAnalyses) }} sous-analyses
                    @else
                        Enregistrer l'analyse
                    @endif
                </button>
                <button type="button" wire:click="backToList" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-white px-6 py-3 rounded-lg flex items-center transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>