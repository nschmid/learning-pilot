<div class="mx-auto max-w-4xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('learner.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('learner.task.show', $task->id) }}" wire:navigate class="hover:text-gray-700">{{ $task->title }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ __('Einreichung') }}</span>
    </nav>

    <!-- Submission Card -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ __('Einreichung Details') }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ __('Eingereicht am') }} {{ $submission->submitted_at->format('d.m.Y H:i') }}
                    </p>
                </div>

                <!-- Status Badge -->
                @switch($submission->status->value)
                    @case('pending')
                        <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Wartet auf Bewertung') }}
                        </span>
                        @break
                    @case('reviewed')
                        <span class="inline-flex items-center gap-1 rounded-full {{ $submission->scorePercent() >= 60 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-3 py-1 text-sm font-medium">
                            @if($submission->scorePercent() >= 60)
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
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Score (if reviewed) -->
            @if($submission->isReviewed())
                <div class="mb-6 rounded-lg {{ $submission->scorePercent() >= 60 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} p-6 text-center">
                    <div class="text-4xl font-bold {{ $submission->scorePercent() >= 60 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $submission->score }}/{{ $task->max_points }}
                    </div>
                    <div class="text-lg {{ $submission->scorePercent() >= 60 ? 'text-green-700' : 'text-red-700' }}">
                        {{ number_format($submission->scorePercent(), 1) }}%
                    </div>
                    @if($submission->reviewer)
                        <p class="mt-2 text-sm text-gray-500">
                            {{ __('Bewertet von') }} {{ $submission->reviewer->name }}
                            {{ __('am') }} {{ $submission->reviewed_at->format('d.m.Y H:i') }}
                        </p>
                    @endif
                </div>
            @endif

            <!-- Feedback -->
            @if($submission->feedback)
                <div class="mb-6">
                    <h3 class="mb-3 text-lg font-semibold text-gray-900">{{ __('Feedback') }}</h3>
                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <div class="prose prose-sm prose-blue max-w-none">
                            {!! nl2br(e($submission->feedback)) !!}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submitted Content -->
            <div class="mb-6">
                <h3 class="mb-3 text-lg font-semibold text-gray-900">{{ __('Deine Antwort') }}</h3>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <div class="prose prose-sm max-w-none">
                        {!! nl2br(e($submission->content)) !!}
                    </div>
                </div>
            </div>

            <!-- Attached Files -->
            @if($submission->getMedia('submissions')->count() > 0)
                <div class="mb-6">
                    <h3 class="mb-3 text-lg font-semibold text-gray-900">{{ __('Angehängte Dateien') }}</h3>
                    <ul class="space-y-2">
                        @foreach($submission->getMedia('submissions') as $media)
                            <li class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $media->file_name }}</p>
                                        <p class="text-sm text-gray-500">{{ number_format($media->size / 1024, 1) }} KB</p>
                                    </div>
                                </div>
                                <a
                                    href="{{ $media->getUrl() }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 text-sm text-orange-600 hover:text-orange-800"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    {{ __('Herunterladen') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Task Reference -->
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <h4 class="mb-2 font-medium text-gray-900">{{ __('Aufgabe') }}</h4>
                <p class="text-gray-700">{{ $task->title }}</p>
                <a
                    href="{{ route('learner.task.show', $task->id) }}"
                    wire:navigate
                    class="mt-2 inline-flex items-center gap-1 text-sm text-orange-600 hover:text-orange-800"
                >
                    {{ __('Aufgabe ansehen') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Back Link -->
    <div class="mt-8">
        <a
            href="{{ route('learner.task.show', $task->id) }}"
            wire:navigate
            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ __('Zurück zur Aufgabe') }}
        </a>
    </div>
</div>
