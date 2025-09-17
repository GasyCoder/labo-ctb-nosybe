<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl transition-colors duration-200">
                <h6 class="font-semibold text-yellow-900 dark:text-yellow-300 flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier le Prélèvement: {{ $prelevement->code }} - {{ $prelevement->denomination }}
                </h6>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="update" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="edit_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Code du Prélèvement <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('code') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="edit_code" 
                                   wire:model="code"
                                   style="text-transform: uppercase;">
                            @error('code')
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
                                   wire:model="prix">
                            @error('prix')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="edit_denomination" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                            Dénomination <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="denomination" 
                                  id="edit_denomination"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('denomination') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"></textarea>
                        @error('denomination')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="edit_type_tube_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Type de Tube Recommandé
                            </label>
                            <select wire:model="type_tube_id" 
                                    id="edit_type_tube_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('type_tube_id') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                <option value="">-- Sélectionner un type de tube --</option>
                                @foreach($this->typesTubes as $typeTube)
                                    <option value="{{ $typeTube->id }}">
                                        {{ $typeTube->code }} ({{ $typeTube->couleur }}) - {{ $typeTube->description }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type_tube_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="edit_quantite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Quantité par défaut <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('quantite') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="edit_quantite" 
                                   wire:model="quantite">
                            @error('quantite')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="edit_is_active" 
                               wire:model="is_active"
                               class="w-4 h-4 text-yellow-600 bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded focus:ring-yellow-500 focus:ring-2 transition-colors duration-200">
                        <label for="edit_is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            Prélèvement actif
                        </label>
                    </div>

                    {{-- Comparaison avant/après --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3">Modifications apportées</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            @if($code !== $prelevement->code)
                                <div>
                                    <span class="font-medium text-gray-600 dark:text-gray-400">Code:</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-red-600 line-through">{{ $prelevement->code }}</span>
                                        <span>→</span>
                                        <span class="text-green-600 font-medium">{{ $code }}</span>
                                    </div>
                                </div>
                            @endif
                            @if($prix != $prelevement->prix)
                                <div>
                                    <span class="font-medium text-gray-600 dark:text-gray-400">Prix:</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-red-600 line-through">{{ number_format($prelevement->prix, 0, ',', ' ') }} Ar</span>
                                        <span>→</span>
                                        <span class="text-green-600 font-medium">{{ number_format($prix, 0, ',', ' ') }} Ar</span>
                                    </div>
                                </div>
                            @endif
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