{{-- resources/views/livewire/secretaire/archives.blade.php --}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-slate-900 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Archives</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400">Gestion des prescriptions archivées</p>
            </div>
            <div class="flex gap-3">
                <button wire:click="export" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <em class="ni ni-download mr-2"></em>
                    Exporter
                </button>
                <button wire:click="resetFilters" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <em class="ni ni-reload mr-2"></em>
                    Réinitialiser
                </button>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            {{-- Recherche --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Rechercher
                </label>
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           placeholder="Patient, prescripteur, référence..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <em class="ni ni-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></em>
                </div>
            </div>

            {{-- Filtre par prescripteur --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Prescripteur
                </label>
                <select wire:model.live="prescripteurFilter" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Tous</option>
                    @foreach($prescripteurs as $prescripteur)
                        <option value="{{ $prescripteur->id }}">{{ $prescripteur->nom }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre par date --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Période
                </label>
                <select wire:model.live="dateFilter" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Toutes</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>
                    <option value="year">Cette année</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Messages Flash --}}
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
            <div class="flex">
                <em class="ni ni-check-circle mr-2 mt-0.5"></em>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
            <div class="flex">
                <em class="ni ni-alert-circle mr-2 mt-0.5"></em>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg" role="alert">
            <div class="flex">
                <em class="ni ni-info-circle mr-2 mt-0.5"></em>
                <span>{{ session('info') }}</span>
            </div>
        </div>
    @endif

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
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-900 dark:text-slate-100">
                                            {{ $prescription->patient->nom ?? 'N/A' }} {{ $prescription->patient->prenom ?? '' }}
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $prescription->patient->telephone ?? 'Téléphone non renseigné' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Prescripteur --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0 flex items-center justify-center text-xs text-white bg-primary-600 h-8 w-8 rounded-full font-medium">
                                        <span>{{ strtoupper(substr($prescription->prescripteur->nom ?? '', 0, 2)) }}</span>
                                    </div>
                                    <span class="text-slate-900 dark:text-slate-100">{{ $prescription->prescripteur->nom ?? 'N/A' }}</span>
                                </div>
                            </td>

                            {{-- Nombre d'analyses --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $prescription->analyses->count() ?? 0 }} analyse(s)
                                </span>
                            </td>

                            {{-- Date d'archivage --}}
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col">
                                    <span>{{ $prescription->updated_at ? $prescription->updated_at->format('d/m/Y') : 'N/A' }}</span>
                                    <span class="text-xs">{{ $prescription->updated_at ? $prescription->updated_at->diffForHumans() : '' }}</span>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex gap-2 justify-end">
                                    {{-- Bouton Désarchiver --}}
                                    <button wire:click="confirmUnarchive({{ $prescription->id }})"
                                            class="inline-flex items-center justify-center w-8 h-8 text-amber-600 bg-amber-100 rounded-lg hover:bg-amber-200 transition-colors"
                                            title="Désarchiver">
                                        <em class="ni ni-undo"></em>
                                    </button>

                                    {{-- Bouton de visualisation --}}
                                   

                                    {{-- Supprimer définitivement (Admin seulement) --}}
                                    @if(auth()->check() && auth()->user()->isAdmin())
                                        <button wire:click="confirmPermanentDelete({{ $prescription->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors"
                                                title="Supprimer définitivement">
                                            <em class="ni ni-delete-fill"></em>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center">
                                    <em class="ni ni-archive text-4xl mb-4 text-slate-300 dark:text-slate-600"></em>
                                    <p class="text-base font-medium">Aucune prescription archivée trouvée</p>
                                    @if($search || $prescripteurFilter || $dateFilter)
                                        <p class="text-sm mt-2">Essayez de modifier vos critères de recherche</p>
                                    @else
                                        <p class="text-sm mt-2">Les prescriptions archivées apparaîtront ici</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($prescriptions->hasPages())
            <div class="p-6 border-t border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
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

    {{-- Modal de confirmation de désarchivage --}}
    @if($showUnarchiveModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-slate-800">
                <div class="mt-3">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-amber-100 rounded-full">
                        <em class="ni ni-undo text-xl text-amber-600"></em>
                    </div>
                    <div class="mt-5 text-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-slate-100">
                            Désarchiver cette prescription ?
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                            Cette action remettra la prescription dans les prescriptions actives.
                        </p>
                        <div class="flex gap-3 mt-6">
                            <button wire:click="resetModal"
                                    class="flex-1 px-4 py-2 bg-gray-300 dark:bg-slate-600 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-400 dark:hover:bg-slate-700 transition-colors">
                                Annuler
                            </button>
                            <button wire:click="unarchive"
                                    class="flex-1 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                                Désarchiver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de confirmation de suppression définitive --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-slate-800">
                <div class="mt-3">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                        <em class="ni ni-alert-circle text-xl text-red-600"></em>
                    </div>
                    <div class="mt-5 text-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-slate-100">
                            Supprimer définitivement ?
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                            Cette action est irréversible. La prescription sera définitivement supprimée de la base de données.
                        </p>
                        <div class="flex gap-3 mt-6">
                            <button wire:click="resetModal"
                                    class="flex-1 px-4 py-2 bg-gray-300 dark:bg-slate-600 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-400 dark:hover:bg-slate-700 transition-colors">
                                Annuler
                            </button>
                            <button wire:click="permanentDelete"
                                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                Supprimer définitivement
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>