{{-- livewire/secretaire/prescription/prescription-index.blade.php --}}
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

    @include('livewire.secretaire.dashboard')

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
                          transition-all duration-200">
            @if($search)
                <button wire:click="clearSearch"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <em class="ni ni-cross text-base"></em>
                </button>
            @endif
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 mb-8">
        <div class="border-b border-gray-200 dark:border-slate-700">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button wire:click.prevent="switchTab('actives')"
                        class="relative py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                               {{ $tab === 'actives' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300' }}">
                    <div class="flex items-center gap-2">
                        <em class="ni ni-list-ul"></em>
                        <span>Actives</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                   {{ $tab === 'actives' ? 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200' }}">
                            {{ $activePrescriptions->total() }}
                        </span>
                    </div>
                </button>

                <button wire:click.prevent="switchTab('valide')"
                        class="relative py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                               {{ $tab === 'valide' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300' }}">
                    <div class="flex items-center gap-2">
                        <em class="ni ni-check-circle"></em>
                        <span>Validées</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                   {{ $tab === 'valide' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200' }}">
                            {{ $validePrescriptions->total() }}
                        </span>
                    </div>
                </button>

                <button wire:click.prevent="switchTab('deleted')"
                        class="relative py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                               {{ $tab === 'deleted' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300' }}">
                    <div class="flex items-center gap-2">
                        <em class="ni ni-trash"></em>
                        <span>Corbeille</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                   {{ $tab === 'deleted' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200' }}">
                            {{ $deletedPrescriptions->total() }}
                        </span>
                    </div>
                </button>
            </nav>
        </div>
    </div>

    {{-- Tab Content --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 overflow-hidden">
        @if($tab === 'actives')
            @include('livewire.secretaire.prescription.prescription-table', [
                'prescriptions' => $activePrescriptions,
                'currentTab' => 'actives'
            ])
        @elseif($tab === 'valide')
            @include('livewire.secretaire.prescription.prescription-table', [
                'prescriptions' => $validePrescriptions,
                'currentTab' => 'valide'
            ])
        @elseif($tab === 'deleted')
            @include('livewire.secretaire.prescription.prescription-table', [
                'prescriptions' => $deletedPrescriptions,
                'currentTab' => 'deleted'
            ])
        @endif
    </div>

    {{-- Modal de confirmation de suppression --}}
    @include('livewire.secretaire.prescription.modals.action-modal-prescription')
</div>  