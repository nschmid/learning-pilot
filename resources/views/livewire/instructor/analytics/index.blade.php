<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Analytik') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Überblick über deine Lernpfade und Teilnehmer.') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Paths -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-100">
                    <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ __('Lernpfade') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->stats['paths']['total'] }}</p>
                    <p class="text-xs text-gray-400">{{ $this->stats['paths']['published'] }} {{ __('veröffentlicht') }}</p>
                </div>
            </div>
        </div>

        <!-- Enrollments -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ __('Einschreibungen') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->stats['enrollments']['total'] }}</p>
                    <p class="text-xs text-gray-400">{{ $this->stats['enrollments']['active'] }} {{ __('aktiv') }}</p>
                </div>
            </div>
        </div>

        <!-- Completions -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ __('Abschlüsse') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->stats['enrollments']['completed'] }}</p>
                    <p class="text-xs text-gray-400">{{ $this->stats['avg_completion_rate'] }}% {{ __('Rate') }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ __('Ausstehend') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->pendingSubmissions }}</p>
                    <p class="text-xs text-gray-400">{{ __('Bewertungen') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Top Paths -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Top Lernpfade') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->topPaths as $path)
                    <a href="{{ route('instructor.paths.show', $path['slug']) }}" wire:navigate class="flex items-center justify-between px-6 py-4 hover:bg-gray-50">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $path['title'] }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $path['enrollments'] }} {{ __('Teilnehmer') }}
                                @if(!$path['is_published'])
                                    <span class="ml-2 rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ __('Entwurf') }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="ml-4 text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $path['avg_progress'] }}%</p>
                            <p class="text-xs text-gray-500">{{ __('Ø Fortschritt') }}</p>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <p>{{ __('Noch keine Lernpfade erstellt.') }}</p>
                        <a href="{{ route('instructor.paths.create') }}" wire:navigate class="mt-2 inline-block text-teal-600 hover:text-teal-800">
                            {{ __('Ersten Lernpfad erstellen') }}
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Enrollments -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Letzte Einschreibungen') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->recentEnrollments as $enrollment)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $enrollment['user_name'] }}</p>
                            <p class="truncate text-sm text-gray-500">{{ $enrollment['path_title'] }}</p>
                        </div>
                        <div class="ml-4 text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $enrollment['progress'] }}%</p>
                            <p class="text-xs text-gray-400">{{ $enrollment['created_at'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <p>{{ __('Noch keine Einschreibungen.') }}</p>
                    </div>
                @endforelse
            </div>
            @if(count($this->recentEnrollments) > 0)
                <div class="border-t border-gray-200 px-6 py-3">
                    <a href="{{ route('instructor.students.index') }}" wire:navigate class="text-sm text-teal-600 hover:text-teal-800">
                        {{ __('Alle Teilnehmer anzeigen') }} &rarr;
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Schnellzugriff') }}</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('instructor.submissions.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                {{ __('Einreichungen bewerten') }}
                @if($this->pendingSubmissions > 0)
                    <span class="rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800">{{ $this->pendingSubmissions }}</span>
                @endif
            </a>
            <a href="{{ route('instructor.paths.create') }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Neuer Lernpfad') }}
            </a>
            <a href="{{ route('instructor.students.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                {{ __('Teilnehmer verwalten') }}
            </a>
        </div>
    </div>
</div>
