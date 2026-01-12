<div>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('instructor.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.students.index') }}" wire:navigate class="hover:text-gray-700">{{ __('Teilnehmer') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $enrollment->user->name }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8 flex items-start justify-between">
        <div class="flex items-center gap-4">
            @if($enrollment->user->profile_photo_url)
                <img class="h-16 w-16 rounded-full object-cover" src="{{ $enrollment->user->profile_photo_url }}" alt="">
            @else
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-teal-100 text-xl font-medium text-teal-600">
                    {{ substr($enrollment->user->name, 0, 2) }}
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $enrollment->user->name }}</h1>
                <p class="text-gray-500">{{ $enrollment->user->email }}</p>
                <p class="mt-1 text-sm text-gray-400">{{ __('Eingeschrieben am') }}: {{ $enrollment->created_at->format('d.m.Y') }}</p>
            </div>
        </div>
        <div>
            @switch($enrollment->status->value)
                @case('active')
                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">{{ __('Aktiv') }}</span>
                    @break
                @case('completed')
                    <span class="inline-flex rounded-full bg-purple-100 px-3 py-1 text-sm font-semibold text-purple-800">{{ __('Abgeschlossen') }}</span>
                    @break
                @case('paused')
                    <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-800">{{ __('Pausiert') }}</span>
                    @break
            @endswitch
        </div>
    </div>

    <!-- Learning Path Info -->
    <div class="mb-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $enrollment->learningPath->title }}</h2>
                <p class="text-sm text-gray-500">{{ __('Lernpfad') }}</p>
            </div>
            <a href="{{ route('instructor.paths.show', $enrollment->learningPath->slug) }}" wire:navigate class="text-sm text-teal-600 hover:text-teal-800">
                {{ __('Lernpfad anzeigen') }} &rarr;
            </a>
        </div>
        <div class="mt-4">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">{{ __('Fortschritt') }}</span>
                <span class="font-medium text-gray-900">{{ $enrollment->progress_percent }}%</span>
            </div>
            <div class="mt-2 h-3 overflow-hidden rounded-full bg-gray-200">
                <div class="h-full rounded-full bg-teal-600 transition-all" style="width: {{ $enrollment->progress_percent }}%"></div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Lernzeit') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->stats['total_time'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Punkte') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->stats['points_earned'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Aufgaben') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->stats['tasks_submitted'] }}</p>
            @if($this->stats['tasks_pending'] > 0)
                <p class="text-xs text-orange-600">{{ $this->stats['tasks_pending'] }} {{ __('ausstehend') }}</p>
            @endif
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Tests') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->stats['assessments_passed'] }}/{{ $this->stats['assessments_taken'] }}</p>
            <p class="text-xs text-gray-400">{{ __('bestanden') }}</p>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Module Progress -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Modulfortschritt') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->moduleProgress as $module)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-gray-900">{{ $module['title'] }}</p>
                            <span class="text-sm text-gray-500">{{ $module['completed_steps'] }}/{{ $module['total_steps'] }}</span>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                            <div class="h-full rounded-full {{ $module['progress'] === 100 ? 'bg-green-500' : 'bg-teal-600' }}" style="width: {{ $module['progress'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        {{ __('Keine Module vorhanden.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="space-y-8">
            <!-- Recent Submissions -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Letzte Einreichungen') }}</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($this->recentSubmissions as $submission)
                        <div class="flex items-center justify-between px-6 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $submission['task_title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $submission['submitted_at'] }}</p>
                            </div>
                            <div class="ml-4 flex items-center gap-3">
                                @if($submission['status'] === 'pending')
                                    <span class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">{{ __('Ausstehend') }}</span>
                                @elseif($submission['status'] === 'reviewed')
                                    <span class="text-sm font-medium text-gray-900">{{ $submission['score'] }}/{{ $submission['max_points'] }}</span>
                                @endif
                                <button wire:click="viewSubmission('{{ $submission['id'] }}')" class="text-sm text-teal-600 hover:text-teal-800">
                                    {{ __('Ansehen') }}
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">
                            {{ __('Keine Einreichungen.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Assessment Results -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Testergebnisse') }}</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($this->assessmentResults as $result)
                        <div class="flex items-center justify-between px-6 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $result['assessment_title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $result['started_at'] }}</p>
                            </div>
                            <div class="ml-4 flex items-center gap-2">
                                <span class="text-sm font-medium {{ $result['passed'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $result['score_percent'] }}%
                                </span>
                                @if($result['passed'])
                                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">
                            {{ __('Keine Tests absolviert.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
