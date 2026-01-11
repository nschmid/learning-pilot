<div class="mx-auto max-w-4xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('learner.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('learner.path.show', $task->step->module->learningPath->slug) }}" wire:navigate class="hover:text-gray-700">
            {{ $task->step->module->learningPath->title }}
        </a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $task->title }}</span>
    </nav>

    <!-- Task Card -->
    <div class="mb-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <!-- Header -->
        <div class="border-b border-gray-200 bg-gradient-to-r from-orange-500 to-amber-600 px-6 py-6 text-white">
            <div class="mb-2 flex items-center gap-2">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span class="font-medium">{{ __('Aufgabe') }}</span>
            </div>
            <h1 class="text-2xl font-bold">{{ $task->title }}</h1>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Task Info -->
            <div class="mb-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-gray-50 p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $task->max_points }}</div>
                    <div class="text-sm text-gray-500">{{ __('Max. Punkte') }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $task->task_type->label() }}</div>
                    <div class="text-sm text-gray-500">{{ __('Aufgabentyp') }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">
                        @if($task->due_days)
                            {{ $task->due_days }} {{ __('Tage') }}
                        @else
                            {{ __('Unbegrenzt') }}
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">{{ __('Bearbeitungszeit') }}</div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mb-6">
                <h3 class="mb-3 text-lg font-semibold text-gray-900">{{ __('Aufgabenstellung') }}</h3>
                <div class="prose prose-orange max-w-none rounded-lg bg-gray-50 p-4">
                    {!! nl2br(e($task->instructions)) !!}
                </div>
            </div>

            <!-- AI Hint Button -->
            @if($this->stepProgressId)
                <div class="mb-6">
                    <livewire:learner.ai.hint-button :step-progress-id="$this->stepProgressId" />
                </div>
            @endif

            <!-- Rubric -->
            @if($task->rubric && count($task->rubric) > 0)
                <div class="mb-6">
                    <h3 class="mb-3 text-lg font-semibold text-gray-900">{{ __('Bewertungskriterien') }}</h3>
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Kriterium') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Punkte') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($task->rubric as $criterion)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $criterion['name'] ?? $criterion }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">{{ $criterion['points'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- File Requirements -->
            @if($task->allowed_file_types)
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <h4 class="mb-2 flex items-center gap-2 font-medium text-blue-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('Dateianforderungen') }}
                    </h4>
                    <p class="text-sm text-blue-700">
                        {{ __('Erlaubte Dateitypen:') }} {{ implode(', ', $task->allowed_file_types) }}<br>
                        {{ __('Maximale Dateigröße:') }} {{ $task->max_file_size_mb ?? 10 }} MB
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Current Status / Latest Submission -->
    @if($this->latestSubmission)
        <div class="mb-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Deine Einreichung') }}</h2>

            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @switch($this->latestSubmission->status->value)
                        @case('pending')
                            <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Wartet auf Bewertung') }}
                            </span>
                            @break
                        @case('reviewed')
                            <span class="inline-flex items-center gap-1 rounded-full {{ $this->hasPassed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-3 py-1 text-sm font-medium">
                                @if($this->hasPassed)
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ __('Bestanden') }}
                                @else
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ __('Nicht bestanden') }}
                                @endif
                            </span>
                            @break
                        @case('revision_requested')
                            <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-sm font-medium text-orange-800">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                {{ __('Überarbeitung erforderlich') }}
                            </span>
                            @break
                    @endswitch
                </div>

                @if($this->latestSubmission->isReviewed())
                    <div class="text-right">
                        <div class="text-2xl font-bold {{ $this->hasPassed ? 'text-green-600' : 'text-red-600' }}">
                            {{ $this->latestSubmission->score }}/{{ $task->max_points }}
                        </div>
                        <div class="text-sm text-gray-500">{{ number_format($this->latestSubmission->scorePercent(), 1) }}%</div>
                    </div>
                @endif
            </div>

            <!-- Feedback -->
            @if($this->latestSubmission->feedback)
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <h4 class="mb-2 font-medium text-gray-900">{{ __('Feedback') }}</h4>
                    <p class="text-gray-700">{{ $this->latestSubmission->feedback }}</p>
                </div>
            @endif

            <!-- Submission Details -->
            <div class="mt-4 text-sm text-gray-500">
                {{ __('Eingereicht am') }} {{ $this->latestSubmission->submitted_at->format('d.m.Y H:i') }}
                @if($this->latestSubmission->reviewed_at)
                    &bull; {{ __('Bewertet am') }} {{ $this->latestSubmission->reviewed_at->format('d.m.Y H:i') }}
                @endif
            </div>
        </div>
    @endif

    <!-- Submit Form -->
    @if($this->canSubmit)
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <button
                    wire:click="toggleSubmitForm"
                    class="flex w-full items-center justify-between"
                >
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $this->latestSubmission ? __('Neue Einreichung') : __('Aufgabe einreichen') }}
                    </h2>
                    <svg
                        class="h-5 w-5 text-gray-400 transition-transform {{ $showSubmitForm ? 'rotate-180' : '' }}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            @if($showSubmitForm)
                <form wire:submit="submit" class="p-6">
                    <!-- Content -->
                    <div class="mb-6">
                        <label for="content" class="mb-2 block text-sm font-medium text-gray-700">
                            {{ __('Deine Antwort') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="content"
                            id="content"
                            rows="8"
                            class="w-full rounded-lg border border-gray-300 p-4 text-gray-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            placeholder="{{ __('Schreibe hier deine Antwort...') }}"
                        ></textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    @if($task->allowed_file_types)
                        <div class="mb-6">
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ __('Dateien anhängen') }}
                            </label>
                            <div class="rounded-lg border-2 border-dashed border-gray-300 p-6 text-center">
                                <input
                                    type="file"
                                    wire:model="files"
                                    multiple
                                    accept="{{ implode(',', array_map(fn($t) => '.' . $t, $task->allowed_file_types)) }}"
                                    class="hidden"
                                    id="file-upload"
                                >
                                <label for="file-upload" class="cursor-pointer">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium text-orange-600 hover:text-orange-500">{{ __('Dateien auswählen') }}</span>
                                        {{ __('oder hierher ziehen') }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ implode(', ', $task->allowed_file_types) }} {{ __('bis') }} {{ $task->max_file_size_mb ?? 10 }}MB
                                    </p>
                                </label>
                            </div>

                            @if(count($files) > 0)
                                <ul class="mt-4 space-y-2">
                                    @foreach($files as $index => $file)
                                        <li class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-2">
                                            <span class="text-sm text-gray-700">{{ $file->getClientOriginalName() }}</span>
                                            <button
                                                type="button"
                                                wire:click="$set('files.{{ $index }}', null)"
                                                class="text-red-500 hover:text-red-700"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-4">
                        <button
                            type="button"
                            wire:click="toggleSubmitForm"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            {{ __('Abbrechen') }}
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-6 py-2 font-semibold text-white transition hover:bg-orange-700 disabled:cursor-wait disabled:opacity-75"
                        >
                            <span wire:loading.remove wire:target="submit">{{ __('Einreichen') }}</span>
                            <span wire:loading wire:target="submit">{{ __('Wird eingereicht...') }}</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @elseif(!$this->enrollment)
        <div class="rounded-xl border border-red-200 bg-red-50 p-6 text-center">
            <p class="text-red-800">{{ __('Du musst für diesen Kurs eingeschrieben sein, um Aufgaben einzureichen.') }}</p>
        </div>
    @elseif($this->latestSubmission && !$task->allow_resubmit)
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 text-center">
            <p class="text-gray-600">{{ __('Du hast diese Aufgabe bereits eingereicht. Erneute Einreichungen sind nicht erlaubt.') }}</p>
        </div>
    @endif

    <!-- Previous Submissions -->
    @if($this->submissions->count() > 1)
        <div class="mt-8 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Alle Einreichungen') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($this->submissions as $submission)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-900">
                                {{ __('Einreichung vom') }} {{ $submission->submitted_at->format('d.m.Y H:i') }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $submission->status->label() }}
                                @if($submission->score !== null)
                                    &bull; {{ $submission->score }}/{{ $task->max_points }} {{ __('Punkte') }}
                                @endif
                            </p>
                        </div>
                        <button
                            wire:click="viewSubmission('{{ $submission->id }}')"
                            class="text-sm text-orange-600 hover:text-orange-800"
                        >
                            {{ __('Details') }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Back to Course -->
    <div class="mt-8">
        <a
            href="{{ route('learner.learn.index', $task->step->module->learningPath->slug) }}"
            wire:navigate
            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ __('Zurück zum Kurs') }}
        </a>
    </div>
</div>
