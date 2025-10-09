{{-- livewire/secretaire/dashboard.blade.php --}}
{{-- Cards de statistiques refactorisées avec UI/UX améliorée --}}
<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 mb-6">
    
    {{-- Prescriptions Actives (En Attente + En Cours + Terminé) --}}
    <div class="group bg-gradient-to-br from-white to-blue-50/50 dark:from-slate-800 dark:to-slate-800/80 rounded-lg shadow-sm border border-blue-100 dark:border-slate-700 p-3 sm:p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer">
        <div class="flex flex-col items-center text-center space-y-2">
            <div class="p-2 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/50 dark:to-blue-800/50 rounded-lg group-hover:scale-110 transition-transform duration-300">
                <em class="ni ni-activity text-blue-600 dark:text-blue-400 text-lg"></em>
            </div>
            <div>
                <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format($countEnAttente + $countEnCours + $countTermine) }}
                </p>
                <p class="text-xs font-medium text-slate-600 dark:text-slate-400 leading-tight">
                    En Traitement
                </p>
            </div>
        </div>
        <div class="mt-2 pt-2 border-t border-blue-100 dark:border-slate-700">
            <div class="flex justify-between items-center text-xs text-slate-500 dark:text-slate-400">
                <span>En attente: {{ $countEnAttente }}</span>
                <span>En cours: {{ $countEnCours }}</span>
                <span>Terminé: {{ $countTermine }}</span>
            </div>
        </div>
    </div>

    {{-- Validées --}}
    <div class="group bg-gradient-to-br from-white to-green-50/50 dark:from-slate-800 dark:to-slate-800/80 rounded-lg shadow-sm border border-green-100 dark:border-slate-700 p-3 sm:p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer">
        <div class="flex flex-col items-center text-center space-y-2">
            <div class="p-2 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/50 dark:to-green-800/50 rounded-lg group-hover:scale-110 transition-transform duration-300">
                <em class="ni ni-check-circle text-green-600 dark:text-green-400 text-lg"></em>
            </div>
            <div>
                <p class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ number_format($countValide) }}
                </p>
                <p class="text-xs font-medium text-slate-600 dark:text-slate-400 leading-tight">
                    Validées
                </p>
            </div>
        </div>
        <div class="mt-2 pt-2 border-t border-green-100 dark:border-slate-700">
            <span class="text-xs text-slate-500 dark:text-slate-400 block text-center">
                Prêtes archivage
            </span>
        </div>
    </div>

    {{-- 🔥 MODIFIÉ : Analyses Payées (CLIQUABLE) --}}
    <div wire:click="filterByPaymentStatus('paye')" 
         class="group bg-gradient-to-br from-white to-emerald-50/50 dark:from-slate-800 dark:to-slate-800/80 rounded-lg shadow-sm border border-emerald-100 dark:border-slate-700 p-3 sm:p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer
         {{ $paymentFilter === 'paye' ? 'ring-2 ring-emerald-500 shadow-emerald-200' : '' }}">
        <div class="flex flex-col items-center text-center space-y-2">
            <div class="p-2 bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/50 dark:to-emerald-800/50 rounded-lg group-hover:scale-110 transition-transform duration-300">
                <em class="ni ni-check-circle text-emerald-600 dark:text-emerald-400 text-lg"></em>
            </div>
            <div>
                <p class="text-xl sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                    {{ number_format($countPaye ?? 0) }}
                </p>
                <p class="text-xs font-medium text-slate-600 dark:text-slate-400 leading-tight">
                    Payées
                </p>
            </div>
        </div>
        <div class="mt-2 pt-2 border-t border-emerald-100 dark:border-slate-700">
            <span class="text-xs text-slate-500 dark:text-slate-400 block text-center">
                {{ $paymentFilter === 'paye' ? '✓ Filtre actif' : 'Cliquer pour filtrer' }}
            </span>
        </div>
    </div>

    {{-- 🔥 MODIFIÉ : Analyses Non Payées (CLIQUABLE) --}}
    <div wire:click="filterByPaymentStatus('non_paye')" 
         class="group bg-gradient-to-br from-white to-red-50/50 dark:from-slate-800 dark:to-slate-800/80 rounded-lg shadow-sm border border-red-100 dark:border-slate-700 p-3 sm:p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer
         {{ $paymentFilter === 'non_paye' ? 'ring-2 ring-red-500 shadow-red-200' : '' }}">
        <div class="flex flex-col items-center text-center space-y-2">
            <div class="p-2 bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900/50 dark:to-red-800/50 rounded-lg group-hover:scale-110 transition-transform duration-300">
                <em class="ni ni-alert-circle text-red-600 dark:text-red-400 text-lg"></em>
            </div>
            <div>
                <p class="text-xl sm:text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ number_format($countNonPaye ?? 0) }}
                </p>
                <p class="text-xs font-medium text-slate-600 dark:text-slate-400 leading-tight">
                    Non Payées
                </p>
            </div>
        </div>
        <div class="mt-2 pt-2 border-t border-red-100 dark:border-slate-700">
            <span class="text-xs text-slate-500 dark:text-slate-400 block text-center">
                {{ $paymentFilter === 'non_paye' ? '✓ Filtre actif' : 'Cliquer pour filtrer' }}
            </span>
        </div>
    </div>

    {{-- Total Général --}}
    <div class="group bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600 p-3 sm:p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer">
        <div class="flex flex-col items-center text-center space-y-2">
            <div class="p-2 bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-600 rounded-lg group-hover:scale-110 transition-transform duration-300">
                <em class="ni ni-chart-bar text-slate-700 dark:text-slate-300 text-lg"></em>
            </div>
            <div>
                <p class="text-xl sm:text-2xl font-bold text-slate-700 dark:text-slate-200">
                    {{ number_format($countEnAttente + $countEnCours + $countTermine + $countValide + ($countArchive ?? 0) + $countDeleted) }}
                </p>
                <p class="text-xs font-medium text-slate-600 dark:text-slate-400 leading-tight">
                    Total
                </p>
            </div>
        </div>
        <div class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-600">
            <span class="text-xs text-slate-500 dark:text-slate-400 block text-center">
                Toutes prescriptions
            </span>
        </div>
    </div>
