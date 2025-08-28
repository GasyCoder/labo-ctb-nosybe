<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }} - {{ $user->type_name }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($user->type === 'admin')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg sm:text-xl font-semibold mb-2 sm:mb-4">Dashboard Administrateur</h3>
                        <p class="text-sm sm:text-base">Bienvenue {{ $user->name }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>