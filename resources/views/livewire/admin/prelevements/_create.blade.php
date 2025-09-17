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
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Code du Prélèvement <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('code') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="code" 
                                   wire:model="code"
                                   placeholder="Ex: PL1, PL2..."
                                   style="text-transform: uppercase;">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Le code sera automatiquement mis en majuscules</p>
                        </div>

                        <div>
                            <label for="prix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Prix (Ar) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('prix') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="prix" 
                                   wire:model="prix"
                                   placeholder="0.00">
                            @error('prix')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="denomination" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                            Dénomination <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="denomination" 
                                  id="denomination"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('denomination') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                  placeholder="Dénomination détaillée du prélèvement"></textarea>
                        @error('denomination')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type_tube_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Type de Tube Recommandé
                            </label>
                            <select wire:model="type_tube_id" 
                                    id="type_tube_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('type_tube_id') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
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
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Le type de tube sera suggéré automatiquement selon le type de prélèvement</p>
                        </div>

                        <div>
                            <label for="quantite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                Quantité par défaut <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200 @error('quantite') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="quantite" 
                                   wire:model="quantite"
                                   placeholder="1">
                            @error('quantite')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               wire:model="is_active"
                               class="w-4 h-4 text-emerald-600 bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded focus:ring-emerald-500 focus:ring-2 transition-colors duration-200"
                               checked>
                        <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-200">
                            Prélèvement actif
                        </label>
                    </div>

                    {{-- Aperçu du type de tube sélectionné --}}
                    @if($type_tube_id)
                        @php
                            $typeTubeSelectionne = $this->typesTubes->find($type_tube_id);
                        @endphp
                        @if($typeTubeSelectionne)
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                                <h4 class="font-medium text-blue-900 dark:text-blue-300 mb-2">Tube sélectionné</h4>
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300"
                                         style="background-color: {{ strtolower($typeTubeSelectionne->couleur) === 'rouge' ? '#dc2626' : (strtolower($typeTubeSelectionne->couleur) === 'bleu' ? '#2563eb' : (strtolower($typeTubeSelectionne->couleur) === 'vert' ? '#059669' : (strtolower($typeTubeSelectionne->couleur) === 'violet' ? '#7c3aed' : '#6b7280'))) }}"></div>
                                    <span class="font-medium text-blue-800 dark:text-blue-300">{{ $typeTubeSelectionne->code }}</span>
                                    <span class="text-blue-600 dark:text-blue-400">{{ $typeTubeSelectionne->couleur }}</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">{{ $typeTubeSelectionne->description }}</p>
                            </div>
                        @endif
                    @endif

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