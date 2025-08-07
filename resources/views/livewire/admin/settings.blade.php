<div>
    <div class="py-6">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        ⚙️ Paramètres du système
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Configuration générale de l'application et gestion des commissions
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button wire:click="resetForm" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Réinitialiser
                    </button>
                    <button wire:click="sauvegarder" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:border-primary-700 focus:shadow-outline-primary active:bg-primary-600 transition ease-in-out duration-150 disabled:opacity-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" wire:loading>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>Sauvegarder</span>
                        <span wire:loading>Sauvegarde...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Messages de feedback -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Alerte changement de commission -->
        @if($showCommissionAlert && $impactChangement)
            <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            ⚠️ Changement de commission détecté
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>Le changement du pourcentage de commission aura l'impact suivant :</p>
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded border">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Paiements affectés</div>
                                    <div class="text-lg font-semibold">{{ $impactChangement['paiementsAfectes'] ?? 0 }}</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded border">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Ancien total</div>
                                    <div class="text-lg font-semibold">{{ number_format($impactChangement['ancienTotal'] ?? 0, 0, ',', ' ') }} Ar</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded border">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Différence</div>
                                    <div class="text-lg font-semibold {{ ($impactChangement['difference'] ?? 0) >= 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ ($impactChangement['difference'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($impactChangement['difference'] ?? 0, 0, ',', ' ') }} Ar
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-xs">
                                🤖 <strong>Le recalcul sera automatique</strong> après sauvegarde - toutes les commissions existantes seront mises à jour.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="sauvegarder">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Informations générales -->
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Informations de l'entreprise
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nom de l'entreprise *
                                    </label>
                                    <input type="text" wire:model="nom_entreprise" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 text-gray-900 dark:text-white">
                                    @error('nom_entreprise')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        NIF
                                    </label>
                                    <input type="text" wire:model="nif" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 text-gray-900 dark:text-white">
                                    @error('nif')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Statut juridique
                                    </label>
                                    <input type="text" wire:model="statut" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 text-gray-900 dark:text-white">
                                    @error('statut')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Format Unité monétaire *
                                    </label>
                                    <select wire:model="format_unite_argent" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 text-gray-900 dark:text-white">
                                        <option value="MGA">MGA</option>
                                        <option value="Ar">Ar</option>
                                        <option value="Ariary">Ariary</option>
                                    </select>
                                    @error('unite_argent')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuration des commissions -->
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 border-b border-yellow-200 dark:border-yellow-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Gestion des commissions prescripteurs
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="commission_prescripteur" id="commission_prescripteur" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="commission_prescripteur" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Activer les commissions prescripteurs
                                    </label>
                                </div>
                                @if($commission_prescripteur)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Activé
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Désactivé
                                    </span>
                                @endif
                            </div>

                            @if($commission_prescripteur)
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Pourcentage de commission (%) *
                                            </label>
                                            <div class="relative">
                                                <input type="number" wire:model.live="commission_prescripteur_pourcentage" step="0.01" min="0" max="100" class="w-full px-3 py-2 pr-12 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 text-gray-900 dark:text-white">
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">%</span>
                                                </div>
                                            </div>
                                            @error('commission_prescripteur_pourcentage')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="flex items-center justify-center p-6 bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-lg border border-primary-200 dark:border-primary-700">
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                                    {{ $commission_prescripteur_pourcentage }}%
                                                </div>
                                                <div class="text-xs text-primary-500 dark:text-primary-300 mt-1">
                                                    Taux actuel
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Exemple de calcul -->
                                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-2">📊 Exemple de calcul</h4>
                                        <div class="text-sm text-blue-800 dark:text-blue-300">
                                            <p>Pour un paiement de <strong>17 000 Ar</strong> :</p>
                                            <ul class="mt-1 list-disc list-inside space-y-1">
                                                <li><strong>Médecin</strong> : {{ number_format(17000 * ($commission_prescripteur_pourcentage / 100), 0, ',', ' ') }} Ar de commission</li>
                                                <li><strong>Biologie Solidaire</strong> : 0 Ar (pas de commission)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Colonne latérale - Statistiques -->
                <div class="space-y-6">
                    <!-- Configuration des remises -->
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Gestion des remises
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="activer_remise" id="activer_remise" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="activer_remise" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Activer les remises
                                    </label>
                                </div>
                            </div>

                            @if($activer_remise)
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Pourcentage de remise par défaut (%)
                                        </label>
                                        <div class="relative">
                                            <input type="number" wire:model="remise_pourcentage" step="0.01" min="0" max="100" class="w-full px-3 py-2 pr-12 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 text-gray-900 dark:text-white">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">%</span>
                                            </div>
                                        </div>
                                        @error('remise_pourcentage')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Aide -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 shadow-lg rounded-lg overflow-hidden border border-indigo-200 dark:border-indigo-700">
                        <div class="px-6 py-4">
                            <h3 class="text-lg font-medium text-indigo-900 dark:text-indigo-200 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                💡 Aide
                            </h3>
                        </div>
                        <div class="px-6 pb-6">
                            <div class="text-sm text-indigo-800 dark:text-indigo-300 space-y-2">
                                <p><strong>🤖 Recalcul automatique :</strong></p>
                                <p class="text-xs">Dès que vous changez le pourcentage de commission et sauvegardez, le système recalcule automatiquement toutes les commissions existantes.</p>
                                
                                <p class="pt-2"><strong>🧪 Biologie Solidaire :</strong></p>
                                <p class="text-xs">Les prescripteurs "Biologie Solidaire" ne perçoivent jamais de commission (toujours 0%).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>