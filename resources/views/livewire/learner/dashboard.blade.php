<div>
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Willkommen zurück, :name!', ['name' => auth()->user()->name]) }}</h1>
        <p class="mt-1 text-gray-600">{{ __('Setze dein Lernen fort oder entdecke neue Lernpfade.') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-sky-100">
                    <svg class="size-6 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('Eingeschrieben') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->stats['total_enrollments'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-emerald-100">
                    <svg class="size-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('Abgeschlossen') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->stats['completed_paths'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-violet-100">
                    <svg class="size-6 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('Lernzeit') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->stats['total_time_hours'] }}h</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-amber-100">
                    <svg class="size-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('Punkte') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($this->stats['total_points']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Enrollments -->
    @if($this->activeEnrollments->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Aktive Lernpfade') }}</h2>
                <a href="{{ route('learner.catalog') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">
                    {{ __('Alle anzeigen') }} &rarr;
                </a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->activeEnrollments as $enrollment)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Thumbnail -->
                        <div class="h-32 bg-gradient-to-br from-sky-500 to-teal-600 relative">
                            @if($enrollment->learningPath->thumbnail)
                                <img src="{{ $enrollment->learningPath->thumbnail }}" alt="" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            <div class="absolute bottom-3 left-3 right-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                                    {{ $enrollment->learningPath->category?->name ?? __('Allgemein') }}
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1 line-clamp-1">
                                {{ $enrollment->learningPath->title }}
                            </h3>
                            <p class="text-sm text-gray-500 mb-3">
                                {{ $enrollment->learningPath->creator->name }}
                            </p>

                            <!-- Progress Bar -->
                            <div class="mb-3">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ __('Fortschritt') }}</span>
                                    <span class="font-medium text-gray-900">{{ $enrollment->progress_percent }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-sky-500 h-2 rounded-full transition-all" style="width: {{ $enrollment->progress_percent }}%"></div>
                                </div>
                            </div>

                            <!-- Continue Button -->
                            <button
                                wire:click="continueLearning('{{ $enrollment->id }}')"
                                class="w-full py-2 px-4 bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium rounded-lg transition-colors"
                            >
                                {{ __('Weiterlernen') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Empty State if no enrollments -->
    @if($this->activeEnrollments->isEmpty() && $this->completedEnrollments->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-sky-100 rounded-full flex items-center justify-center mb-4">
                <svg class="size-8 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Starte deine Lernreise') }}</h3>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                {{ __('Du bist noch in keinem Lernpfad eingeschrieben. Entdecke unseren Katalog und finde den passenden Kurs für dich.') }}
            </p>
            <a href="{{ route('learner.catalog') }}" class="inline-flex items-center px-6 py-3 bg-sky-500 hover:bg-sky-600 text-white font-medium rounded-lg transition-colors">
                <svg class="size-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                {{ __('Katalog durchsuchen') }}
            </a>
        </div>
    @endif

    <!-- Completed Paths -->
    @if($this->completedEnrollments->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Kürzlich abgeschlossen') }}</h2>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-100">
                @foreach($this->completedEnrollments as $enrollment)
                    <div class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                        <div class="size-12 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="size-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 truncate">{{ $enrollment->learningPath?->title }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ __('Abgeschlossen am :date', ['date' => $enrollment->completed_at->format('d.m.Y')]) }}
                            </p>
                        </div>
                        <div class="ml-4 flex items-center gap-2">
                            <span class="text-sm font-medium text-emerald-600">{{ $enrollment->points_earned }} {{ __('Punkte') }}</span>
                            <a href="{{ route('learner.certificates') }}" class="p-2 text-gray-400 hover:text-gray-600">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recommended Paths -->
    @if($this->recommendedPaths->isNotEmpty())
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Empfohlen für dich') }}</h2>
                <a href="{{ route('learner.catalog') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">
                    {{ __('Mehr entdecken') }} &rarr;
                </a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($this->recommendedPaths as $path)
                    <a href="{{ route('learner.path.show', $path->slug) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                        <!-- Thumbnail -->
                        <div class="h-28 bg-gradient-to-br from-teal-500 to-purple-600 relative">
                            @if($path->thumbnail)
                                <img src="{{ $path->thumbnail }}" alt="" class="w-full h-full object-cover">
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1 line-clamp-1 group-hover:text-sky-600 transition-colors">
                                {{ $path->title }}
                            </h3>
                            <p class="text-sm text-gray-500 mb-2">
                                {{ $path->creator->name }}
                            </p>
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span class="flex items-center">
                                    <svg class="size-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $path->estimated_hours ?? '?' }}h
                                </span>
                                <span class="flex items-center">
                                    <svg class="size-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    {{ $path->enrollments_count }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                    {{ $path->difficulty->value }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
