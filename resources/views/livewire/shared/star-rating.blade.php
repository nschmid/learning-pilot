<div class="flex items-center gap-1">
    @for($i = 1; $i <= $maxStars; $i++)
        @php
            $filled = $rating >= $i;
            $halfFilled = !$filled && $rating >= ($i - 0.5);
        @endphp
        <button
            type="button"
            @if($interactive)
                wire:click="setRating({{ $i }})"
                class="focus:outline-none {{ $interactive ? 'cursor-pointer hover:scale-110 transition-transform' : 'cursor-default' }}"
            @else
                class="cursor-default"
                disabled
            @endif
        >
            @if($filled)
                <svg class="{{ $this->getSizeClass() }} text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            @elseif($halfFilled)
                <svg class="{{ $this->getSizeClass() }} text-yellow-400" viewBox="0 0 20 20">
                    <defs>
                        <linearGradient id="half-{{ $i }}">
                            <stop offset="50%" stop-color="currentColor"/>
                            <stop offset="50%" stop-color="#D1D5DB"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#half-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            @else
                <svg class="{{ $this->getSizeClass() }} text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            @endif
        </button>
    @endfor

    @if($showValue)
        <span class="ml-1 text-sm font-medium text-gray-700">{{ number_format($rating, 1) }}</span>
    @endif

    @if($reviewCount !== null)
        <span class="ml-1 text-sm text-gray-500">({{ $reviewCount }})</span>
    @endif
</div>
