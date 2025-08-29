<div>
    <!-- Header avec statistiques -->
    <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 sm:gap-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-heading font-bold text-gray-900 dark:text-white">Dashboard Biologiste</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1 sm:mt-2 text-sm sm:text-base">Bienvenue {{ Auth::user()->name }}, gérez vos validations d'analyses</p>
            </div>
            
            <!-- Statistiques -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 max-w-md lg:max-w-none">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 px-2 sm:px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-700">
                    <div class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide">Total Terminées</div>
                    <div class="text-lg sm:text-xl font-bold text-purple-800 dark:text-purple-300">{{ $stats['total_termine'] }}</div>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 px-2 sm:px-3 py-2 rounded-xl border border-blue-200 dark:border-blue-700">
                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Total Validées</div>
                    <div class="text-lg sm:text-xl font-bold text-blue-800 dark:text-blue-300">{{ $stats['total_valide'] }}</div>
                </div>
                <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 px-2 sm:px-3 py-2 rounded-xl border border-green-200 dark:border-green-700">
                    <div class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Urgences Nuit</div>
                    <div class="text-lg sm:text-xl font-bold text-green-800 dark:text-green-300">{{ $stats['urgences_nuit'] }}</div>
                </div>
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 px-2 sm:px-3 py-2 rounded-xl border border-orange-200 dark:border-orange-700">
                    <div class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wide">Urgences Jour</div>
                    <div class="text-lg sm:text-xl font-bold text-orange-800 dark:text-orange-300">{{ $stats['urgences_jour'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Recherche -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="w-full sm:w-1/2 lg:w-1/3">
                    <label for="search" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Rechercher</label>
                    <div class="relative">
                        <input 
                            wire:model.live.debounce.300ms="search"
                            type="text" 
                            id="search"
                            placeholder="Référence, patient, prescripteur..."
                            class="w-full pl-10 pr-4 py-2 sm:py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-all"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 sm:w-5 h-4 sm:h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('biologiste.analyse.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Voir toutes les validations
                    </a>
                </div>
            </div>
        </div>

        <!-- Liste des prescriptions à valider -->
        <div class="bg-white dark:bg-slate-900 rounded-lg shadow overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Prescriptions terminées en attente de validation</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Les dernières analyses terminées par les techniciens</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600 dark:text-slate-200">
                    <thead class="bg-gray-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-4 sm:px-6 py-2 sm:py-4">Référence</th>
                            <th class="px-4 sm:px-6 py-2 sm:py-4">Patient</th>
                            <th class="px-4 sm:px-6 py-2 sm:py-4">Prescripteur</th>
                            <th class="px-4 sm:px-6 py-2 sm:py-4">Date</th>
                            <th class="px-4 sm:px-6 py-2 sm:py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prescriptions as $prescription)
                            <tr class="border-t border-gray-200 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors duration-200">
                                <td class="px-4 sm:px-6 py-2 sm:py-4 font-medium text-slate-900 dark:text-slate-100">
                                    {{ $prescription->reference ?? 'N/A' }}
                                </td>
                                <td class="px-4 sm:px-6 py-2 sm:py-4">
                                    <span class="font-medium text-slate-900 dark:text-slate-100 text-sm sm:text-base">
                                        {{ $prescription->patient->nom ?? 'N/A' }} {{ $prescription->patient->prenom ?? '' }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-2 sm:py-4">
                                    <span class="font-medium text-slate-900 dark:text-slate-100 text-sm sm:text-base">
                                        {{ $prescription->prescripteur->nom ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-2 sm:py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $prescription->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 sm:px-6 py-2 sm:py-4 text-center">
                                    <div class="flex justify-center gap-1 sm:gap-2">
                                        <button wire:click="viewAnalyseDetails({{ $prescription->id }})"
                                                class="p-1 sm:p-2 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors"
                                                title="Valider">
                                            <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 sm:px-6 py-8 sm:py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 sm:w-16 h-12 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-2 sm:mb-4">
                                            <svg class="w-6 sm:w-8 h-6 sm:h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-1 sm:mb-2">Aucune prescription à valider</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm text-center">
                                            @if($search)
                                                Aucune prescription ne correspond à votre recherche. Essayez de modifier vos critères.
                                            @else
                                                Les prescriptions terminées par les techniciens apparaîtront ici pour validation.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($prescriptions->hasPages())
                <div class="px-4 sm:px-6 py-2 sm:py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Affichage de {{ $prescriptions->firstItem() }} à {{ $prescriptions->lastItem() }} 
                            sur {{ $prescriptions->total() }} résultats
                        </div>
                        <div class="flex space-x-1 sm:space-x-2">
                            {{ $prescriptions->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>