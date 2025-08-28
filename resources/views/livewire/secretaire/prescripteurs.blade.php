<div>
    <!-- Header avec statistiques -->
    <div class="container mx-auto mt-8 px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-heading font-bold text-gray-900 dark:text-white">Gestion des Prescripteurs</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Consultez et gérez les prescripteurs associés à votre laboratoire</p>
            </div>
            
            <!-- Statistiques améliorées -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 max-w-md lg:max-w-none">
                <div class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 px-3 py-2 rounded-xl border border-primary-200 dark:border-primary-700">
                    <div class="text-xs font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wide">Total</div>
                    <div class="text-xl font-bold text-primary-800 dark:text-primary-300">{{ $totalPrescripteurs }}</div>
                </div>
                <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 px-3 py-2 rounded-xl border border-green-200 dark:border-green-700">
                    <div class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Actifs</div>
                    <div class="text-xl font-bold text-green-800 dark:text-green-300">{{ $prescripteursActifs }}</div>
                </div>
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-700">
                    <div class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide">Prescriptions</div>
                    <div class="text-xl font-bold text-purple-800 dark:text-purple-300">{{ number_format($totalPrescriptionsCommissionnables ?? 0, 0, ',', ' ') }}</div>
                </div>
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 px-3 py-2 rounded-xl border border-yellow-200 dark:border-yellow-700">
                    <div class="text-xs font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wide">Commissions</div>
                    <div class="text-xl font-bold text-yellow-800 dark:text-yellow-300">{{ number_format($totalCommissions ?? 0, 0, ',', ' ') }} Ar</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Filtres et recherche -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Recherche -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Rechercher un prescripteur</label>
                    <div class="relative">
                        <input 
                            wire:model.live.debounce.300ms="search"
                            type="text" 
                            id="search"
                            placeholder="Nom, prénom, grade, spécialité, téléphone, email..."
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-all"
                        >
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filtre par spécialité -->
                <div>
                    <label for="specialiteFilter" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Spécialité</label>
                    <select wire:model.live="specialiteFilter" id="specialiteFilter" class="w-full py-3 px-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-all">
                        <option value="">Toutes</option>
                        @foreach($specialites as $specialite)
                            <option value="{{ $specialite }}">{{ $specialite }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre statut -->
                <div>
                    <label for="statutFilter" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Statut</label>
                    <select wire:model.live="statutFilter" id="statutFilter" class="w-full py-3 px-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-all">
                        <option value="">Tous</option>
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-4">
                    <label for="perPage" class="text-sm font-medium text-gray-600 dark:text-gray-400">Afficher :</label>
                    <select wire:model.live="perPage" id="perPage" class="py-2 px-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">par page</span>
                </div>

                <div class="flex items-center space-x-3">
                    <button 
                        wire:click="resetFilters"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Réinitialiser
                    </button>

                    <button 
                        wire:click="createPrescripteur"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 border border-transparent rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nouveau prescripteur
                    </button>

                    <button 
                        wire:click="export"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 border border-transparent rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exporter
                    </button>
                </div>
            </div>
        </div>

        <!-- Table des prescripteurs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left">
                                <button wire:click="sortBy('nom')" class="group flex items-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                    Prescripteur
                                    @if($sortField === 'nom')
                                        <svg class="ml-2 w-4 h-4 text-primary-500 dark:text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortDirection === 'asc')
                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                            @else
                                                <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/>
                                            @endif
                                        </svg>
                                    @else
                                        <svg class="ml-2 w-4 h-4 opacity-0 group-hover:opacity-50 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Type & Spécialité
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Contact
                            </th>
                            <th class="px-6 py-4 text-left">
                                <button wire:click="sortBy('prescriptions_commissionnables')" class="group flex items-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                    Prescriptions
                                    @if($sortField === 'prescriptions_commissionnables')
                                        <svg class="ml-2 w-4 h-4 text-primary-500 dark:text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortDirection === 'asc')
                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                            @else
                                                <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/>
                                            @endif
                                        </svg>
                                    @else
                                        <svg class="ml-2 w-4 h-4 opacity-0 group-hover:opacity-50 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($prescripteurs as $prescripteur)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <div class="w-10 h-10 rounded-full {{ $prescripteur->status === 'BiologieSolidaire' ? 'bg-gradient-to-br from-orange-400 to-orange-600 dark:from-orange-500 dark:to-orange-700' : 'bg-gradient-to-br from-primary-400 to-primary-600 dark:from-primary-500 dark:to-primary-700' }} flex items-center justify-center shadow-lg">
                                                <span class="text-white font-bold text-sm">
                                                    {{ strtoupper(substr($prescripteur->nom, 0, 1) . substr($prescripteur->prenom ?? 'X', 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $prescripteur->nom_complet }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $prescripteur->email ?? 'Pas d\'email' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-2">
                                        <!-- Badge du type de prescripteur -->
                                        <div>
                                            @if($prescripteur->status === 'BiologieSolidaire')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300">
                                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                                    </svg>
                                                    Biologie Solidaire
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    Médecin
                                                </span>
                                            @endif
                                        </div>
                                        <!-- Badge spécialité -->
                                        <div>
                                            @if($prescripteur->specialite)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                                                    {{ $prescripteur->specialite }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                    Non spécifié
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $prescripteur->telephone ?? 'Pas de téléphone' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-semibold rounded-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            {{ $prescripteur->prescriptions_commissionnables ?? 0 }}
                                        </span>
                                        @if($prescripteur->total_prescriptions != $prescripteur->prescriptions_commissionnables)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                / {{ $prescripteur->total_prescriptions ?? 0 }} total
                                            </span>
                                        @endif
                                        
                                        <!-- Indicateur commission -->
                                        @if($prescripteur->status === 'BiologieSolidaire')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                                Sans commission
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                                {{ $commissionPourcentage }}% commission
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($prescripteur->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Inactif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center space-x-2">
                                        @if($prescripteur->status === 'BiologieSolidaire')
                                            <!-- Bouton commission désactivé pour BiologieSolidaire -->
                                            <button 
                                                disabled 
                                                class="p-2 text-gray-400 dark:text-gray-600 cursor-not-allowed rounded-lg" 
                                                title="Biologie Solidaire ne perçoit pas de commission"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <!-- Bouton commission normal pour médecins -->
                                            <button 
                                                wire:click="showCommissions({{ $prescripteur->id }})"
                                                class="p-2 text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 rounded-lg transition-colors"
                                                title="Voir les commissions"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        
                                        <button 
                                            wire:click="editPrescripteur({{ $prescripteur->id }})"
                                            class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors" 
                                            title="Modifier"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>

                                        <button 
                                            wire:click="toggleStatus({{ $prescripteur->id }})"
                                            class="p-2 {{ $prescripteur->is_active ? 'text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-900/30' : 'text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/30' }} rounded-lg transition-colors" 
                                            title="{{ $prescripteur->is_active ? 'Désactiver' : 'Activer' }}"
                                        >
                                            @if($prescripteur->is_active)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </button>

                                        <button 
                                            wire:click="confirmDelete({{ $prescripteur->id }})"
                                            class="p-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors" 
                                            title="Supprimer"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Aucun prescripteur trouvé</h3>
                                        <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                                            @if($search || $specialiteFilter || $statutFilter)
                                                Essayez de modifier vos critères de recherche pour voir plus de résultats.
                                            @else
                                                Votre base de prescripteurs est vide pour le moment.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($prescripteurs->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    {{ $prescripteurs->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Inclure les modaux -->
    @include('livewire.secretaire.modals.modal-form-prescripteurs')
    @include('livewire.secretaire.modals.modal-delete-prescripteur')
    @include('livewire.secretaire.modals.modal-comission-prescripteur')
</div>