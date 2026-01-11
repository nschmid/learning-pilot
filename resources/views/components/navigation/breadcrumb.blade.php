@props([
    'items' => [],
])

<nav {{ $attributes->merge(['class' => 'flex']) }} aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li>
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <span class="sr-only">{{ __('Startseite') }}</span>
            </a>
        </li>

        @foreach ($items as $item)
            <li class="flex items-center">
                <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                @if (isset($item['href']) && !$loop->last)
                    <a href="{{ $item['href'] }}" class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="ml-2 text-sm font-medium text-gray-900" aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
