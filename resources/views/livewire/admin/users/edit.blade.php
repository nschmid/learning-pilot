<div class="mx-auto max-w-2xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-teal-600 transition">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('admin.users.index') }}" wire:navigate class="hover:text-teal-600 transition">{{ __('Benutzer') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $user->name }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Benutzer bearbeiten') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Aktualisiere die Benutzerdaten.') }}</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Benutzerinformationen') }}</h2>

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700">
                    {{ __('Name') }} <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="name"
                    type="text"
                    id="name"
                    class="mt-1 block w-full rounded-lg border-0 bg-gray-50 ring-1 ring-gray-200 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700">
                    {{ __('E-Mail-Adresse') }} <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="email"
                    type="email"
                    id="email"
                    class="mt-1 block w-full rounded-lg border-0 bg-gray-50 ring-1 ring-gray-200 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">
                    {{ __('Neues Passwort') }}
                </label>
                <input
                    wire:model="password"
                    type="password"
                    id="password"
                    placeholder="{{ __('Leer lassen um beizubehalten') }}"
                    class="mt-1 block w-full rounded-lg border-0 bg-gray-50 ring-1 ring-gray-200 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700">
                    {{ __('Rolle') }} <span class="text-red-500">*</span>
                </label>
                <select
                    wire:model="role"
                    id="role"
                    class="mt-1 block w-full rounded-lg border-0 bg-gray-50 ring-1 ring-gray-200 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                >
                    @foreach($this->roles as $r)
                        <option value="{{ $r['value'] }}">{{ $r['label'] }}</option>
                    @endforeach
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Team -->
            <div class="mb-6">
                <label for="teamId" class="block text-sm font-medium text-gray-700">
                    {{ __('Team / Schule') }}
                </label>
                <select
                    wire:model="teamId"
                    id="teamId"
                    class="mt-1 block w-full rounded-lg border-0 bg-gray-50 ring-1 ring-gray-200 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                >
                    <option value="">{{ __('Kein Team') }}</option>
                    @foreach($this->teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
                @error('teamId')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="flex items-center gap-3">
                    <input
                        wire:model="isActive"
                        type="checkbox"
                        class="rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                    >
                    <span class="text-sm font-medium text-gray-700">{{ __('Benutzer ist aktiv') }}</span>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900">
                {{ __('Abbrechen') }}
            </a>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-6 py-2 font-medium text-white hover:bg-teal-700 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="save">{{ __('Änderungen speichern') }}</span>
                <span wire:loading wire:target="save">{{ __('Wird gespeichert...') }}</span>
            </button>
        </div>
    </form>
</div>
