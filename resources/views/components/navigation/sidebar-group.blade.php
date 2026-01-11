@props([
    'label' => null,
    'collapsible' => false,
    'open' => true,
])

<div {{ $attributes->merge(['class' => 'space-y-1']) }} @if($collapsible) x-data="{ open: {{ $open ? 'true' : 'false' }} }" @endif>
    @if ($label)
        @if ($collapsible)
            <button
                @click="open = !open"
                class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-600"
            >
                {{ $label }}
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        @else
            <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                {{ $label }}
            </div>
        @endif
    @endif

    <div @if($collapsible) x-show="open" x-collapse @endif class="space-y-1">
        {{ $slot }}
    </div>
</div>
