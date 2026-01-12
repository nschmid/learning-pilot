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
    'primary' => 'bg-teal-50 text-teal-600 ring-1 ring-teal-600/10',
    'success' => 'bg-green-50 text-green-600 ring-1 ring-green-600/10',
    'warning' => 'bg-amber-50 text-amber-600 ring-1 ring-amber-600/10',
    'danger' => 'bg-rose-50 text-rose-600 ring-1 ring-rose-600/10',
    'info' => 'bg-sky-50 text-sky-600 ring-1 ring-sky-600/10',
    'accent' => 'bg-purple-50 text-purple-600 ring-1 ring-purple-600/10',
];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6']) }}>
    <div class="flex items-center">
        @if ($icon)
            <div class="flex-shrink-0 p-3 rounded-xl {{ $iconVariants[$variant] ?? $iconVariants['primary'] }}">
                {{ $icon }}
            </div>
        @endif
        <div class="@if($icon) ml-4 @endif flex-1">
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <div class="flex items-baseline mt-1">
                <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
                @if ($change !== null)
                    <p class="ml-2 flex items-baseline text-sm font-semibold {{ $changeType === 'increase' ? 'text-green-600' : 'text-rose-600' }}">
                        @if ($changeType === 'increase')
                            <svg class="size-4 flex-shrink-0 self-center" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="size-4 flex-shrink-0 self-center" fill="currentColor" viewBox="0 0 20 20">
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
