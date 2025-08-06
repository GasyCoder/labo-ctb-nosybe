{{-- components/prescription-status.blade.php --}}
@props(['status'])

@php
$statusConfig = [
    'EN_ATTENTE' => [
        'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200', 
        'icon' => 'clock', 
        'text' => 'En attente'
    ],
    'EN_COURS' => [
        'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200', 
        'icon' => 'refresh', 
        'text' => 'En cours'
    ],
    'TERMINE' => [
        'class' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200', 
        'icon' => 'check', 
        'text' => 'Terminé'
    ],
    'VALIDE' => [
        'class' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200', 
        'icon' => 'check-circle', 
        'text' => 'Validé'
    ],
    'A_REFAIRE' => [
        'class' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200', 
        'icon' => 'refresh', 
        'text' => 'À refaire'
    ],
    'ARCHIVE' => [
        'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200', 
        'icon' => 'archive', 
        'text' => 'Archivé'
    ],
][$status] ?? [
    'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200', 
    'icon' => 'help', 
    'text' => 'Inconnu'
];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusConfig['class'] }}">
    <em class="ni ni-{{ $statusConfig['icon'] }} mr-1"></em>
    {{ $statusConfig['text'] }}
</span>