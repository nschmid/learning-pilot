<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.teams.index') }}" class="rounded-lg border border-gray-300 p-2 hover:bg-gray-50 transition">
                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Team erstellen') }}</h1>
                <p class="mt-1 text-gray-500">{{ __('Erstellen Sie ein neues Team/Schule') }}</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <form wire:submit="save" class="space-y-6">
            <!-- Team Name -->
            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Team-Name') }} *</label>
                <input type="text" id="name" wire:model="name"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="{{ __('z.B. Berufsschule Zürich') }}">
                @error('name') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <!-- Team Owner -->
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Team-Eigentümer') }} *</label>
                <div class="mb-2">
                    <input type="text" wire:model.live.debounce.300ms="ownerSearch"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-teal-500 focus:ring-teal-500"
                        placeholder="{{ __('Benutzer suchen...') }}">
                </div>
                <div class="max-h-48 overflow-y-auto rounded-lg border border-gray-200">
                    @forelse($this->availableOwners as $user)
                        <label class="flex cursor-pointer items-center gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $ownerId === (string) $user->id ? 'bg-teal-50' : '' }}">
                            <input type="radio" wire:model="ownerId" value="{{ $user->id }}"
                                class="text-teal-600 focus:ring-teal-500">
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </label>
                    @empty
                        <p class="px-4 py-3 text-sm text-gray-500">{{ __('Keine Benutzer gefunden') }}</p>
                    @endforelse
                </div>
                @error('ownerId') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Beschreibung') }}</label>
                <textarea id="description" wire:model="description" rows="3"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="{{ __('Optionale Beschreibung...') }}"></textarea>
                @error('description') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
                <a href="{{ route('admin.teams.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                    {{ __('Abbrechen') }}
                </a>
                <button type="submit" class="rounded-lg bg-teal-600 px-4 py-2 text-white hover:bg-teal-700">
                    {{ __('Team erstellen') }}
                </button>
            </div>
        </form>
    </div>
</div>
