<div>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('admin.users.index') }}" wire:navigate class="hover:text-gray-700">{{ __('Benutzer') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $user->name }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-full object-cover">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-gray-500">{{ $user->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button
                wire:click="toggleStatus"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                @if($user->is_active)
                    <span class="h-2 w-2 rounded-full bg-green-500"></span>
                    {{ __('Aktiv') }}
                @else
                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                    {{ __('Inaktiv') }}
                @endif
            </button>
            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                {{ __('Bearbeiten') }}
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-4">
                @php $stats = $this->stats; @endphp
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <p class="text-sm text-gray-500">{{ __('Einschreibungen') }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['total_enrollments'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <p class="text-sm text-gray-500">{{ __('Abgeschlossen') }}</p>
                    <p class="mt-1 text-2xl font-bold text-green-600">{{ $stats['completed_enrollments'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <p class="text-sm text-gray-500">{{ __('Punkte') }}</p>
                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ number_format($stats['total_points']) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <p class="text-sm text-gray-500">{{ __('Lernzeit') }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ floor($stats['total_time'] / 3600) }}h</p>
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Einschreibungen') }}</h2>
                <div class="divide-y divide-gray-100">
                    @forelse($this->enrollments as $enrollment)
                        <div class="flex items-center justify-between py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-gray-900">{{ $enrollment->learningPath->title }}</p>
                                <p class="text-sm text-gray-500">{{ $enrollment->created_at->format('d.m.Y') }}</p>
                            </div>
                            <div class="ml-4 flex items-center gap-4">
                                <div class="text-right">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-20 overflow-hidden rounded-full bg-gray-200">
                                            <div class="h-full rounded-full bg-indigo-600" style="width: {{ $enrollment->progress_percent }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $enrollment->progress_percent }}%</span>
                                    </div>
                                </div>
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                    @if($enrollment->status->value === 'active') bg-blue-100 text-blue-700
                                    @elseif($enrollment->status->value === 'completed') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $enrollment->status->value === 'active' ? __('Aktiv') : ($enrollment->status->value === 'completed' ? __('Abgeschlossen') : __('Pausiert')) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-gray-500">{{ __('Keine Einschreibungen vorhanden') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- User Details -->
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Details') }}</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('Rolle') }}</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                @if($user->role->value === 'admin') bg-red-100 text-red-700
                                @elseif($user->role->value === 'instructor') bg-blue-100 text-blue-700
                                @else bg-green-100 text-green-700
                                @endif">
                                {{ $user->role->label() }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('Team') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900">
                            {{ $user->currentTeam?->name ?? __('Kein Team') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('Registriert') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900">
                            {{ $user->created_at->format('d.m.Y H:i') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('Letzte Aktualisierung') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900">
                            {{ $user->updated_at->format('d.m.Y H:i') }}
                        </dd>
                    </div>
                    @if($user->email_verified_at)
                        <div>
                            <dt class="text-sm text-gray-500">{{ __('E-Mail verifiziert') }}</dt>
                            <dd class="mt-1 font-medium text-green-600">
                                {{ $user->email_verified_at->format('d.m.Y H:i') }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Teams -->
            @if($user->teams->count() > 0)
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Teams') }}</h2>
                    <ul class="space-y-2">
                        @foreach($user->teams as $team)
                            <li class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                <span class="font-medium text-gray-900">{{ $team->name }}</span>
                                @if($team->id === $user->current_team_id)
                                    <span class="text-xs text-indigo-600">{{ __('Aktuell') }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
