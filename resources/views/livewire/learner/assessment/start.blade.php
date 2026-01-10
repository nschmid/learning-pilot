<div class="mx-auto max-w-3xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('learner.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('learner.path.show', $assessment->step->module->learningPath->slug) }}" wire:navigate class="hover:text-gray-700">
            {{ $assessment->step->module->learningPath->title }}
        </a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $assessment->title }}</span>
    </nav>

    <!-- Main Card -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <!-- Header -->
        <div class="border-b border-gray-200 bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-8 text-white">
            <div class="mb-2 flex items-center gap-2">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ __('Wissenstest') }}</span>
            </div>
            <h1 class="text-2xl font-bold">{{ $assessment->title }}</h1>
            @if($assessment->description)
                <p class="mt-2 text-purple-100">{{ $assessment->description }}</p>
            @endif
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Assessment Info -->
            <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-gray-50 p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $assessment->questionCount() }}</div>
                    <div class="text-sm text-gray-500">{{ __('Fragen') }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">
                        @if($assessment->hasTimeLimit())
                            {{ $assessment->time_limit_minutes }} min
                        @else
                            &infin;
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">{{ __('Zeitlimit') }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $assessment->passing_score_percent }}%</div>
                    <div class="text-sm text-gray-500">{{ __('Zum Bestehen') }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">
                        @if($assessment->hasAttemptLimit())
                            {{ $this->attemptsRemaining ?? 0 }}/{{ $assessment->max_attempts }}
                        @else
                            &infin;
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">{{ __('Versuche übrig') }}</div>
                </div>
            </div>

            <!-- Instructions -->
            @if($assessment->instructions)
                <div class="mb-8">
                    <h3 class="mb-3 font-semibold text-gray-900">{{ __('Anweisungen') }}</h3>
                    <div class="prose prose-sm max-w-none rounded-lg bg-amber-50 p-4 text-amber-900">
                        {!! nl2br(e($assessment->instructions)) !!}
                    </div>
                </div>
            @endif

            <!-- Rules -->
            <div class="mb-8">
                <h3 class="mb-3 font-semibold text-gray-900">{{ __('Wichtige Hinweise') }}</h3>
                <ul class="space-y-2">
                    @if($assessment->hasTimeLimit())
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Du hast :minutes Minuten Zeit, um den Test abzuschließen.', ['minutes' => $assessment->time_limit_minutes]) }}
                        </li>
                    @endif
                    @if($assessment->shuffle_questions)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            {{ __('Die Reihenfolge der Fragen wird zufällig gemischt.') }}
                        </li>
                    @endif
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('Du benötigst mindestens :percent% um zu bestehen.', ['percent' => $assessment->passing_score_percent]) }}
                    </li>
                    @if($assessment->show_correct_answers)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ __('Nach Abschluss werden dir die richtigen Antworten angezeigt.') }}
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Previous Attempts -->
            @if($this->previousAttempts->count() > 0)
                <div class="mb-8">
                    <h3 class="mb-3 font-semibold text-gray-900">{{ __('Bisherige Versuche') }}</h3>
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Versuch') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Datum') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Ergebnis') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($this->previousAttempts as $attempt)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                            #{{ $attempt->attempt_number }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                            {{ $attempt->completed_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                                            <span class="font-semibold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($attempt->score_percent, 1) }}%
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                                            @if($attempt->passed)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ __('Bestanden') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">
                                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ __('Nicht bestanden') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                            <a
                                                href="{{ route('learner.assessment.result', [$assessment->id, $attempt->id]) }}"
                                                wire:navigate
                                                class="text-indigo-600 hover:text-indigo-800"
                                            >
                                                {{ __('Details') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                <a
                    href="{{ route('learner.learn.index', $assessment->step->module->learningPath->slug) }}"
                    wire:navigate
                    class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Zurück zum Kurs') }}
                </a>

                @if($this->inProgressAttempt)
                    <button
                        wire:click="continueAssessment"
                        class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-6 py-3 font-semibold text-white transition hover:bg-orange-700"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('Test fortsetzen') }}
                    </button>
                @elseif($this->canStart)
                    <button
                        wire:click="startAssessment"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-6 py-3 font-semibold text-white transition hover:bg-purple-700 disabled:cursor-wait disabled:opacity-75"
                    >
                        <span wire:loading.remove wire:target="startAssessment">{{ __('Test starten') }}</span>
                        <span wire:loading wire:target="startAssessment">{{ __('Wird gestartet...') }}</span>
                        <svg wire:loading.remove wire:target="startAssessment" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                @else
                    <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">
                        @if(!$this->enrollment)
                            {{ __('Du musst für diesen Kurs eingeschrieben sein.') }}
                        @else
                            {{ __('Du hast alle Versuche aufgebraucht.') }}
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
