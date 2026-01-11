@props([
    'label' => '',
    'value' => '',
    'change' => null,
    'changeType' => 'increase',
    'icon' => null,
    'variant' => 'primary',
])

@php
$iconVariants = [
    'primary' => 'bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400',
    'success' => 'bg-success-50 text-success-600 dark:bg-success-900/30 dark:text-success-400',
    'warning' => 'bg-warning-50 text-warning-600 dark:bg-warning-900/30 dark:text-warning-400',
    'danger' => 'bg-danger-50 text-danger-600 dark:bg-danger-900/30 dark:text-danger-400',
    'info' => 'bg-info-50 text-info-600 dark:bg-info-900/30 dark:text-info-400',
    'accent' => 'bg-accent-50 text-accent-600 dark:bg-accent-900/30 dark:text-accent-400',
];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-secondary-800 rounded-xl shadow-soft border border-secondary-200 dark:border-secondary-700 p-6']) }}>
    <div class="flex items-center">
        @if ($icon)
            <div class="flex-shrink-0 p-3 rounded-xl {{ $iconVariants[$variant] ?? $iconVariants['primary'] }}">
                {{ $icon }}
            </div>
        @endif
        <div class="@if($icon) ml-4 @endif flex-1">
            <p class="text-sm font-medium text-secondary-500 dark:text-secondary-400">{{ $label }}</p>
            <div class="flex items-baseline mt-1">
                <p class="text-2xl font-bold text-secondary-900 dark:text-white">{{ $value }}</p>
                @if ($change !== null)
                    <p class="ml-2 flex items-baseline text-sm font-semibold {{ $changeType === 'increase' ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                        @if ($changeType === 'increase')
                            <svg class="h-4 w-4 flex-shrink-0 self-center" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="h-4 w-4 flex-shrink-0 self-center" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                        {{ $change }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
