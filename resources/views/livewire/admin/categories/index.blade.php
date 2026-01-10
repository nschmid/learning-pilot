<div>
    <!-- Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Kategorien') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Verwalte Kategorien für Lernpfade') }}</p>
        </div>
        <button
            wire:click="openCreateModal"
            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ __('Neue Kategorie') }}
        </button>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search -->
    <div class="mb-6">
        <div class="relative max-w-md">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="{{ __('Kategorie suchen...') }}"
                class="block w-full rounded-lg border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>
    </div>

    <!-- Categories Tree -->
    <div class="rounded-xl border border-gray-200 bg-white">
        @forelse($this->categories as $category)
            <div wire:key="cat-{{ $category->id }}" class="border-b border-gray-200 last:border-b-0">
                <!-- Parent Category -->
                <div class="flex items-center justify-between p-4 hover:bg-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $category->name }}</p>
                            <p class="text-sm text-gray-500">{{ $category->slug }} · {{ $category->learning_paths_count }} {{ __('Lernpfade') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            wire:click="toggleActive('{{ $category->id }}')"
                            class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium transition
                                {{ $category->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            <span class="h-2 w-2 rounded-full {{ $category->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $category->is_active ? __('Aktiv') : __('Inaktiv') }}
                        </button>
                        <button wire:click="openEditModal('{{ $category->id }}')" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-indigo-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button wire:click="confirmDelete('{{ $category->id }}')" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Child Categories -->
                @if($category->children->count() > 0)
                    <div class="border-t border-gray-100 bg-gray-50">
                        @foreach($category->children as $child)
                            <div wire:key="cat-{{ $child->id }}" class="flex items-center justify-between border-b border-gray-100 py-3 pl-14 pr-4 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-gray-700">{{ $child->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $child->slug }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        wire:click="toggleActive('{{ $child->id }}')"
                                        class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium
                                            {{ $child->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}"
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full {{ $child->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                        {{ $child->is_active ? __('Aktiv') : __('Inaktiv') }}
                                    </button>
                                    <button wire:click="openEditModal('{{ $child->id }}')" class="rounded p-1 text-gray-400 hover:text-indigo-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete('{{ $child->id }}')" class="rounded p-1 text-gray-400 hover:text-red-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Keine Kategorien') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('Erstelle deine erste Kategorie.') }}</p>
                <button
                    wire:click="openCreateModal"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Neue Kategorie') }}
                </button>
            </div>
        @endforelse
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>

                <div class="relative inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                    <form wire:submit="save">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">
                                {{ $editingId ? __('Kategorie bearbeiten') : __('Neue Kategorie') }}
                            </h3>

                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }} <span class="text-red-500">*</span></label>
                                    <input wire:model.live="name" type="text" id="name" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Slug -->
                                <div>
                                    <label for="slug" class="block text-sm font-medium text-gray-700">{{ __('Slug') }} <span class="text-red-500">*</span></label>
                                    <input wire:model="slug" type="text" id="slug" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Beschreibung') }}</label>
                                    <textarea wire:model="description" id="description" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Parent -->
                                <div>
                                    <label for="parentId" class="block text-sm font-medium text-gray-700">{{ __('Übergeordnete Kategorie') }}</label>
                                    <select wire:model="parentId" id="parentId" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">{{ __('Keine (Hauptkategorie)') }}</option>
                                        @foreach($this->parentCategories as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label for="sortOrder" class="block text-sm font-medium text-gray-700">{{ __('Sortierung') }}</label>
                                    <input wire:model="sortOrder" type="number" id="sortOrder" min="0" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <!-- Active -->
                                <div>
                                    <label class="flex items-center gap-3">
                                        <input wire:model="isActive" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm font-medium text-gray-700">{{ __('Aktiv') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingId ? __('Speichern') : __('Erstellen') }}
                            </button>
                            <button type="button" wire:click="closeModal" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                                {{ __('Abbrechen') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

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
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Kategorie löschen') }}</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">{{ __('Bist du sicher? Unterkategorien werden zu Hauptkategorien.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button wire:click="deleteCategory" type="button" class="inline-flex w-full justify-center rounded-md bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('Löschen') }}
                        </button>
                        <button wire:click="$set('showDeleteModal', false)" type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                            {{ __('Abbrechen') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
