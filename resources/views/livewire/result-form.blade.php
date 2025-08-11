<div class="bg-gray-900 rounded-xl border border-gray-700">
    {{-- Header --}}
    <div class="p-6 border-b border-gray-700 bg-gradient-to-r from-purple-600 to-blue-600 rounded-t-xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-white rounded-full mr-3"></div>
                <div>
                    <h1 class="text-2xl font-bold text-white {{ $analyse->is_bold ? 'font-black' : '' }}">
                        {{ strtoupper($analyse->designation) }}
                    </h1>
                    <div class="flex items-center space-x-2 mt-1">
                        @if($analyse->level === 'PARENT')
                            <span class="px-2 py-1 bg-purple-500 text-white text-xs rounded-full">PANEL</span>
                        @elseif($analyse->level === 'CHILD')
                            <span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full">SOUS-ANALYSE</span>
                        @endif
                        @if($analyse->is_bold)
                            <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full">Important</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($resultat)
                <div class="text-green-400 flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Saisi</span>
                </div>
            @endif
        </div>

        {{-- Infos analyse --}}
        <div class="mt-3 text-sm text-purple-100">
            <p><strong>Examen :</strong> {{ $analyse->examen->name ?? '—' }}</p>
            <p><strong>Type :</strong> {{ $analyse->type->libelle ?? ($analyse->type->name ?? '—') }}</p>
            @if($analyse->description)
                <p class="text-xs text-purple-200 mt-1 italic">{{ $analyse->description }}</p>
            @endif
        </div>
    </div>

    {{-- Arbre des sous-analyses (générique et récursif) --}}
    @if($analyse->level === 'PARENT' || ($analyse->enfantsRecursive?->count() > 0))
        <div class="p-6">
            <div class="p-4 bg-gray-800 border border-gray-700 rounded-lg">
                <h3 class="text-gray-200 font-semibold mb-3">Analyses incluses</h3>
                <x-analyse-tree :analyse="$analyse" />
            </div>
        </div>
    @endif

    {{-- Formulaire principal --}}
    <form wire:submit="save" class="p-6">
        {{-- Widget selon le type --}}
        <div class="mb-6">
            @switch($analyse->type->name ?? '')
                @case('LABEL')
                    <div class="p-4 bg-gray-800 rounded-lg border border-gray-600">
                        <p class="text-gray-300 font-medium">{{ $analyse->designation }}</p>
                        @if($analyse->description)
                            <p class="text-sm text-gray-400 mt-1">{{ $analyse->description }}</p>
                        @endif
                    </div>
                    @break

                @case('MULTIPLE')
                @case('MULTIPLE_SELECTIF')
                    <div class="p-4 bg-blue-900 border border-blue-600 rounded-lg">
                        <h3 class="text-lg font-bold text-blue-100">{{ $analyse->designation }}</h3>
                        @if($analyse->description)
                            <p class="text-sm text-blue-200 mt-1">{{ $analyse->description }}</p>
                        @endif
                        <p class="text-xs text-blue-300 mt-2">Ce panel contient plusieurs analyses détaillées.</p>
                    </div>
                    @break

                @case('INPUT')
                @case('DOSAGE')
                @case('COMPTAGE')
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Valeur {{ $analyse->unite ? "($analyse->unite)" : '' }}
                            </label>
                            <input type="text" wire:model="valeur"
                                   class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
                                   placeholder="Entrez une valeur">
                            @error('valeur') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Interprétation</label>
                            <select wire:model="interpretation"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500 focus:ring-1 focus:border-purple-500">
                                <option value="NORMAL">NORMAL</option>
                                <option value="PATHOLOGIQUE">PATHOLOGIQUE</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('INPUT_SUFFIXE')
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Valeur</label>
                            <div class="flex">
                                <input type="text" wire:model="valeur"
                                       class="flex-1 bg-gray-800 border border-gray-600 rounded-l-lg px-4 py-3 text-white placeholder-gray-400 focus:border-purple-500 focus:ring-1 focus:border-purple-500">
                                @if($analyse->suffixe)
                                    <span class="px-4 py-3 bg-gray-700 border border-l-0 border-gray-600 rounded-r-lg text-sm text-gray-300">
                                        {{ $analyse->suffixe }}
                                    </span>
                                @endif
                            </div>
                            @error('valeur') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Interprétation</label>
                            <select wire:model="interpretation"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                <option value="NORMAL">NORMAL</option>
                                <option value="PATHOLOGIQUE">PATHOLOGIQUE</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('SELECT')
                @case('TEST')
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Sélection</label>
                            @if($analyse->valeurs_predefinies)
                                <select wire:model="valeur"
                                        class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                    <option value="">Choisir...</option>
                                    @foreach($analyse->valeurs_predefinies as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" wire:model="valeur"
                                       class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:border-purple-500"
                                       placeholder="Entrez une valeur">
                            @endif
                            @error('valeur') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Interprétation</label>
                            <select wire:model="interpretation"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                <option value="NORMAL">NORMAL</option>
                                <option value="PATHOLOGIQUE">PATHOLOGIQUE</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('SELECT_MULTIPLE')
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Sélection multiple</label>
                            @if($analyse->valeurs_predefinies)
                                <div class="bg-gray-800 border border-gray-600 rounded-lg p-4 max-h-32 overflow-y-auto">
                                    @foreach($analyse->valeurs_predefinies as $option)
                                        <label class="flex items-center mb-2 text-white">
                                            <input type="checkbox" wire:model="resultats" value="{{ $option }}"
                                                   class="rounded border-gray-600 text-purple-600 focus:ring-purple-500 mr-2">
                                            <span class="text-sm">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            @error('resultats') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Interprétation</label>
                            <select wire:model="interpretation"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                <option value="NORMAL">NORMAL</option>
                                <option value="PATHOLOGIQUE">PATHOLOGIQUE</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('NEGATIF_POSITIF_1')
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Résultat</label>
                            <div class="flex space-x-6">
                                <label class="flex items-center text-white">
                                    <input type="radio" wire:model="valeur" value="NEGATIF" class="mr-2">
                                    <span class="text-green-400">Négatif</span>
                                </label>
                                <label class="flex items-center text-white">
                                    <input type="radio" wire:model="valeur" value="POSITIF" class="mr-2">
                                    <span class="text-red-400">Positif</span>
                                </label>
                            </div>
                            @error('valeur') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Interprétation</label>
                            <select wire:model="interpretation"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                <option value="NORMAL">NORMAL</option>
                                <option value="PATHOLOGIQUE">PATHOLOGIQUE</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('NEGATIF_POSITIF_2')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Résultat</label>
                            <div class="flex space-x-6 mb-4">
                                <label class="flex items-center text-white">
                                    <input type="radio" wire:model="resultats" value="NEGATIF" class="mr-2">
                                    <span class="text-green-400">Négatif</span>
                                </label>
                                <label class="flex items-center text-white">
                                    <input type="radio" wire:model="resultats" value="POSITIF" class="mr-2">
                                    <span class="text-red-400">Positif</span>
                                </label>
                            </div>
                            @if($resultats === 'POSITIF')
                                <input type="text" wire:model="valeur"
                                       class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:border-purple-500"
                                       placeholder="Préciser la valeur...">
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Interprétation</label>
                            <select wire:model="interpretation"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                <option value="NORMAL">NORMAL</option>
                                <option value="PATHOLOGIQUE">PATHOLOGIQUE</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('ABSENCE_PRESENCE_2')
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Résultat</label>
                            <div class="flex space-x-6">
                                <label class="flex items-center text-white">
                                    <input type="radio" wire:model="valeur" value="ABSENCE" class="mr-2">
                                    <span class="text-green-400">Absence</span>
                                </label>
                                <label class="flex items-center text-white">
                                    <input type="radio" wire:model="valeur" value="PRESENCE" class="mr-2">
                                    <span class="text-red-400">Présence</span>
                                </label>
                            </div>
                            @error('valeur') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Interprétation</label>
                            <select wire:model="interpretation"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                <option value="NORMAL">NORMAL</option>
                                <option value="PATHOLOGIQUE">PATHOLOGIQUE</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('FV')
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Flore vaginale</label>
                            <select wire:model="valeur"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                <option value="">Choisir...</option>
                                <option value="FLORE_NORMALE">Flore normale</option>
                                <option value="FLORE_INTERMEDIAIRE">Flore intermédiaire</option>
                                <option value="VAGINOSE_BACTERIENNE">Vaginose bactérienne</option>
                            </select>
                            @error('valeur') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Interprétation</label>
                            <select wire:model="interpretation"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                <option value="NORMAL">NORMAL</option>
                                <option value="PATHOLOGIQUE">PATHOLOGIQUE</option>
                            </select>
                        </div>
                    </div>
                    @break

                @case('GERME')
                @case('CULTURE')
                    <div class="p-4 bg-yellow-900 border border-yellow-600 rounded-lg">
                        <h4 class="font-medium text-yellow-100 mb-4">🧫 Analyse bactériologique</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Famille de bactérie</label>
                                <select wire:model.live="famille_id"
                                        class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                    <option value="">Choisir une famille...</option>
                                    @foreach($familles as $famille)
                                        <option value="{{ $famille->id }}">{{ $famille->designation }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if($famille_id && count($bacteries) > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Bactérie isolée</label>
                                    <select wire:model="bacterie_id"
                                            class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-purple-500">
                                        <option value="">Choisir une bactérie...</option>
                                        @foreach($bacteries as $bacterie)
                                            <option value="{{ $bacterie->id }}">{{ $bacterie->designation }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        @if($bacterie_id)
                            <div class="mt-4">
                                <livewire:technicien.antibiogramme-table 
                                    :prescription-id="$prescriptionId" 
                                    :analyse-id="$analyseId"
                                    :bacterie-id="$bacterie_id"
                                    key="antibiogramme-{{ $bacterie_id }}" />
                            </div>
                        @endif
                    </div>
                    @break

                @default
                    <div class="p-4 bg-orange-900 border border-orange-600 rounded-lg">
                        <p class="text-orange-200 mb-2">
                            <strong>Type non géré :</strong> {{ $analyse->type->name ?? 'Inconnu' }}
                        </p>
                        <textarea wire:model="resultats" rows="3"
                                  class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:border-purple-500"
                                  placeholder="Saisie libre..."></textarea>
                    </div>
            @endswitch
        </div>

        {{-- Valeurs de référence --}}
        @if($analyse->valeur_ref || $analyse->unite)
            <div class="mb-6 p-4 bg-blue-900 border border-blue-600 rounded-lg">
                <div class="flex items-center text-blue-200">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Valeurs de référence: {{ $analyse->valeur_ref }}</span>
                    @if($analyse->unite)
                        <span class="ml-2 px-2 py-1 bg-blue-800 rounded text-sm font-bold">{{ $analyse->unite }}</span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Résultats complémentaires --}}
        @if(!in_array(($analyse->type->name ?? ''), ['LABEL', 'MULTIPLE', 'MULTIPLE_SELECTIF']))
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-300 mb-2">Résultats détaillés</label>
                <textarea wire:model="resultats" rows="3"
                          class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:border-purple-500 resize-none"
                          placeholder="Observations complémentaires..."></textarea>
            </div>
        @endif

        {{-- Conclusion --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-300 mb-2">Conclusion</label>
            <textarea wire:model.live.debounce.2000ms="conclusion" rows="3"
                      class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:border-purple-500 resize-none"
                      placeholder="Conclusion et notes complémentaires..."></textarea>
        </div>

        {{-- Actions --}}
        @if(!in_array(($analyse->type->name ?? ''), ['LABEL', 'MULTIPLE', 'MULTIPLE_SELECTIF']))
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 font-medium transition-all shadow-lg">
                    <span wire:loading.remove wire:target="save">Enregistrer</span>
                    <span wire:loading wire:target="save">Enregistrement…</span>
                </button>

                @if($resultat)
                    <button type="button" wire:click="markIncomplete"
                            class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg hover:from-yellow-600 hover:to-yellow-700 font-medium transition-all">
                        Marquer incomplet
                    </button>
                @endif

                <button type="button" wire:click="resetForm"
                        class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:from-gray-600 hover:to-gray-700 font-medium transition-all">
                    Réinitialiser
                </button>
            </div>
        @endif

        {{-- Messages --}}
        @if (session()->has('message'))
            <div class="mt-6 p-4 bg-green-800 border border-green-600 text-green-200 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="mt-6 p-4 bg-yellow-800 border border-yellow-600 text-yellow-200 rounded-lg">
                {{ session('warning') }}
            </div>
        @endif
    </form>
</div>
