@props([
    'href' => '#',
    'icon' => null,
    'active' => false,
    'badge' => null,
])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors ' .
            ($active
                ? 'bg-indigo-50 text-indigo-600'
                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900')
    ]) }}
>
    @if ($icon)
        <span class="flex-shrink-0 w-5 h-5 {{ $active ? 'text-indigo-600' : 'text-gray-400' }}">
            {{ $icon }}
        </span>
    @endif

    <span class="flex-1">{{ $slot }}</span>

    @if ($badge)
        <span class="flex-shrink-0 px-2 py-0.5 text-xs font-medium rounded-full
            {{ $active ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600' }}">
            {{ $badge }}
        </span>
    @endif
</a>
