<div class="mx-auto max-w-6xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('instructor.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.paths.show', $assessment->step->module->learningPath->slug) }}" wire:navigate class="hover:text-gray-700">
            {{ $assessment->step->module->learningPath->title }}
        </a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.assessments.show', $assessment->id) }}" wire:navigate class="hover:text-gray-700">{{ $assessment->title }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ __('Ergebnisse') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Testergebnisse') }}</h1>
        <p class="mt-1 text-gray-500">{{ $assessment->title }}</p>
    </div>

    <!-- Stats -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Versuche') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->stats['total_attempts'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Bestehensrate') }}</p>
            <p class="text-2xl font-bold {{ $this->stats['pass_rate'] >= 60 ? 'text-green-600' : 'text-orange-600' }}">{{ $this->stats['pass_rate'] }}%</p>
            <p class="text-xs text-gray-400">{{ $this->stats['passed'] }} {{ __('bestanden') }}, {{ $this->stats['failed'] }} {{ __('nicht bestanden') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Ø Punktzahl') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->stats['avg_score'] }}%</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Ø Zeit') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->stats['avg_time'] }}</p>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Attempts Table -->
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Alle Versuche') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Teilnehmer') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Ergebnis') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Zeit') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Datum') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($this->attempts as $attempt)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 flex-shrink-0">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-medium text-indigo-600">
                                                    {{ substr($attempt->enrollment->user->name, 0, 2) }}
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $attempt->enrollment->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ __('Versuch') }} #{{ $attempt->attempt_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-sm font-medium {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $attempt->score_percent }}%
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($attempt->passed)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">
                                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ __('Bestanden') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">
                                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ __('Nicht bestanden') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $attempt->getFormattedTimeSpent() }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $attempt->completed_at->format('d.m.Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Versuche') }}</h3>
                                        <p class="mt-2 text-gray-500">{{ __('Dieser Test wurde noch von niemandem absolviert.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($this->attempts->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $this->attempts->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Question Analysis -->
        <div>
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Fragenanalyse') }}</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($this->questionStats as $index => $question)
                        <div class="px-6 py-4">
                            <div class="flex items-start justify-between">
                                <p class="text-sm font-medium text-gray-900">{{ __('Frage') }} {{ $index + 1 }}</p>
                                <span class="ml-2 rounded-full px-2 py-0.5 text-xs font-medium {{ $question['success_rate'] >= 70 ? 'bg-green-100 text-green-800' : ($question['success_rate'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $question['success_rate'] }}%
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">{{ $question['question_text'] }}</p>
                            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-gray-200">
                                <div class="h-full rounded-full {{ $question['success_rate'] >= 70 ? 'bg-green-500' : ($question['success_rate'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $question['success_rate'] }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-400">{{ $question['correct'] }}/{{ $question['total_responses'] }} {{ __('richtig') }}</p>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">
                            {{ __('Keine Fragen vorhanden.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Test Info -->
            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">{{ __('Testeinstellungen') }}</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Bestehensgrenze') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $assessment->passing_score_percent }}%</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Zeitlimit') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $assessment->time_limit_minutes ? $assessment->time_limit_minutes . ' Min.' : __('Kein Limit') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Max. Versuche') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $assessment->max_attempts ?? __('Unbegrenzt') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Fragen') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $assessment->questionCount() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
