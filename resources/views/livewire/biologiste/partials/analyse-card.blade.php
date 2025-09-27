<div>
    @if ($prescriptions->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600 dark:text-slate-200">
                <thead
                    class="bg-gray-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
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
                                
                                @if($paiement)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $estPaye ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $estPaye ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                        {{ $estPaye ? 'Payé' : 'Non Payé' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-gray-400"></span>
                                        Aucun paiement
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $prescription->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $prescription->created_at->format('H:i') }}
                                    </div>
                            </td>
                            {{-- Colonne Actions corrigée --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    {{-- Bouton Valider (si statut TERMINE) --}}
                                    @if($prescription->status === 'TERMINE')
                                        <button wire:click="openConfirmModal({{ $prescription->id }})" 
                                                wire:loading.attr="disabled"
                                                class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center justify-center gap-2">
                                            
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span>Valider</span>
                                        </button>
                                    @endif

                                    {{-- ✅ CORRECTION : Bouton APERÇU PDF - TOUJOURS DISPONIBLE --}}
                                    @php
                                        // Vérifier s'il y a des résultats saisis
                                        $hasAnyResults = $prescription->resultats()
                                            ->whereNotNull('valeur')
                                            ->exists();
                                    @endphp

                                    @if($hasAnyResults)
                                        <a href="{{ route('laboratoire.prescription.pdf', $prescription->id) }}" 
                                        target="_blank"
                                        class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center justify-center gap-2"
                                        title="Générer un aperçu PDF des résultats saisis (peu importe le statut)">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Aperçu PDF</span>
                                        </a>
                                    @else
                                        {{-- ✅ Bouton désactivé si aucun résultat saisi --}}
                                        <span class="bg-gray-400 text-white px-3 py-2 rounded-lg text-sm font-medium inline-flex items-center justify-center gap-2 cursor-not-allowed opacity-50"
                                            title="Aucun résultat saisi disponible">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Pas d'aperçu</span>
                                        </span>
                                    @endif                    
                                    {{-- Bouton À refaire (si prescription validée) --}}
                                        <button wire:click="redoPrescription({{ $prescription->id }})" 
                                                wire:loading.attr="disabled"
                                                wire:target="redoPrescription({{ $prescription->id }})"
                                                onclick="return confirm('Êtes-vous sûr de vouloir remettre cette prescription à refaire ?')"
                                                class="bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center justify-center gap-2">
                                            
                                            <span wire:loading.remove wire:target="redoPrescription({{ $prescription->id }})" class="inline-flex items-center gap-2">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                <span>À refaire</span>
                                            </span>

                                            <span wire:loading wire:target="redoPrescription({{ $prescription->id }})" class="inline-flex items-center gap-2">
                                                <svg class="animate-spin w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span>Traitement...</span>
                                            </span>
                                        </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-500 dark:text-slate-400">
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

        <!-- Pagination -->
        @if ($prescriptions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $prescriptions->links() }}
            </div>
        @endif
    @else
        <!-- État vide -->
        <div class="text-center py-12">
            <div
                class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <em class="ni ni-virus text-3xl"></em>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune analyse trouvée</h3>
            <p class="text-gray-500 dark:text-gray-400">
                @if ($search)
                    Aucun résultat pour "{{ $search }}"
                @else
                    Il n'y a actuellement aucune analyse {{ $statusLabel }}.
                @endif
            </p>
            @if ($search)
                <button wire:click="$set('search', '')"
                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Effacer la recherche
                </button>
            @endif
        </div>
    @endif
