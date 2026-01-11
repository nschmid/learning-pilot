@props([
    'tabs' => [],
    'active' => null,
])

<div {{ $attributes->merge(['class' => 'border-b border-gray-200']) }}>
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        @foreach ($tabs as $tab)
            @php
                $isActive = $active === ($tab['key'] ?? $tab['href'] ?? $loop->index);
            @endphp

            <a
                href="{{ $tab['href'] ?? '#' }}"
                @if(isset($tab['wire:click']))
                    wire:click="{{ $tab['wire:click'] }}"
                @endif
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors
                    {{ $isActive
                        ? 'border-indigo-500 text-indigo-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                @if($isActive) aria-current="page" @endif
            >
                @if (isset($tab['icon']))
                    <span class="inline-flex items-center gap-2">
                        {!! $tab['icon'] !!}
                        {{ $tab['label'] }}
                    </span>
                @else
                    {{ $tab['label'] }}
                @endif

                @if (isset($tab['badge']))
                    <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium
                        {{ $isActive ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }}">
                        {{ $tab['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>
</div>
