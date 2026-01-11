@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-secondary-700 dark:text-secondary-300']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-danger-500 ml-0.5">*</span>
    @endif
</label>
