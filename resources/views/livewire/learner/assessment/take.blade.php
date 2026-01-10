<div
    class="flex h-screen flex-col bg-gray-100"
    x-data="{
        timeRemaining: {{ $this->timeRemaining ?? 'null' }},
        hasTimeLimit: {{ $assessment->hasTimeLimit() ? 'true' : 'false' }},
        init() {
            if (this.hasTimeLimit && this.timeRemaining !== null) {
                this.startTimer();
            }
        },
        startTimer() {
            setInterval(() => {
                if (this.timeRemaining > 0) {
                    this.timeRemaining--;
                } else if (this.timeRemaining === 0) {
                    $wire.submitAssessment();
                }
            }, 1000);
        },
        formatTime(seconds) {
            if (seconds === null) return '--:--';
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        }
    }"
>
    <!-- Top Bar -->
    <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 lg:px-6">
        <div class="flex items-center gap-4">
            <h1 class="text-lg font-semibold text-gray-900">{{ $assessment->title }}</h1>
        </div>

        <div class="flex items-center gap-6">
            <!-- Progress -->
            <div class="text-sm text-gray-600">
                <span class="font-medium">{{ $this->answeredCount }}</span> / {{ $this->totalQuestions }} {{ __('beantwortet') }}
            </div>

            <!-- Timer -->
            @if($assessment->hasTimeLimit())
                <div
                    class="flex items-center gap-2 rounded-lg px-3 py-2"
                    :class="timeRemaining < 60 ? 'bg-red-100 text-red-700' : (timeRemaining < 300 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700')"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-mono font-semibold" x-text="formatTime(timeRemaining)"></span>
                </div>
            @endif

            <!-- Submit Button -->
            <button
                wire:click="submitAssessment"
                wire:loading.attr="disabled"
                wire:confirm="{{ __('Bist du sicher, dass du den Test abgeben möchtest? Du kannst danach keine Änderungen mehr vornehmen.') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-700 disabled:cursor-wait disabled:opacity-75"
            >
                <span wire:loading.remove wire:target="submitAssessment">{{ __('Test abgeben') }}</span>
                <span wire:loading wire:target="submitAssessment">{{ __('Wird ausgewertet...') }}</span>
            </button>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Question Navigator Sidebar -->
        <aside class="hidden w-64 border-r border-gray-200 bg-white p-4 lg:block">
            <h2 class="mb-4 text-sm font-medium text-gray-500">{{ __('Fragenübersicht') }}</h2>
            <div class="grid grid-cols-5 gap-2">
                @foreach($questions as $index => $question)
                    <button
                        wire:click="goToQuestion({{ $index }})"
                        class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium transition
                            {{ $currentQuestionIndex === $index ? 'bg-purple-600 text-white' : ($this->isAnswered($question['id']) ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200') }}"
                    >
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>

            <div class="mt-6 space-y-2 text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded bg-purple-600"></span>
                    {{ __('Aktuelle Frage') }}
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded bg-green-100"></span>
                    {{ __('Beantwortet') }}
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded bg-gray-100"></span>
                    {{ __('Unbeantwortet') }}
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex flex-1 flex-col overflow-hidden">
            <!-- Question Content -->
            <div class="flex-1 overflow-y-auto p-6 lg:p-8">
                @if($this->currentQuestion)
                    <div class="mx-auto max-w-3xl">
                        <!-- Question Header -->
                        <div class="mb-6">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-sm font-medium text-purple-600">
                                    {{ __('Frage') }} {{ $currentQuestionIndex + 1 }} {{ __('von') }} {{ $this->totalQuestions }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $this->currentQuestion['points'] }} {{ trans_choice('Punkt|Punkte', $this->currentQuestion['points']) }}
                                </span>
                            </div>
                            <div class="h-1 overflow-hidden rounded-full bg-gray-200">
                                <div
                                    class="h-full rounded-full bg-purple-600 transition-all duration-300"
                                    style="width: {{ (($currentQuestionIndex + 1) / $this->totalQuestions) * 100 }}%"
                                ></div>
                            </div>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                            <h2 class="text-xl font-medium text-gray-900">
                                {{ $this->currentQuestion['text'] }}
                            </h2>

                            @if($this->currentQuestion['image'])
                                <img
                                    src="{{ Storage::url($this->currentQuestion['image']) }}"
                                    alt="{{ __('Bild zur Frage') }}"
                                    class="mt-4 max-h-64 rounded-lg"
                                >
                            @endif
                        </div>

                        <!-- Answer Options -->
                        <div class="space-y-3">
                            @if($this->currentQuestion['type'] === 'text')
                                <!-- Text Input -->
                                <textarea
                                    wire:model.blur="answers.{{ $this->currentQuestion['id'] }}"
                                    rows="4"
                                    class="w-full rounded-lg border border-gray-300 p-4 text-gray-900 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    placeholder="{{ __('Deine Antwort eingeben...') }}"
                                ></textarea>
                            @elseif($this->currentQuestion['type'] === 'true_false')
                                <!-- True/False Options -->
                                @foreach([['id' => 'true', 'text' => __('Richtig')], ['id' => 'false', 'text' => __('Falsch')]] as $option)
                                    <button
                                        wire:click="selectOption('{{ $option['id'] }}')"
                                        class="flex w-full items-center gap-4 rounded-lg border-2 p-4 text-left transition
                                            {{ ($answers[$this->currentQuestion['id']] ?? null) === $option['id'] ? 'border-purple-500 bg-purple-50' : 'border-gray-200 bg-white hover:border-gray-300' }}"
                                    >
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full border-2 {{ ($answers[$this->currentQuestion['id']] ?? null) === $option['id'] ? 'border-purple-500 bg-purple-500 text-white' : 'border-gray-300' }}">
                                            @if(($answers[$this->currentQuestion['id']] ?? null) === $option['id'])
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </span>
                                        <span class="flex-1 font-medium text-gray-900">{{ $option['text'] }}</span>
                                    </button>
                                @endforeach
                            @else
                                <!-- Single/Multiple Choice Options -->
                                @foreach($this->currentQuestion['options'] as $option)
                                    <button
                                        wire:click="selectOption('{{ $option['id'] }}')"
                                        class="flex w-full items-center gap-4 rounded-lg border-2 p-4 text-left transition
                                            {{ $this->isOptionSelected($option['id']) ? 'border-purple-500 bg-purple-50' : 'border-gray-200 bg-white hover:border-gray-300' }}"
                                    >
                                        @if($this->currentQuestion['type'] === 'multiple_choice')
                                            <!-- Checkbox style -->
                                            <span class="flex h-6 w-6 items-center justify-center rounded border-2 {{ $this->isOptionSelected($option['id']) ? 'border-purple-500 bg-purple-500 text-white' : 'border-gray-300' }}">
                                                @if($this->isOptionSelected($option['id']))
                                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        @else
                                            <!-- Radio style -->
                                            <span class="flex h-6 w-6 items-center justify-center rounded-full border-2 {{ $this->isOptionSelected($option['id']) ? 'border-purple-500 bg-purple-500 text-white' : 'border-gray-300' }}">
                                                @if($this->isOptionSelected($option['id']))
                                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        @endif
                                        <span class="flex-1 text-gray-900">{{ $option['text'] }}</span>
                                    </button>
                                @endforeach

                                @if($this->currentQuestion['type'] === 'multiple_choice')
                                    <p class="mt-2 text-sm text-gray-500">
                                        {{ __('Mehrere Antworten möglich') }}
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Bottom Navigation -->
            <footer class="border-t border-gray-200 bg-white px-6 py-4">
                <div class="mx-auto flex max-w-3xl items-center justify-between">
                    <button
                        wire:click="previousQuestion"
                        @disabled($currentQuestionIndex <= 0)
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2 font-medium text-gray-600 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        {{ __('Zurück') }}
                    </button>

                    <!-- Mobile question indicator -->
                    <div class="lg:hidden">
                        <span class="text-sm text-gray-500">
                            {{ $currentQuestionIndex + 1 }} / {{ $this->totalQuestions }}
                        </span>
                    </div>

                    @if($currentQuestionIndex < $this->totalQuestions - 1)
                        <button
                            wire:click="nextQuestion"
                            class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2 font-medium text-white transition hover:bg-purple-700"
                        >
                            {{ __('Weiter') }}
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @else
                        <button
                            wire:click="submitAssessment"
                            wire:loading.attr="disabled"
                            wire:confirm="{{ __('Bist du sicher, dass du den Test abgeben möchtest?') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 font-medium text-white transition hover:bg-green-700 disabled:cursor-wait disabled:opacity-75"
                        >
                            <span wire:loading.remove wire:target="submitAssessment">{{ __('Test abgeben') }}</span>
                            <span wire:loading wire:target="submitAssessment">{{ __('Wird ausgewertet...') }}</span>
                            <svg wire:loading.remove wire:target="submitAssessment" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            </footer>
        </main>
    </div>
</div>
