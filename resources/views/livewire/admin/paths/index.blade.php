<div>
    <!-- Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Lernpfade verwalten') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Übersicht aller Lernpfade auf der Plattform') }}</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4">
        <div class="grid gap-4 sm:grid-cols-5">
            <!-- Search -->
            <div class="sm:col-span-2">
                <label for="search" class="sr-only">{{ __('Suchen') }}</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="search"
                        id="search"
                        placeholder="{{ __('Lernpfad suchen...') }}"
                        class="block w-full rounded-lg border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="sr-only">{{ __('Status') }}</label>
                <select
                    wire:model.live="status"
                    id="status"
                    class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">{{ __('Alle Status') }}</option>
                    <option value="published">{{ __('Veröffentlicht') }}</option>
                    <option value="draft">{{ __('Entwurf') }}</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label for="categoryId" class="sr-only">{{ __('Kategorie') }}</label>
                <select
                    wire:model.live="categoryId"
                    id="categoryId"
                    class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">{{ __('Alle Kategorien') }}</option>
                    @foreach($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @foreach($category->children as $child)
                            <option value="{{ $child->id }}">&nbsp;&nbsp;{{ $child->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <!-- Creator Filter -->
            <div>
                <label for="creatorId" class="sr-only">{{ __('Ersteller') }}</label>
                <select
                    wire:model.live="creatorId"
                    id="creatorId"
                    class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">{{ __('Alle Ersteller') }}</option>
                    @foreach($this->creators as $creator)
                        <option value="{{ $creator->id }}">{{ $creator->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Paths Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        <button wire:click="sortBy('title')" class="group inline-flex items-center gap-1">
                            {{ __('Lernpfad') }}
                            @if($sortBy === 'title')
                                <svg class="h-4 w-4 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        {{ __('Ersteller') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        {{ __('Kategorie') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        {{ __('Einschreibungen') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        {{ __('Status') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        <button wire:click="sortBy('created_at')" class="group inline-flex items-center gap-1">
                            {{ __('Erstellt') }}
                            @if($sortBy === 'created_at')
                                <svg class="h-4 w-4 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">{{ __('Aktionen') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($this->paths as $path)
                    <tr wire:key="path-{{ $path->id }}">
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-4">
                                @if($path->getFirstMediaUrl('thumbnail'))
                                    <img src="{{ $path->getFirstMediaUrl('thumbnail') }}" alt="{{ $path->title }}" class="h-10 w-14 rounded-lg object-cover">
                                @else
                                    <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-gray-100">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $path->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $path->difficulty?->label() }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img src="{{ $path->creator?->profile_photo_url }}" alt="{{ $path->creator?->name }}" class="h-6 w-6 rounded-full object-cover">
                                <span class="text-sm text-gray-700">{{ $path->creator?->name ?? __('Unbekannt') }}</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                            {{ $path->category?->name ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="text-sm font-medium text-gray-900">{{ number_format($path->enrollments_count) }}</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <button
                                wire:click="togglePublished('{{ $path->id }}')"
                                class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium transition
                                    {{ $path->is_published ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            >
                                <span class="h-2 w-2 rounded-full {{ $path->is_published ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $path->is_published ? __('Veröffentlicht') : __('Entwurf') }}
                            </button>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ $path->created_at->format('d.m.Y') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.paths.show', $path) }}" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <button wire:click="confirmDelete('{{ $path->id }}')" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-red-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Keine Lernpfade gefunden') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Passe deine Filterkriterien an.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($this->paths->hasPages())
            <div class="border-t border-gray-200 bg-white px-4 py-3">
                {{ $this->paths->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showDeleteModal', false)"></div>

                <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>

                <div class="relative inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                    {{ __('Lernpfad löschen') }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        {{ __('Bist du sicher, dass du diesen Lernpfad löschen möchtest? Alle zugehörigen Module, Schritte und Einschreibungen werden ebenfalls gelöscht.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button
                            wire:click="deletePath"
                            type="button"
                            class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            {{ __('Löschen') }}
                        </button>
                        <button
                            wire:click="$set('showDeleteModal', false)"
                            type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            {{ __('Abbrechen') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
