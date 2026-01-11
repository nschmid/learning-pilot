@props(['variant' => 'primary', 'size' => 'md'])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-primary-600 text-white shadow-sm hover:bg-primary-500 focus:ring-primary-500 active:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-400',
    'secondary' => 'bg-secondary-100 text-secondary-700 hover:bg-secondary-200 focus:ring-secondary-500 dark:bg-secondary-700 dark:text-secondary-100 dark:hover:bg-secondary-600',
    'outline' => 'border-2 border-primary-600 text-primary-600 hover:bg-primary-50 focus:ring-primary-500 dark:border-primary-400 dark:text-primary-400 dark:hover:bg-primary-900/20',
    'ghost' => 'text-secondary-600 hover:bg-secondary-100 hover:text-secondary-900 focus:ring-secondary-500 dark:text-secondary-300 dark:hover:bg-secondary-800 dark:hover:text-secondary-100',
    'danger' => 'bg-danger-600 text-white shadow-sm hover:bg-danger-500 focus:ring-danger-500 active:bg-danger-700 dark:bg-danger-500 dark:hover:bg-danger-400',
    'success' => 'bg-success-600 text-white shadow-sm hover:bg-success-500 focus:ring-success-500 active:bg-success-700 dark:bg-success-500 dark:hover:bg-success-400',
];

$sizes = [
    'xs' => 'px-2.5 py-1.5 text-xs',
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2.5 text-sm',
    'lg' => 'px-5 py-3 text-base',
    'xl' => 'px-6 py-3.5 text-base',
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>
    {{ $slot }}
</button>
