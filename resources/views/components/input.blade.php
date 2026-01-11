@props(['disabled' => false, 'error' => false])

@php
$classes = 'block w-full rounded-lg border shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-0 disabled:bg-secondary-100 disabled:cursor-not-allowed dark:bg-secondary-800 dark:text-secondary-100';

if ($error) {
    $classes .= ' border-danger-300 text-danger-900 placeholder-danger-400 focus:border-danger-500 focus:ring-danger-500 dark:border-danger-500 dark:text-danger-100';
} else {
    $classes .= ' border-secondary-300 text-secondary-900 placeholder-secondary-400 focus:border-primary-500 focus:ring-primary-500 dark:border-secondary-600 dark:placeholder-secondary-500';
}
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>
