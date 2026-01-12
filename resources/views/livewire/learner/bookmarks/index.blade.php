<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Lesezeichen') }}</h1>
        <p class="mt-2 text-gray-600">{{ __('Deine gespeicherten Lektionen für später') }}</p>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <div class="relative max-w-md">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Lesezeichen durchsuchen...') }}"
                class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-gray-900 placeholder-gray-500 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500"
            >
        </div>
    </div>

    @if($this->bookmarks->count() > 0)
        <!-- Bookmarks List -->
        <div class="space-y-4">
            @foreach($this->bookmarks as $bookmark)
                <div class="group flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                    <!-- Icon -->
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-teal-100 text-teal-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <a
                            href="{{ route('learner.learn.step', ['path' => $bookmark->step->module->learningPath->slug, 'step' => $bookmark->step->position]) }}"
                            wire:navigate
                            class="block"
                        >
                            <h3 class="font-medium text-gray-900 group-hover:text-teal-600">
                                {{ $bookmark->step->title }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $bookmark->step->module->learningPath->title }} &bull; {{ $bookmark->step->module->title }}
                            </p>
                        </a>
                        <p class="mt-2 text-xs text-gray-400">
                            {{ __('Gespeichert') }} {{ $bookmark->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <a
                            href="{{ route('learner.learn.step', ['path' => $bookmark->step->module->learningPath->slug, 'step' => $bookmark->step->position]) }}"
                            wire:navigate
                            class="rounded-lg bg-teal-50 px-3 py-2 text-sm font-medium text-teal-600 hover:bg-teal-100"
                        >
                            {{ __('Öffnen') }}
                        </a>
                        <button
                            wire:click="removeBookmark('{{ $bookmark->step_id }}')"
                            wire:confirm="{{ __('Lesezeichen wirklich entfernen?') }}"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-red-500"
                            title="{{ __('Entfernen') }}"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $this->bookmarks->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 py-16 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Lesezeichen vorhanden') }}</h3>
            <p class="mt-2 text-gray-500">{{ __('Speichere Lektionen als Lesezeichen, um sie später schnell wiederzufinden.') }}</p>
            <a
                href="{{ route('learner.catalog') }}"
                wire:navigate
                class="mt-4 inline-flex items-center rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700"
            >
                {{ __('Kurskatalog durchsuchen') }}
            </a>
        </div>
    @endif
</div>
