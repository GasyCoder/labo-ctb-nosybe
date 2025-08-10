{{-- livewire.technicien.partials.statistique-technicien --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- En attente --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-sm transition-shadow">
        <div class="flex items-center">
            <div class="p-2 bg-amber-100 rounded-lg">
                <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">En attente</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['en_attente'] }}</p>
            </div>
        </div>
        <p class="mt-3 text-sm text-gray-500">Prescriptions à traiter</p>
    </div>

    {{-- En cours --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-sm transition-shadow">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1V8z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">En cours</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['en_cours'] }}</p>
            </div>
        </div>
        <p class="mt-3 text-sm text-gray-500">Analyses en traitement</p>
    </div>

    {{-- Terminé --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-sm transition-shadow">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Terminé</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['termine'] }}</p>
            </div>
        </div>
        <p class="mt-3 text-sm text-gray-500">Prêtes à valider</p>
    </div>
</div>