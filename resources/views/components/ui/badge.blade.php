@props([
    'type' => 'default',
    'size' => 'md',
    'rounded' => 'full',
    'dot' => false,
])

@php
    $types = [
        'default' => 'bg-secondary-100 text-secondary-700 dark:bg-secondary-700 dark:text-secondary-200',
        'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300',
        'success' => 'bg-success-100 text-success-700 dark:bg-success-900/50 dark:text-success-300',
        'warning' => 'bg-warning-100 text-warning-700 dark:bg-warning-900/50 dark:text-warning-300',
        'error' => 'bg-danger-100 text-danger-700 dark:bg-danger-900/50 dark:text-danger-300',
        'danger' => 'bg-danger-100 text-danger-700 dark:bg-danger-900/50 dark:text-danger-300',
        'info' => 'bg-info-100 text-info-700 dark:bg-info-900/50 dark:text-info-300',
        'accent' => 'bg-accent-100 text-accent-700 dark:bg-accent-900/50 dark:text-accent-300',
    ];

    $dotColors = [
        'default' => 'bg-secondary-400 dark:bg-secondary-500',
        'primary' => 'bg-primary-500',
        'success' => 'bg-success-500',
        'warning' => 'bg-warning-500',
        'error' => 'bg-danger-500',
        'danger' => 'bg-danger-500',
        'info' => 'bg-info-500',
        'accent' => 'bg-accent-500',
    ];

    $sizes = [
        'xs' => 'px-1.5 py-0.5 text-xs',
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-0.5 text-sm',
        'lg' => 'px-3 py-1 text-sm',
    ];

    $roundedOptions = [
        'none' => 'rounded-none',
        'sm' => 'rounded',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-1.5 font-medium ' .
        ($types[$type] ?? $types['default']) . ' ' .
        ($sizes[$size] ?? $sizes['md']) . ' ' .
        ($roundedOptions[$rounded] ?? $roundedOptions['full'])
]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $dotColors[$type] ?? $dotColors['default'] }}"></span>
    @endif
    {{ $slot }}
</span>
