<div class="w-full">
    @if($showLabel)
        <div class="mb-1 flex items-center justify-between text-sm">
            <span class="font-medium text-gray-700">{{ $label ?? __('Fortschritt') }}</span>
            <span class="text-gray-500">{{ number_format($percent, 0) }}%</span>
        </div>
    @endif
    <div class="w-full overflow-hidden rounded-full bg-gray-200 {{ $this->getHeightClass() }}">
        <div
            class="{{ $this->getHeightClass() }} {{ $this->getColorClass() }} rounded-full {{ $animate ? 'transition-all duration-500 ease-out' : '' }}"
            style="width: {{ $percent }}%"
            role="progressbar"
            aria-valuenow="{{ $percent }}"
            aria-valuemin="0"
            aria-valuemax="100"
        ></div>
    </div>
</div>
