@php
    $sizeClasses = $this->getSizeClasses();
@endphp

<div
    x-data="{
        init() {
            if (@js($running)) {
                this.startInterval();
            }
        },
        interval: null,
        startInterval() {
            this.interval = setInterval(() => {
                $wire.dispatch('tick');
            }, 1000);
        },
        stopInterval() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        }
    }"
    x-init="init()"
    @timer-started.window="startInterval()"
    @timer-paused.window="stopInterval()"
    @timer-reset.window="stopInterval()"
    @timer-finished.window="stopInterval()"
    class="inline-flex items-center gap-2"
>
    <div class="rounded-lg {{ $this->isWarning() ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-900' }} {{ $sizeClasses[1] }} font-mono font-bold {{ $sizeClasses[0] }} transition-colors">
        {{ $this->getFormattedTime() }}
    </div>

    <div class="flex items-center gap-1">
        @if(!$running)
            <button
                wire:click="start"
                type="button"
                class="rounded-lg bg-green-500 p-2 text-white hover:bg-green-600"
                title="{{ __('Start') }}"
            >
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path>
                </svg>
            </button>
        @else
            <button
                wire:click="pause"
                type="button"
                class="rounded-lg bg-yellow-500 p-2 text-white hover:bg-yellow-600"
                title="{{ __('Pause') }}"
            >
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75A.75.75 0 007.25 3h-1.5zM12.75 3a.75.75 0 00-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75V3.75a.75.75 0 00-.75-.75h-1.5z"></path>
                </svg>
            </button>
        @endif
        <button
            wire:click="reset"
            type="button"
            class="rounded-lg bg-gray-500 p-2 text-white hover:bg-gray-600"
            title="{{ __('Zurücksetzen') }}"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </button>
    </div>
</div>
