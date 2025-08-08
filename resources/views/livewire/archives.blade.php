{{-- resources/views/livewire/secretaire/archives.blade.php --}}
<div>
    <!-- Header avec statistiques -->
    <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-heading font-bold text-gray-900 dark:text-white">Archives des Prescriptions</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Consultez et gérez les prescriptions archivées du laboratoire</p>
            </div>
            
            <!-- Statistiques -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 max-w-md lg:max-w-none">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-700">
                    <div class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide">Total Archivées</div>
                    <div class="text-xl font-bold text-purple-800 dark:text-purple-300">{{ $prescriptions->total() ?? 0 }}</div>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 px-3 py-2 rounded-xl border border-blue-200 dark:border-blue-700">
                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Ce Mois</div>
                    <div class="text-xl font-bold text-blue-800 dark:text-blue-300">{{ $archivesMoisActuel ?? 0 }}</div>
                </div>
                <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 px-3 py-2 rounded-xl border border-green-200 dark:border-green-700">
                    <div class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Analyses</div>
                    <div class="text-xl font-bold text-green-800 dark:text-green-300">{{ $totalAnalyses ?? 0 }}</div>
                </div>
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 px-3 py-2 rounded-xl border border-orange-200 dark:border-orange-700">
                    <div class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wide">Patients</div>
                    <div class="text-xl font-bold text-orange-800 dark:text-orange-300">{{ $patientsUniques ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Filtres et recherche -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Recherche -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Rechercher</label>
                    <div class="relative">
                        <input 
                            wire:model.live.debounce.300ms="search"
                            type="text" 
                            id="search"
                            placeholder="Patient, prescripteur, référence..."
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-all"
                        >
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filtre par prescripteur -->
                <div>
                    <label for="prescripteurFilter" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Prescripteur</label>
                    <select wire:model.live="prescripteurFilter" id="prescripteurFilter" class="w-full py-3 px-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-all">
                        <option value="">Tous</option>
                        @foreach($prescripteurs as $prescripteur)
                            <option value="{{ $prescripteur->id }}">{{ $prescripteur->nom_complet }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre par période -->
                <div>
                    <label for="dateFilter" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Période</label>
                    <select wire:model.live="dateFilter" id="dateFilter" class="w-full py-3 px-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-all">
                        <option value="">Toutes</option>
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                        <option value="year">Cette année</option>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ $prescriptions->total() }} prescription(s) archivée(s)
                    </span>
                </div>

                <div class="flex items-center space-x-3">
                    <button 
                        wire:click="resetFilters"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Réinitialiser
                    </button>

                    <button 
                        wire:click="export"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 border border-transparent rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exporter
                    </button>
                </div>
            </div>
        </div>
   

    {{-- Tableau des archives --}}
    <div class="bg-white dark:bg-slate-900 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600 dark:text-slate-200">
                <thead class="bg-gray-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Référence</th>
                        <th class="px-6 py-4">Patient</th>
                        <th class="px-6 py-4">Prescripteur</th>
                        <th class="px-6 py-4">Analyses</th>
                        <th class="px-6 py-4">Archivé le</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions as $prescription)
                        <tr class="border-t border-gray-200 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors duration-200">
                            {{-- Référence --}}
                            <td class="px-6 py-4 font-medium">{{ $prescription->reference }}</td>

                            {{-- Patient --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0 flex items-center justify-center text-xs text-white bg-green-600 h-8 w-8 rounded-full font-medium">
                                        <span>{{ strtoupper(substr($prescription->patient->nom ?? 'N', 0, 1) . substr($prescription->patient->prenom ?? 'A', 0, 1)) }}</span>
                                    </div>
                                </td>

                                <!-- Patient -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 dark:from-green-500 dark:to-green-700 flex items-center justify-center shadow-lg">
                                                <span class="text-white font-bold text-sm">
                                                    {{ strtoupper(substr($prescription->patient->nom ?? 'N', 0, 1) . substr($prescription->patient->prenom ?? 'A', 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $prescription->patient->nom ?? 'N/A' }} {{ $prescription->patient->prenom ?? '' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $prescription->patient->telephone ?? 'Téléphone non renseigné' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Prescripteur -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 dark:from-blue-500 dark:to-blue-700 flex items-center justify-center shadow-lg">
                                                <span class="text-white font-bold text-sm">
                                                    {{ strtoupper(substr($prescription->prescripteur->nom ?? 'N', 0, 1) . substr($prescription->prescripteur->prenom ?? 'A', 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $prescription->prescripteur->nom_complet ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $prescription->prescripteur->specialite ?? 'Spécialité non renseignée' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Analyses -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-semibold rounded-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                            {{ $prescription->analyses->count() ?? 0 }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Date d'archivage -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $prescription->updated_at ? $prescription->updated_at->format('d/m/Y') : 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $prescription->updated_at ? $prescription->updated_at->diffForHumans() : '' }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center space-x-2">
                                        <!-- Bouton Désarchiver -->
                                        <button 
                                            wire:click="confirmUnarchive({{ $prescription->id }})"
                                            class="p-2 text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/30 rounded-lg transition-colors"
                                            title="Désarchiver"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                            </svg>
                                        </button>

                                        <!-- Bouton Visualiser -->
                                        <button 
                                            wire:click="viewPrescription({{ $prescription->id }})"
                                            class="p-2 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors"
                                            title="Visualiser"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>

                                        <!-- Supprimer définitivement (Admin seulement) -->
                                        @if(auth()->check() && auth()->user()->isAdmin())
                                            <button 
                                                wire:click="confirmPermanentDelete({{ $prescription->id }})"
                                                class="p-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                                title="Supprimer définitivement"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Aucune prescription archivée trouvée</h3>
                                        <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                                            @if($search || $prescripteurFilter || $dateFilter)
                                                Essayez de modifier vos critères de recherche pour voir plus de résultats.
                                            @else
                                                Les prescriptions archivées apparaîtront ici une fois que des prescriptions auront été archivées.
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
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Affichage de {{ $prescriptions->firstItem() }} à {{ $prescriptions->lastItem() }} 
                            sur {{ $prescriptions->total() }} résultats
                        </div>
                        <div class="flex space-x-1">
                            {{ $prescriptions->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de confirmation de désarchivage -->
    @if($showUnarchiveModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-[9999]" wire:click="resetModal">
            <div class="fixed inset-0 z-[9999] w-screen">
                <div class="flex min-h-full items-center justify-center p-4" style="margin-top: 4rem; padding-top: 2rem; padding-bottom: 4rem;">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-md" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-6 py-6">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-amber-100 dark:bg-amber-900/30 rounded-full mb-4">
                                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    Désarchiver cette prescription ?
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    Cette action remettra la prescription dans les prescriptions actives.
                                </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                            <button 
                                wire:click="resetModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors"
                            >
                                Annuler
                            </button>
                            <button 
                                wire:click="unarchive"
                                class="px-4 py-2 bg-amber-600 hover:bg-amber-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800 transition-colors"
                            >
                                Désarchiver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de confirmation de suppression définitive -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-[9999]" wire:click="resetModal">
            <div class="fixed inset-0 z-[9999] w-screen">
                <div class="flex min-h-full items-center justify-center p-4" style="margin-top: 4rem; padding-top: 2rem; padding-bottom: 4rem;">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-md" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-6 py-6">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    Supprimer définitivement ?
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    Cette action est irréversible. La prescription sera définitivement supprimée de la base de données.
                                </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                            <button 
                                wire:click="resetModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors"
                            >
                                Annuler
                            </button>
                            <button 
                                wire:click="permanentDelete"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition-colors"
                            >
                                Supprimer définitivement
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>