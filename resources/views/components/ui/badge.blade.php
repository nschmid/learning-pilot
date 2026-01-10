@props([
    'type' => 'default',
    'size' => 'md',
    'rounded' => 'full',
])

@php
    $types = [
        'default' => 'bg-gray-100 text-gray-800',
        'primary' => 'bg-indigo-100 text-indigo-800',
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'error' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
        'purple' => 'bg-purple-100 text-purple-800',
        'teal' => 'bg-teal-100 text-teal-800',
    ];

    $sizes = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-0.5 text-sm',
        'lg' => 'px-3 py-1 text-base',
    ];

    $roundedOptions = [
        'none' => 'rounded-none',
        'sm' => 'rounded',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center font-medium ' .
        $types[$type] . ' ' .
        $sizes[$size] . ' ' .
        $roundedOptions[$rounded]
]) }}>
    {{ $slot }}
</span>
