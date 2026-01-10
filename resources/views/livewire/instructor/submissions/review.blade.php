<div class="mx-auto max-w-4xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('instructor.dashboard') }}" class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.submissions.index') }}" class="hover:text-gray-700">{{ __('Einreichungen') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ __('Bewertung') }}</span>
    </nav>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Submission Card -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                <!-- Header -->
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">{{ $submission->task->title }}</h1>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $submission->task->step->module->learningPath->title }}
                            </p>
                        </div>
                        @switch($submission->status->value)
                            @case('pending')
                                <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
                                    {{ __('Ausstehend') }}
                                </span>
                                @break
                            @case('reviewed')
                                <span class="inline-flex items-center gap-1 rounded-full {{ $submission->scorePercent() >= 60 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-3 py-1 text-sm font-medium">
                                    {{ $submission->score }}/{{ $submission->task->max_points }} {{ __('Punkte') }}
                                </span>
                                @break
                            @case('revision_requested')
                                <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-sm font-medium text-orange-800">
                                    {{ __('Überarbeitung angefordert') }}
                                </span>
                                @break
                        @endswitch
                    </div>
                </div>

                <!-- Task Instructions -->
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="mb-2 text-sm font-medium text-gray-500">{{ __('Aufgabenstellung') }}</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($submission->task->instructions)) !!}
                    </div>
                </div>

                <!-- Submission Content -->
                <div class="px-6 py-4">
                    <h3 class="mb-2 text-sm font-medium text-gray-500">{{ __('Eingereichte Antwort') }}</h3>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="prose prose-sm max-w-none">
                            {!! nl2br(e($submission->content)) !!}
                        </div>
                    </div>
                </div>

                <!-- Attached Files -->
                @if($submission->getMedia('submissions')->count() > 0)
                    <div class="border-t border-gray-200 px-6 py-4">
                        <h3 class="mb-3 text-sm font-medium text-gray-500">{{ __('Angehängte Dateien') }}</h3>
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
                                        {{ __('Öffnen') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Student Info -->
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h3 class="mb-4 font-semibold text-gray-900">{{ __('Teilnehmer') }}</h3>
                <div class="flex items-center gap-4">
                    <img
                        src="{{ $submission->enrollment->user->profile_photo_url }}"
                        alt="{{ $submission->enrollment->user->name }}"
                        class="h-12 w-12 rounded-full object-cover"
                    >
                    <div>
                        <p class="font-medium text-gray-900">{{ $submission->enrollment->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $submission->enrollment->user->email }}</p>
                    </div>
                </div>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">{{ __('Eingereicht am') }}</dt>
                        <dd class="text-gray-900">{{ $submission->submitted_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">{{ __('Kursfortschritt') }}</dt>
                        <dd class="text-gray-900">{{ number_format($submission->enrollment->progress_percent, 0) }}%</dd>
                    </div>
                </dl>
            </div>

            <!-- Grading Form -->
            @if($submission->status === \App\Enums\SubmissionStatus::Pending)
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h3 class="mb-4 font-semibold text-gray-900">{{ __('Bewertung') }}</h3>

                    <!-- Score -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Punkte') }} (max. {{ $submission->task->max_points }})
                        </label>
                        <div class="mt-1 flex items-center gap-2">
                            <input
                                wire:model="score"
                                type="number"
                                min="0"
                                max="{{ $submission->task->max_points }}"
                                class="block w-24 rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                            >
                            <span class="text-gray-500">/ {{ $submission->task->max_points }}</span>
                        </div>
                        @error('score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Feedback -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Feedback') }}
                        </label>
                        <textarea
                            wire:model="feedback"
                            rows="4"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="{{ __('Feedback für den Teilnehmer...') }}"
                        ></textarea>
                        @error('feedback')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        <button
                            wire:click="approve"
                            wire:loading.attr="disabled"
                            class="w-full rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="approve">
                                {{ __('Bewertung speichern') }}
                            </span>
                            <span wire:loading wire:target="approve">
                                {{ __('Wird gespeichert...') }}
                            </span>
                        </button>

                        <button
                            wire:click="requestRevision"
                            wire:loading.attr="disabled"
                            class="w-full rounded-lg border border-orange-300 px-4 py-2 text-sm font-medium text-orange-600 hover:bg-orange-50 disabled:opacity-50"
                        >
                            {{ __('Überarbeitung anfordern') }}
                        </button>
                    </div>
                </div>
            @else
                <!-- Already Reviewed -->
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h3 class="mb-4 font-semibold text-gray-900">{{ __('Bewertung') }}</h3>

                    @if($submission->status === \App\Enums\SubmissionStatus::Reviewed)
                        <div class="mb-4 text-center">
                            <div class="text-3xl font-bold {{ $submission->scorePercent() >= 60 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $submission->score }}/{{ $submission->task->max_points }}
                            </div>
                            <p class="text-sm text-gray-500">{{ number_format($submission->scorePercent(), 1) }}%</p>
                        </div>
                    @endif

                    @if($submission->feedback)
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Feedback') }}</h4>
                            <p class="mt-1 text-gray-700">{{ $submission->feedback }}</p>
                        </div>
                    @endif

                    @if($submission->reviewer)
                        <p class="text-sm text-gray-500">
                            {{ __('Bewertet von') }} {{ $submission->reviewer->name }}
                            {{ __('am') }} {{ $submission->reviewed_at->format('d.m.Y H:i') }}
                        </p>
                    @endif
                </div>
            @endif

            <!-- Back Link -->
            <a
                href="{{ route('instructor.submissions.index') }}"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Zurück zu Einreichungen') }}
            </a>
        </div>
    </div>
</div>
