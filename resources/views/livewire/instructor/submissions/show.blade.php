<div class="mx-auto max-w-4xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('instructor.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.submissions.index') }}" wire:navigate class="hover:text-gray-700">{{ __('Einreichungen') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $submission->task->title }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <div class="mb-2 flex items-center gap-2">
                @switch($submission->status->value)
                    @case('pending')
                        <span class="inline-flex rounded-full bg-yellow-100 px-2.5 py-1 text-sm font-medium text-yellow-800">{{ __('Ausstehend') }}</span>
                        @break
                    @case('reviewed')
                        <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-sm font-medium text-green-800">{{ __('Bewertet') }}</span>
                        @break
                    @case('revision_requested')
                        <span class="inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-sm font-medium text-orange-800">{{ __('Überarbeitung') }}</span>
                        @break
                @endswitch
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $submission->task->title }}</h1>
            <p class="mt-1 text-gray-500">
                {{ $submission->task->step->module->learningPath->title }} &bull;
                {{ $submission->task->step->module->title }}
            </p>
        </div>
        @if($submission->status->value === 'pending')
            <button
                wire:click="review"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ __('Jetzt bewerten') }}
            </button>
        @else
            <button
                wire:click="review"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                {{ __('Bewertung bearbeiten') }}
            </button>
        @endif
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Submission Content -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Eingereichte Lösung') }}</h2>
                </div>
                <div class="p-6">
                    @if($submission->content)
                        <div class="prose prose-indigo max-w-none">
                            {!! nl2br(e($submission->content)) !!}
                        </div>
                    @else
                        <p class="text-gray-500">{{ __('Kein Textinhalt eingereicht.') }}</p>
                    @endif

                    @if($submission->getMedia('submissions')->count() > 0)
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <h3 class="mb-4 font-medium text-gray-900">{{ __('Dateien') }}</h3>
                            <div class="space-y-2">
                                @foreach($submission->getMedia('submissions') as $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank" class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-gray-900">{{ $media->file_name }}</p>
                                            <p class="text-xs text-gray-500">{{ number_format($media->size / 1024, 1) }} KB</p>
                                        </div>
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Task Instructions -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Aufgabenstellung') }}</h2>
                </div>
                <div class="p-6">
                    <div class="prose prose-indigo max-w-none text-gray-600">
                        {!! nl2br(e($submission->task->instructions)) !!}
                    </div>

                    @if($submission->task->rubric && count($submission->task->rubric) > 0)
                        <div class="mt-6 border-t border-gray-200 pt-6">
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
                                        @foreach($submission->task->rubric as $criterion)
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

            <!-- Feedback -->
            @if($submission->feedback)
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Feedback') }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="prose prose-indigo max-w-none">
                            {!! nl2br(e($submission->feedback)) !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Student Info -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">{{ __('Teilnehmer') }}</h3>
                <div class="flex items-center gap-3">
                    @if($submission->enrollment->user->profile_photo_url)
                        <img class="h-12 w-12 rounded-full object-cover" src="{{ $submission->enrollment->user->profile_photo_url }}" alt="">
                    @else
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-sm font-medium text-indigo-600">
                            {{ substr($submission->enrollment->user->name, 0, 2) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-medium text-gray-900">{{ $submission->enrollment->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $submission->enrollment->user->email }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('instructor.students.show', $submission->enrollment->id) }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-800">
                        {{ __('Teilnehmer-Profil anzeigen') }} &rarr;
                    </a>
                </div>
            </div>

            <!-- Submission Details -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">{{ __('Details') }}</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Eingereicht') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $submission->submitted_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @if($submission->reviewed_at)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">{{ __('Bewertet') }}</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $submission->reviewed_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    @endif
                    @if($submission->reviewer)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">{{ __('Bewertet von') }}</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $submission->reviewer->name }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Score -->
            @if($submission->score !== null)
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 font-semibold text-gray-900">{{ __('Bewertung') }}</h3>
                    <div class="text-center">
                        <p class="text-4xl font-bold {{ $submission->scorePercent() >= 60 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $submission->score }}/{{ $submission->task->max_points }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">{{ $submission->scorePercent() }}%</p>
                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-gray-200">
                            <div class="h-full rounded-full {{ $submission->scorePercent() >= 60 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $submission->scorePercent() }}%"></div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">{{ __('Aktionen') }}</h3>
                <div class="space-y-2">
                    <a href="{{ route('instructor.tasks.show', $submission->task->id) }}" wire:navigate class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        {{ __('Alle Einreichungen für diese Aufgabe') }}
                    </a>
                    <a href="{{ route('instructor.paths.show', $submission->task->step->module->learningPath->slug) }}" wire:navigate class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        {{ __('Zum Lernpfad') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
