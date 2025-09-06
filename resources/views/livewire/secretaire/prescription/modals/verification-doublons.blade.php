{{-- livewire.secretaire.prescription.modals.verification-doublons.blade.php --}}
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- OVERLAY --}}
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="fermerVerificationDoublons"></div>

        {{-- MODAL --}}
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            {{-- HEADER --}}
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 px-6 py-4 border-b border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-yellow-500 rounded-xl flex items-center justify-center mr-3">
                            <em class="ni ni-alert-triangle text-white text-lg"></em>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                Patients similaires détectés
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                {{ count($patientsSimilaires) }} patient(s) avec des informations similaires trouvé(s)
                            </p>
                        </div>
                    </div>
                    <button wire:click="fermerVerificationDoublons" 
                            class="text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors">
                        <em class="ni ni-cross text-xl"></em>
                    </button>
                </div>
            </div>

            {{-- CONTENU --}}
            <div class="px-6 py-4 max-h-96 overflow-y-auto">
                {{-- PATIENT À CRÉER --}}
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-2 flex items-center">
                        <em class="ni ni-user-add text-blue-600 mr-2"></em>
                        Patient à créer
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                        <div>
                            <span class="text-slate-500 dark:text-slate-400">Nom :</span>
                            <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">{{ $nom }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400">Prénom :</span>
                            <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">{{ $prenom }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400">Civilité :</span>
                            <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">{{ $civilite }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400">Naissance :</span>
                            <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">
                                @if($dateNaissance)
                                    {{ \Carbon\Carbon::parse($dateNaissance)->format('d/m/Y') }}
                                @else
                                    Non renseignée
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- LISTE DES PATIENTS SIMILAIRES --}}
                <div class="space-y-3">
                    <h4 class="font-medium text-slate-700 dark:text-slate-300 flex items-center">
                        <em class="ni ni-users text-slate-500 mr-2"></em>
                        Patients existants similaires
                    </h4>

                    @foreach($patientsSimilaires as $index => $similarite)
                        @php $patient = $similarite['patient']; @endphp
                        <div class="border border-slate-200 dark:border-slate-600 rounded-lg p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    {{-- Informations patient --}}
                                    <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white mr-3">
                                            <em class="ni ni-user text-xs"></em>
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-800 dark:text-slate-100">
                                                {{ $patient['nom'] }} {{ $patient['prenom'] }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                Réf: {{ $patient['reference'] }}
                                            </div>
                                        </div>
                                        
                                        {{-- Badge de similarité --}}
                                        <div class="ml-auto">
                                            <span @class([
                                                'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                                'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300' => $similarite['score'] >= 80,
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' => $similarite['score'] >= 60 && $similarite['score'] < 80,
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300' => $similarite['score'] < 60
                                            ])>
                                                {{ $similarite['score'] }}% similaire
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Détails comparatifs --}}
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mb-2">
                                        <div>
                                            <span class="text-slate-500 dark:text-slate-400">Civilité :</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">{{ $patient['civilite'] }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-500 dark:text-slate-400">Naissance :</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">
                                                @if($patient['date_naissance'])
                                                    {{ \Carbon\Carbon::parse($patient['date_naissance'])->format('d/m/Y') }}
                                                @else
                                                    Non renseignée
                                                @endif
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-slate-500 dark:text-slate-400">Téléphone :</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">{{ $patient['telephone'] ?: 'Non renseigné' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-500 dark:text-slate-400">Dernière visite :</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">
                                                @if(isset($patient['derniere_visite']))
                                                    {{ \Carbon\Carbon::parse($patient['derniere_visite'])->format('d/m/Y') }}
                                                @else
                                                    Aucune
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Raison de la similarité --}}
                                    <div class="flex items-center text-xs text-slate-600 dark:text-slate-400 mb-3">
                                        <em class="ni ni-info-circle mr-1"></em>
                                        <span>{{ $similarite['raison'] }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-600">
                                <div class="flex items-center space-x-3">
                                    {{-- Bouton Historique --}}
                                    <button wire:click="afficherHistoriquePatient({{ $patient['id'] }})"
                                            class="inline-flex items-center px-3 py-1.5 text-xs bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-md hover:bg-blue-200 dark:hover:bg-blue-900/40 transition-colors">
                                        <em class="ni ni-activity mr-1"></em>
                                        Historique
                                    </button>
                                </div>

                                {{-- Bouton Sélectionner --}}
                                <button wire:click="selectionnerPatientSimilaire({{ $patient['id'] }})"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors text-sm">
                                    <em class="ni ni-check mr-1.5"></em>
                                    Utiliser ce patient
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="bg-slate-50 dark:bg-slate-700 px-6 py-4 border-t border-slate-200 dark:border-slate-600">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                        <em class="ni ni-info-circle mr-1.5"></em>
                        <span>Vérifiez attentivement avant de continuer</span>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button wire:click="fermerVerificationDoublons"
                                class="px-4 py-2 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-md hover:bg-slate-300 dark:hover:bg-slate-500 transition-colors">
                            Annuler
                        </button>
                        
                        <button wire:click="confirmerCreationPatient"
                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-md transition-colors">
                            <em class="ni ni-plus mr-1.5"></em>
                            Créer un nouveau patient quand même
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>