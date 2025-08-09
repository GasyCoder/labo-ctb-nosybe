<div>
    @if($prescriptions->count() > 0)
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
                                <span>{{ strtoupper(substr($prescription->prescripteur->nom ?? '', 3, 3)) }}</span>
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
                        <x-prescription-status :status="$prescription->status" />
                    </td>

                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $prescription->created_at->format('d/m/Y H:i') }}
                        </td>
                        
                       <td class="px-6 py-4 whitespace-nowrap">
    <div class="flex items-center space-x-2">
        @if($prescription->status === 'VALIDE')
            <button 
                wire:click="generatePDF({{ $prescription->id }})" 
                class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                title="Générer PDF"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </button>
        @endif
        
        <button 
            wire:click="openAnalyse({{ $prescription->id }})" 
            class="p-2 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors"
            title="Voir détails"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
        
        @if($prescription->status === 'VALIDE')
            <button 
                wire:click="redoPrescription({{ $prescription->id }})" 
                wire:confirm="Êtes-vous sûr de vouloir remettre cette prescription à refaire ?" 
                class="p-2 text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 transition-colors"
                title="Refaire"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        @endif
    </div>
</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center">
                            <em class="ni ni-info text-4xl mb-4 text-slate-300 dark:text-slate-600"></em>
                            <p class="text-base font-medium">Aucune prescription trouvée</p>
                            @if($search ?? false)
                                <p class="text-sm mt-2">Essayez de modifier vos critères de recherche</p>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

        <!-- Pagination -->
        @if($prescriptions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $prescriptions->links() }}
            </div>
        @endif
    @else
        <!-- État vide -->
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-microscope text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune analyse trouvée</h3>
            <p class="text-gray-500 dark:text-gray-400">
                @if($search)
                    Aucun résultat pour "{{ $search }}"
                @else
                    Il n'y a actuellement aucune analyse {{ $statusLabel }}.
                @endif
            </p>
            @if($search)
                <button wire:click="$set('search', '')" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Effacer la recherche
                </button>
            @endif
        </div>
    @endif