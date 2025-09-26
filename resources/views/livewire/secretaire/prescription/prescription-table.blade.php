<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-slate-600 dark:text-slate-200">
        <thead class="bg-gray-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
            <tr>
                <th class="px-6 py-4">Référence</th>
                <th class="px-6 py-4">Patient</th>
                <th class="px-6 py-4">Prescripteur</th>
                <th class="px-6 py-4">Analyses</th>
                <th class="px-6 py-4">Statut</th>
                <th class="px-6 py-4">Paiement</th>
                <th class="px-6 py-4">Date création</th>
                <th class="px-6 py-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prescriptions as $prescription)
                <tr
                    class="border-t border-gray-200 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors duration-200">
                    {{-- Référence --}}
                    <td class="px-6 py-4 font-medium">{{ $prescription->reference }}</td>
                    {{-- Patient --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="relative flex-shrink-0 flex items-center justify-center text-xs text-white bg-green-600 h-8 w-8 rounded-full font-medium">
                                <span>{{ strtoupper(substr($prescription->patient->nom ?? 'N', 0, 1) . substr($prescription->patient->prenom ?? 'A', 0, 1)) }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ Str::limit(($prescription->patient->nom ?? 'N/A') . ' ' . ($prescription->patient->prenom ?? ''), 18) }}
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
                            <div
                                class="relative flex-shrink-0 flex items-center justify-center text-xs text-white bg-primary-600 h-8 w-8 rounded-full font-medium">
                                <span>{{ strtoupper(substr($prescription->prescripteur->nom ?? '', 3, 3)) }}</span>
                            </div>
                            <span class="text-slate-900 dark:text-slate-100">
                                {{ Str::limit(($prescription->prescripteur->nom ?? 'N/A') . ' ' . ($prescription->prescripteur->prenom ?? 'N/A'), 18) }}
                            </span>
                        </div>
                    </td>

                    {{-- Nombre d'analyses --}}
                    <td class="px-6 py-4">
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $prescription->analyses->count() ?? 0 }}
                        </span>
                    </td>

                    {{-- Statut --}}
                    <td class="px-6 py-4">
                        <x-prescription-status :status="$prescription->status" />
                    </td>

                    {{-- Statut Paiement --}}
                    <td class="px-6 py-4">
                        @php
                            $paiement = $prescription->paiements->first();
                            $estPaye = $paiement ? $paiement->status : false;
                        @endphp
                        @if ($paiement)
                            <div class="flex flex-col space-y-2">
                                {{-- Toggle Switch avec Confirmation --}}
                                <div class="flex items-center space-x-3">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        {{-- Toggle Switch - Le clic ouvre la modale de confirmation --}}
                                        <input type="checkbox" 
                                            wire:click="togglePaiementStatus({{ $prescription->id }})"
                                            class="sr-only peer"
                                            {{ $estPaye ? 'checked' : '' }} readonly>
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 
                                                    peer-checked:after:translate-x-full peer-checked:after:border-white 
                                                    after:content-[''] after:absolute after:top-0.5 after:left-[2px] 
                                                    after:bg-white after:border-gray-300 after:border after:rounded-full 
                                                    after:h-5 after:w-5 after:transition-all 
                                                    peer-checked:bg-green-600">
                                        </div>
                                    </label>
                                    
                                    {{-- Statut et Date --}}
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium 
                                            {{ $estPaye ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $estPaye ? 'Payé' : 'Non Payé' }}
                                        </span>
                                        
                                        {{-- Date de paiement --}}
                                        @if($estPaye && $paiement->date_paiement)
                                            <span class="text-xs text-gray-500 dark:text-gray-400" title="Date de paiement">
                                                {{ $paiement->date_paiement->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            
                                {{-- Informations supplémentaires --}}
                                @if($paiement->paymentMethod)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                        </svg>
                                        <span title="Méthode de paiement">{{ $paiement->paymentMethod->label ?? 'N/A' }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="flex items-center justify-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-gray-400"></span>
                                    Aucun paiement
                                </span>
                            </div>
                        @endif
                    </td>

                    {{-- Date de création --}}
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col">
                            <span>{{ $prescription->created_at ? $prescription->created_at->format('d/m/Y') : 'N/A' }}</span>
                            <span
                                class="text-xs">{{ $prescription->created_at ? $prescription->created_at->diffForHumans() : '' }}</span>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            @if (isset($currentTab) && $currentTab === 'deleted')
                                {{-- Actions pour la corbeille --}}
                                <button wire:click="confirmRestore({{ $prescription->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-amber-600 bg-amber-100 rounded-lg hover:bg-amber-200 transition-colors"
                                    title="Récupérer">
                                    <em class="ni ni-undo"></em>
                                </button>
                                @if (auth()->check() && auth()->user()->type === 'admin')
                                    <button wire:click="confirmPermanentDelete({{ $prescription->id }})"
                                        class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors"
                                        title="Supprimer définitivement">
                                        <em class="ni ni-delete-fill"></em>
                                    </button>
                                @endif
                            @elseif(isset($currentTab) && $currentTab === 'actives')
                                {{-- Actions pour les prescriptions actives --}}
                                <a href="{{ route('secretaire.prescription.edit', ['prescriptionId' => $prescription->id]) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-100 rounded-lg hover:bg-green-200 transition-colors"
                                    title="Modifier">
                                    <em class="ni ni-edit"></em>
                                </a>
                                <button wire:click="confirmDelete({{ $prescription->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors"
                                    title="Corbeille">
                                    <em class="ni ni-trash"></em>
                                </button>
                            @elseif(isset($currentTab) && $currentTab === 'valide')
                                {{-- Actions pour les prescriptions validées --}}
                                <a href="{{ route('laboratoire.prescription.pdf', $prescription->id) }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-green-100 rounded-lg hover:bg-green-200 transition-colors"
                                    title="Voir PDF">
                                    <em class="text-xl ni ni-file-pdf"></em>
                                </a>

                                <button wire:click="confirmArchive({{ $prescription->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                                    title="Archiver">
                                    <em class="ni ni-archive"></em>
                                </button>
                                <button wire:click="confirmDelete({{ $prescription->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors"
                                    title="Corbeille">
                                    <em class="ni ni-trash"></em>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center">
                            <em class="ni ni-info text-4xl mb-4 text-slate-300 dark:text-slate-600"></em>
                            <p class="text-base font-medium">Aucune prescription trouvée</p>
                            @if ($search ?? false)
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
@if ($prescriptions->hasPages())
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
