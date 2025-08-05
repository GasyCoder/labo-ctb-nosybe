{{-- resources/views/livewire/secretaire/prescription/prescription-index.blade.php --}}
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <em class="ni ni-list-round text-primary-600 text-xl mr-3"></em>
            <h1 class="text-2xl font-heading font-bold text-slate-800 dark:text-slate-100">
                Liste des prescriptions
            </h1>
        </div>
        <a href="{{ route('secretaire.add-prescription') }}"
           wire:navigate
           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
            <em class="ni ni-plus mr-2"></em> Nouvelle prescription
        </a>
    </div>

    {{-- Search Bar --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6 mb-6">
        <div class="relative mb-4">
            <em class="ni ni-search absolute left-3 top-3 text-slate-400"></em>
            <input type="text"
                   wire:model.live="search"
                   placeholder="Rechercher par patient, référence ou prescripteur..."
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-lg 
                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                          focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
    </div>

        <!-- Prescriptions Table -->
        <div class="bg-white dark:bg-gray-950 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 text-left text-sm font-bold leading-4.5 text-slate-600 dark:text-slate-200">
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
                        @forelse($prescriptions as $key => $prescription)
                            <tr class="border-t border-gray-200 dark:border-gray-800 text-sm text-slate-600 dark:text-slate-200 hover:bg-gray-50 hover:dark:bg-gray-900 transition-all duration-300">
                                <td class="px-6 py-4">{{ $prescription->patient->reference }}</td>
                                <td class="px-6 py-4">
                                    {{ $prescription->patient->nom }} {{ $prescription->patient->prenom }}
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $prescription->patient->telephone ? : '' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $prescription->prescripteur->nom }}</td>
                                <td class="px-6 py-4">{{ $prescription->analyses->count() }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                        {{ $prescription->status === 'EN_ATTENTE' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                           ($prescription->status === 'EN_COURS' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                           ($prescription->status === 'TERMINE' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                           ($prescription->status === 'VALIDE' ? 'bg-green-600 text-white dark:bg-green-700 dark:text-white' :
                                           ($prescription->status === 'A_REFAIRE' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                           ($prescription->status === 'ARCHIVE' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' :
                                           ($prescription->status === 'PRELEVEMENTS_GENERES' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '')))))) }}">
                                        {{ $prescription->status_label ?? $prescription->status }}
                                    </span>
                                </td>
                               <td class="px-6 py-4">
                                    {{ $prescription->created_at ? $prescription->created_at->diffForHumans() : '' }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('secretaire.prescriptions.edit', $prescription->id) }}"
                                       wire:navigate
                                       class="inline-flex items-center text-sm font-medium text-slate-600 dark:text-slate-200 hover:text-primary-600 hover:dark:text-primary-500 active:text-primary-700 active:dark:text-primary-600 transition-all duration-300">
                                        <em class="text-lg ni ni-user mr-1"></em>
                                    </a>
                                     <a href="{{ route('secretaire.prescriptions.edit', $prescription->id) }}"
                                       wire:navigate
                                       class="inline-flex items-center text-sm font-medium text-slate-600 dark:text-slate-200 hover:text-primary-600 hover:dark:text-primary-500 active:text-primary-700 active:dark:text-primary-600 transition-all duration-300">
                                        <em class="text-lg ni ni-edit mr-1"></em>
                                    </a>
                                     <a href="{{ route('secretaire.prescriptions.edit', $prescription->id) }}"
                                       wire:navigate
                                       class="inline-flex items-center text-sm font-medium text-slate-600 dark:text-slate-200 hover:text-primary-600 hover:dark:text-primary-500 active:text-primary-700 active:dark:text-primary-600 transition-all duration-300">
                                        <em class="text-lg ni ni-trash mr-1"></em>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-500 dark:text-slate-400">
                                    <em class="ni ni-info text-4xl mb-4"></em>
                                    <p>Aucune prescription trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($prescriptions->hasPages())
                <div class="p-6 border-t border-gray-200 dark:border-gray-800">
                    {{ $prescriptions->links() }}
                </div>
            @endif
        </div>
</div>