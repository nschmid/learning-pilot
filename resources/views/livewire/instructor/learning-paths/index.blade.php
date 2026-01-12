<div>
    <!-- Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Meine Lernpfade') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Verwalte und erstelle deine Kurse') }}</p>
        </div>
        <a
            href="{{ route('instructor.paths.create') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ __('Neuer Lernpfad') }}
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="mb-6 flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 sm:flex-row sm:items-center">
        <!-- Search -->
        <div class="flex-1">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="{{ __('Suchen...') }}"
                    class="w-full rounded-lg border-gray-300 pl-10 focus:border-orange-500 focus:ring-orange-500"
                >
            </div>
        </div>

        <!-- Status Filter -->
        <div class="sm:w-48">
            <select
                wire:model.live="status"
                class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
            >
                <option value="">{{ __('Alle Status') }}</option>
                <option value="published">{{ __('Veröffentlicht') }}</option>
                <option value="draft">{{ __('Entwurf') }}</option>
            </select>
        </div>

        <!-- Sort -->
        <div class="sm:w-48">
            <select
                wire:model.live="sort"
                class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
            >
                <option value="updated_at">{{ __('Zuletzt bearbeitet') }}</option>
                <option value="created_at">{{ __('Erstellungsdatum') }}</option>
                <option value="title">{{ __('Titel') }}</option>
                <option value="enrollments_count">{{ __('Teilnehmer') }}</option>
            </select>
        </div>
    </div>

    <!-- Paths Grid -->
    @if($this->paths->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Lernpfade gefunden') }}</h3>
            <p class="mt-2 text-gray-500">
                @if($search || $status)
                    {{ __('Versuche andere Suchkriterien.') }}
                @else
                    {{ __('Erstelle deinen ersten Lernpfad, um loszulegen.') }}
                @endif
            </p>
            @if(!$search && !$status)
                <a
                    href="{{ route('instructor.paths.create') }}"
                    class="mt-6 inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Ersten Lernpfad erstellen') }}
                </a>
            @endif
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($this->paths as $path)
                <div class="group overflow-hidden rounded-xl border border-gray-200 bg-white transition hover:shadow-md">
                    <!-- Thumbnail -->
                    <div class="relative aspect-video bg-gradient-to-br from-teal-500 to-purple-600">
                        @if($path->thumbnail)
                            <img
                                src="{{ Storage::url($path->thumbnail) }}"
                                alt="{{ $path->title }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <div class="flex h-full items-center justify-center">
                                <svg class="h-16 w-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif

                        <!-- Status Badge -->
                        <div class="absolute left-3 top-3">
                            @if($path->is_published)
                                <span class="inline-flex items-center rounded-full bg-green-500 px-2 py-1 text-xs font-medium text-white">
                                    {{ __('Veröffentlicht') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-700 px-2 py-1 text-xs font-medium text-white">
                                    {{ __('Entwurf') }}
                                </span>
                            @endif
                        </div>

                        <!-- Quick Actions -->
                        <div class="absolute right-3 top-3 flex gap-2 opacity-0 transition group-hover:opacity-100">
                            <button
                                wire:click="togglePublish({{ $path->id }})"
                                class="rounded-lg bg-white p-2 text-gray-600 shadow hover:text-gray-900"
                                title="{{ $path->is_published ? __('Verbergen') : __('Veröffentlichen') }}"
                            >
                                @if($path->is_published)
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                @else
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                @endif
                            </button>
                            <button
                                wire:click="duplicate({{ $path->id }})"
                                class="rounded-lg bg-white p-2 text-gray-600 shadow hover:text-gray-900"
                                title="{{ __('Duplizieren') }}"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900">
                            <a href="{{ route('instructor.paths.show', $path) }}" class="hover:text-orange-600">
                                {{ $path->title }}
                            </a>
                        </h3>

                        <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                {{ $path->modules_count }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                {{ $path->steps_count }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                {{ $path->enrollments_count }}
                            </span>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xs text-gray-400">
                                {{ __('Bearbeitet') }} {{ $path->updated_at->diffForHumans() }}
                            </span>
                            <div class="flex gap-2">
                                <a
                                    href="{{ route('instructor.paths.edit', $path) }}"
                                    class="rounded-lg border border-gray-300 px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    {{ __('Bearbeiten') }}
                                </a>
                                <button
                                    wire:click="confirmDelete('{{ $path->id }}')"
                                    class="rounded-lg border border-red-300 px-3 py-1 text-sm font-medium text-red-600 hover:bg-red-50"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $this->paths->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <div class="fixed inset-0 bg-black/50" wire:click="cancelDelete"></div>

            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 rounded-full bg-red-100 p-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Lernpfad löschen') }}</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            {{ __('Bist du sicher, dass du diesen Lernpfad löschen möchtest? Diese Aktion kann nicht rückgängig gemacht werden.') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="cancelDelete"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        {{ __('Abbrechen') }}
                    </button>
                    <button
                        wire:click="delete"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                    >
                        {{ __('Löschen') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
