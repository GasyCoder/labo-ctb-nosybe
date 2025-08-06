<div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 gap-4">
        <div class="flex items-center gap-3">
            <em class="ni ni-list-round text-primary-600 text-xl"></em>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 tracking-tight">
                Liste des prescriptions
            </h1>
        </div>
        <a href="{{ route('secretaire.prescription.create') }}"
           wire:navigate
           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 text-sm font-medium">
            <em class="ni ni-plus mr-2 text-base"></em> Nouvelle prescription
        </a>
    </div>

    {{-- Search Bar --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 mb-8">
        <div class="relative">
            <em class="ni ni-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></em>
            <input type="text"
                   wire:model.live.debounce.500ms="search"
                   placeholder="Rechercher par patient, référence ou prescripteur..."
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-lg 
                          bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                          focus:ring-2 focus:ring-primary-500 focus:border-primary-500 placeholder-slate-400
                          transition-all duration-200"
                   aria-label="Rechercher des prescriptions">
            @if($search)
                <button wire:click="clearSearch"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                        aria-label="Effacer la recherche">
                    <em class="ni ni-cross text-base"></em>
                </button>
            @endif
        </div>
    </div>



    {{-- Tab Content --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 overflow-hidden">
        @if($tab === 'actives')
            @include('livewire.secretaire.prescription.prescription-table', ['prescriptions' => $activePrescriptions, 'showActions' => true])
        @elseif($tab === 'valide')
            @include('livewire.secretaire.prescription.prescription-table', ['prescriptions' => $analyseValides, 'showActions' => false])
        @elseif($tab === 'deleted')
            @include('livewire.secretaire.prescription.prescription-table', ['prescriptions' => $deletedPrescriptions, 'showRestore' => true])
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Confirmation de suppression avec SweetAlert ou confirmation native
    function confirmDelete(prescriptionId) {
        if (confirm('Voulez-vous vraiment supprimer cette prescription ?')) {
            @this.call('deletePrescription', prescriptionId);
        }
    }
</script>
@endpush