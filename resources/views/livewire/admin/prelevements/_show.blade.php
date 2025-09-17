<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="bg-indigo-50 dark:bg-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl transition-colors duration-200">
                <h6 class="font-semibold text-indigo-900 dark:text-indigo-300 flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span class="text-lg mr-2">{{ $prelevement->icone }}</span>
                    Détails du Prélèvement: {{ $prelevement->code }} - {{ $prelevement->denomination }}
                </h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Informations principales --}}
                    <div class="lg:col-span-2 space-y-4">
                        <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Code :</span>
                            <span class="font-bold text-gray-900 dark:text-white text-lg">{{ $prelevement->code }}</span>
                        </div>
                        
                        <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Dénomination :</span>
                            <span class="font-medium text-gray-900 dark:text-white text-right max-w-md">{{ $prelevement->denomination }}</span>
                        </div>
                        
                        <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Prix :</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 text-lg">{{ number_format($prelevement->prix, 0, ',', ' ') }} Ar</span>
                        </div>
                        
                        <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Quantité par défaut :</span>
                            <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 px-3 py-1 rounded-full text-sm font-medium">{{ $prelevement->quantite }}</span>
                        </div>
                        
                        <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Statut :</span>
                            @if($prelevement->is_active)
                                <span class="inline-flex items-center bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 px-3 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 px-3 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Inactif
                                </span>
                            @endif
                        </div>

                        <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-600 transition-colors duration-200">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Catégorie :</span>
                            <div class="flex items-center">
                                @if($prelevement->estSanguin())
                                    <span class="inline-flex items-center bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 px-2 py-1 rounded-full text-xs font-medium">
                                        🩸 Prélèvement sanguin
                                    </span>
                                @elseif($prelevement->estEcouvillon())
                                    <span class="inline-flex items-center bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-medium">
                                        🦠 Prélèvement par écouvillon
                                    </span>
                                @else
                                    <span class="inline-flex items-center bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded-full text-xs font-medium">
                                        🧪 Autre prélèvement
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Informations sur le tube et métadonnées --}}
                    <div class="space-y-6">
                        {{-- Type de tube recommandé --}}
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                                Tube recommandé
                            </h4>
                            @php $tubeInfo = $prelevement->getTypeTubeRecommande() @endphp
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-6 h-6 rounded-full border-2 border-gray-300"
                                     style="background-color: {{ strtolower($tubeInfo['couleur']) === 'rouge' ? '#dc2626' : (strtolower($tubeInfo['couleur']) === 'bleu' ? '#2563eb' : (strtolower($tubeInfo['couleur']) === 'vert' ? '#059669' : (strtolower($tubeInfo['couleur']) === 'violet' ? '#7c3aed' : '#6b7280'))) }}"></div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $tubeInfo['code'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $tubeInfo['couleur'] }}</div>
                                </div>
                            </div>
                            @if($prelevement->typeTubeRecommande)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $prelevement->typeTubeRecommande->description }}</p>
                            @endif
                        </div>

                        {{-- Autres tubes possibles --}}
                        @php $autresTubes = $prelevement->getTypesTubesPossibles() @endphp
                        @if($autresTubes->count() > 1)
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                <h4 class="font-medium text-blue-900 dark:text-blue-300 mb-3">Autres tubes possibles</h4>
                                <div class="space-y-2">
                                    @foreach($autresTubes as $tube)
                                        @if($tube->id !== $prelevement->type_tube_id)
                                            <div class="flex items-center space-x-2 text-sm">
                                                <div class="w-3 h-3 rounded-full"
                                                     style="background-color: {{ strtolower($tube->couleur) === 'rouge' ? '#dc2626' : (strtolower($tube->couleur) === 'bleu' ? '#2563eb' : (strtolower($tube->couleur) === 'vert' ? '#059669' : (strtolower($tube->couleur) === 'violet' ? '#7c3aed' : '#6b7280'))) }}"></div>
                                                <span class="text-blue-800 dark:text-blue-300">{{ $tube->code }} ({{ $tube->couleur }})</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Métadonnées --}}
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600 dark:text-gray-400">Créé le :</span>
                                <span class="text-gray-900 dark:text-white">{{ $prelevement->created_at ? $prelevement->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600 dark:text-gray-400">Modifié le :</span>
                                <span class="text-gray-900 dark:text-white">{{ $prelevement->updated_at ? $prelevement->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                            @if($prelevement->deleted_at)
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">Supprimé le :</span>
                                    <span class="text-red-600 dark:text-red-400">{{ $prelevement->deleted_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Statistiques d'utilisation --}}
                <div class="mt-8 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-700">
                    <h4 class="font-medium text-emerald-900 dark:text-emerald-300 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4"/>
                        </svg>
                        Informations du prélèvement
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $prelevement->code }}</div>
                            <div class="text-sm text-emerald-700 dark:text-emerald-300">Code</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $prelevement->quantite }}</div>
                            <div class="text-sm text-emerald-700 dark:text-emerald-300">Quantité</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($prelevement->calculerPrixTotal($prelevement->quantite), 0, ',', ' ') }}</div>
                            <div class="text-sm text-emerald-700 dark:text-emerald-300">Prix total (Ar)</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $prelevement->estDisponible() ? '✓' : '✗' }}</div>
                            <div class="text-sm text-emerald-700 dark:text-emerald-300">Disponible</div>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-4 mt-8">
                    <button wire:click="edit({{ $prelevement->id }})" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </button>
                    <button wire:click="backToList" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Retour à la liste
                    </button>
                </div>
            </div>
        </div>