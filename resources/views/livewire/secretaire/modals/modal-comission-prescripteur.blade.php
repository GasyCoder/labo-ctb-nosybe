@if($showCommissionModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" wire:click="$set('showCommissionModal', false)">
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl" wire:click.stop>
                    <div class="bg-white dark:bg-gray-800 px-6 py-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Commissions de {{ $selectedPrescripteur?->nom_complet ?? '' }}
                            </h2>
                            <button wire:click="$set('showCommissionModal', false)" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Filtres de dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de début</label>
                                <input type="date" wire:model.live="dateDebut" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400">
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de fin</label>
                                <input type="date" wire:model.live="dateFin" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400">
                            </div>
                        </div>

                        <div class="flex justify-center mb-6">
                            <button wire:click="loadCommissionDetailsAll" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-600 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Voir toutes les données
                            </button>
                        </div>

                        <!-- Statistiques de la période -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-4 rounded-lg border border-green-200 dark:border-green-700">
                                <div class="text-sm font-medium text-green-600 dark:text-green-400">Prescriptions</div>
                                <div class="text-2xl font-bold text-green-800 dark:text-green-300">{{ $commissionDetails['total_prescriptions'] ?? 0 }}</div>
                            </div>
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-4 rounded-lg border border-blue-200 dark:border-blue-700">
                                <div class="text-sm font-medium text-blue-600 dark:text-blue-400">Montant analyses</div>
                                <div class="text-2xl font-bold text-blue-800 dark:text-blue-300">{{ number_format($commissionDetails['montant_total_analyses'] ?? 0, 0, ',', ' ') }} Ar</div>
                            </div>
                            <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 p-4 rounded-lg border border-purple-200 dark:border-purple-700">
                                <div class="text-sm font-medium text-purple-600 dark:text-purple-400">Total payé</div>
                                <div class="text-2xl font-bold text-purple-800 dark:text-purple-300">{{ number_format($commissionDetails['montant_total_paye'] ?? 0, 0, ',', ' ') }} Ar</div>
                            </div>
                            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-700">
                                <div class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Commission totale ({{ $commissionPourcentage }}%)</div>
                                <div class="text-2xl font-bold text-yellow-800 dark:text-yellow-300">{{ number_format($commissionDetails['total_commission'] ?? 0, 0, ',', ' ') }} Ar</div>
                            </div>
                        </div>

                        <!-- Tableau par mois -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Répartition par mois</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mois</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prescriptions</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Montant analyses</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Montant payé</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Commission ({{ $commissionPourcentage }}%)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($commissionDetails['data'] ?? [] as $detail)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ \Carbon\Carbon::create()->month($detail->mois)->locale('fr')->monthName }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        {{ $detail->nombre_prescriptions }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium">
                                                    {{ number_format($detail->montant_analyses, 0, ',', ' ') }} Ar
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium">
                                                    {{ number_format($detail->montant_paye ?? 0, 0, ',', ' ') }} Ar
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">
                                                    {{ number_format($detail->commission ?? 0, 0, ',', ' ') }} Ar
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-12 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                            </svg>
                                                        </div>
                                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Aucune commission</h3>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            Aucune commission trouvée pour la période sélectionnée
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if(($commissionDetails['total_commission'] ?? 0) > 0)
                            <!-- Résumé final -->
                            <div class="mt-6 bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 p-6 rounded-lg border border-green-200 dark:border-green-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Commission totale à percevoir</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Sur {{ $commissionDetails['total_prescriptions'] ?? 0 }} prescription(s) payée(s)
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                                            {{ number_format($commissionDetails['total_commission'] ?? 0, 0, ',', ' ') }} Ar
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Taux : {{ $commissionPourcentage }}% du montant payé
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions du modal -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                        <button wire:click="$set('showCommissionModal', false)" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800 transition-colors">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif