<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('Lernkarten') }}</h2>
                @if($this->module)
                    <p class="mt-1 text-sm text-gray-500">{{ $this->module->title }}</p>
                @endif
            </div>
            <livewire:learner.ai.usage-stats />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-6 rounded-lg bg-red-50 p-4">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            @if(empty($flashcards))
                <!-- No flashcards yet -->
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-12 text-center sm:px-6">
                        <svg class="mx-auto size-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Lernkarten vorhanden') }}</h3>
                        <p class="mt-2 text-sm text-gray-500">{{ __('Lassen Sie Lernkarten für dieses Modul generieren.') }}</p>
                        <button
                            wire:click="generateFlashcards"
                            wire:loading.attr="disabled"
                            class="mt-6 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="generateFlashcards">{{ __('Lernkarten generieren') }}</span>
                            <span wire:loading wire:target="generateFlashcards">{{ __('Wird generiert...') }}</span>
                        </button>
                    </div>
                </div>
            @else
                <!-- Progress Stats -->
                <div class="mb-6 rounded-lg bg-white p-4 shadow">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            {{ __(':reviewed von :total Karten durchgearbeitet', ['reviewed' => $this->progressStats['reviewed'], 'total' => $this->progressStats['total']]) }}
                        </div>
                        <div class="text-sm font-medium text-indigo-600">
                            {{ $this->progressStats['mastered'] }} {{ __('gemeistert') }}
                        </div>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                        <div class="h-2 bg-indigo-600" style="width: {{ $this->progressStats['percent'] }}%"></div>
                    </div>
                </div>

                <!-- Flashcard -->
                @if($this->currentCard)
                    <div
                        wire:click="flipCard"
                        class="relative min-h-[300px] cursor-pointer overflow-hidden rounded-2xl bg-white shadow-lg transition-all hover:shadow-xl"
                    >
                        <div class="absolute inset-0 flex items-center justify-center p-8">
                            @if(!$isFlipped)
                                <!-- Front (Question) -->
                                <div class="text-center">
                                    <p class="text-xs font-medium uppercase tracking-wider text-indigo-600">{{ __('Frage') }}</p>
                                    <p class="mt-4 text-xl font-medium text-gray-900">{{ $this->currentCard['front'] ?? $this->currentCard['question'] ?? '' }}</p>
                                    <p class="mt-8 text-sm text-gray-400">{{ __('Zum Umdrehen klicken') }}</p>
                                </div>
                            @else
                                <!-- Back (Answer) -->
                                <div class="text-center">
                                    <p class="text-xs font-medium uppercase tracking-wider text-green-600">{{ __('Antwort') }}</p>
                                    <p class="mt-4 text-xl font-medium text-gray-900">{{ $this->currentCard['back'] ?? $this->currentCard['answer'] ?? '' }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Card number indicator -->
                        <div class="absolute bottom-4 left-4 text-sm text-gray-400">
                            {{ $currentIndex + 1 }} / {{ count($flashcards) }}
                        </div>
                    </div>

                    <!-- Rating buttons (shown after flip) -->
                    @if($isFlipped)
                        <div class="mt-6 grid grid-cols-4 gap-3">
                            <button
                                wire:click="markCard('again')"
                                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 hover:bg-red-100"
                            >
                                {{ __('Nochmal') }}
                            </button>
                            <button
                                wire:click="markCard('hard')"
                                class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm font-medium text-yellow-700 hover:bg-yellow-100"
                            >
                                {{ __('Schwer') }}
                            </button>
                            <button
                                wire:click="markCard('good')"
                                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 hover:bg-green-100"
                            >
                                {{ __('Gut') }}
                            </button>
                            <button
                                wire:click="markCard('easy')"
                                class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-medium text-indigo-700 hover:bg-indigo-100"
                            >
                                {{ __('Einfach') }}
                            </button>
                        </div>
                    @endif

                    <!-- Navigation -->
                    <div class="mt-6 flex items-center justify-between">
                        <button
                            wire:click="previousCard"
                            class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900"
                        >
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                            {{ __('Zurück') }}
                        </button>
                        <button
                            wire:click="nextCard"
                            class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900"
                        >
                            {{ __('Weiter') }}
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    </div>

                    <!-- Card dots navigation -->
                    <div class="mt-6 flex flex-wrap justify-center gap-2">
                        @foreach($flashcards as $index => $card)
                            <button
                                wire:click="goToCard({{ $index }})"
                                class="size-3 rounded-full {{ $currentIndex === $index ? 'bg-indigo-600' : 'bg-gray-300 hover:bg-gray-400' }}"
                            ></button>
                        @endforeach
                    </div>
                @endif

                <!-- Regenerate button -->
                <div class="mt-8 text-center">
                    <button
                        wire:click="generateFlashcards"
                        wire:loading.attr="disabled"
                        class="text-sm font-medium text-gray-500 hover:text-gray-700"
                    >
                        <span wire:loading.remove wire:target="generateFlashcards">{{ __('Neue Lernkarten generieren') }}</span>
                        <span wire:loading wire:target="generateFlashcards">{{ __('Wird generiert...') }}</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
