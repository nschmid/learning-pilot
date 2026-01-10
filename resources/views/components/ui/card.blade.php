@props([
    'padding' => true,
    'hover' => false,
])

<div {{ $attributes->merge([
    'class' => 'bg-white rounded-lg shadow-sm border border-gray-200 ' .
        ($padding ? 'p-6' : '') . ' ' .
        ($hover ? 'hover:shadow-md hover:border-gray-300 transition-all duration-200' : '')
]) }}>
    @if (isset($header))
        <div class="border-b border-gray-200 -mx-6 -mt-6 px-6 py-4 mb-6 bg-gray-50 rounded-t-lg">
            {{ $header }}
        </div>
    @endif

    {{ $slot }}

    @if (isset($footer))
        <div class="border-t border-gray-200 -mx-6 -mb-6 px-6 py-4 mt-6 bg-gray-50 rounded-b-lg">
            {{ $footer }}
        </div>
    @endif
</div>
