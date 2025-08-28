<div class="p-6">
    {{-- Messages flash --}}
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-green-800 font-medium">{{ session('message') }}</span>
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                @if($mode === 'list')
                    <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    Gestion des Examens
                @elseif($mode === 'create')
                    <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouvel Examen
                @elseif($mode === 'edit')
                    <svg class="w-6 h-6 mr-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier l'Examen
                @elseif($mode === 'show')
                    <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Détails de l'Examen
                @endif
            </h2>
            <p class="text-gray-600 mt-1">
                @if($mode === 'list')
                    Gérez les différents types d'examens médicaux
                @else
                    <button wire:click="backToList" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </button>
                @endif
            </p>
        </div>
        
        @if($mode === 'list')
            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouvel Examen
            </button>
        @endif
    </div>

    {{-- Mode Liste --}}
    @if($mode === 'list')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h6 class="font-semibold text-gray-900">Liste des Examens ({{ count($examens) }})</h6>
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="flex items-center mr-4">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            Actif
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                            Inactif
                        </div>
                    </div>
                </div>
            </div>
            
            @if($examens && count($examens) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-6 py-4 font-medium text-gray-900">ID</th>
                                <th class="text-left py-4 font-medium text-gray-900">Nom de l'Examen</th>
                                <th class="text-left py-4 font-medium text-gray-900">Abréviation</th>
                                <th class="text-center py-4 font-medium text-gray-900">Statut</th>
                                <th class="text-center py-4 font-medium text-gray-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($examens as $exam)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-md text-sm font-medium">{{ $exam->id }}</span>
                                    </td>
                                    <td class="py-4 pr-4">
                                        <div class="font-medium text-gray-900">{{ $exam->name }}</div>
                                    </td>
                                    <td class="py-4 pr-4">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-md text-sm font-medium">{{ $exam->abr }}</span>
                                    </td>
                                    <td class="py-4 text-center">
                                        @if($exam->status)
                                            <span class="inline-flex items-center bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Actif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Inactif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <button wire:click="show({{ $exam->id }})" 
                                                    class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 p-2 rounded-lg transition-colors"
                                                    title="Voir les détails">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="edit({{ $exam->id }})" 
                                                    class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 p-2 rounded-lg transition-colors"
                                                    title="Modifier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <h5 class="text-xl font-medium text-gray-900 mb-2">Aucun examen trouvé</h5>
                    <p class="text-gray-600 mb-4">Commencez par créer votre premier examen.</p>
                    <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Créer un examen
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- Mode Création --}}
    @if($mode === 'create')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="bg-blue-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                <h6 class="font-semibold text-blue-900 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Créer un Nouvel Examen
                </h6>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="store" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom de l'Examen <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="name" 
                                   wire:model="name"
                                   placeholder="Ex: BIOCHIMIE">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="abr" class="block text-sm font-medium text-gray-700 mb-2">
                                Abréviation <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('abr') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="abr" 
                                   wire:model="abr"
                                   placeholder="Ex: BIO"
                                   maxlength="10">
                            @error('abr')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="status" 
                                   wire:model="status"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                   checked>
                            <label for="status" class="ml-2 text-sm text-gray-700">
                                Examen actif
                            </label>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Enregistrer
                        </button>
                        <button type="button" wire:click="backToList" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Mode Édition --}}
    @if($mode === 'edit' && $examen)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="bg-yellow-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                <h6 class="font-semibold text-yellow-900 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier l'Examen
                </h6>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="update" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom de l'Examen <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="edit_name" 
                                   wire:model="name"
                                   value="{{ $examen->name }}">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="edit_abr" class="block text-sm font-medium text-gray-700 mb-2">
                                Abréviation <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('abr') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="edit_abr" 
                                   wire:model="abr"
                                   value="{{ $examen->abr }}"
                                   maxlength="10">
                            @error('abr')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="edit_status" 
                                   wire:model="status"
                                   class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 focus:ring-2"
                                   {{ $examen->status ? 'checked' : '' }}>
                            <label for="edit_status" class="ml-2 text-sm text-gray-700">
                                Examen actif
                            </label>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Mettre à jour
                        </button>
                        <button type="button" wire:click="backToList" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Mode Affichage --}}
    @if($mode === 'show' && $examen)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="bg-indigo-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                <h6 class="font-semibold text-indigo-900 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Détails de l'Examen
                </h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">ID :</span>
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-md text-sm font-medium">{{ $examen->id }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Nom :</span>
                            <span class="font-medium text-gray-900">{{ $examen->name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Abréviation :</span>
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-md text-sm font-medium">{{ $examen->abr }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Statut :</span>
                            @if($examen->status)
                                <span class="inline-flex items-center bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Inactif
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Créé le :</span>
                            <span class="text-gray-900">{{ $examen->created_at ? $examen->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Modifié le :</span>
                            <span class="text-gray-900">{{ $examen->updated_at ? $examen->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        @if($examen->deleted_at)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Supprimé le :</span>
                            <span class="text-red-600">{{ $examen->deleted_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex space-x-4 mt-8">
                    <button wire:click="edit({{ $examen->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </button>
                    <button wire:click="backToList" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Retour à la liste
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>