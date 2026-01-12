<div class="flex h-screen bg-gray-100" x-data="{ sidebarOpen: @entangle('showSidebar') }">
    <!-- Sidebar -->
    <aside
        class="fixed inset-y-0 left-0 z-30 w-80 transform bg-white shadow-lg transition-transform duration-300 lg:relative lg:translate-x-0"
        :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    >
        <!-- Sidebar Header -->
        <div class="flex h-16 items-center justify-between border-b border-gray-200 px-4">
            <a href="{{ route('learner.path.show', $path->slug) }}" wire:navigate class="flex items-center gap-2 text-gray-600 hover:text-gray-900">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="text-sm font-medium">{{ __('Zurück') }}</span>
            </a>
            <button
                @click="sidebarOpen = false"
                class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 lg:hidden"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Progress Bar -->
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="mb-1 flex items-center justify-between text-sm">
                <span class="font-medium text-gray-700">{{ __('Fortschritt') }}</span>
                <span class="text-gray-500">{{ $this->progress }}%</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                <div
                    class="h-full rounded-full bg-teal-600 transition-all duration-500"
                    style="width: {{ $this->progress }}%"
                ></div>
            </div>
        </div>

        <!-- AI Learning Aids -->
        @if($currentStep)
            <div class="border-b border-gray-200 px-4 py-3">
                <div class="rounded-lg border border-teal-200 bg-teal-50 p-3">
                    <h4 class="flex items-center gap-2 text-sm font-medium text-teal-900">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        {{ __('KI-Lernhilfen') }}
                    </h4>
                    <div class="mt-2 space-y-1">
                        <a
                            href="{{ route('learner.ai.tutor.step', $currentStep->id) }}"
                            wire:navigate
                            class="flex items-center gap-2 rounded px-2 py-1.5 text-sm text-teal-700 hover:bg-teal-100"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            {{ __('Frage stellen') }}
                        </a>
                        <a
                            href="{{ route('learner.ai.practice', $currentStep->module_id) }}"
                            wire:navigate
                            class="flex items-center gap-2 rounded px-2 py-1.5 text-sm text-teal-700 hover:bg-teal-100"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"></path>
                            </svg>
                            {{ __('Übungsfragen') }}
                        </a>
                        <a
                            href="{{ route('learner.ai.flashcards', $currentStep->module_id) }}"
                            wire:navigate
                            class="flex items-center gap-2 rounded px-2 py-1.5 text-sm text-teal-700 hover:bg-teal-100"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            {{ __('Lernkarten') }}
                        </a>
                        <a
                            href="{{ route('learner.ai.summary', $currentStep->module_id) }}"
                            wire:navigate
                            class="flex items-center gap-2 rounded px-2 py-1.5 text-sm text-teal-700 hover:bg-teal-100"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('Zusammenfassung') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Course Navigation -->
        <nav class="h-[calc(100vh-8rem)] overflow-y-auto p-4">
            <h2 class="mb-4 truncate text-lg font-semibold text-gray-900">{{ $path->title }}</h2>

            <div class="space-y-4">
                @php
                    $stepCounter = 0;
                @endphp
                @foreach($path->modules as $module)
                    <div>
                        <h3 class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-500">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs">
                                {{ $loop->iteration }}
                            </span>
                            <span class="truncate">{{ $module->title }}</span>
                        </h3>

                        <ul class="ml-3 space-y-1 border-l-2 border-gray-200 pl-4">
                            @foreach($module->steps as $step)
                                @php
                                    $stepCounter++;
                                    $isCompleted = $this->isStepCompleted($step->id);
                                    $isCurrent = $stepCounter === $stepNumber;
                                @endphp
                                <li>
                                    <button
                                        wire:click="goToStep({{ $stepCounter }})"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm transition
                                            {{ $isCurrent ? 'bg-teal-50 text-teal-700' : ($isCompleted ? 'text-gray-600 hover:bg-gray-50' : 'text-gray-500 hover:bg-gray-50') }}"
                                    >
                                        @if($isCompleted)
                                            <svg class="h-4 w-4 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @elseif($isCurrent)
                                            <div class="h-4 w-4 flex-shrink-0 rounded-full border-2 border-teal-500 bg-teal-500"></div>
                                        @else
                                            <div class="h-4 w-4 flex-shrink-0 rounded-full border-2 border-gray-300"></div>
                                        @endif
                                        <span class="truncate {{ $isCurrent ? 'font-medium' : '' }}">{{ $step->title }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </nav>
    </aside>

    <!-- Mobile sidebar backdrop -->
    <div
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-20 bg-black/50 lg:hidden"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
    ></div>

    <!-- Main Content -->
    <main class="flex flex-1 flex-col overflow-hidden">
        <!-- Top Bar -->
        <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 lg:px-6">
            <div class="flex items-center gap-4">
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div>
                    <p class="text-xs text-gray-500">
                        {{ __('Lektion') }} {{ $stepNumber }} {{ __('von') }} {{ $this->totalSteps }}
                    </p>
                    <h1 class="text-lg font-semibold text-gray-900">
                        {{ $currentStep?->title ?? __('Keine Lektion ausgewählt') }}
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Bookmark Button -->
                <button class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Lesezeichen') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                </button>

                <!-- Notes Button -->
                <button class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Notizen') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto">
            @if($currentStep)
                <div class="mx-auto max-w-4xl px-4 py-8 lg:px-8">
                    <!-- Step Description -->
                    @if($currentStep->description)
                        <div class="mb-6 rounded-lg bg-gray-50 p-4">
                            <p class="text-gray-600">{{ $currentStep->description }}</p>
                        </div>
                    @endif

                    <!-- Material Content -->
                    @if($currentStep->isMaterial())
                        <div class="space-y-6">
                            @foreach($currentStep->materials as $material)
                                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                                    @if($material->title)
                                        <div class="border-b border-gray-200 px-6 py-4">
                                            <h3 class="font-medium text-gray-900">{{ $material->title }}</h3>
                                        </div>
                                    @endif

                                    <div class="p-6">
                                        @switch($material->material_type->value)
                                            @case('text')
                                                <div class="prose prose-indigo max-w-none">
                                                    {!! $material->content !!}
                                                </div>
                                                @break

                                            @case('video')
                                                @if($material->isEmbed() && $material->getEmbedUrl())
                                                    <div class="aspect-video overflow-hidden rounded-lg bg-gray-900">
                                                        <iframe
                                                            src="{{ $material->getEmbedUrl() }}"
                                                            class="h-full w-full"
                                                            frameborder="0"
                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                            allowfullscreen
                                                        ></iframe>
                                                    </div>
                                                @elseif($material->file_path)
                                                    <video controls class="w-full rounded-lg">
                                                        <source src="{{ Storage::url($material->file_path) }}" type="{{ $material->mime_type }}">
                                                        {{ __('Dein Browser unterstützt das Video-Element nicht.') }}
                                                    </video>
                                                @endif
                                                @break

                                            @case('audio')
                                                <audio controls class="w-full">
                                                    <source src="{{ Storage::url($material->file_path) }}" type="{{ $material->mime_type }}">
                                                    {{ __('Dein Browser unterstützt das Audio-Element nicht.') }}
                                                </audio>
                                                @break

                                            @case('pdf')
                                                <div class="aspect-[4/3] overflow-hidden rounded-lg border border-gray-200">
                                                    <iframe
                                                        src="{{ Storage::url($material->file_path) }}"
                                                        class="h-full w-full"
                                                    ></iframe>
                                                </div>
                                                <div class="mt-4">
                                                    <a
                                                        href="{{ Storage::url($material->file_path) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-800"
                                                    >
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                        </svg>
                                                        {{ __('PDF herunterladen') }}
                                                    </a>
                                                </div>
                                                @break

                                            @case('image')
                                                <img
                                                    src="{{ Storage::url($material->file_path) }}"
                                                    alt="{{ $material->title }}"
                                                    class="max-h-[600px] w-full rounded-lg object-contain"
                                                >
                                                @break

                                            @case('link')
                                                <a
                                                    href="{{ $material->external_url }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 hover:bg-gray-50"
                                                >
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $material->external_url }}</p>
                                                        <p class="text-sm text-gray-500">{{ __('Externer Link') }}</p>
                                                    </div>
                                                </a>
                                                @break

                                            @default
                                                <p class="text-gray-500">{{ __('Inhalt nicht verfügbar') }}</p>
                                        @endswitch
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Task Content -->
                    @if($currentStep->isTask() && $currentStep->task)
                        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="mb-4 flex items-center gap-2 text-orange-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <span class="font-medium">{{ __('Aufgabe') }}</span>
                            </div>
                            <h3 class="mb-4 text-xl font-semibold text-gray-900">{{ $currentStep->task->title }}</h3>
                            <div class="prose prose-indigo max-w-none">
                                {!! $currentStep->task->instructions !!}
                            </div>
                            <div class="mt-6">
                                <a
                                    href="{{ route('learner.task.show', $currentStep->task->id) }}"
                                    wire:navigate
                                    class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 font-medium text-white hover:bg-orange-700"
                                >
                                    {{ __('Aufgabe bearbeiten') }}
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Assessment Content -->
                    @if($currentStep->isAssessment() && $currentStep->assessment)
                        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="mb-4 flex items-center gap-2 text-purple-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">{{ __('Wissenstest') }}</span>
                            </div>
                            <h3 class="mb-4 text-xl font-semibold text-gray-900">{{ $currentStep->assessment->title }}</h3>
                            @if($currentStep->assessment->description)
                                <p class="mb-4 text-gray-600">{{ $currentStep->assessment->description }}</p>
                            @endif
                            <div class="mb-6 flex flex-wrap gap-4 text-sm text-gray-500">
                                @if($currentStep->assessment->time_limit_minutes)
                                    <span class="flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $currentStep->assessment->time_limit_minutes }} {{ __('Minuten') }}
                                    </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    {{ $currentStep->assessment->questions()->count() }} {{ __('Fragen') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $currentStep->assessment->passing_score_percent }}% {{ __('zum Bestehen') }}
                                </span>
                            </div>
                            <a
                                href="{{ route('learner.assessment.start', $currentStep->assessment->id) }}"
                                wire:navigate
                                class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2 font-medium text-white hover:bg-purple-700"
                            >
                                {{ __('Test starten') }}
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <!-- No Step Selected -->
                <div class="flex h-full items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Lektion verfügbar') }}</h3>
                        <p class="mt-2 text-gray-500">{{ __('Dieser Lernpfad enthält noch keine Inhalte.') }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Bottom Navigation -->
        <footer class="border-t border-gray-200 bg-white px-4 py-4 lg:px-6">
            <div class="mx-auto flex max-w-4xl items-center justify-between">
                <button
                    wire:click="previousStep"
                    @disabled($stepNumber <= 1)
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 font-medium text-gray-600 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    {{ __('Zurück') }}
                </button>

                @if($currentStep && !$this->isStepCompleted($currentStep->id))
                    <button
                        wire:click="completeStep"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-6 py-2 font-medium text-white transition hover:bg-green-700 disabled:cursor-wait disabled:opacity-75"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span wire:loading.remove wire:target="completeStep">{{ __('Als erledigt markieren') }}</span>
                        <span wire:loading wire:target="completeStep">{{ __('Wird gespeichert...') }}</span>
                    </button>
                @else
                    <button
                        wire:click="nextStep"
                        @disabled($stepNumber >= $this->totalSteps)
                        class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-6 py-2 font-medium text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ $stepNumber >= $this->totalSteps ? __('Abgeschlossen') : __('Weiter') }}
                        @if($stepNumber < $this->totalSteps)
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        @endif
                    </button>
                @endif
            </div>
        </footer>
    </main>
</div>
