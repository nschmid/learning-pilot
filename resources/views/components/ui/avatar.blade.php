@props([
    'src' => null,
    'alt' => '',
    'size' => 'md',
    'name' => null,
    'ring' => false,
])

@php
    $sizes = [
        'xs' => 'h-6 w-6 text-xs',
        'sm' => 'h-8 w-8 text-sm',
        'md' => 'h-10 w-10 text-base',
        'lg' => 'h-12 w-12 text-lg',
        'xl' => 'h-16 w-16 text-xl',
        '2xl' => 'h-20 w-20 text-2xl',
    ];

    $initials = '';
    if ($name) {
        $words = explode(' ', $name);
        $initials = strtoupper(substr($words[0], 0, 1));
        if (count($words) > 1) {
            $initials .= strtoupper(substr(end($words), 0, 1));
        }
    }

    // Use semantic colors from the design system
    $colors = [
        'A' => 'bg-danger-500', 'B' => 'bg-warning-500', 'C' => 'bg-warning-400',
        'D' => 'bg-warning-300', 'E' => 'bg-success-400', 'F' => 'bg-success-500',
        'G' => 'bg-success-600', 'H' => 'bg-accent-500', 'I' => 'bg-accent-400',
        'J' => 'bg-info-400', 'K' => 'bg-info-500', 'L' => 'bg-primary-500',
        'M' => 'bg-primary-600', 'N' => 'bg-primary-700', 'O' => 'bg-accent-600',
        'P' => 'bg-danger-400', 'Q' => 'bg-danger-500', 'R' => 'bg-danger-600',
        'S' => 'bg-warning-600', 'T' => 'bg-warning-700', 'U' => 'bg-success-300',
        'V' => 'bg-success-700', 'W' => 'bg-accent-700', 'X' => 'bg-info-600',
        'Y' => 'bg-info-700', 'Z' => 'bg-primary-400',
    ];

    $bgColor = $colors[substr($initials, 0, 1)] ?? 'bg-secondary-500';
    $ringClass = $ring ? 'ring-2 ring-white dark:ring-secondary-800' : '';
@endphp

<div {{ $attributes->merge(['class' => 'relative inline-flex flex-shrink-0 rounded-full ' . ($sizes[$size] ?? $sizes['md']) . ' ' . $ringClass]) }}>
    @if ($src)
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="rounded-full object-cover {{ $sizes[$size] ?? $sizes['md'] }}"
        >
    @elseif ($name)
        <span class="inline-flex items-center justify-center rounded-full {{ $bgColor }} {{ $sizes[$size] ?? $sizes['md'] }}">
            <span class="font-medium text-white">{{ $initials }}</span>
        </span>
    @else
        <span class="inline-flex items-center justify-center rounded-full bg-secondary-200 dark:bg-secondary-700 {{ $sizes[$size] ?? $sizes['md'] }}">
            <svg class="h-1/2 w-1/2 text-secondary-400 dark:text-secondary-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </span>
    @endif

    @if (isset($status))
        {{ $status }}
    @endif
</div>
