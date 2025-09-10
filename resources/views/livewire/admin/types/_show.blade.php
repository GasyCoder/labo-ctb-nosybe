   <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="bg-indigo-50 dark:bg-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl">
                <h6 class="font-semibold text-indigo-900 dark:text-indigo-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Détails du Type: {{ $type->name }}
                </h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400">ID :</span>
                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-md text-sm font-medium">{{ $type->id }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Nom :</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $type->name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Libellé :</span>
                            <span class="text-gray-900 dark:text-white">{{ $type->libelle }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Statut :</span>
                            @if($type->status)
                                <span class="inline-flex items-center bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Inactif
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Nombre d'analyses :</span>
                            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full text-sm font-medium">{{ $type->analyses_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Créé le :</span>
                            <span class="text-gray-900 dark:text-white">{{ $type->created_at ? $type->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Modifié le :</span>
                            <span class="text-gray-900 dark:text-white">{{ $type->updated_at ? $type->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        @if($type->deleted_at)
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400">Supprimé le :</span>
                            <span class="text-red-600 dark:text-red-400">{{ $type->deleted_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($type->analyses && count($type->analyses) > 0)
                    <div class="mt-6">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-4">Analyses utilisant ce type ({{ count($type->analyses) }})</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($type->analyses->take(6) as $analyse)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                    <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $analyse->designation }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $analyse->code }}</div>
                                </div>
                            @endforeach
                            @if(count($type->analyses) > 6)
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Et {{ count($type->analyses) - 6 }} autres...</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="flex space-x-4 mt-8">
                    <button wire:click="edit({{ $type->id }})" class="bg-purple-600 dark:bg-purple-500 hover:bg-purple-700 dark:hover:bg-purple-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </button>
                    <button wire:click="backToList" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-6 py-2 rounded-lg flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Retour à la liste
                    </button>
                </div>
            </div>
        </div>