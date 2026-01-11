@props([
    'type' => 'info',
    'dismissible' => false,
    'icon' => true,
])

@php
    $styles = [
        'info' => 'bg-info-50 text-info-800 border-info-200 dark:bg-info-900/20 dark:text-info-200 dark:border-info-800',
        'success' => 'bg-success-50 text-success-800 border-success-200 dark:bg-success-900/20 dark:text-success-200 dark:border-success-800',
        'warning' => 'bg-warning-50 text-warning-800 border-warning-200 dark:bg-warning-900/20 dark:text-warning-200 dark:border-warning-800',
        'error' => 'bg-danger-50 text-danger-800 border-danger-200 dark:bg-danger-900/20 dark:text-danger-200 dark:border-danger-800',
        'danger' => 'bg-danger-50 text-danger-800 border-danger-200 dark:bg-danger-900/20 dark:text-danger-200 dark:border-danger-800',
    ];

    $iconColors = [
        'info' => 'text-info-500 dark:text-info-400',
        'success' => 'text-success-500 dark:text-success-400',
        'warning' => 'text-warning-500 dark:text-warning-400',
        'error' => 'text-danger-500 dark:text-danger-400',
        'danger' => 'text-danger-500 dark:text-danger-400',
    ];

    $icons = [
        'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
        'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'danger' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    {{ $attributes->merge(['class' => 'rounded-xl border p-4 ' . ($styles[$type] ?? $styles['info'])]) }}
    role="alert"
>
    <div class="flex">
        @if ($icon)
            <div class="flex-shrink-0 {{ $iconColors[$type] ?? $iconColors['info'] }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $icons[$type] ?? $icons['info'] !!}
                </svg>
            </div>
        @endif
        <div class="@if($icon) ml-3 @endif flex-1">
            @if (isset($title))
                <h3 class="text-sm font-semibold">{{ $title }}</h3>
                <div class="mt-1 text-sm opacity-90">{{ $slot }}</div>
            @else
                <p class="text-sm">{{ $slot }}</p>
            @endif
        </div>
        @if ($dismissible)
            <div class="ml-auto pl-3">
                <button
                    @click="show = false"
                    type="button"
                    class="-mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-black/5 dark:hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition-colors"
                >
                    <span class="sr-only">{{ __('Schliessen') }}</span>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
    </div>
</div>
