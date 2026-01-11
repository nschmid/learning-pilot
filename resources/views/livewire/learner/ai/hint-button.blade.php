<div class="inline-block">
    <!-- Hint Button -->
    <div class="relative" x-data="{ showHints: false }">
        <button
            type="button"
            @click="showHints = !showHints"
            class="inline-flex items-center gap-2 rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            {{ __('Hinweis') }}
            @if($currentHintLevel > 0)
                <span class="rounded-full bg-amber-200 px-2 py-0.5 text-xs">{{ $currentHintLevel }}/{{ $maxHintLevel }}</span>
            @endif
        </button>

        <!-- Hints Panel -->
        <div
            x-show="showHints"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            @click.outside="showHints = false"
            class="absolute left-0 z-50 mt-2 w-96 rounded-xl border border-gray-200 bg-white p-4 shadow-xl"
            x-cloak
        >
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Hinweise') }}</h3>
                <button @click="showHints = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Error Message -->
            @if($error)
                <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                    {{ $error }}
                </div>
            @endif

            <!-- Hints List -->
            @if(count($hints) > 0)
                <div class="mb-4 space-y-3">
                    @foreach($hints as $hint)
                        <div class="rounded-lg bg-amber-50 p-3">
                            <div class="mb-1 flex items-center gap-2">
                                <span class="rounded bg-amber-200 px-2 py-0.5 text-xs font-medium text-amber-800">
                                    {{ __('Hinweis') }} {{ $hint['level'] }}
                                </span>
                            </div>
                            <div class="prose prose-sm text-gray-700">
                                {!! nl2br(e($hint['content'])) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mb-4 text-sm text-gray-500">
                    {{ __('Stecken Sie fest? Fordern Sie einen Hinweis an, um in die richtige Richtung gewiesen zu werden.') }}
                </p>
            @endif

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>{{ __('Verwendete Hinweise') }}</span>
                    <span>{{ $currentHintLevel }}/{{ $maxHintLevel }}</span>
                </div>
                <div class="mt-1 h-2 overflow-hidden rounded-full bg-gray-200">
                    <div
                        class="h-full rounded-full bg-amber-500 transition-all duration-300"
                        style="width: {{ ($currentHintLevel / $maxHintLevel) * 100 }}%"
                    ></div>
                </div>
                <p class="mt-1 text-xs text-gray-400">
                    {{ __('Je weniger Hinweise Sie verwenden, desto mehr Punkte erhalten Sie!') }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                @if($this->canRequestHint)
                    <button
                        wire:click="requestHint"
                        wire:loading.attr="disabled"
                        class="flex-1 rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="requestHint">
                            {{ __('Hinweis :level anfordern', ['level' => $this->nextHintLevel]) }}
                        </span>
                        <span wire:loading wire:target="requestHint" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Wird generiert...') }}
                        </span>
                    </button>
                @else
                    <div class="flex-1 rounded-lg bg-gray-100 px-4 py-2 text-center text-sm text-gray-500">
                        {{ __('Alle Hinweise verwendet') }}
                    </div>
                @endif

                @if(count($hints) > 0)
                    <button
                        wire:click="clearHints"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 transition hover:bg-gray-50"
                        title="{{ __('Hinweise zurücksetzen') }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
