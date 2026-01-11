@props(['size' => 'md'])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
$variantClasses = 'bg-white border border-secondary-300 text-secondary-700 shadow-sm hover:bg-secondary-50 focus:ring-primary-500 dark:bg-secondary-800 dark:border-secondary-600 dark:text-secondary-200 dark:hover:bg-secondary-700';

$sizes = [
    'xs' => 'px-2.5 py-1.5 text-xs',
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2.5 text-sm',
    'lg' => 'px-5 py-3 text-base',
    'xl' => 'px-6 py-3.5 text-base',
];

$classes = $baseClasses . ' ' . $variantClasses . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
    {{ $slot }}
</button>
