<div>
    <!-- Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Benutzerverwaltung') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Verwalte alle Benutzer der Plattform') }}</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ __('Neuer Benutzer') }}
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4">
        <div class="grid gap-4 sm:grid-cols-4">
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
                        placeholder="{{ __('Name oder E-Mail suchen...') }}"
                        class="block w-full rounded-lg border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <label for="role" class="sr-only">{{ __('Rolle') }}</label>
                <select
                    wire:model.live="role"
                    id="role"
                    class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">{{ __('Alle Rollen') }}</option>
                    @foreach($this->roles as $r)
                        <option value="{{ $r['value'] }}">{{ $r['label'] }}</option>
                    @endforeach
                </select>
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
                    <option value="active">{{ __('Aktiv') }}</option>
                    <option value="inactive">{{ __('Inaktiv') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        <button wire:click="sortBy('name')" class="group inline-flex items-center gap-1">
                            {{ __('Benutzer') }}
                            @if($sortBy === 'name')
                                <svg class="h-4 w-4 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        {{ __('Rolle') }}
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
                @forelse($this->users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                @if($user->role->value === 'admin') bg-red-100 text-red-700
                                @elseif($user->role->value === 'instructor') bg-blue-100 text-blue-700
                                @else bg-green-100 text-green-700
                                @endif">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <button
                                wire:click="toggleStatus('{{ $user->id }}')"
                                class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium transition
                                    {{ $user->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            >
                                <span class="h-2 w-2 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $user->is_active ? __('Aktiv') : __('Inaktiv') }}
                            </button>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ $user->created_at->format('d.m.Y') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-indigo-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button wire:click="confirmDelete('{{ $user->id }}')" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-red-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Keine Benutzer gefunden') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Passe deine Filterkriterien an oder erstelle einen neuen Benutzer.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($this->users->hasPages())
            <div class="border-t border-gray-200 bg-white px-4 py-3">
                {{ $this->users->links() }}
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
                                    {{ __('Benutzer löschen') }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        {{ __('Bist du sicher, dass du diesen Benutzer löschen möchtest? Diese Aktion kann nicht rückgängig gemacht werden.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button
                            wire:click="deleteUser"
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
