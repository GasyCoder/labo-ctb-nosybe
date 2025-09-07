{{-- livewire.secretaire.prescription.modals.historique-patient.blade.php --}}
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- OVERLAY --}}
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="fermerHistorique"></div>

        {{-- MODAL --}}
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
            {{-- HEADER --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mr-3">
                            <em class="ni ni-activity text-white text-lg"></em>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                Historique médical
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $patientSelectionne->nom }} {{ $patientSelectionne->prenom }}
                                <span class="mx-2">•</span>
                                <span class="text-blue-600 dark:text-blue-400">{{ $patientSelectionne->numero_dossier }}</span>
                            </p>
                        </div>
                    </div>
                    <button wire:click="fermerHistorique" 
                            class="text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors">
                        <em class="ni ni-cross text-xl"></em>
                    </button>
                </div>
            </div>

            {{-- INFORMATIONS PATIENT --}}
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-center">
                        <em class="ni ni-user text-slate-500 mr-2"></em>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400">Civilité</span>
                            <div class="font-medium text-slate-700 dark:text-slate-200">{{ $patientSelectionne->civilite }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <em class="ni ni-calendar text-slate-500 mr-2"></em>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400">Âge</span>
                            <div class="font-medium text-slate-700 dark:text-slate-200">
                                @if($patientSelectionne->date_naissance)
                                    {{ $patientSelectionne->age_formate }}
                                @else
                                    Non renseigné
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <em class="ni ni-call text-slate-500 mr-2"></em>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400">Téléphone</span>
                            <div class="font-medium text-slate-700 dark:text-slate-200">{{ $patientSelectionne->telephone ?: 'Non renseigné' }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <em class="ni ni-activity text-slate-500 mr-2"></em>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400">Dernière visite</span>
                            <div class="font-medium text-slate-700 dark:text-slate-200">
                                @if($patientSelectionne->derniere_visite)
                                    {{ $patientSelectionne->derniere_visite->format('d/m/Y') }}
                                @else
                                    Aucune
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATISTIQUES RAPIDES --}}
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600">
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $patientSelectionne->prescriptions->count() }}</div>
                        <div class="text-xs text-slate-600 dark:text-slate-400">Prescription(s)</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $patientSelectionne->nombre_analyses_total }}</div>
                        <div class="text-xs text-slate-600 dark:text-slate-400">Analyse(s) effectuée(s)</div>
                    </div>
                    <div class="text-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            @php
                                $prescripteurs = $patientSelectionne->prescriptions->pluck('prescripteur.nom')->unique();
                            @endphp
                            {{ $prescripteurs->count() }}
                        </div>
                        <div class="text-xs text-slate-600 dark:text-slate-400">Prescripteur(s)</div>
                    </div>
                </div>
            </div>

            {{-- CONTENU - HISTORIQUE DES PRESCRIPTIONS --}}
            <div class="px-6 py-4 max-h-96 overflow-y-auto">
                @if($patientSelectionne->historique_recent->count() > 0)
                    <div class="space-y-4">
                        <h4 class="font-medium text-slate-700 dark:text-slate-300 flex items-center">
                            <em class="ni ni-list text-slate-500 mr-2"></em>
                            Dernières prescriptions ({{ $patientSelectionne->historique_recent->count() }} plus récentes)
                        </h4>

                        @foreach($patientSelectionne->historique_recent as $prescription)
                            <div class="border border-slate-200 dark:border-slate-600 rounded-lg p-4">
                                {{-- En-tête prescription --}}
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white mr-3">
                                            <em class="ni ni-file-docs text-xs"></em>
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-800 dark:text-slate-100">
                                                {{ $prescription->reference }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $prescription->created_at->format('d/m/Y à H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Statut --}}
                                    <span @class([
                                        'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                        'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' => $prescription->status === 'VALIDE',
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300' => $prescription->status === 'EN_COURS',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' => $prescription->status === 'EN_ATTENTE',
                                        'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300' => $prescription->status === 'A_REFAIRE',
                                        'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300' => $prescription->status === 'ARCHIVE'
                                    ])>
                                        {{ $prescription->status_label }}
                                    </span>
                                </div>

                                {{-- Informations prescription --}}
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm mb-3">
                                    <div>
                                        <span class="text-slate-500 dark:text-slate-400">Prescripteur :</span>
                                        <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">{{ $prescription->prescripteur?->nom ?? 'Non renseigné' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 dark:text-slate-400">Âge :</span>
                                        <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">{{ $prescription->age }} {{ strtolower($prescription->unite_age) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 dark:text-slate-400">Type :</span>
                                        <span class="font-medium text-slate-700 dark:text-slate-200 ml-1">{{ $prescription->patient_type }}</span>
                                    </div>
                                </div>

                                {{-- Analyses effectuées --}}
                                @if($prescription->analyses->count() > 0)
                                    <div class="mb-3">
                                        <h5 class="text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">
                                            Analyses ({{ $prescription->analyses->count() }}) :
                                        </h5>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($prescription->analyses->take(6) as $analyse)
                                                <span class="inline-flex items-center px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded text-xs">
                                                    {{ $analyse->code }}
                                                </span>
                                            @endforeach
                                            @if($prescription->analyses->count() > 6)
                                                <span class="inline-flex items-center px-2 py-1 bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-400 rounded text-xs">
                                                    +{{ $prescription->analyses->count() - 6 }} autres
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Informations cliniques --}}
                                @if($prescription->renseignement_clinique)
                                    <div class="mt-3 p-2 bg-slate-50 dark:bg-slate-700/50 rounded text-xs">
                                        <span class="text-slate-500 dark:text-slate-400">Renseignements cliniques :</span>
                                        <div class="text-slate-700 dark:text-slate-300 mt-1">{{ $prescription->renseignement_clinique }}</div>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- Lien vers l'historique complet --}}
                        @if($patientSelectionne->prescriptions->count() > 5)
                            <div class="text-center pt-2 border-t border-slate-200 dark:border-slate-600">
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $patientSelectionne->prescriptions->count() - 5 }} prescription(s) supplémentaire(s) dans l'historique complet
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Aucun historique --}}
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <em class="ni ni-file-docs text-slate-400 text-2xl"></em>
                        </div>
                        <h4 class="text-lg font-medium text-slate-700 dark:text-slate-300 mb-2">Aucun historique</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Ce patient n'a pas encore d'analyses enregistrées
                        </p>
                    </div>
                @endif
            </div>

            {{-- FOOTER --}}
            <div class="bg-slate-50 dark:bg-slate-700 px-6 py-4 border-t border-slate-200 dark:border-slate-600">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        Patient créé le {{ $patientSelectionne->created_at->format('d/m/Y') }}
                    </div>
                    <div class="flex items-center space-x-3">
                        <button wire:click="fermerHistorique"
                                class="px-4 py-2 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-md hover:bg-slate-300 dark:hover:bg-slate-500 transition-colors">
                            Fermer
                        </button>
                        
                        <button wire:click="selectionnerPatientSimilaire({{ $patientSelectionne->id }})"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                            <em class="ni ni-check mr-1.5"></em>
                            Sélectionner ce patient
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>