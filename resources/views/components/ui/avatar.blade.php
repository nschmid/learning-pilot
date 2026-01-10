@props([
    'src' => null,
    'alt' => '',
    'size' => 'md',
    'name' => null,
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

    $colors = [
        'A' => 'bg-red-500', 'B' => 'bg-orange-500', 'C' => 'bg-amber-500',
        'D' => 'bg-yellow-500', 'E' => 'bg-lime-500', 'F' => 'bg-green-500',
        'G' => 'bg-emerald-500', 'H' => 'bg-teal-500', 'I' => 'bg-cyan-500',
        'J' => 'bg-sky-500', 'K' => 'bg-blue-500', 'L' => 'bg-indigo-500',
        'M' => 'bg-violet-500', 'N' => 'bg-purple-500', 'O' => 'bg-fuchsia-500',
        'P' => 'bg-pink-500', 'Q' => 'bg-rose-500', 'R' => 'bg-red-600',
        'S' => 'bg-orange-600', 'T' => 'bg-amber-600', 'U' => 'bg-yellow-600',
        'V' => 'bg-lime-600', 'W' => 'bg-green-600', 'X' => 'bg-emerald-600',
        'Y' => 'bg-teal-600', 'Z' => 'bg-cyan-600',
    ];

    $bgColor = $colors[substr($initials, 0, 1)] ?? 'bg-gray-500';
@endphp

<div {{ $attributes->merge(['class' => 'relative inline-flex flex-shrink-0 rounded-full ' . $sizes[$size]]) }}>
    @if ($src)
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="rounded-full object-cover {{ $sizes[$size] }}"
        >
    @elseif ($name)
        <span class="inline-flex items-center justify-center rounded-full {{ $bgColor }} {{ $sizes[$size] }}">
            <span class="font-medium text-white">{{ $initials }}</span>
        </span>
    @else
        <span class="inline-flex items-center justify-center rounded-full bg-gray-200 {{ $sizes[$size] }}">
            <svg class="h-1/2 w-1/2 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </span>
    @endif

    @if (isset($status))
        {{ $status }}
    @endif
</div>
