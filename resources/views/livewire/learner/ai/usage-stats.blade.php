<div class="inline-flex items-center gap-4">
    <!-- Daily requests -->
    <div class="flex items-center gap-2">
        <div class="text-xs text-gray-500">
            <span class="font-medium text-gray-700">{{ $todayUsage }}</span>/{{ $dailyLimit }}
            {{ __('heute') }}
        </div>
        <div class="h-2 w-16 overflow-hidden rounded-full bg-gray-200">
            @php
                $dailyPercent = $dailyLimit > 0 ? min(100, ($todayUsage / $dailyLimit) * 100) : 0;
            @endphp
            <div
                class="h-2 {{ $dailyPercent > 90 ? 'bg-red-500' : ($dailyPercent > 70 ? 'bg-yellow-500' : 'bg-teal-600') }}"
                style="width: {{ $dailyPercent }}%"
            ></div>
        </div>
    </div>

    <!-- Monthly tokens (smaller, less prominent) -->
    <div class="hidden items-center gap-2 text-xs text-gray-400 sm:flex">
        @php
            $monthlyPercent = $monthlyTokenLimit > 0 ? min(100, ($monthlyUsage / $monthlyTokenLimit) * 100) : 0;
        @endphp
        <span>{{ number_format($monthlyUsage) }}/{{ number_format($monthlyTokenLimit) }} {{ __('Tokens') }}</span>
    </div>
</div>
