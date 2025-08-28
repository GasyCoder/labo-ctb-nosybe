    @if($activeTab === 'infos')
        <!-- Informations Patient Optimisées -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informations Personnelles</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nom complet</label>
                            <div class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $patient->nom }}{{ $patient->prenom ? ' ' . $patient->prenom : '' }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Civilité</label>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $patient->civilite }}
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Téléphone</label>
                            <div class="text-sm text-gray-900 dark:text-white">
                                @if($patient->telephone)
                                    <a href="tel:{{ $patient->telephone }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                                        {{ $patient->telephone }}
                                    </a>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Non renseigné</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email</label>
                            <div class="text-sm text-gray-900 dark:text-white">
                                @if($patient->email)
                                    <a href="mailto:{{ $patient->email }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                                        {{ $patient->email }}
                                    </a>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Non renseigné</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Enregistré le</label>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $patient->created_at->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif