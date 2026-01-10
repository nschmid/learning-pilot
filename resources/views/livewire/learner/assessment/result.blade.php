<div class="mx-auto max-w-4xl">
    <!-- Result Card -->
    <div class="mb-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-8 text-center {{ $attempt->passed ? 'bg-gradient-to-r from-green-500 to-emerald-600' : 'bg-gradient-to-r from-red-500 to-rose-600' }}">
            @if($attempt->passed)
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white">{{ __('Bestanden!') }}</h1>
                <p class="mt-2 text-green-100">{{ __('Herzlichen Glückwunsch! Du hast den Test erfolgreich abgeschlossen.') }}</p>
            @else
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white">{{ __('Leider nicht bestanden') }}</h1>
                <p class="mt-2 text-red-100">{{ __('Keine Sorge, du kannst es erneut versuchen!') }}</p>
            @endif
        </div>

        <!-- Score -->
        <div class="p-6">
            <div class="mb-8 grid gap-4 text-center sm:grid-cols-4">
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-3xl font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($attempt->score_percent, 1) }}%
                    </div>
                    <div class="text-sm text-gray-500">{{ __('Erreicht') }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-3xl font-bold text-gray-900">{{ $assessment->passing_score_percent }}%</div>
                    <div class="text-sm text-gray-500">{{ __('Benötigt') }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-3xl font-bold text-gray-900">{{ $attempt->points_earned }}/{{ $assessment->totalPoints() }}</div>
                    <div class="text-sm text-gray-500">{{ __('Punkte') }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-3xl font-bold text-gray-900">{{ $attempt->getFormattedTimeSpent() }}</div>
                    <div class="text-sm text-gray-500">{{ __('Zeit') }}</div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="mb-2 flex items-center justify-between text-sm">
                    <span class="text-gray-600">{{ __('Dein Ergebnis') }}</span>
                    <span class="font-medium {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($attempt->score_percent, 1) }}%
                    </span>
                </div>
                <div class="relative h-4 overflow-hidden rounded-full bg-gray-200">
                    <!-- Passing threshold marker -->
                    <div
                        class="absolute top-0 h-full w-0.5 bg-gray-600"
                        style="left: {{ $assessment->passing_score_percent }}%"
                    ></div>
                    <!-- Score bar -->
                    <div
                        class="h-full rounded-full transition-all duration-500 {{ $attempt->passed ? 'bg-green-500' : 'bg-red-500' }}"
                        style="width: {{ $attempt->score_percent }}%"
                    ></div>
                </div>
                <div class="mt-1 text-right text-xs text-gray-500">
                    {{ __('Bestehensgrenze:') }} {{ $assessment->passing_score_percent }}%
                </div>
            </div>

            <!-- Statistics -->
            <div class="mb-8 grid gap-4 sm:grid-cols-3">
                <div class="flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 p-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-700">{{ $this->stats['correct_answers'] }}</div>
                        <div class="text-sm text-green-600">{{ __('Richtig') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 p-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-red-700">{{ $this->stats['incorrect_answers'] }}</div>
                        <div class="text-sm text-red-600">{{ __('Falsch') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-700">{{ $this->stats['total_questions'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Gesamt') }}</div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap items-center justify-center gap-4">
                @if(!$attempt->passed)
                    <button
                        wire:click="retryAssessment"
                        class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-6 py-3 font-semibold text-white transition hover:bg-purple-700"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        {{ __('Erneut versuchen') }}
                    </button>
                @endif
                <button
                    wire:click="continueLearning"
                    class="inline-flex items-center gap-2 rounded-lg {{ $attempt->passed ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-600 hover:bg-gray-700' }} px-6 py-3 font-semibold text-white transition"
                >
                    {{ __('Weiterlernen') }}
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Detailed Results -->
    @if($assessment->show_correct_answers && count($results) > 0)
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Detaillierte Auswertung') }}</h2>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($results as $index => $result)
                    <div class="p-6">
                        <!-- Question Header -->
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full {{ $result['is_correct'] ? 'bg-green-100 text-green-700' : ($result['is_correct'] === false ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }} text-sm font-semibold">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $result['question']['text'] }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $result['points_earned'] }}/{{ $result['question']['points'] }} {{ __('Punkte') }}
                                    </p>
                                </div>
                            </div>
                            @if($result['is_correct'] === true)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('Richtig') }}
                                </span>
                            @elseif($result['is_correct'] === false)
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('Falsch') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-800">
                                    {{ __('Ausstehend') }}
                                </span>
                            @endif
                        </div>

                        <!-- Options -->
                        @if($result['options'])
                            <div class="ml-11 space-y-2">
                                @foreach($result['options'] as $option)
                                    @php
                                        $isSelected = is_array($result['user_answer'])
                                            ? in_array($option['id'], $result['user_answer'])
                                            : $result['user_answer'] === $option['id'];
                                        $isCorrect = $option['is_correct'];
                                    @endphp
                                    <div class="flex items-center gap-3 rounded-lg p-3 {{ $isCorrect ? 'bg-green-50 border border-green-200' : ($isSelected ? 'bg-red-50 border border-red-200' : 'bg-gray-50 border border-gray-200') }}">
                                        @if($isSelected && $isCorrect)
                                            <svg class="h-5 w-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        @elseif($isSelected && !$isCorrect)
                                            <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        @elseif(!$isSelected && $isCorrect)
                                            <svg class="h-5 w-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            <div class="h-5 w-5 flex-shrink-0"></div>
                                        @endif
                                        <span class="{{ $isCorrect ? 'text-green-800' : ($isSelected ? 'text-red-800' : 'text-gray-700') }}">
                                            {{ $option['text'] }}
                                        </span>
                                        @if($isSelected)
                                            <span class="ml-auto text-xs {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                                {{ __('Deine Antwort') }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif($result['question']['type'] === 'text')
                            <!-- Text answer -->
                            <div class="ml-11">
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                                    <p class="text-sm text-gray-500">{{ __('Deine Antwort:') }}</p>
                                    <p class="mt-1 text-gray-900">{{ $result['user_answer'] ?: __('(Keine Antwort)') }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Explanation -->
                        @if($result['question']['explanation'])
                            <div class="ml-11 mt-4">
                                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                                    <div class="mb-2 flex items-center gap-2 text-sm font-medium text-blue-800">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ __('Erklärung') }}
                                    </div>
                                    <p class="text-sm text-blue-700">{{ $result['question']['explanation'] }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
