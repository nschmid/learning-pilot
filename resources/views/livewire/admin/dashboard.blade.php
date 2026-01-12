<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Admin Dashboard') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Plattform-Übersicht und Statistiken') }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Users -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Benutzer gesamt') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($this->totalUsers) }}</p>
                </div>
                <div class="rounded-full bg-teal-100 p-3">
                    <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-sm text-gray-500">
                <span class="font-medium text-green-600">+{{ $this->newUsersThisMonth }}</span>
                {{ __('diesen Monat') }}
            </p>
        </div>

        <!-- Total Teams -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Teams / Schulen') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($this->totalTeams) }}</p>
                </div>
                <div class="rounded-full bg-purple-100 p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Learning Paths -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Lernpfade') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($this->totalPaths) }}</p>
                </div>
                <div class="rounded-full bg-orange-100 p-3">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-sm text-gray-500">
                <span class="font-medium text-green-600">{{ $this->publishedPaths }}</span>
                {{ __('veröffentlicht') }}
            </p>
        </div>

        <!-- Enrollments -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Einschreibungen') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($this->totalEnrollments) }}</p>
                </div>
                <div class="rounded-full bg-green-100 p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 flex gap-4 text-sm">
                <span><span class="font-medium text-blue-600">{{ $this->activeEnrollments }}</span> {{ __('aktiv') }}</span>
                <span><span class="font-medium text-green-600">{{ $this->completedEnrollments }}</span> {{ __('abgeschlossen') }}</span>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="mb-8 grid gap-6 lg:grid-cols-3">
        <!-- Users by Role -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Benutzer nach Rolle') }}</h2>
            <div class="space-y-4">
                @php $roles = $this->usersByRole; @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="rounded-full bg-red-100 p-2">
                            <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">{{ __('Administratoren') }}</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">{{ $roles['admins'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="rounded-full bg-blue-100 p-2">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">{{ __('Kursleiter') }}</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">{{ $roles['instructors'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="rounded-full bg-green-100 p-2">
                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">{{ __('Lernende') }}</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">{{ $roles['learners'] }}</span>
                </div>
            </div>
        </div>

        <!-- Pending Actions -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Offene Aktionen') }}</h2>
            <div class="space-y-4">
                <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between rounded-lg bg-yellow-50 p-4 transition hover:bg-yellow-100">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span class="font-medium text-yellow-800">{{ __('Offene Abgaben') }}</span>
                    </div>
                    <span class="rounded-full bg-yellow-200 px-3 py-1 text-sm font-bold text-yellow-800">{{ $this->pendingSubmissions }}</span>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Schnellzugriff') }}</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.users.create') }}" class="flex flex-col items-center rounded-lg border border-gray-200 p-4 text-center transition hover:border-teal-500 hover:bg-teal-50">
                    <svg class="mb-2 h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">{{ __('Benutzer') }}</span>
                </a>
                <a href="{{ route('admin.teams.create') }}" class="flex flex-col items-center rounded-lg border border-gray-200 p-4 text-center transition hover:border-teal-500 hover:bg-teal-50">
                    <svg class="mb-2 h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">{{ __('Team') }}</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex flex-col items-center rounded-lg border border-gray-200 p-4 text-center transition hover:border-teal-500 hover:bg-teal-50">
                    <svg class="mb-2 h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">{{ __('Kategorien') }}</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex flex-col items-center rounded-lg border border-gray-200 p-4 text-center transition hover:border-teal-500 hover:bg-teal-50">
                    <svg class="mb-2 h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">{{ __('Berichte') }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Third Row -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Recent Users -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Neue Benutzer') }}</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-teal-600 hover:text-teal-800">{{ __('Alle anzeigen') }}</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->recentUsers as $user)
                    <div class="flex items-center gap-4 py-3">
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="truncate text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                @if($user->role->value === 'admin') bg-red-100 text-red-700
                                @elseif($user->role->value === 'instructor') bg-blue-100 text-blue-700
                                @else bg-green-100 text-green-700
                                @endif">
                                {{ $user->role->value === 'admin' ? __('Admin') : ($user->role->value === 'instructor' ? __('Kursleiter') : __('Lernender')) }}
                            </span>
                            <p class="mt-1 text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="py-4 text-center text-sm text-gray-500">{{ __('Keine neuen Benutzer') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Top Learning Paths -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Beliebteste Lernpfade') }}</h2>
                <a href="{{ route('admin.paths.index') }}" class="text-sm font-medium text-teal-600 hover:text-teal-800">{{ __('Alle anzeigen') }}</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->topPaths as $path)
                    <div class="flex items-center gap-4 py-3">
                        @if($path->getFirstMediaUrl('thumbnail'))
                            <img src="{{ $path->getFirstMediaUrl('thumbnail') }}" alt="{{ $path->title }}" class="h-12 w-16 rounded-lg object-cover">
                        @else
                            <div class="flex h-12 w-16 items-center justify-center rounded-lg bg-gray-100">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $path->title }}</p>
                            <p class="text-sm text-gray-500">{{ $path->creator->name ?? __('Unbekannt') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">{{ $path->enrollments_count }}</p>
                            <p class="text-xs text-gray-500">{{ __('Einschreibungen') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="py-4 text-center text-sm text-gray-500">{{ __('Keine Lernpfade vorhanden') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Enrollments -->
    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Letzte Einschreibungen') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Benutzer') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Lernpfad') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Fortschritt') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Datum') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($this->recentEnrollments as $enrollment)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $enrollment->user->profile_photo_url }}" alt="{{ $enrollment->user->name }}" class="h-8 w-8 rounded-full object-cover">
                                    <span class="font-medium text-gray-900">{{ $enrollment->user->name }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ Str::limit($enrollment->learningPath->title, 40) }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                    @if($enrollment->status->value === 'active') bg-blue-100 text-blue-700
                                    @elseif($enrollment->status->value === 'completed') bg-green-100 text-green-700
                                    @elseif($enrollment->status->value === 'paused') bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $enrollment->status->value === 'active' ? __('Aktiv') : ($enrollment->status->value === 'completed' ? __('Abgeschlossen') : ($enrollment->status->value === 'paused' ? __('Pausiert') : __('Abgelaufen'))) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-20 overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-full rounded-full bg-teal-600" style="width: {{ $enrollment->progress_percent }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $enrollment->progress_percent }}%</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $enrollment->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('Keine Einschreibungen vorhanden') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
