<div>
    <!-- Hero Section -->
    <div class="relative -mx-4 -mt-6 mb-8 overflow-hidden bg-gradient-to-br from-teal-600 via-purple-600 to-teal-800 px-4 py-12 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="hero-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="20" cy="20" r="2" fill="currentColor"/>
                    </pattern>
                </defs>
                <rect fill="url(#hero-pattern)" width="100%" height="100%"/>
            </svg>
        </div>

        <div class="relative mx-auto max-w-4xl">
            <!-- Breadcrumb -->
            <nav class="mb-4 flex items-center gap-2 text-sm text-teal-200">
                <a href="{{ route('learner.catalog') }}" wire:navigate class="hover:text-white">{{ __('Katalog') }}</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                @if($path->category)
                    <a href="{{ route('learner.catalog.category', $path->category->slug) }}" wire:navigate class="hover:text-white">{{ $path->category->name }}</a>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif
                <span class="text-white">{{ $path->title }}</span>
            </nav>

            <!-- Category & Difficulty -->
            <div class="mb-4 flex flex-wrap items-center gap-2">
                @if($path->category)
                    <span class="rounded-full bg-white/20 px-3 py-1 text-sm font-medium text-white">
                        {{ $path->category->name }}
                    </span>
                @endif
                @if($path->difficulty)
                    <span class="rounded-full px-3 py-1 text-sm font-medium
                        {{ match($path->difficulty->value) {
                            'beginner' => 'bg-green-500/80 text-white',
                            'intermediate' => 'bg-blue-500/80 text-white',
                            'advanced' => 'bg-orange-500/80 text-white',
                            'expert' => 'bg-red-500/80 text-white',
                            default => 'bg-gray-500/80 text-white',
                        } }}">
                        {{ $path->difficulty->label() }}
                    </span>
                @endif
            </div>

            <!-- Title -->
            <h1 class="mb-4 text-3xl font-bold text-white sm:text-4xl">{{ $path->title }}</h1>

            <!-- Description -->
            <p class="mb-6 max-w-2xl text-lg text-teal-100">{{ $path->description }}</p>

            <!-- Stats Row -->
            <div class="mb-6 flex flex-wrap items-center gap-6 text-sm text-teal-100">
                @if($this->stats['rating'] > 0)
                    <div class="flex items-center gap-1">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <span class="font-medium text-white">{{ number_format($this->stats['rating'], 1) }}</span>
                        <span>({{ $this->stats['reviews_count'] }} {{ __('Bewertungen') }})</span>
                    </div>
                @endif
                <div class="flex items-center gap-1">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>{{ $this->stats['enrollments'] }} {{ __('Teilnehmer') }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ $this->stats['duration'] }}h {{ __('Lernzeit') }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    <span>{{ $this->stats['points'] }} {{ __('Punkte') }}</span>
                </div>
            </div>

            <!-- Creator -->
            @if($path->creator)
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 overflow-hidden rounded-full bg-white/20">
                        @if($path->creator->profile_photo_url)
                            <img src="{{ $path->creator->profile_photo_url }}" alt="{{ $path->creator->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-white">
                                {{ substr($path->creator->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-teal-200">{{ __('Erstellt von') }}</p>
                        <p class="font-medium text-white">{{ $path->creator->name }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-8 lg:grid-cols-3">
            <!-- Left Column: Content -->
            <div class="lg:col-span-2">
                <!-- Objectives -->
                @if($path->objectives && count($path->objectives) > 0)
                    <div class="mb-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-semibold text-gray-900">{{ __('Was du lernen wirst') }}</h2>
                        <ul class="grid gap-3 sm:grid-cols-2">
                            @foreach($path->objectives as $objective)
                                <li class="flex items-start gap-3">
                                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $objective }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Curriculum -->
                <div class="mb-8 rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('Kursinhalt') }}</h2>
                        <span class="text-sm text-gray-500">
                            {{ $this->stats['modules'] }} {{ trans_choice('Modul|Module', $this->stats['modules']) }},
                            {{ $this->stats['steps'] }} {{ trans_choice('Lektion|Lektionen', $this->stats['steps']) }}
                        </span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($path->modules as $module)
                            <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                                <button
                                    @click="open = !open"
                                    class="flex w-full items-center justify-between p-4 text-left hover:bg-gray-50"
                                >
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-teal-100 text-sm font-semibold text-teal-600">
                                            {{ $loop->iteration }}
                                        </span>
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $module->title }}</h3>
                                            <p class="text-sm text-gray-500">
                                                {{ $module->steps->count() }} {{ trans_choice('Lektion|Lektionen', $module->steps->count()) }}
                                                @if($module->estimatedMinutes() > 0)
                                                    &bull; {{ $module->estimatedMinutes() }} min
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <svg
                                        class="h-5 w-5 text-gray-400 transition-transform"
                                        :class="{ 'rotate-180': open }"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="open" x-collapse class="border-t border-gray-100 bg-gray-50">
                                    @if($module->description)
                                        <p class="px-4 py-3 text-sm text-gray-600">{{ $module->description }}</p>
                                    @endif
                                    <ul class="divide-y divide-gray-100">
                                        @foreach($module->steps as $step)
                                            <li class="flex items-center gap-3 px-4 py-3 pl-16">
                                                @php
                                                    $stepIcon = match($step->step_type->value ?? $step->step_type) {
                                                        'material' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>',
                                                        'task' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>',
                                                        'assessment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                                                        default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                                                    };
                                                @endphp
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    {!! $stepIcon !!}
                                                </svg>
                                                <span class="flex-1 text-sm text-gray-700">{{ $step->title }}</span>
                                                @if($step->estimated_minutes)
                                                    <span class="text-xs text-gray-400">{{ $step->estimated_minutes }} min</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reviews -->
                @if($path->reviews->count() > 0)
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900">{{ __('Bewertungen') }}</h2>
                            <a href="{{ route('learner.path.reviews', $path->slug) }}" wire:navigate class="text-sm text-teal-600 hover:text-teal-800">
                                {{ __('Alle ansehen') }}
                            </a>
                        </div>

                        <div class="space-y-6">
                            @foreach($path->reviews as $review)
                                <div class="border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                                    <div class="mb-2 flex items-center gap-3">
                                        <div class="h-8 w-8 overflow-hidden rounded-full bg-gray-200">
                                            @if($review->user->profile_photo_url)
                                                <img src="{{ $review->user->profile_photo_url }}" alt="{{ $review->user->name }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-sm text-gray-500">
                                                    {{ substr($review->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $review->user->name }}</p>
                                            <div class="flex items-center gap-2">
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if($review->review_text)
                                        <p class="text-gray-600">{{ $review->review_text }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Enrollment Card -->
            <div class="lg:col-span-1">
                <div class="sticky top-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <!-- Thumbnail -->
                    <div class="mb-6 aspect-video overflow-hidden rounded-lg bg-gradient-to-br from-teal-500 to-purple-600">
                        @if($path->thumbnail)
                            <img src="{{ $path->thumbnail }}" alt="{{ $path->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center">
                                <svg class="h-16 w-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Enrollment Status / Action -->
                    @if($this->isEnrolled)
                        @php
                            $enrollment = $this->enrollment;
                        @endphp
                        <div class="mb-6">
                            <div class="mb-4 rounded-lg bg-green-50 p-4">
                                <div class="flex items-center gap-2 text-green-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">{{ __('Du bist eingeschrieben') }}</span>
                                </div>
                                <div class="mt-3">
                                    <div class="mb-1 flex justify-between text-sm">
                                        <span class="text-green-700">{{ __('Fortschritt') }}</span>
                                        <span class="font-medium text-green-800">{{ round($enrollment->progress_percent) }}%</span>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-green-200">
                                        <div class="h-full rounded-full bg-green-500" style="width: {{ $enrollment->progress_percent }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <button
                                wire:click="continueLearning"
                                class="flex w-full items-center justify-center gap-2 rounded-lg bg-teal-600 px-6 py-3 font-semibold text-white transition hover:bg-teal-700"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Weiterlernen') }}
                            </button>
                        </div>
                    @else
                        <button
                            wire:click="enroll"
                            wire:loading.attr="disabled"
                            class="mb-6 flex w-full items-center justify-center gap-2 rounded-lg bg-teal-600 px-6 py-3 font-semibold text-white transition hover:bg-teal-700 disabled:cursor-wait disabled:opacity-75"
                        >
                            <span wire:loading.remove wire:target="enroll">{{ __('Jetzt starten') }}</span>
                            <span wire:loading wire:target="enroll">{{ __('Wird eingeschrieben...') }}</span>
                        </button>
                    @endif

                    <!-- Course Info -->
                    <div class="space-y-4 border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Module') }}</span>
                            <span class="font-medium text-gray-900">{{ $this->stats['modules'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Lektionen') }}</span>
                            <span class="font-medium text-gray-900">{{ $this->stats['steps'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Lernzeit') }}</span>
                            <span class="font-medium text-gray-900">{{ $this->stats['duration'] }} {{ __('Stunden') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Punkte') }}</span>
                            <span class="font-medium text-gray-900">{{ $this->stats['points'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Schwierigkeit') }}</span>
                            <span class="font-medium text-gray-900">{{ $path->difficulty?->label() ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Zertifikat') }}</span>
                            <span class="font-medium text-green-600">{{ __('Ja') }}</span>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($path->tags->count() > 0)
                        <div class="mt-6 border-t border-gray-100 pt-6">
                            <h3 class="mb-3 text-sm font-medium text-gray-900">{{ __('Themen') }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($path->tags as $tag)
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-600">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
