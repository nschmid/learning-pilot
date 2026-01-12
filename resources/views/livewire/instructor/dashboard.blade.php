<div>
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Willkommen zurück, :name', ['name' => Auth::user()->name]) }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Hier ist eine Übersicht deiner Lernpfade und Aktivitäten.') }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Paths -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Lernpfade') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $this->totalPaths }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $this->publishedPaths }} {{ __('veröffentlicht') }}</p>
                </div>
                <div class="rounded-lg bg-teal-100 p-3">
                    <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Enrollments -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Einschreibungen') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $this->totalEnrollments }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $this->activeStudents }} {{ __('aktiv diese Woche') }}</p>
                </div>
                <div class="rounded-lg bg-green-100 p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Submissions -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Offene Einreichungen') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $this->pendingSubmissionsCount }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ __('warten auf Bewertung') }}</p>
                </div>
                <div class="rounded-lg bg-yellow-100 p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Completion -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Durchschn. Fortschritt') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $this->averageCompletion }}%</p>
                    <p class="mt-1 text-sm text-gray-500">{{ __('über alle Kurse') }}</p>
                </div>
                <div class="rounded-lg bg-purple-100 p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Recent Paths -->
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="font-semibold text-gray-900">{{ __('Meine Lernpfade') }}</h2>
                <a href="{{ route('instructor.paths.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                    {{ __('Alle anzeigen') }}
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($this->paths as $path)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('instructor.paths.show', $path) }}" class="font-medium text-gray-900 hover:text-orange-600">
                                {{ $path->title }}
                            </a>
                            <div class="mt-1 flex items-center gap-4 text-sm text-gray-500">
                                <span>{{ $path->modules_count }} {{ __('Module') }}</span>
                                <span>{{ $path->steps_count }} {{ __('Schritte') }}</span>
                                <span>{{ $path->enrollments_count }} {{ __('Teilnehmer') }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            @if($path->is_published)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                    {{ __('Veröffentlicht') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">
                                    {{ __('Entwurf') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <p>{{ __('Du hast noch keine Lernpfade erstellt.') }}</p>
                        <a href="{{ route('instructor.paths.create') }}" class="mt-2 inline-block text-orange-600 hover:text-orange-800">
                            {{ __('Ersten Lernpfad erstellen') }}
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Submissions -->
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="font-semibold text-gray-900">{{ __('Offene Einreichungen') }}</h2>
                <a href="{{ route('instructor.submissions.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                    {{ __('Alle anzeigen') }}
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($this->pendingSubmissions as $submission)
                    <a href="{{ route('instructor.submissions.review', $submission) }}" class="flex items-center justify-between px-6 py-4 hover:bg-gray-50">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900">{{ $submission->enrollment->user->name }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $submission->task->title }}</p>
                        </div>
                        <div class="ml-4 text-right">
                            <span class="text-sm text-gray-500">{{ $submission->submitted_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="mt-2">{{ __('Keine offenen Einreichungen') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Completions -->
    @if($this->recentCompletions->isNotEmpty())
    <div class="mt-8 rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="font-semibold text-gray-900">{{ __('Kürzliche Abschlüsse') }}</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($this->recentCompletions as $enrollment)
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-4">
                        <img
                            src="{{ $enrollment->user->profile_photo_url }}"
                            alt="{{ $enrollment->user->name }}"
                            class="h-10 w-10 rounded-full object-cover"
                        >
                        <div>
                            <p class="font-medium text-gray-900">{{ $enrollment->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $enrollment->learningPath->title }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('Abgeschlossen') }}
                        </span>
                        <span class="text-sm text-gray-500">{{ $enrollment->completed_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <a
            href="{{ route('instructor.paths.create') }}"
            class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-6 hover:border-orange-500 hover:shadow-sm"
        >
            <div class="rounded-lg bg-orange-100 p-3">
                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900">{{ __('Neuer Lernpfad') }}</p>
                <p class="text-sm text-gray-500">{{ __('Erstelle einen neuen Kurs') }}</p>
            </div>
        </a>

        <a
            href="{{ route('instructor.submissions.index') }}"
            class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-6 hover:border-orange-500 hover:shadow-sm"
        >
            <div class="rounded-lg bg-yellow-100 p-3">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900">{{ __('Einreichungen prüfen') }}</p>
                <p class="text-sm text-gray-500">{{ $this->pendingSubmissionsCount }} {{ __('warten') }}</p>
            </div>
        </a>

        <a
            href="{{ route('instructor.analytics.index') }}"
            class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-6 hover:border-orange-500 hover:shadow-sm"
        >
            <div class="rounded-lg bg-purple-100 p-3">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900">{{ __('Statistiken') }}</p>
                <p class="text-sm text-gray-500">{{ __('Analysiere Fortschritte') }}</p>
            </div>
        </a>
    </div>
</div>
