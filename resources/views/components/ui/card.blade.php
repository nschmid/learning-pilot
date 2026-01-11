@props([
    'padding' => true,
    'hover' => false,
    'variant' => 'default',
])

@php
$variants = [
    'default' => 'bg-white border-secondary-200 dark:bg-secondary-800 dark:border-secondary-700',
    'primary' => 'bg-primary-50 border-primary-200 dark:bg-primary-900/20 dark:border-primary-800',
    'success' => 'bg-success-50 border-success-200 dark:bg-success-900/20 dark:border-success-800',
    'warning' => 'bg-warning-50 border-warning-200 dark:bg-warning-900/20 dark:border-warning-800',
    'danger' => 'bg-danger-50 border-danger-200 dark:bg-danger-900/20 dark:border-danger-800',
];

$headerBg = match($variant) {
    'primary' => 'bg-primary-100/50 dark:bg-primary-900/30',
    'success' => 'bg-success-100/50 dark:bg-success-900/30',
    'warning' => 'bg-warning-100/50 dark:bg-warning-900/30',
    'danger' => 'bg-danger-100/50 dark:bg-danger-900/30',
    default => 'bg-secondary-50 dark:bg-secondary-900/50',
};
@endphp

<div {{ $attributes->merge([
    'class' => 'rounded-xl shadow-soft border ' . ($variants[$variant] ?? $variants['default']) . ' ' .
        ($padding ? 'p-6' : '') . ' ' .
        ($hover ? 'hover:shadow-soft-lg hover:border-secondary-300 dark:hover:border-secondary-600 transition-all duration-200' : '')
]) }}>
    @if (isset($header))
        <div class="border-b border-secondary-200 dark:border-secondary-700 -mx-6 -mt-6 px-6 py-4 mb-6 {{ $headerBg }} rounded-t-xl">
            {{ $header }}
        </div>
    @endif

    {{ $slot }}

    @if (isset($footer))
        <div class="border-t border-secondary-200 dark:border-secondary-700 -mx-6 -mb-6 px-6 py-4 mt-6 {{ $headerBg }} rounded-b-xl">
            {{ $footer }}
        </div>
    @endif
</div>
