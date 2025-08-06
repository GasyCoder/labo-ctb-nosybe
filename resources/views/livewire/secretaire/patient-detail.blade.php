<div>
    @if(!$patient)
        <div class="container mx-auto px-4 py-8">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Patient non trouvé</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Le patient demandé n'existe pas.</p>
                <a href="{{ route('secretaire.patients') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Retour à la liste
                </a>
            </div>
        </div>
    @else
        <!-- Header Patient -->
        <div class="">
            <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <!-- Infos principales -->
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 dark:from-primary-500 dark:to-primary-700 flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-xl">
                                {{ strtoupper(substr($patient->nom, 0, 1) . substr($patient->prenom ?? 'X', 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-heading font-bold text-gray-900 dark:text-white">
                                {{ $patient->nom }}{{ $patient->prenom ? ' ' . $patient->prenom : '' }}
                            </h1>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="inline-flex items-center px-3 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300 text-sm font-semibold rounded-lg">
                                    {{ $patient->reference }}
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                    @if($patient->sexe === 'Monsieur') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                                    @elseif($patient->sexe === 'Madame') bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300
                                    @elseif($patient->sexe === 'Mademoiselle') bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300
                                    @elseif($patient->sexe === 'Enfant') bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                    @endif">
                                    {{ $patient->sexe }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                    @if($patient->statut === 'NOUVEAU') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                    @elseif($patient->statut === 'FIDELE') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                    @elseif($patient->statut === 'VIP') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                    @endif">
                                    {{ $patient->statut }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('secretaire.patients') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Retour
                        </a>
                        <button 
                            wire:click="deletePatient"
                            wire:confirm="Êtes-vous sûr de vouloir supprimer ce patient ? Cette action est irréversible."
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Supprimer
                        </button>
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 px-4 py-3 rounded-xl border border-blue-200 dark:border-blue-700">
                        <div class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Analyses Totales</div>
                        <div class="text-2xl font-bold text-blue-800 dark:text-blue-300">{{ $totalAnalyses }}</div>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 px-4 py-3 rounded-xl border border-green-200 dark:border-green-700">
                        <div class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Paiements</div>
                        <div class="text-2xl font-bold text-green-800 dark:text-green-300">{{ $totalPaiements }}</div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 px-4 py-3 rounded-xl border border-purple-200 dark:border-purple-700">
                        <div class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide">Montant Total</div>
                        <div class="text-2xl font-bold text-purple-800 dark:text-purple-300">{{ number_format($montantTotal, 0, ',', ' ') }} Ar</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <!-- Navigation par onglets -->
            <div class="mb-8">
                <nav class="flex space-x-8 border-b border-gray-200 dark:border-gray-700">
                    <button 
                        wire:click="setActiveTab('infos')"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'infos' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informations
                    </button>
                    <button 
                        wire:click="setActiveTab('analyses')"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'analyses' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Analyses ({{ $totalAnalyses }})
                    </button>
                    <button 
                        wire:click="setActiveTab('paiements')"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'paiements' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Paiements ({{ $totalPaiements }})
                    </button>
                </nav>
            </div>

            <!-- Contenu des onglets -->
            <div class="space-y-6">
                @if($activeTab === 'infos')
                    <!-- Informations Patient -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Informations Personnelles</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom</label>
                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-white">
                                        {{ $patient->nom }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom</label>
                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-white">
                                        {{ $patient->prenom ?: 'Non renseigné' }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Civilité</label>
                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-white">
                                        {{ $patient->sexe }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-white">
                                        {{ $patient->telephone ?: 'Non renseigné' }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-white">
                                        {{ $patient->email ?: 'Non renseigné' }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'enregistrement</label>
                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-white">
                                        {{ $patient->created_at->format('d/m/Y à H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'analyses')
                    <!-- Historique des analyses -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Historique des Analyses</h3>
                        </div>
                        
                        @forelse($patient->prescriptions as $prescription)
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <span class="inline-flex items-center px-3 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300 text-sm font-semibold rounded-lg">
                                            Prescription #{{ $prescription->id }}
                                        </span>
                                        <span class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $prescription->created_at->format('d/m/Y à H:i') }}
                                        </span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-medium rounded-md">
                                        {{ $prescription->analyses->count() }} analyse(s)
                                    </span>
                                </div>
                                
                                @if($prescription->analyses->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($prescription->analyses as $analyse)
                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                                <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $analyse->designation }}</div>
                                                @if($analyse->prix)
                                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ number_format($analyse->prix, 0, ',', ' ') }} Ar</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune analyse</h3>
                                <p class="text-gray-500 dark:text-gray-400">Ce patient n'a encore effectué aucune analyse.</p>
                            </div>
                        @endforelse
                    </div>
                @endif

                @if($activeTab === 'paiements')
                    <!-- Historique des paiements -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Historique des Paiements</h3>
                        </div>
                        
                        @php
                            $allPaiements = $patient->prescriptions->flatMap->paiements;
                        @endphp
                        
                        @forelse($allPaiements as $paiement)
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ number_format($paiement->montant, 0, ',', ' ') }} Ar
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $paiement->created_at->format('d/m/Y à H:i') }} • {{ $paiement->mode_paiement }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs font-medium rounded-md">
                                            Prescription #{{ $paiement->prescription_id }}
                                        </span>
                                        <button 
                                            wire:click="generateInvoice({{ $paiement->id }})"
                                            class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded-md transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Facture
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun paiement</h3>
                                <p class="text-gray-500 dark:text-gray-400">Ce patient n'a encore effectué aucun paiement.</p>
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>