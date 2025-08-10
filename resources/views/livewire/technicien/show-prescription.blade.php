{{-- show-prescription --}}
<div class="mt-8 min-h-screen bg-gray-50">
    {{-- Header optimisé --}}
    @include('livewire.technicien.partials.header-prescription-technicien')

    {{-- Main Content --}}
    <div class="flex min-h-screen">
        {{-- Sidebar améliorée --}}
        <div class="w-80 bg-white border-r border-gray-200">
            <div class="overflow-y-auto h-full">
                <livewire:technicien.analyses-sidebar 
                    :prescription-id="$prescription->id" 
                    :selected-parent-id="$selectedParentId"
                    wire:key="sidebar-{{ $prescription->id }}" />
            </div>
        </div>

        {{-- Main Panel --}}
        <div class="flex-1 bg-gray-50">
            <div class="p-6">
                {{-- MODE PARENT - Formulaire de saisie --}}
                @if($selectedParentId)
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <livewire:technicien.recursive-result-form 
                                :prescription-id="$prescription->id"
                                :parent-id="$selectedParentId"
                                :key="'recursive-form-'.$selectedParentId" />
                        </div>
                    </div>

                {{-- EMPTY STATE - Invite à sélectionner --}}
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center max-w-md">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                Sélectionnez une analyse
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                Utilisez la barre latérale pour commencer la saisie des résultats d'analyses
                            </p>
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span class="text-sm font-medium">Cliquez pour démarrer</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Notifications Toast --}}
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg z-50"
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif
</div>