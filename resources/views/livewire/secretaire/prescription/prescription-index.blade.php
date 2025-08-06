<div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 gap-4">
        <div class="flex items-center gap-3">
            <em class="ni ni-list-round text-primary-600 text-xl"></em>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 tracking-tight">
                Liste des prescriptions
            </h1>
        </div>
        <a href="{{ route('secretaire.prescription.create') }}"
           wire:navigate
           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 text-sm font-medium">
            <em class="ni ni-plus mr-2 text-base"></em> Nouvelle prescription
        </a>
    </div>

    {{-- Search Bar --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 mb-8">
        <div class="relative">
            <em class="ni ni-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></em>
            <input type="text"
                   wire:model.live.debounce.500ms="search"
                   placeholder="Rechercher par patient, référence ou prescripteur..."
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-lg 
                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                          focus:ring-2 focus:ring-primary-500 focus:border-primary-500 placeholder-slate-400
                          transition-all duration-200"
                   aria-label="Rechercher des prescriptions">
            @if($search)
                <button wire:click="clearSearch"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                        aria-label="Effacer la recherche">
                    <em class="ni ni-cross text-base"></em>
                </button>
            @endif
        </div>
    </div>

    {{-- Prescriptions Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600 dark:text-slate-200">
                <thead class="bg-gray-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Référence</th>
                        <th class="px-6 py-4">Patient</th>
                        <th class="px-6 py-4">Prescripteur</th>
                        <th class="px-6 py-4">Analyses</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Crée</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions as $prescription)
                        <tr class="border-t border-gray-200 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium">{{ $prescription->patient->reference }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0 flex items-center justify-center text-xxs text-white bg-green-700 h-7 w-7 rounded-full font-bold">
                                        <span>{{ strtoupper(substr($prescription->patient->nom, 0, 1) . substr($prescription->patient->prenom, 0, 1)) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $prescription->patient->nom }} {{ $prescription->patient->prenom }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $prescription->patient->telephone ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0 flex items-center justify-center text-xxs text-white bg-primary-600 h-7 w-7 rounded-full font-medium">
                                        <span>{{ strtoupper(substr($prescription->prescripteur->nom, 3, 3)) }}</span>
                                    </div>
                                    <span>{{ $prescription->prescripteur->nom }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $prescription->analyses->count() }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                                    @switch($prescription->status)
                                        @case('EN_ATTENTE') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                                        @case('EN_COURS') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @break
                                        @case('TERMINE') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                        @case('VALIDE') bg-green-600 text-white dark:bg-green-700 @break
                                        @case('A_REFAIRE') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                        @case('ARCHIVE') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @break
                                        @case('PRELEVEMENTS_GENERES') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @endswitch">
                                    {{ $prescription->status_label ?? $prescription->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $prescription->created_at ? $prescription->created_at->diffForHumans() : 'N/A' }}</td>
                            <td class="px-6 py-4 flex gap-2">
                                <a href="{{ route('secretaire.prescription.edit', $prescription->id) }}"
                                   wire:navigate
                                   class="p-1 text-slate-600 dark:text-slate-200 hover:text-primary-600 dark:hover:text-primary-500 transition-colors duration-200"
                                   aria-label="Modifier la prescription">
                                    <em class="ni ni-edit text-base"></em>
                                </a>
                                <button wire:click="deletePrescription({{ $prescription->id }})"
                                        class="p-1 text-slate-600 dark:text-slate-200 hover:text-red-600 dark:hover:text-red-500 transition-colors duration-200"
                                        aria-label="Supprimer la prescription"
                                        onclick="return confirm('Voulez-vous vraiment supprimer cette prescription ?')">
                                    <em class="ni ni-trash text-base"></em>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-500 dark:text-slate-400">
                                <em class="ni ni-info text-4xl mb-4"></em>
                                <p class="text-base">Aucune prescription trouvée</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($prescriptions->hasPages())
            <div class="p-6 border-t border-gray-200 dark:border-slate-800">
                {{ $prescriptions->links() }}
            </div>
        @endif
    </div>
</div>