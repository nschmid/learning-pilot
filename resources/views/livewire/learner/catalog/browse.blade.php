<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Kurskatalog') }}</h1>
        <p class="mt-2 text-gray-600">{{ __('Entdecke Lernpfade und erweitere dein Wissen') }}</p>
    </div>

    <!-- Search and Filters -->
    <div class="mb-8 space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Lernpfade durchsuchen...') }}"
                class="block w-full rounded-lg border border-gray-300 bg-white py-3 pl-10 pr-4 text-gray-900 placeholder-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
        </div>

        <!-- Filter Row -->
        <div class="flex flex-wrap items-center gap-4">
            <!-- Category Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button
                    @click="open = !open"
                    @click.away="open = false"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    {{ $this->selectedCategory?->name ?? __('Alle Kategorien') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute left-0 z-10 mt-2 w-56 origin-top-left rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                    style="display: none;"
                >
                    <div class="py-1">
                        <button
                            wire:click="setCategory(null)"
                            @click="open = false"
                            class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 {{ !$category ? 'bg-indigo-50 text-indigo-700' : '' }}"
                        >
                            {{ __('Alle Kategorien') }}
                        </button>
                        @foreach($this->categories as $cat)
                            <button
                                wire:click="setCategory('{{ $cat->slug }}')"
                                @click="open = false"
                                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 {{ $category === $cat->slug ? 'bg-indigo-50 text-indigo-700' : '' }}"
                            >
                                {{ $cat->name }}
                                <span class="text-gray-400">({{ $cat->learning_paths_count }})</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Difficulty Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button
                    @click="open = !open"
                    @click.away="open = false"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    @if($difficulty)
                        {{ App\Enums\Difficulty::tryFrom($difficulty)?->label() ?? __('Schwierigkeit') }}
                    @else
                        {{ __('Schwierigkeit') }}
                    @endif
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-transition
                    class="absolute left-0 z-10 mt-2 w-48 origin-top-left rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                    style="display: none;"
                >
                    <div class="py-1">
                        <button
                            wire:click="setDifficulty(null)"
                            @click="open = false"
                            class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 {{ !$difficulty ? 'bg-indigo-50 text-indigo-700' : '' }}"
                        >
                            {{ __('Alle Stufen') }}
                        </button>
                        @foreach($this->difficulties as $diff)
                            <button
                                wire:click="setDifficulty('{{ $diff->value }}')"
                                @click="open = false"
                                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 {{ $difficulty === $diff->value ? 'bg-indigo-50 text-indigo-700' : '' }}"
                            >
                                {{ $diff->label() }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sort Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button
                    @click="open = !open"
                    @click.away="open = false"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                    </svg>
                    {{ match($sort) {
                        'newest' => __('Neueste'),
                        'rating' => __('Beste Bewertung'),
                        'title' => __('Alphabetisch'),
                        default => __('Beliebteste'),
                    } }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-transition
                    class="absolute left-0 z-10 mt-2 w-48 origin-top-left rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                    style="display: none;"
                >
                    <div class="py-1">
                        @foreach(['popular' => __('Beliebteste'), 'newest' => __('Neueste'), 'rating' => __('Beste Bewertung'), 'title' => __('Alphabetisch')] as $value => $label)
                            <button
                                wire:click="$set('sort', '{{ $value }}')"
                                @click="open = false"
                                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 {{ $sort === $value ? 'bg-indigo-50 text-indigo-700' : '' }}"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Clear Filters -->
            @if($search || $category || $difficulty || $sort !== 'popular')
                <button
                    wire:click="clearFilters"
                    class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ __('Filter zurücksetzen') }}
                </button>
            @endif
        </div>
    </div>

    <!-- Active Filters -->
    @if($search || $category || $difficulty)
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="text-sm text-gray-500">{{ __('Aktive Filter:') }}</span>
            @if($search)
                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-3 py-1 text-sm text-indigo-800">
                    "{{ $search }}"
                    <button wire:click="$set('search', '')" class="hover:text-indigo-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
            @if($category && $this->selectedCategory)
                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-3 py-1 text-sm text-indigo-800">
                    {{ $this->selectedCategory->name }}
                    <button wire:click="setCategory(null)" class="hover:text-indigo-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
            @if($difficulty)
                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-3 py-1 text-sm text-indigo-800">
                    {{ App\Enums\Difficulty::tryFrom($difficulty)?->label() }}
                    <button wire:click="setDifficulty(null)" class="hover:text-indigo-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
        </div>
    @endif

    <!-- Results Count -->
    <div class="mb-4">
        <p class="text-sm text-gray-600">
            {{ trans_choice(':count Lernpfad gefunden|:count Lernpfade gefunden', $this->learningPaths->total(), ['count' => $this->learningPaths->total()]) }}
        </p>
    </div>

    <!-- Learning Paths Grid -->
    @if($this->learningPaths->count() > 0)
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($this->learningPaths as $path)
                <div class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md">
                    <!-- Thumbnail -->
                    <div class="relative aspect-video overflow-hidden bg-gradient-to-br from-indigo-500 to-purple-600">
                        @if($path->thumbnail)
                            <img src="{{ $path->thumbnail }}" alt="{{ $path->title }}" class="h-full w-full object-cover transition group-hover:scale-105">
                        @else
                            <div class="flex h-full items-center justify-center">
                                <svg class="h-16 w-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif

                        <!-- Difficulty Badge -->
                        @if($path->difficulty)
                            <span class="absolute left-3 top-3 rounded-full px-2 py-1 text-xs font-medium
                                {{ match($path->difficulty->value) {
                                    'beginner' => 'bg-green-100 text-green-800',
                                    'intermediate' => 'bg-blue-100 text-blue-800',
                                    'advanced' => 'bg-orange-100 text-orange-800',
                                    'expert' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                } }}">
                                {{ $path->difficulty->label() }}
                            </span>
                        @endif

                        <!-- Enrolled Badge -->
                        @if($this->isEnrolled($path->id))
                            <span class="absolute right-3 top-3 rounded-full bg-indigo-600 px-2 py-1 text-xs font-medium text-white">
                                {{ __('Eingeschrieben') }}
                            </span>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Category -->
                        @if($path->category)
                            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-indigo-600">
                                {{ $path->category->name }}
                            </p>
                        @endif

                        <!-- Title -->
                        <h3 class="mb-2 font-semibold text-gray-900 line-clamp-2">
                            <a href="{{ route('learner.path.show', $path->slug) }}" class="hover:text-indigo-600" wire:navigate>
                                {{ $path->title }}
                            </a>
                        </h3>

                        <!-- Description -->
                        <p class="mb-3 text-sm text-gray-600 line-clamp-2">
                            {{ $path->description }}
                        </p>

                        <!-- Meta -->
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <!-- Rating -->
                            @if($path->reviews_count > 0)
                                <span class="flex items-center gap-1">
                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ number_format($path->reviews_avg_rating ?? 0, 1) }}
                                </span>
                            @endif

                            <!-- Enrollments -->
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                {{ $path->enrollments_count }}
                            </span>

                            <!-- Duration -->
                            @if($path->estimated_hours)
                                <span class="flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $path->estimated_hours }}h
                                </span>
                            @endif
                        </div>

                        <!-- Creator -->
                        @if($path->creator)
                            <div class="mt-3 flex items-center gap-2 border-t border-gray-100 pt-3">
                                <div class="h-6 w-6 rounded-full bg-gray-300">
                                    @if($path->creator->profile_photo_url)
                                        <img src="{{ $path->creator->profile_photo_url }}" alt="{{ $path->creator->name }}" class="h-6 w-6 rounded-full object-cover">
                                    @endif
                                </div>
                                <span class="text-xs text-gray-600">{{ $path->creator->name }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Hover Action -->
                    <a href="{{ route('learner.path.show', $path->slug) }}" wire:navigate class="absolute inset-0 focus:outline-none">
                        <span class="sr-only">{{ __('Lernpfad ansehen') }}: {{ $path->title }}</span>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $this->learningPaths->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 py-16 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Lernpfade gefunden') }}</h3>
            <p class="mt-2 text-gray-500">{{ __('Versuche andere Suchbegriffe oder Filter.') }}</p>
            <button
                wire:click="clearFilters"
                class="mt-4 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                {{ __('Filter zurücksetzen') }}
            </button>
        </div>
    @endif
</div>
