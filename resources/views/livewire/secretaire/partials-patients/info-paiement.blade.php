{{-- resources/views/livewire/secretaire/partials-patients/info-paiement.blade.php --}}
@if ($activeTab === 'paiements')
    <!-- Historique des paiements optimisé -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Historique des Paiements</h3>
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                    Total: {{ number_format($montantTotal, 0, ',', ' ') }} Ar
                </div>
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @php
                $allPaiements = $patient->prescriptions->flatMap->paiements->sortByDesc('created_at');
            @endphp

            @forelse($allPaiements as $paiement)
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($paiement->montant, 0, ',', ' ') }} Ar
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $paiement->created_at->format('d/m/Y à H:i') }} •
                                    {{ $paiement->paymentMethod?->label ?? ($paiement->mode_paiement ?? 'N/A') }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">   
                            <span
                                class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs font-medium rounded">
                                Ref #{{ $paiement->prescription->reference ?? $paiement->prescription_id }}
                            </span>
                            
                            {{-- ✅ CORRECTION : Boutons avec liens directs au lieu de wire:click --}}
                            <div class="flex items-center space-x-2">
                                {{-- Bouton Voir Facture --}}
                                <a href="{{ route('secretaire.prescription.facture', $paiement->prescription_id) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded transition-colors"
                                   title="Voir la facture">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Facture
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div
                        class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun paiement</h3>
                    <p class="text-gray-500 dark:text-gray-400">Ce patient n'a encore effectué aucun paiement.</p>
                </div>
            @endforelse
        </div>
        
      
    </div>
@endif