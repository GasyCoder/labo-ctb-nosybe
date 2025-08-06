{{-- views/livewire/secretaire/partials/prescription-table.blade.php --}}
<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-slate-600 dark:text-slate-200">
        <thead class="bg-gray-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
            <tr>
                <th class="px-6 py-4">Référence</th>
                <th class="px-6 py-4">Patient</th>
                <th class="px-6 py-4">Prescripteur</th>
                <th class="px-6 py-4">Analyses</th>
                <th class="px-6 py-4">Statut</th>
                <th class="px-6 py-4">Date création</th>
                @if(isset($showActions) && $showActions || isset($showRestore) && $showRestore)
                    <th class="px-6 py-4">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($prescriptions as $prescription)
                <tr class="border-t border-gray-200 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors duration-200">
                    {{-- Référence --}}
                    <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">
                        {{ $prescription->patient->reference ?? 'N/A' }}
                    </td>

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
                                <span>{{ strtoupper(substr($prescription->prescripteur->nom ?? 'Dr', 0, 2)) }}</span>
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

                    {{-- Statut --}}
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                            @switch($prescription->status ?? 'UNKNOWN')
                                @case('EN_ATTENTE') 
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 
                                @break
                                @case('EN_COURS') 
                                    bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 
                                @break
                                @case('TERMINE') 
                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 
                                @break
                                @case('VALIDE') 
                                    bg-green-600 text-white dark:bg-green-700 
                                @break
                                @case('A_REFAIRE') 
                                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 
                                @break
                                @case('ARCHIVE') 
                                    bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 
                                @break
                                @case('PRELEVEMENTS_GENERES') 
                                    bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 
                                @break
                                @default 
                                    bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                            @endswitch">
                            {{ $prescription->status_label ?? $prescription->status ?? 'Inconnu' }}
                        </span>
                    </td>

                    {{-- Date de création --}}
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col">
                            <span>{{ $prescription->created_at ? $prescription->created_at->format('d/m/Y') : 'N/A' }}</span>
                            <span class="text-xs">{{ $prescription->created_at ? $prescription->created_at->diffForHumans() : '' }}</span>
                        </div>
                    </td>

                    {{-- Actions --}}
                    @if(isset($showActions) && $showActions)
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('secretaire.prescriptions.show', $prescription->id) }}"
                                   wire:navigate
                                   class="p-2 text-slate-600 dark:text-slate-200 hover:text-primary-600 dark:hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-all duration-200"
                                   title="Voir la prescription">
                                    <em class="ni ni-eye text-base"></em>
                                </a>
                                <a href="{{ route('secretaire.prescriptions.edit', $prescription->id) }}"
                                   wire:navigate
                                   class="p-2 text-slate-600 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200"
                                   title="Modifier la prescription">
                                    <em class="ni ni-edit text-base"></em>
                                </a>
                                <button wire:click="deletePrescription({{ $prescription->id }})"
                                        class="p-2 text-slate-600 dark:text-slate-200 hover:text-red-600 dark:hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200"
                                        title="Supprimer la prescription"
                                        onclick="return confirm('Voulez-vous vraiment supprimer cette prescription ?')">
                                    <em class="ni ni-trash text-base"></em>
                                </button>
                            </div>
                        </td>
                    @endif

                    {{-- Actions pour la corbeille --}}
                    @if(isset($showRestore) && $showRestore)
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button wire:click="restorePrescription({{ $prescription->id }})"
                                        class="p-2 text-slate-600 dark:text-slate-200 hover:text-green-600 dark:hover:text-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-all duration-200"
                                        title="Restaurer la prescription">
                                    <em class="ni ni-reload text-base"></em>
                                </button>
                                <button wire:click="forceDeletePrescription({{ $prescription->id }})"
                                        class="p-2 text-slate-600 dark:text-slate-200 hover:text-red-600 dark:hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200"
                                        title="Supprimer définitivement"
                                        onclick="return confirm('Voulez-vous vraiment supprimer définitivement cette prescription ? Cette action est irréversible.')">
                                    <em class="ni ni-trash text-base"></em>
                                </button>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center">
                            <em class="ni ni-info text-4xl mb-4 text-slate-300 dark:text-slate-600"></em>
                            <p class="text-base font-medium">Aucune prescription trouvée</p>
                            @if($search)
                                <p class="text-sm mt-2">Essayez de modifier vos critères de recherche</p>
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
                {{ $prescriptions->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
@endif