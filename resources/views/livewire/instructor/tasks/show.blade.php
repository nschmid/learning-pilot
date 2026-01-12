<div class="mx-auto max-w-6xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('instructor.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.paths.show', $task->step->module->learningPath->slug) }}" wire:navigate class="hover:text-gray-700">
            {{ $task->step->module->learningPath->title }}
        </a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $task->title }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <div class="mb-2 flex items-center gap-2">
                <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-2.5 py-1 text-sm font-medium text-orange-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    {{ $task->task_type->label() }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
            <p class="mt-1 text-gray-500">{{ $task->step->module->title }} &bull; {{ $task->step->title }}</p>
        </div>
        <button
            wire:click="editTask"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            {{ __('Bearbeiten') }}
        </button>
    </div>

    <!-- Stats -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Max. Punkte') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $task->max_points }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Einreichungen') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Ausstehend') }}</p>
            <p class="text-2xl font-bold text-orange-600">{{ $this->stats['pending'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Ø Punktzahl') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->stats['avg_score'] ?? '-' }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Bestehensrate') }}</p>
            <p class="text-2xl font-bold text-green-600">{{ $this->stats['pass_rate'] }}%</p>
        </div>
    </div>

    <!-- Task Details -->
    <div class="mb-8 rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Aufgabenstellung') }}</h2>
        </div>
        <div class="p-6">
            <div class="prose prose-indigo max-w-none">
                {!! nl2br(e($task->instructions)) !!}
            </div>

            @if($task->rubric && count($task->rubric) > 0)
                <div class="mt-6">
                    <h3 class="mb-3 font-medium text-gray-900">{{ __('Bewertungskriterien') }}</h3>
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">{{ __('Kriterium') }}</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium uppercase text-gray-500">{{ __('Punkte') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($task->rubric as $criterion)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $criterion['name'] ?? $criterion }}</td>
                                        <td class="px-4 py-2 text-right text-sm font-medium text-gray-900">{{ $criterion['points'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Submissions -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Einreichungen') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Teilnehmer') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Punkte') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Eingereicht') }}</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($this->submissions as $submission)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 flex-shrink-0">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-teal-100 text-xs font-medium text-teal-600">
                                            {{ substr($submission->enrollment->user->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $submission->enrollment->user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @switch($submission->status->value)
                                    @case('pending')
                                        <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">{{ __('Ausstehend') }}</span>
                                        @break
                                    @case('reviewed')
                                        <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">{{ __('Bewertet') }}</span>
                                        @break
                                    @case('revision_requested')
                                        <span class="inline-flex rounded-full bg-orange-100 px-2 py-1 text-xs font-semibold text-orange-800">{{ __('Überarbeitung') }}</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                @if($submission->score !== null)
                                    {{ $submission->score }}/{{ $task->max_points }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $submission->submitted_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <button
                                    wire:click="reviewSubmission('{{ $submission->id }}')"
                                    class="text-teal-600 hover:text-teal-900"
                                >
                                    {{ $submission->status->value === 'pending' ? __('Bewerten') : __('Ansehen') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Einreichungen') }}</h3>
                                <p class="mt-2 text-gray-500">{{ __('Für diese Aufgabe wurden noch keine Lösungen eingereicht.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->submissions->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $this->submissions->links() }}
            </div>
        @endif
    </div>
</div>
