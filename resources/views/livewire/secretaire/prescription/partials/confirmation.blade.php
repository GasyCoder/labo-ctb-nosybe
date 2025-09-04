@if ($etape === 'confirmation')
    <div class="max-w-md mx-auto">
        <!-- Message de succès en haut -->
        <div
            class="bg-white dark:bg-slate-800 rounded-t-lg shadow-sm border {{ $isEditMode ? 'border-orange-200 dark:border-orange-800' : 'border-green-200 dark:border-green-800' }} p-4 text-center mb-0">
            <div
                class="w-12 h-12 {{ $isEditMode ? 'bg-orange-50 dark:bg-orange-900/20' : 'bg-green-50 dark:bg-green-900/20' }} rounded-full flex items-center justify-center mx-auto mb-3">
                <em
                    class="ni ni-{{ $isEditMode ? 'edit' : 'check-circle' }} text-xl {{ $isEditMode ? 'text-orange-500 dark:text-orange-400' : 'text-green-500 dark:text-green-400' }}"></em>
            </div>

            <h2
                class="text-lg font-semibold {{ $isEditMode ? 'text-orange-900 dark:text-orange-100' : 'text-green-900 dark:text-green-100' }} mb-2">
                @if ($isEditMode)
                    Prescription modifiée avec succès !
                @else
                    Prescription enregistrée avec succès !
                @endif
            </h2>
            <p class="text-sm text-slate-600 dark:text-slate-300">
                @if ($isEditMode)
                    Les modifications ont été sauvegardées.
                @else
                    La nouvelle prescription est prête.
                @endif
            </p>
            
            <!-- Afficher la référence si disponible -->
            @if($prescription && $prescription->reference)
                <div class="mt-2 inline-flex items-center bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded-full">
                    <em class="ni ni-tag mr-1 text-xs text-slate-500"></em>
                    <span class="text-xs font-medium text-slate-700 dark:text-slate-300">
                        {{ $prescription->reference }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Ticket style facture -->
        <div
            class="bg-white dark:bg-slate-800 rounded-b-lg shadow-sm border {{ $isEditMode ? 'border-orange-200 dark:border-orange-800' : 'border-green-200 dark:border-green-800' }} border-t-0 p-4">
            <!-- En-tête ticket -->
            <div class="text-center border-b border-dashed border-gray-200 dark:border-slate-700 pb-2 mb-3">
                <h3 class="font-medium text-slate-800 dark:text-slate-100">
                    {{ $this->getTitle() }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    {{ now()->format('d/m/Y H:i') }}
                </p>
            </div>

            <!-- Corps du ticket -->
            <div class="text-sm space-y-2 mb-4">
                <div class="flex justify-between">
                    <span class="font-medium text-slate-700 dark:text-slate-300">Patient:</span>
                    <span class="text-slate-900 dark:text-slate-100">
                        {{ $patient->nom ?? '' }} {{ $patient->prenom ?? '' }}
                        @if ($patient->latest_age ?? '')
                            ({{ $patient->latest_age ?? '' }} ans)
                        @endif
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="font-medium text-slate-700 dark:text-slate-300">Analyses:</span>
                    <span class="text-slate-900 dark:text-slate-100">{{ count($analysesPanier) }}</span>
                </div>

                @if (!empty($prelevementsSelectionnes))
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Prélèvements:</span>
                        <span class="text-slate-900 dark:text-slate-100">{{ count($prelevementsSelectionnes) }}</span>
                    </div>
                @endif

                @if (!empty($tubesGeneres))
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Tubes:</span>
                        <span class="text-slate-900 dark:text-slate-100">{{ count($tubesGeneres) }}</span>
                    </div>
                @endif

                <div class="border-t border-dashed border-gray-200 dark:border-slate-700 pt-2 mt-2">
                    <div class="flex justify-between font-bold">
                        <span class="text-slate-800 dark:text-slate-200">MONTANT TOTAL:</span>
                        <span
                            class="{{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ number_format($total, 0) }} Ar
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions améliorées avec facturation -->
            <div class="space-y-3 mt-4">
                {{-- BOUTON PRINCIPAL FACTURATION --}}
                @if($prescription)
                    <button
                        wire:click="afficherFactureComplete"
                        class="w-full flex items-center justify-center px-4 py-3 {{ $isEditMode ? 'bg-purple-500 hover:bg-purple-600' : 'bg-blue-500 hover:bg-blue-600' }} text-white rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-md">
                        <em class="ni ni-file-docs mr-2 text-base"></em>
                        {{ $isEditMode ? 'Voir Facture Modifiée' : 'Voir Facture' }}
                    </button>
                @else
                    <button
                        class="w-full flex items-center justify-center px-4 py-3 bg-gray-400 text-white rounded-lg text-sm font-medium transition-all duration-200 cursor-not-allowed opacity-70">
                        <em class="ni ni-file-docs mr-2 text-base"></em>
                        Facture non disponible
                    </button>
                @endif

               {{-- ACTIONS SECONDAIRES --}}
<div class="grid grid-cols-3 gap-2">
    <button wire:click="nouveauPrescription"
        class="flex items-center justify-center px-3 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm transition-colors">
        <em class="ni ni-plus mr-1 text-xs"></em> Nouvelle
    </button>

    @if($prescription)
        <button onclick="imprimerFacture()"
            class="flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-lg text-sm transition-colors">
            <em class="ni ni-printer mr-1 text-xs"></em> Imprimer
        </button>
    @else
        <button
            class="flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-lg text-sm transition-colors opacity-50 cursor-not-allowed">
            <em class="ni ni-printer mr-1 text-xs"></em> Imprimer
        </button>
    @endif
    
    <a href="{{ route('secretaire.prescription.index') }}" wire:navigate
        class="flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-lg text-sm transition-colors">
        <em class="ni ni-list mr-1 text-xs"></em> Liste
    </a>
</div>

                {{-- ACTIONS RAPIDES SUPPLÉMENTAIRES --}}
                <div class="pt-2 border-t border-gray-100 dark:border-slate-600">
                    <div class="flex justify-center space-x-4 text-xs">
                        @if($prescription)
                            <button wire:click="facture"
                                class="flex items-center text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
                                <em class="ni ni-send mr-1 text-xs"></em>
                                <span>Envoyer par email</span>
                            </button>
                            <button
                                class="flex items-center text-slate-500 hover:text-green-600 dark:text-slate-400 dark:hover:text-green-400 transition-colors">
                                <em class="ni ni-whatsapp mr-1 text-xs"></em>
                                <span>WhatsApp</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- INFORMATIONS COMPLÉMENTAIRES --}}
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-slate-600">
                <div class="text-xs text-slate-500 dark:text-slate-400 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center">
                            <em class="ni ni-clock mr-1 text-xs"></em>
                            Créé le {{ now()->format('d/m/Y à H:i') }}
                        </span>
                        <span
                            class="px-2 py-0.5 {{ $isEditMode ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' }} rounded-full text-xxs font-medium">
                            {{ $isEditMode ? 'Modifié' : 'Nouveau' }}
                        </span>
                    </div>
                    @if ($prescription && $prescription->reference)
                        <div class="flex items-center">
                            <em class="ni ni-tag mr-1 text-xs"></em>
                            Référence: <code
                                class="ml-1 px-1 bg-slate-100 dark:bg-slate-700 rounded text-xxs">{{ $prescription->reference }}</code>
                        </div>
                    @endif
                    <div class="flex items-center">
                        <em class="ni ni-user-circle mr-1 text-xs"></em>
                        Par {{ Auth::user()->name ?? 'Utilisateur' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL POUR LA FACTURE COMPLÈTE --}}
    @if($afficherFactureComplete && $prescription)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-semibold">Facture - {{ $prescription->reference }}</h3>
                    <button wire:click="fermerFacture" class="text-gray-500 hover:text-gray-700">
                        <em class="ni ni-cross text-xl"></em>
                    </button>
                </div>
                <div class="p-6">
                    <!-- Intégration de la facture complète -->
                    @include('livewire.secretaire.prescription.facture-impression', ['prescription' => $prescription])
                </div>
                <div class="flex justify-end p-4 border-t">
                    <button onclick="generatePDF()" class="btn-download mr-2">
                        📥 Télécharger PDF
                    </button>
                    <button onclick="printInvoice()" class="btn-print mr-2">
                        🖨️ Imprimer
                    </button>
                    <button wire:click="fermerFacture" class="btn btn-back">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif

   @push('scripts')
    <script>
        function imprimerFacture() {
            @if($prescription)
                // ✅ CORRECTION : Utiliser le bon nom de route
                const url = "{{ route('secretaire.prescription.facture', ['prescription' => $prescription->id]) }}";
                const fenetreImpression = window.open(url, '_blank');
                
                // Vérifier si la fenêtre a été bloquée par le navigateur
                if (!fenetreImpression || fenetreImpression.closed || typeof fenetreImpression.closed == 'undefined') {
                    alert('Veuvez autoriser les pop-ups pour imprimer la facture.');
                    // Alternative: redirection vers la page de facture
                    window.location.href = url;
                } else {
                    // Attendre que la fenêtre soit chargée puis imprimer
                    fenetreImpression.onload = function() {
                        fenetreImpression.print();
                    };
                }
            @else
                alert('Aucune prescription disponible pour l\'impression.');
            @endif
        }
    </script>
@endpush
@endif