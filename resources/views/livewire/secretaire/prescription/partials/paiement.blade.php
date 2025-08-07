{{-- livewire.secretaire.prescription.partials.paiement - VERSION UNIFIÉE CRÉATION/ÉDITION --}}
@if($etape === 'paiement')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        {{-- HEADER SECTION ADAPTATIF --}}
        <div class="bg-gradient-to-r {{ $isEditMode ? 'from-orange-50 to-amber-50' : 'from-red-50 to-pink-50' }} dark:from-slate-700 dark:to-slate-800 px-4 py-3 border-b border-gray-100 dark:border-slate-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 {{ $isEditMode ? 'bg-orange-500' : 'bg-red-500' }} rounded-lg flex items-center justify-center">
                        <em class="ni ni-coin text-white text-sm"></em>
                    </div>
                    <div class="ml-3">
                        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">
                            {{ $isEditMode ? 'Modification Paiement & Facturation' : 'Paiement & Facturation' }}
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $isEditMode ? 'Modifier la facturation de la prescription' : 'Finalisation de la prescription' }}
                        </p>
                    </div>
                </div>
                
                <div class="text-right">
                    <div class="text-lg font-bold {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ number_format($total, 0) }} Ar
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $isEditMode ? 'Nouveau total' : 'Total à payer' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4">
            {{-- ALERTE MODE ÉDITION --}}
            @if($isEditMode)
                <div class="mb-4 p-3 bg-orange-50/50 dark:bg-orange-900/10 border border-orange-200/50 dark:border-orange-800/50 rounded-lg">
                    <div class="flex items-center">
                        <em class="ni ni-edit text-orange-500 mr-2 text-sm"></em>
                        <div>
                            <h4 class="font-medium text-orange-800 dark:text-orange-200 text-sm">Modification du paiement</h4>
                            <p class="text-xs text-orange-600 dark:text-orange-300 mt-0.5">
                                Vous modifiez la facturation d'une prescription existante. Le nouveau montant remplacera l'ancien.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                {{-- RÉCAPITULATIF COMMANDE --}}
                <div class="xl:col-span-2 space-y-4">
                    {{-- ANALYSES SÉLECTIONNÉES --}}
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3">
                        <h3 class="text-sm font-medium text-slate-800 dark:text-slate-100 mb-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5 {{ $isEditMode ? 'text-orange-500' : 'text-blue-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v6l-4.5 8A2 2 0 006.5 21h11a2 2 0 001.5-4l-4.5-8V3m-6 0h6" />
                            </svg>
                            {{ $isEditMode ? 'Analyses modifiées' : 'Analyses sélectionnées' }}
                            <span class="ml-2 px-1.5 py-0.5 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-200' : 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' }} rounded-full text-xxs font-medium">
                                {{ count($analysesPanier) }}
                            </span>
                        </h3>
                        
                        <div class="space-y-2">
                            @foreach($analysesPanier as $analyse)
                                <div class="bg-white dark:bg-slate-800 rounded-lg p-2.5 border border-slate-200 dark:border-slate-600">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2 flex-1">
                                            <div class="w-6 h-6 bg-gradient-to-br from-{{ $isEditMode ? 'orange' : 'blue' }}-500 to-{{ $isEditMode ? 'orange' : 'blue' }}-600 rounded flex items-center justify-center text-white">
                                                <em class="ni ni-folder-list text-xs"></em>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-1.5 mb-0.5">
                                                    @if($analyse['code'] ?? '')
                                                        <span class="px-1.5 py-0.5 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-200' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200' }} rounded-full font-mono text-xxs font-bold">
                                                            {{ $analyse['code'] }}
                                                        </span>
                                                    @endif
                                                    @if(isset($analyse['is_parent']) && $analyse['is_parent'])
                                                        <span class="px-1.5 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-200 rounded-full text-xxs font-medium">
                                                            Panel
                                                        </span>
                                                    @endif
                                                    @if($isEditMode)
                                                        <span class="px-1.5 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-200 rounded-full text-xxs font-medium">
                                                            Modifié
                                                        </span>
                                                    @endif
                                                </div>
                                                <h4 class="font-medium text-slate-800 dark:text-slate-100 text-xs">
                                                    {{ $analyse['designation'] ?? 'N/A' }}
                                                </h4>
                                                @if($analyse['parent_nom'] ?? '')
                                                    <p class="text-xxs text-slate-500 dark:text-slate-400 mt-0.5">
                                                        {{ $analyse['parent_nom'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="text-right">
                                            <div class="font-semibold text-slate-800 dark:text-slate-100 text-xs">
                                                {{ number_format($analyse['prix_affiche'] ?? $analyse['prix_effectif'] ?? 0, 0) }} Ar
                                            </div>
                                            @if(($analyse['prix_original'] ?? 0) > ($analyse['prix_effectif'] ?? 0))
                                                <div class="text-xxs text-slate-500 dark:text-slate-400 line-through">
                                                    {{ number_format($analyse['prix_original'], 0) }} Ar
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- ENFANTS INCLUS POUR LES PANELS --}}
                                    @if(isset($analyse['enfants_inclus']) && !empty($analyse['enfants_inclus']))
                                        <div class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-600">
                                            <p class="text-xxs text-slate-600 dark:text-slate-400 mb-1">Analyses incluses :</p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($analyse['enfants_inclus'] as $enfant)
                                                    <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded text-xxs">
                                                        {{ $enfant }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- PRÉLÈVEMENTS SÉLECTIONNÉS --}}
                    @if(count($prelevementsSelectionnes) > 0)
                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3">
                            <h3 class="text-sm font-medium text-slate-800 dark:text-slate-100 mb-3 flex items-center">
                                <em class="ni ni-package mr-1.5 {{ $isEditMode ? 'text-orange-500' : 'text-yellow-500' }} text-xs"></em>
                                {{ $isEditMode ? 'Prélèvements modifiés' : 'Prélèvements requis' }}
                                <span class="ml-2 px-1.5 py-0.5 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200' }} rounded-full text-xxs font-medium">
                                    {{ count($prelevementsSelectionnes) }}
                                </span>
                            </h3>
                            
                            <div class="space-y-2">
                                @foreach($prelevementsSelectionnes as $prelevement)
                                    <div class="bg-white dark:bg-slate-800 rounded-lg p-2.5 border border-slate-200 dark:border-slate-600">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2 flex-1">
                                                <div class="w-6 h-6 bg-gradient-to-br from-{{ $isEditMode ? 'orange' : 'yellow' }}-500 to-{{ $isEditMode ? 'amber' : 'orange' }}-600 rounded flex items-center justify-center text-white">
                                                    <em class="ni ni-tag text-xs"></em>
                                                </div>
                                                <div>
                                                    <div class="flex items-center space-x-1.5 mb-0.5">
                                                        <h4 class="font-medium text-slate-800 dark:text-slate-100 text-xs">
                                                            {{ $prelevement['nom'] ?? 'N/A' }}
                                                        </h4>
                                                        @if($isEditMode)
                                                            <span class="px-1.5 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-200 rounded-full text-xxs font-medium">
                                                                Modifié
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($prelevement['description'] ?? '')
                                                        <p class="text-xxs text-slate-500 dark:text-slate-400 mt-0.5">
                                                            {{ $prelevement['description'] }}
                                                        </p>
                                                    @endif
                                                    <div class="flex items-center space-x-2 mt-1 text-xxs text-slate-600 dark:text-slate-400">
                                                        <span class="flex items-center">
                                                            <em class="ni ni-hash mr-0.5 text-xs"></em>
                                                            Qté: {{ $prelevement['quantite'] ?? 1 }}
                                                        </span>
                                                        <span class="flex items-center">
                                                            <em class="ni ni-coin mr-0.5 text-xs"></em>
                                                            {{ number_format($prelevement['prix'] ?? 0, 0) }} Ar / unité
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-right">
                                                <div class="font-semibold text-slate-800 dark:text-slate-100 text-xs">
                                                    {{ number_format(($prelevement['prix'] ?? 0) * ($prelevement['quantite'] ?? 1), 0) }} Ar
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                {{-- PANNEAU PAIEMENT --}}
                <div class="space-y-4">
                    {{-- RÉSUMÉ FINANCIER --}}
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3">
                        <h3 class="text-sm font-medium text-slate-800 dark:text-slate-100 mb-3 flex items-center">
                            <em class="ni ni-calculator mr-1.5 text-green-500 text-xs"></em>
                            {{ $isEditMode ? 'Nouveau résumé financier' : 'Résumé financier' }}
                        </h3>
                        
                        <div class="space-y-2">
                            {{-- SOUS-TOTAL ANALYSES --}}
                            <div class="flex justify-between items-center py-1.5 border-b border-slate-200 dark:border-slate-600">
                                <span class="text-xs text-slate-600 dark:text-slate-400 flex items-center">
                                    <em class="ni ni-flask mr-1.5 {{ $isEditMode ? 'text-orange-500' : 'text-blue-500' }} text-xs"></em>
                                    Analyses ({{ count($analysesPanier) }})
                                </span>
                                <span class="font-medium text-slate-800 dark:text-slate-100 text-xs">
                                    @php
                                        $sousAnalyses = 0;
                                        $parentsTraites = [];
                                        
                                        foreach($analysesPanier as $analyse) {
                                            if (isset($analyse['parent_id']) && $analyse['parent_id'] && !in_array($analyse['parent_id'], $parentsTraites)) {
                                                $parent = \App\Models\Analyse::find($analyse['parent_id']);
                                                if ($parent && $parent->prix > 0) {
                                                    $sousAnalyses += $parent->prix;
                                                    $parentsTraites[] = $analyse['parent_id'];
                                                    continue;
                                                }
                                            }
                                            
                                            if (!isset($analyse['parent_id']) || !$analyse['parent_id'] || !in_array($analyse['parent_id'], $parentsTraites)) {
                                                $sousAnalyses += $analyse['prix_effectif'] ?? $analyse['prix_original'] ?? 0;
                                            }
                                        }
                                    @endphp
                                    {{ number_format($sousAnalyses, 0) }} Ar
                                </span>
                            </div>
                            
                            {{-- SOUS-TOTAL PRÉLÈVEMENTS --}}
                            @if(count($prelevementsSelectionnes) > 0)
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-200 dark:border-slate-600">
                                    <span class="text-xs text-slate-600 dark:text-slate-400 flex items-center">
                                        <em class="ni ni-package mr-1.5 {{ $isEditMode ? 'text-orange-500' : 'text-yellow-500' }} text-xs"></em>
                                        Prélèvements ({{ count($prelevementsSelectionnes) }})
                                    </span>
                                    <span class="font-medium text-slate-800 dark:text-slate-100 text-xs">
                                        {{ number_format(collect($prelevementsSelectionnes)->sum(fn($p) => ($p['prix'] ?? 0) * ($p['quantite'] ?? 1)), 0) }} Ar
                                    </span>
                                </div>
                            @endif
                            
                            {{-- REMISE --}}
                            @if($remise > 0)
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-200 dark:border-slate-600">
                                    <span class="text-xs text-red-600 dark:text-red-400 flex items-center">
                                        <em class="ni ni-tag mr-1.5 text-xs"></em>
                                        {{ $isEditMode ? 'Nouvelle remise accordée' : 'Remise accordée' }}
                                    </span>
                                    <span class="font-medium text-red-600 dark:text-red-400 text-xs">
                                        -{{ number_format($remise, 0) }} Ar
                                    </span>
                                </div>
                            @endif
                            
                            {{-- TOTAL FINAL --}}
                            <div class="bg-{{ $isEditMode ? 'orange' : 'red' }}-50 dark:bg-{{ $isEditMode ? 'orange' : 'red' }}-900/20 rounded-lg p-3 border border-{{ $isEditMode ? 'orange' : 'red' }}-200 dark:border-{{ $isEditMode ? 'orange' : 'red' }}-800">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold {{ $isEditMode ? 'text-orange-800 dark:text-orange-200' : 'text-red-800 dark:text-red-200' }}">
                                        {{ $isEditMode ? 'Nouveau total à payer' : 'Total à payer' }}
                                    </span>
                                    <span class="text-base font-bold {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ number_format($total, 0) }} Ar
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DÉTAILS PAIEMENT --}}
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3">
                        <h3 class="text-sm font-medium text-slate-800 dark:text-slate-100 mb-3 flex items-center">
                            <em class="ni ni-wallet mr-1.5 text-purple-500 text-xs"></em>
                            {{ $isEditMode ? 'Nouveau mode de paiement' : 'Détails du paiement' }}
                        </h3>
                        
                        <div class="space-y-3">
                            {{-- MODE DE PAIEMENT --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Mode de paiement
                                </label>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <!-- Espèces -->
                                    <input type="radio" id="mode_especes" wire:model.live="modePaiement" value="ESPECES" class="hidden peer/especes">
                                    <label for="mode_especes"
                                        class="peer-checked/especes:bg-green-500 peer-checked/especes:text-white
                                            peer-checked/especes:ring-2 peer-checked/especes:ring-green-400
                                            bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600
                                            rounded-lg px-2 py-1.5 cursor-pointer flex items-center gap-1.5
                                            transition-colors text-xs
                                            text-slate-700 dark:text-slate-300 hover:border-green-400">
                                        💵 Espèces
                                    </label>

                                    <!-- Carte bancaire -->
                                    <input type="radio" id="mode_carte" wire:model.live="modePaiement" value="CARTE" class="hidden peer/carte">
                                    <label for="mode_carte"
                                        class="peer-checked/carte:bg-blue-500 peer-checked/carte:text-white
                                            peer-checked/carte:ring-2 peer-checked/carte:ring-blue-400
                                            bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600
                                            rounded-lg px-2 py-1.5 cursor-pointer flex items-center gap-1.5
                                            transition-colors text-xs
                                            text-slate-700 dark:text-slate-300 hover:border-blue-400">
                                        💳 Carte
                                    </label>

                                    <!-- Chèque -->
                                    <input type="radio" id="mode_cheque" wire:model.live="modePaiement" value="CHEQUE" class="hidden peer/cheque">
                                    <label for="mode_cheque"
                                        class="peer-checked/cheque:bg-purple-500 peer-checked/cheque:text-white
                                            peer-checked/cheque:ring-2 peer-checked/cheque:ring-purple-400
                                            bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600
                                            rounded-lg px-2 py-1.5 cursor-pointer flex items-center gap-1.5
                                            transition-colors text-xs
                                            text-slate-700 dark:text-slate-300 hover:border-purple-400">
                                        📄 Chèque
                                    </label>

                                    <!-- Mobile Money -->
                                    <input type="radio" id="mode_mobilemoney" wire:model.live="modePaiement" value="MOBILEMONEY" class="hidden peer/mobilemoney">
                                    <label for="mode_mobilemoney"
                                        class="peer-checked/mobilemoney:bg-yellow-400 peer-checked/mobilemoney:text-white
                                            peer-checked/mobilemoney:ring-2 peer-checked/mobilemoney:ring-yellow-300
                                            bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600
                                            rounded-lg px-2 py-1.5 cursor-pointer flex items-center gap-1.5
                                            transition-colors text-xs
                                            text-slate-700 dark:text-slate-300 hover:border-yellow-400">
                                        📱 MobileMoney
                                    </label>
                                </div>
                            </div>

                            {{-- REMISE --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                    <em class="ni ni-tag mr-1.5 text-orange-500 text-xs"></em>
                                    {{ $isEditMode ? 'Nouvelle remise accordée (Ar)' : 'Remise accordée (Ar)' }}
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <em class="ni ni-arrow-down-round text-slate-400 dark:text-slate-500 text-sm"></em>
                                    </div>
                                    <input type="number" 
                                           wire:model.live="remise" 
                                           min="0" 
                                           step="100" 
                                           placeholder="0"
                                           class="w-full pl-9 pr-3 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                                  bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                                  placeholder-slate-400 dark:placeholder-slate-500
                                                  focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'red' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'red' }}-500 
                                                  transition-colors
                                                  hover:border-{{ $isEditMode ? 'orange' : 'red' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'red' }}-600">
                                </div>
                                <p class="text-xxs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Maximum: {{ number_format($total + $remise, 0) }} Ar
                                </p>
                            </div>
                            
                            {{-- MONTANT PAYÉ --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                    <em class="ni ni-wallet mr-1.5 text-green-500 text-xs"></em>
                                    {{ $isEditMode ? 'Nouveau montant reçu (Ar)' : 'Montant reçu (Ar)' }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <em class="ni ni-coin text-slate-400 dark:text-slate-500 text-sm"></em>
                                    </div>
                                    <input type="number" 
                                           wire:model.live="montantPaye" 
                                           min="0" 
                                           step="100" 
                                           placeholder="{{ $total }}"
                                           class="w-full pl-9 pr-3 py-2.5 border border-gray-200 dark:border-slate-600 rounded-lg text-sm
                                                  bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                                  placeholder-slate-400 dark:placeholder-slate-500
                                                  focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'red' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'red' }}-500 
                                                  transition-colors
                                                  hover:border-{{ $isEditMode ? 'orange' : 'red' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'red' }}-600
                                                  {{ $montantPaye < $total ? 'border-red-300 dark:border-red-600' : 'border-green-300 dark:border-green-600' }}">
                                </div>
                            </div>
                            
                            {{-- MONNAIE À RENDRE --}}
                            @if($monnaieRendue > 0)
                                <div class="bg-green-50/50 dark:bg-green-900/10 border border-green-200/50 dark:border-green-800/50 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center mr-2">
                                            <em class="ni ni-money text-white text-xs"></em>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-green-800 dark:text-green-200 text-sm">
                                                {{ $isEditMode ? 'Nouvelle monnaie à rendre' : 'Monnaie à rendre' }}
                                            </h4>
                                            <p class="text-base font-bold text-green-600 dark:text-green-400">
                                                {{ number_format($monnaieRendue, 0) }} Ar
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($montantPaye > 0 && $montantPaye < $total)
                                <div class="bg-red-50/50 dark:bg-red-900/10 border border-red-200/50 dark:border-red-800/50 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-red-500 rounded flex items-center justify-center mr-2">
                                            <em class="ni ni-alert-circle text-white text-xs"></em>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-red-800 dark:text-red-200 text-sm">
                                                Montant insuffisant
                                            </h4>
                                            <p class="text-xs text-red-600 dark:text-red-400">
                                                Il manque {{ number_format($total - $montantPaye, 0) }} Ar
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($montantPaye >= $total && $montantPaye > 0)
                                <div class="bg-green-50/50 dark:bg-green-900/10 border border-green-200/50 dark:border-green-800/50 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center mr-2">
                                            <em class="ni ni-check-circle text-white text-xs"></em>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-green-800 dark:text-green-200 text-sm">
                                                {{ $isEditMode ? 'Modification validée' : 'Paiement validé' }}
                                            </h4>
                                            <p class="text-xs text-green-600 dark:text-green-400">
                                                {{ $isEditMode ? 'Le nouveau montant est suffisant' : 'Le montant est suffisant pour finaliser' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex flex-col">
                        <button wire:click="validerPaiement" 
                                {{ $montantPaye < $total ? 'disabled' : '' }}
                                class="w-full px-4 py-3 bg-{{ $isEditMode ? 'green' : 'blue' }}-500 hover:bg-{{ $isEditMode ? 'green' : 'blue' }}-600 text-white font-medium rounded-lg text-sm
                                    disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform {{ $montantPaye >= $total ? 'hover:scale-105' : '' }} shadow-md">
                            <em class="ni ni-{{ $isEditMode ? 'save' : 'check-circle' }} mr-1.5 text-xs"></em>
                            @if($montantPaye < $total)
                                Montant insuffisant
                            @else
                                {{ $isEditMode ? 'Enregistrer les modifications' : 'Finaliser le paiement' }}
                            @endif
                            @if($montantPaye >= $total)
                                <em class="ni ni-arrow-right ml-1.5 text-xs"></em>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- BOUTONS DE NAVIGATION --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-4 border-t border-gray-100 dark:border-slate-600 mt-6">
                <button wire:click="allerEtape('prelevements')" 
                        class="w-full sm:w-auto inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-slate-700 
                               text-gray-700 dark:text-slate-300 font-medium rounded-lg 
                               hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors text-sm
                               focus:ring-2 focus:ring-gray-500 focus:ring-offset-1 dark:focus:ring-offset-slate-800">
                    <em class="ni ni-arrow-left mr-1.5 text-xs"></em>
                    Retour Prélèvements
                </button>
                
                <div class="flex items-center text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex space-x-1">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                        <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full"></div>
                        <div class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></div>
                        <div class="w-1.5 h-1.5 {{ $isEditMode ? 'bg-orange-500' : 'bg-red-500' }} rounded-full"></div>
                        <div class="w-1.5 h-1.5 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
                    </div>
                    <span class="ml-2">Étape 5/7</span>
                </div>
                
                <div class="hidden sm:block w-auto"></div>
            </div>
        </div>
    </div>
@endif