@props([
    'padding' => true,
    'hover' => false,
    'variant' => 'default',
])

@php
$variants = [
    'default' => 'bg-white ring-1 ring-gray-900/5',
    'primary' => 'bg-teal-50 ring-1 ring-teal-600/10',
    'success' => 'bg-green-50 ring-1 ring-green-600/10',
    'warning' => 'bg-amber-50 ring-1 ring-amber-600/10',
    'danger' => 'bg-rose-50 ring-1 ring-rose-600/10',
];

$headerBg = match($variant) {
    'primary' => 'bg-teal-100/50',
    'success' => 'bg-green-100/50',
    'warning' => 'bg-amber-100/50',
    'danger' => 'bg-rose-100/50',
    default => 'bg-gray-50',
};
@endphp

<div {{ $attributes->merge([
    'class' => 'rounded-xl shadow-sm ' . ($variants[$variant] ?? $variants['default']) . ' ' .
        ($padding ? 'p-6' : '') . ' ' .
        ($hover ? 'hover:shadow-md hover:ring-gray-900/10 transition-all duration-200' : '')
]) }}>
    @if (isset($header))
        <div class="border-b border-gray-100 -mx-6 -mt-6 px-6 py-4 mb-6 {{ $headerBg }} rounded-t-xl">
            {{ $header }}
        </div>
    @endif

    {{ $slot }}

    @if (isset($footer))
        <div class="border-t border-gray-100 -mx-6 -mb-6 px-6 py-4 mt-6 {{ $headerBg }} rounded-b-xl">
            {{ $footer }}
        </div>
    @endif
</div>
