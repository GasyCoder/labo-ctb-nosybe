{{-- livewire.secretaire.prescription.partials.paiement - VERSION UNIFIÉE CRÉATION/ÉDITION --}}
@if($etape === 'paiement')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
        {{-- HEADER SECTION ADAPTATIF --}}
        <div class="bg-gradient-to-r {{ $isEditMode ? 'from-orange-50 to-amber-100' : 'from-red-50 to-pink-100' }} dark:from-slate-700 dark:to-slate-800 px-6 py-5 border-b border-gray-200 dark:border-slate-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 {{ $isEditMode ? 'bg-orange-600' : 'bg-red-600' }} dark:bg-red-500 rounded-xl flex items-center justify-center shadow-lg">
                        <em class="ni ni-coin text-white text-xl"></em>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                            {{ $isEditMode ? 'Modification Paiement & Facturation' : 'Paiement & Facturation' }}
                        </h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $isEditMode ? 'Modifier la facturation de la prescription' : 'Finalisation de la prescription' }}
                        </p>
                    </div>
                </div>
                
                <div class="text-right">
                    <div class="text-2xl font-bold {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ number_format($total, 0) }} Ar
                    </div>
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $isEditMode ? 'Nouveau total' : 'Total à payer' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            {{-- ALERTE MODE ÉDITION --}}
            @if($isEditMode)
                <div class="mb-6 p-4 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 border border-orange-200 dark:border-orange-800 rounded-xl">
                    <div class="flex items-center">
                        <em class="ni ni-edit text-orange-600 mr-3"></em>
                        <div>
                            <h4 class="font-semibold text-orange-800 dark:text-orange-200">Modification du paiement</h4>
                            <p class="text-sm text-orange-600 dark:text-orange-300 mt-1">
                                Vous modifiez la facturation d'une prescription existante. Le nouveau montant remplacera l'ancien.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                {{-- RÉCAPITULATIF COMMANDE --}}
                <div class="xl:col-span-2 space-y-6">
                    {{-- ANALYSES SÉLECTIONNÉES --}}
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-5">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 {{ $isEditMode ? 'text-orange-600' : 'text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v6l-4.5 8A2 2 0 006.5 21h11a2 2 0 001.5-4l-4.5-8V3m-6 0h6" />
                            </svg>
                            {{ $isEditMode ? 'Analyses modifiées' : 'Analyses sélectionnées' }}
                            <span class="ml-2 px-2 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' }} rounded-full text-xs font-medium">
                                {{ count($analysesPanier) }}
                            </span>
                        </h3>
                        
                        <div class="space-y-3">
                            @foreach($analysesPanier as $analyse)
                                <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-600">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3 flex-1">
                                            <div class="w-10 h-10 bg-gradient-to-br from-{{ $isEditMode ? 'orange' : 'blue' }}-500 to-{{ $isEditMode ? 'orange' : 'blue' }}-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                                                <em class="ni ni-test-tube text-sm"></em>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-1">
                                                    @if($analyse['code'] ?? '')
                                                        <span class="px-2 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200' }} rounded-full font-mono text-xs font-bold">
                                                            {{ $analyse['code'] }}
                                                        </span>
                                                    @endif
                                                    @if(isset($analyse['is_parent']) && $analyse['is_parent'])
                                                        <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 rounded-full text-xs font-medium">
                                                            Panel complet
                                                        </span>
                                                    @endif
                                                    @if($isEditMode)
                                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-full text-xs font-medium">
                                                            Modifié
                                                        </span>
                                                    @endif
                                                </div>
                                                <h4 class="font-semibold text-slate-800 dark:text-slate-100 text-sm">
                                                    {{ $analyse['designation'] ?? 'N/A' }}
                                                </h4>
                                                @if($analyse['parent_nom'] ?? '')
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                        {{ $analyse['parent_nom'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="text-right">
                                            <div class="font-bold text-slate-800 dark:text-slate-100">
                                                {{ number_format($analyse['prix_affiche'] ?? $analyse['prix_effectif'] ?? 0, 0) }} Ar
                                            </div>
                                            @if(($analyse['prix_original'] ?? 0) > ($analyse['prix_effectif'] ?? 0))
                                                <div class="text-xs text-slate-500 dark:text-slate-400 line-through">
                                                    {{ number_format($analyse['prix_original'], 0) }} Ar
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- ENFANTS INCLUS POUR LES PANELS --}}
                                    @if(isset($analyse['enfants_inclus']) && !empty($analyse['enfants_inclus']))
                                        <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-600">
                                            <p class="text-xs text-slate-600 dark:text-slate-400 mb-2">Analyses incluses :</p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($analyse['enfants_inclus'] as $enfant)
                                                    <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded text-xs">
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
                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-5">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 flex items-center">
                                <em class="ni ni-package mr-2 {{ $isEditMode ? 'text-orange-500' : 'text-yellow-500' }}"></em>
                                {{ $isEditMode ? 'Prélèvements modifiés' : 'Prélèvements requis' }}
                                <span class="ml-2 px-2 py-1 {{ $isEditMode ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }} rounded-full text-xs font-medium">
                                    {{ count($prelevementsSelectionnes) }}
                                </span>
                            </h3>
                            
                            <div class="space-y-3">
                                @foreach($prelevementsSelectionnes as $prelevement)
                                    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-600">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 flex-1">
                                                <div class="w-10 h-10 bg-gradient-to-br from-{{ $isEditMode ? 'orange' : 'yellow' }}-500 to-{{ $isEditMode ? 'amber' : 'orange' }}-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                                                    <em class="ni ni-capsule text-sm"></em>
                                                </div>
                                                <div>
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <h4 class="font-semibold text-slate-800 dark:text-slate-100 text-sm">
                                                            {{ $prelevement['nom'] ?? 'N/A' }}
                                                        </h4>
                                                        @if($isEditMode)
                                                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-full text-xs font-medium">
                                                                Modifié
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($prelevement['description'] ?? '')
                                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                            {{ $prelevement['description'] }}
                                                        </p>
                                                    @endif
                                                    <div class="flex items-center space-x-3 mt-2 text-xs text-slate-600 dark:text-slate-400">
                                                        <span class="flex items-center">
                                                            <em class="ni ni-hash mr-1"></em>
                                                            Quantité: {{ $prelevement['quantite'] ?? 1 }}
                                                        </span>
                                                        <span class="flex items-center">
                                                            <em class="ni ni-coin mr-1"></em>
                                                            {{ number_format($prelevement['prix'] ?? 0, 0) }} Ar / unité
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-right">
                                                <div class="font-bold text-slate-800 dark:text-slate-100">
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
                <div class="space-y-6">
                    {{-- RÉSUMÉ FINANCIER --}}
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-5">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 flex items-center">
                            <em class="ni ni-calculator mr-2 text-green-500"></em>
                            {{ $isEditMode ? 'Nouveau résumé financier' : 'Résumé financier' }}
                        </h3>
                        
                        <div class="space-y-3">
                            {{-- SOUS-TOTAL ANALYSES --}}
                            <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-600">
                                <span class="text-sm text-slate-600 dark:text-slate-400 flex items-center">
                                    <em class="ni ni-flask mr-2 {{ $isEditMode ? 'text-orange-500' : 'text-blue-500' }}"></em>
                                    Analyses ({{ count($analysesPanier) }})
                                </span>
                                <span class="font-semibold text-slate-800 dark:text-slate-100">
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
                                <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-600">
                                    <span class="text-sm text-slate-600 dark:text-slate-400 flex items-center">
                                        <em class="ni ni-package mr-2 {{ $isEditMode ? 'text-orange-500' : 'text-yellow-500' }}"></em>
                                        Prélèvements ({{ count($prelevementsSelectionnes) }})
                                    </span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-100">
                                        {{ number_format(collect($prelevementsSelectionnes)->sum(fn($p) => ($p['prix'] ?? 0) * ($p['quantite'] ?? 1)), 0) }} Ar
                                    </span>
                                </div>
                            @endif
                            
                            {{-- REMISE --}}
                            @if($remise > 0)
                                <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-600">
                                    <span class="text-sm text-red-600 dark:text-red-400 flex items-center">
                                        <em class="ni ni-tag mr-2"></em>
                                        {{ $isEditMode ? 'Nouvelle remise accordée' : 'Remise accordée' }}
                                    </span>
                                    <span class="font-semibold text-red-600 dark:text-red-400">
                                        -{{ number_format($remise, 0) }} Ar
                                    </span>
                                </div>
                            @endif
                            
                            {{-- TOTAL FINAL --}}
                            <div class="bg-{{ $isEditMode ? 'orange' : 'red' }}-50 dark:bg-{{ $isEditMode ? 'orange' : 'red' }}-900/20 rounded-lg p-4 border border-{{ $isEditMode ? 'orange' : 'red' }}-200 dark:border-{{ $isEditMode ? 'orange' : 'red' }}-800">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold {{ $isEditMode ? 'text-orange-800 dark:text-orange-200' : 'text-red-800 dark:text-red-200' }}">
                                        {{ $isEditMode ? 'Nouveau total à payer' : 'Total à payer' }}
                                    </span>
                                    <span class="text-2xl font-bold {{ $isEditMode ? 'text-orange-600 dark:text-orange-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ number_format($total, 0) }} Ar
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DÉTAILS PAIEMENT --}}
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-5">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 flex items-center">
                            <em class="ni ni-wallet mr-2 text-purple-500"></em>
                            {{ $isEditMode ? 'Nouveau mode de paiement' : 'Détails du paiement' }}
                        </h3>
                        
                        <div class="space-y-4">
                            {{-- MODE DE PAIEMENT --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                                    Mode de paiement
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <!-- Espèces -->
                                    <input type="radio" id="mode_especes" wire:model.live="modePaiement" value="ESPECES" class="hidden peer/especes">
                                    <label for="mode_especes"
                                        class="peer-checked/especes:bg-green-500 peer-checked/especes:text-white
                                            peer-checked/especes:ring-2 peer-checked/especes:ring-green-400
                                            bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600
                                            rounded-xl px-4 py-2 cursor-pointer flex items-center gap-2
                                            transition-all duration-200
                                            text-slate-700 dark:text-slate-300 hover:border-green-400">
                                        💵 Espèces
                                    </label>

                                    <!-- Carte bancaire -->
                                    <input type="radio" id="mode_carte" wire:model.live="modePaiement" value="CARTE" class="hidden peer/carte">
                                    <label for="mode_carte"
                                        class="peer-checked/carte:bg-blue-500 peer-checked/carte:text-white
                                            peer-checked/carte:ring-2 peer-checked/carte:ring-blue-400
                                            bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600
                                            rounded-xl px-4 py-2 cursor-pointer flex items-center gap-2
                                            transition-all duration-200
                                            text-slate-700 dark:text-slate-300 hover:border-blue-400">
                                        💳 Carte
                                    </label>

                                    <!-- Chèque -->
                                    <input type="radio" id="mode_cheque" wire:model.live="modePaiement" value="CHEQUE" class="hidden peer/cheque">
                                    <label for="mode_cheque"
                                        class="peer-checked/cheque:bg-purple-500 peer-checked/cheque:text-white
                                            peer-checked/cheque:ring-2 peer-checked/cheque:ring-purple-400
                                            bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600
                                            rounded-xl px-4 py-2 cursor-pointer flex items-center gap-2
                                            transition-all duration-200
                                            text-slate-700 dark:text-slate-300 hover:border-purple-400">
                                        📄 Chèque
                                    </label>

                                    <!-- Mobile Money -->
                                    <input type="radio" id="mode_mobilemoney" wire:model.live="modePaiement" value="MOBILE_MONEY" class="hidden peer/mobilemoney">
                                    <label for="mode_mobilemoney"
                                        class="peer-checked/mobilemoney:bg-yellow-400 peer-checked/mobilemoney:text-white
                                            peer-checked/mobilemoney:ring-2 peer-checked/mobilemoney:ring-yellow-300
                                            bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600
                                            rounded-xl px-4 py-2 cursor-pointer flex items-center gap-2
                                            transition-all duration-200
                                            text-slate-700 dark:text-slate-300 hover:border-yellow-400">
                                        📱 MobileMoney
                                    </label>
                                </div>
                            </div>

                            {{-- REMISE --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <em class="ni ni-tag mr-2 text-orange-500"></em>
                                    {{ $isEditMode ? 'Nouvelle remise accordée (Ar)' : 'Remise accordée (Ar)' }}
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <em class="ni ni-arrow-down-round text-slate-400 dark:text-slate-500"></em>
                                    </div>
                                    <input type="number" 
                                           wire:model.live="remise" 
                                           min="0" 
                                           step="100" 
                                           placeholder="0"
                                           class="w-full pl-12 pr-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl 
                                                  bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                                  placeholder-slate-400 dark:placeholder-slate-500
                                                  focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'red' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'red' }}-500 
                                                  transition-all duration-200 shadow-sm
                                                  hover:border-{{ $isEditMode ? 'orange' : 'red' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'red' }}-600">
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Maximum: {{ number_format($total + $remise, 0) }} Ar
                                </p>
                            </div>
                            
                            {{-- MONTANT PAYÉ --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <em class="ni ni-wallet mr-2 text-green-500"></em>
                                    {{ $isEditMode ? 'Nouveau montant reçu (Ar)' : 'Montant reçu (Ar)' }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <em class="ni ni-coin text-slate-400 dark:text-slate-500"></em>
                                    </div>
                                    <input type="number" 
                                           wire:model.live="montantPaye" 
                                           min="0" 
                                           step="100" 
                                           placeholder="{{ $total }}"
                                           class="w-full pl-12 pr-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl 
                                                  bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                                                  placeholder-slate-400 dark:placeholder-slate-500
                                                  focus:ring-2 focus:ring-{{ $isEditMode ? 'orange' : 'red' }}-500 focus:border-{{ $isEditMode ? 'orange' : 'red' }}-500 
                                                  transition-all duration-200 shadow-sm
                                                  hover:border-{{ $isEditMode ? 'orange' : 'red' }}-300 dark:hover:border-{{ $isEditMode ? 'orange' : 'red' }}-600
                                                  {{ $montantPaye < $total ? 'border-red-300 dark:border-red-600' : 'border-green-300 dark:border-green-600' }}">
                                </div>
                            </div>
                            
                            {{-- MONNAIE À RENDRE --}}
                            @if($monnaieRendue > 0)
                                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 
                                            border border-green-200 dark:border-green-800 rounded-xl p-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                                            <em class="ni ni-money text-white"></em>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-green-800 dark:text-green-200">
                                                {{ $isEditMode ? 'Nouvelle monnaie à rendre' : 'Monnaie à rendre' }}
                                            </h4>
                                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                                {{ number_format($monnaieRendue, 0) }} Ar
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($montantPaye > 0 && $montantPaye < $total)
                                <div class="bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 
                                            border border-red-200 dark:border-red-800 rounded-xl p-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                                            <em class="ni ni-alert-circle text-white"></em>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-red-800 dark:text-red-200">
                                                Montant insuffisant
                                            </h4>
                                            <p class="text-sm text-red-600 dark:text-red-400">
                                                Il manque {{ number_format($total - $montantPaye, 0) }} Ar
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($montantPaye >= $total && $montantPaye > 0)
                                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 
                                            border border-green-200 dark:border-green-800 rounded-xl p-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                                            <em class="ni ni-check-circle text-white"></em>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-green-800 dark:text-green-200">
                                                {{ $isEditMode ? 'Modification validée' : 'Paiement validé' }}
                                            </h4>
                                            <p class="text-sm text-green-600 dark:text-green-400">
                                                {{ $isEditMode ? 'Le nouveau montant est suffisant' : 'Le montant est suffisant pour finaliser' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex flex-col gap-3">
                        <button wire:click="validerPaiement" 
                                {{ $montantPaye < $total ? 'disabled' : '' }}
                                class="w-full px-6 py-4 bg-{{ $isEditMode ? 'green' : 'blue' }}-600 text-white font-bold rounded-xl 
                                    disabled:opacity-50 disabled:cursor-not-allowed">
                            <em class="ni ni-{{ $isEditMode ? 'save' : 'check-circle' }} mr-2"></em>
                            @if($montantPaye < $total)
                                Montant insuffisant
                            @else
                                {{ $isEditMode ? 'Enregistrer les modifications' : 'Finaliser le paiement' }}
                            @endif
                            @if($montantPaye >= $total)
                                <em class="ni ni-arrow-right ml-2"></em>
                            @endif
                        </button>
                        
                        <button wire:click="allerEtape('prelevements')" 
                                class="w-full px-4 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl">
                            <em class="ni ni-arrow-left mr-2"></em>
                            Retour aux prélèvements
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif