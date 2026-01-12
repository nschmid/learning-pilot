<div>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-teal-600 transition">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('admin.teams.index') }}" wire:navigate class="hover:text-teal-600 transition">{{ __('Teams') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $team->name }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-purple-100">
                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $team->name }}</h1>
                <p class="text-gray-500">{{ __('Erstellt am :date', ['date' => $team->created_at->format('d.m.Y')]) }}</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    @php $stats = $this->stats; @endphp
    <div class="mb-8 grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Mitglieder') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['total_members'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Einschreibungen') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['total_enrollments'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Aktiv') }}</p>
            <p class="mt-1 text-2xl font-bold text-blue-600">{{ $stats['active_enrollments'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Abgeschlossen') }}</p>
            <p class="mt-1 text-2xl font-bold text-green-600">{{ $stats['completed_enrollments'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Durchschn. Fortschritt') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['avg_progress'] }}%</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Lernzeit') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['total_time_hours'] }}h</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Members -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Mitglieder') }} ({{ $this->members->count() }})</h2>
            <div class="divide-y divide-gray-100">
                @forelse($this->members as $member)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}" class="h-10 w-10 rounded-full object-cover">
                            <div>
                                <p class="font-medium text-gray-900">{{ $member->name }}</p>
                                <p class="text-sm text-gray-500">{{ $member->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                @if($member->role->value === 'admin') bg-red-100 text-red-700
                                @elseif($member->role->value === 'instructor') bg-blue-100 text-blue-700
                                @else bg-green-100 text-green-700
                                @endif">
                                {{ $member->role->label() }}
                            </span>
                            @if($member->id === $team->user_id)
                                <span class="inline-flex rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-700">
                                    {{ __('Inhaber') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="py-4 text-center text-sm text-gray-500">{{ __('Keine Mitglieder') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Team Info -->
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Team-Inhaber') }}</h2>
                @if($team->owner)
                    <div class="flex items-center gap-3">
                        <img src="{{ $team->owner->profile_photo_url }}" alt="{{ $team->owner->name }}" class="h-12 w-12 rounded-full object-cover">
                        <div>
                            <p class="font-medium text-gray-900">{{ $team->owner->name }}</p>
                            <p class="text-sm text-gray-500">{{ $team->owner->email }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('Kein Inhaber') }}</p>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Details') }}</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('Team-ID') }}</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $team->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('Erstellt am') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $team->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @if($team->personal_team)
                        <div>
                            <dt class="text-sm text-gray-500">{{ __('Typ') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ __('Persönliches Team') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <!-- Recent Enrollments -->
    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Letzte Einschreibungen') }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Mitglied') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Lernpfad') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Fortschritt') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($this->recentEnrollments as $enrollment)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $enrollment->user->profile_photo_url }}" alt="{{ $enrollment->user->name }}" class="h-6 w-6 rounded-full object-cover">
                                    <span class="text-sm text-gray-900">{{ $enrollment->user->name }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ Str::limit($enrollment->learningPath->title, 30) }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-16 overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-full rounded-full bg-teal-600" style="width: {{ $enrollment->progress_percent }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $enrollment->progress_percent }}%</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                    @if($enrollment->status->value === 'active') bg-blue-100 text-blue-700
                                    @elseif($enrollment->status->value === 'completed') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $enrollment->status->value === 'active' ? __('Aktiv') : ($enrollment->status->value === 'completed' ? __('Abgeschlossen') : __('Pausiert')) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('Keine Einschreibungen') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
