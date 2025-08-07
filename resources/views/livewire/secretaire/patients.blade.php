<div>
    <!-- Header avec statistiques -->

    <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-heading font-bold text-gray-900 dark:text-white">Liste des Patients</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Consultez et recherchez dans votre base de patients</p>
            </div>
            
            <!-- Statistiques -->
            @if($totalPatients > 0)
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 max-w-md lg:max-w-none">
                    <div class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 px-3 py-2 rounded-xl border border-primary-200 dark:border-primary-700">
                        <div class="text-xs font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wide">Total</div>
                        <div class="text-xl font-bold text-primary-800 dark:text-primary-300">{{ $totalPatients }}</div>
                    </div>
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600">
                        <div class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Nouveaux</div>
                        <div class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $patientsNouveaux }}</div>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 px-3 py-2 rounded-xl border border-green-200 dark:border-green-700">
                        <div class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Fidèles</div>
                        <div class="text-xl font-bold text-green-800 dark:text-green-300">{{ $patientsFideles }}</div>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 px-3 py-2 rounded-xl border border-yellow-200 dark:border-yellow-700">
                        <div class="text-xs font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wide">VIP</div>
                        <div class="text-xl font-bold text-yellow-800 dark:text-yellow-300">{{ $patientsVip }}</div>
                    </div>
                </div>
            @else
                <div class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 px-4 py-3 rounded-xl border border-primary-200 dark:border-primary-700">
                    <div class="text-sm font-medium text-primary-600 dark:text-primary-400">Total Patients</div>
                    <div class="text-2xl font-bold text-primary-800 dark:text-primary-300">0</div>
                </div>
            @endif
        </div>
    </div>


    <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
        @if($selectionMode && $selectedPatient)
            <!-- Patient sélectionné -->
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-700 rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-800 dark:text-green-300">Patient sélectionné</p>
                            <p class="text-green-700 dark:text-green-400 font-semibold">{{ $selectedPatient->nom }}{{ $selectedPatient->prenom ? ' ' . $selectedPatient->prenom : '' }}</p>
                            <p class="text-xs text-green-600 dark:text-green-500">{{ $selectedPatient->reference }}</p>
                        </div>
                    </div>
                    <button wire:click="selectPatient(null)" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Filtres et recherche -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Recherche -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Rechercher un patient</label>
                    <div class="relative">
                        <input 
                            wire:model.live.debounce.300ms="search"
                            type="text" 
                            id="search"
                            placeholder="Nom, prénom, téléphone, email, référence..."
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-all"
                        >
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filtre par civilité -->
                <div>
                    <label for="sexeFilter" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Civilité</label>
                    <select wire:model.live="sexeFilter" id="sexeFilter" class="w-full py-3 px-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-all">
                        <option value="">Toutes</option>
                        <option value="Monsieur">Monsieur</option>
                        <option value="Madame">Madame</option>
                        <option value="Mademoiselle">Mademoiselle</option>
                        <option value="Enfant">Enfant</option>
                    </select>
                </div>

                <!-- Filtre statut -->
                <div>
                    <label for="statutFilter" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Statut</label>
                    <select wire:model.live="statutFilter" id="statutFilter" class="w-full py-3 px-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-primary-500 dark:focus:border-primary-400 transition-all">
                        <option value="">Tous</option>
                        <option value="NOUVEAU">Nouveau</option>
                        <option value="FIDELE">Fidèle</option>
                        <option value="VIP">VIP</option>
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

                <button 
                    wire:click="resetFilters"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Table des patients -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left">
                                <button wire:click="sortBy('reference')" class="group flex items-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                    #
                                    @if($sortField === 'reference')
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
                            <th class="px-6 py-4 text-left">
                                <button wire:click="sortBy('nom')" class="group flex items-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                    Patient
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
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Analyses</th>
                            <th class="px-6 py-4 text-left">
                                <button wire:click="sortBy('created_at')" class="group flex items-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                    Enregistré le
                                    @if($sortField === 'created_at')
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
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            @if($selectionMode)
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sélectionner</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($patients as $key => $patient)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $selectionMode && $selectedPatient && $selectedPatient->id === $patient->id ? 'bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300 text-sm font-semibold rounded-lg">{{ $key+1 }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 dark:from-primary-500 dark:to-primary-700 flex items-center justify-center shadow-lg">
                                                <span class="text-white font-bold text-sm">
                                                    {{ strtoupper(substr($patient->nom, 0, 1) . substr($patient->prenom ?? 'X', 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $patient->nom }}{{ $patient->prenom ? ' ' . $patient->prenom : '' }}
                                            </div>
                                            <div class="flex items-center mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                                    @if($patient->civilite === 'Monsieur') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                                                    @elseif($patient->secivilitexe === 'Madame') bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300
                                                    @elseif($patient->civilite === 'Mademoiselle') bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300
                                                    @elseif($patient->civilite === 'Enfant') bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300
                                                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                                    @endif">
                                                    {{ $patient->civilite }}
                                                </span>
                                                @if(!$patient->prenom)
                                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400 italic">(sans prénom)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-2">
                                        @if($patient->telephone)
                                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                <svg class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                {{ $patient->telephone }}
                                            </div>
                                        @else
                                            <div class="flex items-center text-sm text-gray-400 dark:text-gray-500">
                                                <em class="text-lg ni ni-mobile"></em>
                                                <span class="italic">Pas de téléphone</span>
                                            </div>
                                        @endif
                                        
                                        @if($patient->email)
                                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                <svg class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="truncate">{{ $patient->email }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-semibold rounded-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                            </svg>
                                            {{ $patient->prescriptions_count }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $patient->created_at->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('secretaire.patient.detail', $patient->id) }}" 
                                        class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="sr-only">Voir le profil</span>
                                    </a>
                                </td>
                                @if($selectionMode)
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button 
                                            wire:click="selectPatient({{ $patient->id }})"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white transition-all transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 
                                                {{ $selectedPatient && $selectedPatient->id === $patient->id ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500 shadow-lg' : 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500 shadow-md' }}">
                                            @if($selectedPatient && $selectedPatient->id === $patient->id)
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Sélectionné
                                            @else
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Sélectionner
                                            @endif
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $selectionMode ? '8' : '7' }}" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Aucun patient trouvé</h3>
                                        <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                                            @if($search || $sexeFilter || $statutFilter)
                                                Essayez de modifier vos critères de recherche pour voir plus de résultats.
                                            @else
                                                Votre base de patients est vide pour le moment.
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
            @if($patients->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>
</div>