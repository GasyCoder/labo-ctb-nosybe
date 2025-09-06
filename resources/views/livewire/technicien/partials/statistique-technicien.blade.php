{{-- livewire.technicien.partials.statistique-technicien --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- En attente --}}
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white shadow-lg hover:shadow-xl transition-shadow cursor-pointer"
         wire:click="$set('activeTab', 'en_attente')">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">En attente</p>
                <p class="text-3xl font-bold">{{ $stats['en_attente'] ?? 0 }}</p>
                <p class="text-orange-100 text-sm mt-1">Prescriptions à traiter</p>
            </div>
            <div class="p-3 bg-white/20 rounded-lg">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
        {{-- Indicateur de progression --}}
        <div class="mt-4 flex items-center">
            <div class="flex-1 bg-white/20 rounded-full h-2">
                <div class="bg-white h-2 rounded-full" style="width: {{ $stats['en_attente'] > 0 ? min(($stats['en_attente'] / ($stats['total'] ?: 1)) * 100, 100) : 0 }}%"></div>
            </div>
            <span class="ml-2 text-xs text-orange-100">
                {{ $stats['en_attente'] > 0 ? round(($stats['en_attente'] / ($stats['total'] ?: 1)) * 100) : 0 }}%
            </span>
        </div>
    </div>

    {{-- Terminé --}}
    <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-lg p-6 text-white shadow-lg hover:shadow-xl transition-shadow cursor-pointer"
         wire:click="$set('activeTab', 'termine')">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-teal-100 text-sm font-medium uppercase tracking-wide">Terminé</p>
                <p class="text-3xl font-bold">{{ $stats['termine'] ?? 0 }}</p>
                <p class="text-teal-100 text-sm mt-1">Prêtes à valider</p>
            </div>
            <div class="p-3 bg-white/20 rounded-lg">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
        {{-- Indicateur de progression --}}
        <div class="mt-4 flex items-center">
            <div class="flex-1 bg-white/20 rounded-full h-2">
                <div class="bg-white h-2 rounded-full" style="width: {{ $stats['termine'] > 0 ? min(($stats['termine'] / ($stats['total'] ?: 1)) * 100, 100) : 0 }}%"></div>
            </div>
            <span class="ml-2 text-xs text-teal-100">
                {{ $stats['termine'] > 0 ? round(($stats['termine'] / ($stats['total'] ?: 1)) * 100) : 0 }}%
            </span>
        </div>
    </div>

    {{-- À refaire --}}
    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white shadow-lg hover:shadow-xl transition-shadow cursor-pointer"
         wire:click="$set('activeTab', 'a_refaire')">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm font-medium uppercase tracking-wide">À refaire</p>
                <p class="text-3xl font-bold">{{ $stats['a_refaire'] ?? 0 }}</p>
                <p class="text-red-100 text-sm mt-1">Analyses urgentes</p>
            </div>
            <div class="p-3 bg-white/20 rounded-lg">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
        {{-- Indicateur de progression --}}
        <div class="mt-4 flex items-center">
            <div class="flex-1 bg-white/20 rounded-full h-2">
                <div class="bg-white h-2 rounded-full" style="width: {{ $stats['a_refaire'] > 0 ? min(($stats['a_refaire'] / ($stats['total'] ?: 1)) * 100, 100) : 0 }}%"></div>
            </div>
            <span class="ml-2 text-xs text-red-100">
                {{ $stats['a_refaire'] > 0 ? round(($stats['a_refaire'] / ($stats['total'] ?: 1)) * 100) : 0 }}%
            </span>
        </div>
    </div>
</div>