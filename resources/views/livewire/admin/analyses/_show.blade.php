{{-- resources/views/livewire/admin/analyses/_show.blade.php - Version Mobile Optimisée --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mx-2 sm:mx-0">
    <div
        class="bg-indigo-50 dark:bg-indigo-900/20 px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-xl">
        <h6 class="font-semibold text-indigo-900 dark:text-indigo-200 flex flex-col sm:flex-row sm:items-center">
            <div class="flex items-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span class="text-sm sm:text-base truncate">Détails de l'Analyse: {{ $analyse->code }}</span>
            </div>
            <span
                class="mt-1 sm:mt-0 sm:ml-2 text-xs sm:text-sm 
                @if ($analyse->level === 'PARENT') bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200
                @elseif($analyse->level === 'NORMAL') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 @endif
                px-2 py-1 rounded-full font-medium inline-block">
                {{ $analyse->level }}
            </span>
        </h6>
    </div>

    <div class="p-3 sm:p-6">
        {{-- Section 1: Informations principales - Stack sur mobile --}}
        <div class="space-y-6 lg:grid lg:grid-cols-2 lg:gap-8 lg:space-y-0 mb-6 sm:mb-8">
            {{-- Colonne gauche --}}
            <div class="space-y-4 sm:space-y-6">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 sm:p-6">
                    <h3
                        class="text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Informations de base
                    </h3>

                    <div class="space-y-3 sm:space-y-4">
                        <div
                            class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 sm:py-3 border-b border-gray-200 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Code
                                :</span>
                            <span
                                class="font-mono bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 sm:px-3 py-1 rounded-md text-sm font-semibold {{ $analyse->is_bold ? 'font-bold' : '' }} mt-1 sm:mt-0 self-start sm:self-auto">
                                {{ $analyse->code }}
                            </span>
                        </div>

                        <div
                            class="flex flex-col sm:flex-row sm:justify-between sm:items-start py-2 sm:py-3 border-b border-gray-200 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Désignation
                                :</span>
                            <span
                                class="font-medium text-gray-900 dark:text-white sm:text-right sm:max-w-xs {{ $analyse->is_bold ? 'font-bold' : '' }} mt-1 sm:mt-0 text-sm sm:text-base">
                                {{ $analyse->designation }}
                            </span>
                        </div>

                        <div
                            class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 sm:py-3 border-b border-gray-200 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Niveau
                                :</span>
                            <span
                                class="
                                @if ($analyse->level === 'PARENT') bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200
                                @elseif($analyse->level === 'NORMAL') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 @endif
                                px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium mt-1 sm:mt-0 self-start sm:self-auto">
                                {{ $analyse->level }}
                            </span>
                        </div>

                        <div
                            class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 sm:py-3 border-b border-gray-200 dark:border-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Prix
                                :</span>
                            <span class="font-bold text-lg sm:text-xl text-green-600 dark:text-green-400 mt-1 sm:mt-0">
                                {{ number_format($analyse->prix, 0, ',', ' ') }} Ar
                            </span>
                        </div>

                        @if ($analyse->parent)
                            <div
                                class="flex flex-col sm:flex-row sm:justify-between sm:items-start py-2 sm:py-3 border-b border-gray-200 dark:border-gray-600">
                                <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Parent
                                    :</span>
                                <div class="sm:text-right mt-1 sm:mt-0">
                                    <span
                                        class="text-gray-900 dark:text-white font-medium text-sm sm:text-base">{{ $analyse->parent->code }}</span>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                        {{ $analyse->parent->designation }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 sm:py-3">
                            <span
                                class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base mb-2 sm:mb-0">Statut
                                :</span>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:click="toggleStatus({{ $analyse->id }})"
                                    @if ($analyse->status) checked @endif class="sr-only peer"
                                    wire:loading.attr="disabled">

                                <div
                                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                                </div>

                                <span class="ml-3 text-sm font-medium">
                                    <span wire:loading.remove wire:target="toggleStatus({{ $analyse->id }})">
                                        <span
                                            class="{{ $analyse->status ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ $analyse->status ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </span>

                                    <span wire:loading wire:target="toggleStatus({{ $analyse->id }})"
                                        class="text-blue-600">
                                        Modification...
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne droite --}}
            <div class="space-y-4 sm:space-y-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 sm:p-6">
                    <h3
                        class="text-base sm:text-lg font-medium text-blue-900 dark:text-blue-200 mb-3 sm:mb-4 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Paramètres techniques
                    </h3>

                    <div class="space-y-3 sm:space-y-4">
                        <div
                            class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 sm:py-3 border-b border-blue-200 dark:border-blue-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Examen
                                :</span>
                            <span
                                class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 sm:px-3 py-1 rounded-md text-xs sm:text-sm font-medium mt-1 sm:mt-0 break-words">
                                {{ $analyse->examen ? $analyse->examen->abr . ' - ' . Str::limit($analyse->examen->name, 30) : 'N/A' }}
                            </span>
                        </div>

                        <div
                            class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 sm:py-3 border-b border-blue-200 dark:border-blue-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Type
                                :</span>
                            <span
                                class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium mt-1 sm:mt-0">
                                {{ $analyse->type ? $analyse->type->name : 'N/A' }}
                            </span>
                        </div>

                        {{-- Valeurs de référence --}}
                        @if (
                            $analyse->valeur_ref_homme ||
                                $analyse->valeur_ref_femme ||
                                $analyse->valeur_ref_enfant_garcon ||
                                $analyse->valeur_ref_enfant_fille)
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 mb-6">
                                <h3
                                    class="text-lg font-medium text-green-900 dark:text-green-200 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Valeurs de Référence
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Adulte --}}
                                    <div class="space-y-3">
                                        <h4 class="font-medium text-gray-900 dark:text-white flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Adulte
                                        </h4>

                                        @if ($analyse->valeur_ref_homme)
                                            <div
                                                class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-600">
                                                <span
                                                    class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    Homme
                                                </span>
                                                <span
                                                    class="font-semibold text-gray-900 dark:text-white">{{ $analyse->valeur_homme_complete }}</span>
                                            </div>
                                        @endif

                                        @if ($analyse->valeur_ref_femme)
                                            <div
                                                class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-600">
                                                <span
                                                    class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-pink-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m0 0H9" />
                                                    </svg>
                                                    Femme
                                                </span>
                                                <span
                                                    class="font-semibold text-gray-900 dark:text-white">{{ $analyse->valeur_femme_complete }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Enfant --}}
                                    <div class="space-y-3">
                                        <h4 class="font-medium text-gray-900 dark:text-white flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-orange-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m0 0H9" />
                                            </svg>
                                            Enfant
                                        </h4>

                                        @if ($analyse->valeur_ref_enfant_garcon)
                                            <div
                                                class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-600">
                                                <span
                                                    class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m0 0H9" />
                                                    </svg>
                                                    Garçon
                                                </span>
                                                <span
                                                    class="font-semibold text-gray-900 dark:text-white">{{ $analyse->valeur_enfant_garcon_complete }}</span>
                                            </div>
                                        @endif

                                        @if ($analyse->valeur_ref_enfant_fille)
                                            <div
                                                class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-600">
                                                <span
                                                    class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-pink-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m0 0H9" />
                                                    </svg>
                                                    Fille
                                                </span>
                                                <span
                                                    class="font-semibold text-gray-900 dark:text-white">{{ $analyse->valeur_enfant_fille_complete }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($analyse->unite)
                            <div
                                class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 sm:py-3 border-b border-blue-200 dark:border-blue-700">
                                <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Unité
                                    :</span>
                                <span
                                    class="text-gray-900 dark:text-white font-medium text-sm sm:text-base mt-1 sm:mt-0">{{ $analyse->unite }}</span>
                            </div>
                        @endif

                        @if ($analyse->suffixe)
                            <div
                                class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 sm:py-3 border-b border-blue-200 dark:border-blue-700">
                                <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Suffixe
                                    :</span>
                                <span
                                    class="text-gray-900 dark:text-white font-medium text-sm sm:text-base mt-1 sm:mt-0">{{ $analyse->suffixe }}</span>
                            </div>
                        @endif

                        <div
                            class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 sm:py-3 border-b border-blue-200 dark:border-blue-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Ordre
                                d'affichage :</span>
                            <span
                                class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded text-xs sm:text-sm mt-1 sm:mt-0">
                                {{ $analyse->ordre ?? 'Non défini' }}
                            </span>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 sm:py-3">
                            <span class="font-medium text-gray-600 dark:text-gray-400 text-sm sm:text-base">Formatage
                                :</span>
                            @if ($analyse->is_bold)
                                <span
                                    class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 px-2 py-1 rounded text-xs sm:text-sm font-bold mt-1 sm:mt-0">
                                    Texte en gras
                                </span>
                            @else
                                <span
                                    class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mt-1 sm:mt-0">Formatage
                                    normal</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Description (si présente) --}}
        @if ($analyse->description)
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 sm:p-6 mb-6 sm:mb-8">
                <h3
                    class="text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-2 sm:mb-3 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Description
                </h3>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-sm sm:text-base">
                    {{ $analyse->description }}</p>
            </div>
        @endif

        {{-- Section 3: Sous-analyses (si applicable) --}}
        @if ($analyse->enfants && count($analyse->enfants) > 0)
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 sm:p-6 mb-6 sm:mb-8">
                <h3
                    class="text-base sm:text-lg font-medium text-purple-900 dark:text-purple-200 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Sous-analyses
                    <span
                        class="ml-2 bg-purple-200 dark:bg-purple-800 text-purple-800 dark:text-purple-200 px-2 py-1 rounded-full text-xs sm:text-sm">
                        {{ count($analyse->enfants) }}
                    </span>
                </h3>

                {{-- Grid responsive des sous-analyses --}}
                <div class="space-y-3 sm:grid sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 sm:gap-3 sm:space-y-0">
                    @foreach ($analyse->enfants as $index => $enfant)
                        <div
                            class="bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-600 rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center mb-1">
                                        <span
                                            class="w-5 h-5 sm:w-6 sm:h-6 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded-full text-xs font-medium flex items-center justify-center mr-2 flex-shrink-0">
                                            {{ $index + 1 }}
                                        </span>
                                        <span
                                            class="font-mono text-xs sm:text-sm font-semibold dark:text-white {{ $enfant->is_bold ? 'font-bold' : '' }} truncate">
                                            {{ $enfant->code }}
                                        </span>
                                    </div>
                                    <p
                                        class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 {{ $enfant->is_bold ? 'font-bold' : '' }} mb-1">
                                        {{ $enfant->designation }}
                                    </p>
                                    @if ($enfant->valeur_ref)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Réf: {{ Str::limit($enfant->valeur_ref, 20) }}
                                            @if ($enfant->unite)
                                                {{ $enfant->unite }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right ml-2 flex-shrink-0">
                                    <span class="text-xs sm:text-sm font-medium text-green-600 dark:text-green-400">
                                        {{ number_format($enfant->prix, 0, ',', ' ') }} Ar
                                    </span>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <span
                                            class="
                                            @if ($enfant->level === 'CHILD') text-gray-600 dark:text-gray-400
                                            @elseif($enfant->level === 'NORMAL') text-blue-600 dark:text-blue-400 @endif
                                            font-medium">
                                            {{ $enfant->level }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions compactes --}}
                            <div
                                class="flex justify-end space-x-1 pt-2 border-t border-purple-200 dark:border-purple-600">
                                <button wire:click="show({{ $enfant->id }})"
                                    class="bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800 p-1.5 sm:p-2 rounded text-xs transition-colors"
                                    title="Voir détails">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button wire:click="edit({{ $enfant->id }})"
                                    class="bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-800 p-1.5 sm:p-2 rounded text-xs transition-colors"
                                    title="Modifier">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Statistiques des sous-analyses - Stack sur mobile --}}
                <div
                    class="mt-4 sm:mt-6 p-3 sm:p-4 bg-white dark:bg-gray-800 rounded-lg border border-purple-200 dark:border-purple-600">
                    <div class="space-y-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0">
                        <div class="text-center">
                            <div class="text-xl sm:text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ count($analyse->enfants) }}
                            </div>
                            <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Sous-analyses</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($analyse->enfants->sum('prix'), 0, ',', ' ') }} Ar
                            </div>
                            <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Prix total des
                                sous-analyses</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($analyse->prix + $analyse->enfants->sum('prix'), 0, ',', ' ') }} Ar
                            </div>
                            <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Prix total du panel</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Section 4: Actions - Stack sur mobile --}}
        <div
            class="space-y-3 sm:flex sm:flex-wrap sm:gap-4 sm:space-y-0 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-600">
            <button wire:click="edit({{ $analyse->id }})"
                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg flex items-center justify-center transition-colors font-medium text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Modifier l'analyse
            </button>

            <button wire:click="backToList"
                class="w-full sm:w-auto bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg flex items-center justify-center transition-colors font-medium text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Retour à la liste
            </button>
        </div>
    </div>
</div>
