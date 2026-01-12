<div>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-teal-600 transition">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('admin.paths.index') }}" wire:navigate class="hover:text-teal-600 transition">{{ __('Lernpfade') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $path->title }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex gap-4">
            @if($path->getFirstMediaUrl('thumbnail'))
                <img src="{{ $path->getFirstMediaUrl('thumbnail') }}" alt="{{ $path->title }}" class="h-20 w-32 rounded-xl object-cover">
            @else
                <div class="flex h-20 w-32 items-center justify-center rounded-xl bg-gray-100">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $path->title }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    @if($path->category)
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">
                            {{ $path->category->name }}
                        </span>
                    @endif
                    @if($path->difficulty)
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                            {{ $path->difficulty->label() }}
                        </span>
                    @endif
                    <span class="text-sm text-gray-500">
                        {{ __('von') }} {{ $path->creator?->name ?? __('Unbekannt') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button
                wire:click="togglePublished"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
            >
                @if($path->is_published)
                    <span class="h-2 w-2 rounded-full bg-green-500"></span>
                    {{ __('Veröffentlicht') }}
                @else
                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                    {{ __('Entwurf') }}
                @endif
            </button>
        </div>
    </div>

    <!-- Stats -->
    @php $stats = $this->stats; @endphp
    <div class="mb-8 grid gap-4 sm:grid-cols-5">
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Einschreibungen') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total_enrollments']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Aktiv') }}</p>
            <p class="mt-1 text-2xl font-bold text-blue-600">{{ number_format($stats['active_enrollments']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Abgeschlossen') }}</p>
            <p class="mt-1 text-2xl font-bold text-green-600">{{ number_format($stats['completed_enrollments']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Durchschn. Fortschritt') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['avg_progress'] }}%</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ __('Durchschn. Zeit') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['avg_time'] }}h</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Description -->
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Beschreibung') }}</h2>
                <p class="text-gray-700">{{ $path->description }}</p>

                @if($path->objectives && count($path->objectives) > 0)
                    <h3 class="mb-3 mt-6 font-semibold text-gray-900">{{ __('Lernziele') }}</h3>
                    <ul class="space-y-2">
                        @foreach($path->objectives as $objective)
                            <li class="flex items-start gap-2">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">{{ $objective }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Modules -->
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Module') }} ({{ $path->modules->count() }})</h2>
                <div class="space-y-3">
                    @forelse($this->moduleStats as $module)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $module['title'] }}</p>
                                <p class="text-sm text-gray-500">{{ $module['steps_count'] }} {{ __('Schritte') }}</p>
                            </div>
                            <div class="text-right text-sm">
                                <p class="text-gray-700">{{ $module['total_points'] }} {{ __('Punkte') }}</p>
                                <p class="text-gray-500">{{ $module['total_minutes'] }} {{ __('Min.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-sm text-gray-500">{{ __('Keine Module vorhanden') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Letzte Einschreibungen') }}</h2>
                <div class="divide-y divide-gray-100">
                    @forelse($this->recentEnrollments as $enrollment)
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $enrollment->user->profile_photo_url }}" alt="{{ $enrollment->user->name }}" class="h-8 w-8 rounded-full object-cover">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $enrollment->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $enrollment->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-16 overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-full rounded-full bg-teal-600" style="width: {{ $enrollment->progress_percent }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $enrollment->progress_percent }}%</span>
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
                        <p class="py-4 text-center text-sm text-gray-500">{{ __('Keine Einschreibungen vorhanden') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Details -->
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Details') }}</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('Erstellt von') }}</dt>
                        <dd class="mt-1 flex items-center gap-2">
                            <img src="{{ $path->creator?->profile_photo_url }}" alt="{{ $path->creator?->name }}" class="h-6 w-6 rounded-full object-cover">
                            <span class="font-medium text-gray-900">{{ $path->creator?->name ?? __('Unbekannt') }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('Erstellt am') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $path->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('Letzte Aktualisierung') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $path->updated_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @if($path->estimated_hours)
                        <div>
                            <dt class="text-sm text-gray-500">{{ __('Geschätzte Dauer') }}</dt>
                            <dd class="mt-1 font-medium text-gray-900">{{ $path->estimated_hours }} {{ __('Stunden') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('Version') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $path->version ?? 1 }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Tags -->
            @if($path->tags->count() > 0)
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Tags') }}</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($path->tags as $tag)
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-700">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