</div>

{{-- Indicateurs de performance rapides --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    {{-- Taux de paiement --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Taux de paiement</span>
            </div>
            @php
                $totalPaiements = ($countPaye ?? 0) + ($countNonPaye ?? 0);
                $tauxPaiement = $totalPaiements > 0 ? round(($countPaye ?? 0) / $totalPaiements * 100, 1) : 0;
            @endphp
            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $tauxPaiement }}%</span>
        </div>
        <div class="mt-2 w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2 rounded-full transition-all duration-500" 
                 style="width: {{ $tauxPaiement }}%"></div>
        </div>
    </div>

    {{-- Progression des analyses --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Progression</span>
            </div>
            @php
                $totalActives = $countEnAttente + $countEnCours + $countTermine;
                $tauxProgression = $totalActives > 0 ? round(($countTermine / $totalActives) * 100, 1) : 0;
            @endphp
            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $tauxProgression }}%</span>
        </div>
        <div class="mt-2 w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" 
                 style="width: {{ $tauxProgression }}%"></div>
        </div>
    </div>

    {{-- Efficacité opérationnelle --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Efficacité</span>
            </div>
            @php
                $totalGlobal = $countEnAttente + $countEnCours + $countTermine + $countValide;
                $tauxEfficacite = $totalGlobal > 0 ? round((($countTermine + $countValide) / $totalGlobal) * 100, 1) : 0;
            @endphp
            <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $tauxEfficacite }}%</span>
        </div>
        <div class="mt-1">
            <span class="text-xs text-slate-500 dark:text-slate-400">Analyses complétées</span>
        </div>
    </div>
</div>