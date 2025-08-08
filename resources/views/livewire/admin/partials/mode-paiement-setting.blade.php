<!-- Section 2: Méthodes de paiement -->
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-b border-blue-200 dark:border-blue-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                Méthodes de paiement
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Liste des méthodes existantes -->
                            @if(count($payment_methods) > 0)
                                <div class="space-y-3">
                                    <h4 class="text-md font-medium text-gray-900 dark:text-white">Méthodes configurées</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($payment_methods as $method)
                                            @if(isset($editingPaymentMethod) && $editingPaymentMethod == $method['id'])
                                                {{-- MODE ÉDITION --}}
                                                <div class="col-span-1 md:col-span-2 p-4 bg-blue-50 dark:bg-blue-900/30 border-2 border-blue-200 dark:border-blue-600 rounded-lg">
                                                    <form wire:submit.prevent="sauvegarderPaymentMethod({{ $method['id'] }})">
                                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
                                                                <input type="text" 
                                                                       wire:model="edit_payment_method.code" 
                                                                       class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white">
                                                                @error('edit_payment_method.code')
                                                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Libellé</label>
                                                                <input type="text" 
                                                                       wire:model="edit_payment_method.label" 
                                                                       class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white">
                                                                @error('edit_payment_method.label')
                                                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ordre</label>
                                                                <input type="number" 
                                                                       wire:model="edit_payment_method.display_order" 
                                                                       min="1"
                                                                       class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white">
                                                                @error('edit_payment_method.display_order')
                                                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                            <div class="flex items-end space-x-2">
                                                                <button type="submit" 
                                                                        wire:loading.attr="disabled"
                                                                        class="flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition-colors">
                                                                    <em class="ni ni-check text-sm mr-1" wire:loading.remove wire:target="sauvegarderPaymentMethod({{ $method['id'] }})"></em>
                                                                    <svg class="animate-spin w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" wire:loading wire:target="sauvegarderPaymentMethod({{ $method['id'] }})">
                                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                    </svg>
                                                                    <span wire:loading.remove wire:target="sauvegarderPaymentMethod({{ $method['id'] }})">Sauver</span>
                                                                </button>
                                                                <button type="button" 
                                                                        wire:click="annulerEdition"
                                                                        class="flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-md transition-colors">
                                                                    <em class="ni ni-cross text-sm mr-1"></em>
                                                                    Annuler
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 flex items-center">
                                                            <input type="checkbox" 
                                                                   wire:model="edit_payment_method.is_active" 
                                                                   id="edit_is_active_{{ $method['id'] }}" 
                                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                            <label for="edit_is_active_{{ $method['id'] }}" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                Méthode active
                                                            </label>
                                                        </div>
                                                    </form>
                                                </div>
                                            @else
                                                {{-- MODE AFFICHAGE --}}
                                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="flex-shrink-0">
                                                            @if($method['is_active'])
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                    <em class="text-xs ni ni-check-thick"></em>
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-600 dark:text-red-200">
                                                                    <em class="text-xs ni ni-cross"></em>
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <p class="font-medium text-gray-900 dark:text-white">{{ $method['label'] }}</p>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $method['code'] }} • Ordre: {{ $method['display_order'] }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-3">
                                                        <!-- Switch Toggle -->
                                                        <div class="flex items-center">
                                                            <label class="relative inline-flex items-center cursor-pointer">
                                                                <input type="checkbox" 
                                                                       value="" 
                                                                       class="sr-only peer" 
                                                                       {{ $method['is_active'] ? 'checked' : '' }}
                                                                       wire:click="togglePaymentMethodStatus({{ $method['id'] }})">
                                                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                            </label>
                                                        </div>
                                                        
                                                        <!-- Bouton Modifier -->
                                                        <button wire:click="modifierPaymentMethod({{ $method['id'] }})" 
                                                                class="p-2 rounded-md text-blue-600 hover:text-blue-800 hover:bg-blue-50 dark:text-blue-400 dark:hover:text-blue-200 dark:hover:bg-blue-900/20 transition-colors duration-200"
                                                                title="Modifier">
                                                            <em class="ni ni-edit text-lg"></em>
                                                        </button>
                                                        
                                                        <!-- Bouton Supprimer -->
                                                        <button type="button"
                                                                onclick="
                                                                    if (confirm('Êtes-vous sûr de vouloir supprimer la méthode « {{ $method['label'] }} » ?')) {
                                                                        @this.supprimerPaymentMethod({{ $method['id'] }});
                                                                    }
                                                                "
                                                                class="p-2 rounded-md text-red-600 hover:text-red-800 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-200 dark:hover:bg-red-900/20 transition-colors duration-200"
                                                                title="Supprimer">
                                                            <em class="ni ni-trash text-lg"></em>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Formulaire d'ajout -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Ajouter une nouvelle méthode</h4>
                                <form wire:submit.prevent="ajouterPaymentMethod">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Code *
                                            </label>
                                            <input type="text" wire:model="new_payment_method.code" placeholder="ESPECES" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 text-gray-900 dark:text-white">
                                            @error('new_payment_method.code')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Libellé *
                                            </label>
                                            <input type="text" wire:model="new_payment_method.label" placeholder="Espèces" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 text-gray-900 dark:text-white">
                                            @error('new_payment_method.label')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Ordre d'affichage
                                            </label>
                                            <input type="number" wire:model="new_payment_method.display_order" min="1" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 text-gray-900 dark:text-white">
                                            @error('new_payment_method.display_order')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="flex items-end">
                                            <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-600 transition ease-in-out duration-150 disabled:opacity-50">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="ajouterPaymentMethod">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" wire:loading wire:target="ajouterPaymentMethod">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span wire:loading.remove wire:target="ajouterPaymentMethod">Ajouter</span>
                                                <span wire:loading wire:target="ajouterPaymentMethod">Ajout...</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center">
                                        <input type="checkbox" wire:model="new_payment_method.is_active" id="new_is_active" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                        <label for="new_is_active" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Activer immédiatement
                                        </label>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>