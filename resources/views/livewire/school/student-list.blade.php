<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Benutzerverwaltung') }}
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('school.students.import') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('CSV importieren') }}
                </a>
                <button wire:click="openInviteModal" class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-500">
                    {{ __('Einladen') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-lg bg-green-50 p-4">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-lg bg-red-50 p-4">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Stats -->
            <div class="mb-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-white p-4 shadow">
                    <div class="text-sm text-gray-500">{{ __('Lernende') }}</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</div>
                </div>
                <div class="rounded-lg bg-white p-4 shadow">
                    <div class="text-sm text-gray-500">{{ __('Dozenten') }}</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalInstructors }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row">
                <div class="flex-1">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Suchen nach Name oder E-Mail...') }}"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                    >
                </div>
                <select
                    wire:model.live="role"
                    class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                >
                    <option value="">{{ __('Alle Rollen') }}</option>
                    <option value="learner">{{ __('Lernende') }}</option>
                    <option value="instructor">{{ __('Dozenten') }}</option>
                </select>
            </div>

            <!-- User List -->
            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('Name') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('E-Mail') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('Rolle') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('Beigetreten') }}
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">{{ __('Aktionen') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($users as $user)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="size-10 flex-shrink-0">
                                            <img class="size-10 rounded-full" src="{{ $user->profile_photo_url }}" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @php
                                        $pivotRole = $user->pivot->role ?? 'learner';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $pivotRole === 'instructor' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $pivotRole === 'instructor' ? __('Dozent') : __('Lernende/r') }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ $user->created_at->format('d.m.Y') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    @if($user->id !== auth()->user()->currentTeam->user_id)
                                        <button
                                            wire:click="removeUser('{{ $user->id }}')"
                                            wire:confirm="{{ __('Möchten Sie diesen Benutzer wirklich entfernen?') }}"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            {{ __('Entfernen') }}
                                        </button>
                                    @else
                                        <span class="text-gray-400">{{ __('Eigentümer') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                    {{ __('Keine Benutzer gefunden.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($users->hasPages())
                    <div class="border-t border-gray-200 px-4 py-3">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Invite Modal -->
    @if($showInviteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" wire:click="$set('showInviteModal', false)">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                    <form wire:submit="invite">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Benutzer einladen') }}</h3>

                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="inviteName" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                                    <input
                                        type="text"
                                        id="inviteName"
                                        wire:model="inviteName"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                    >
                                    @error('inviteName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="inviteEmail" class="block text-sm font-medium text-gray-700">{{ __('E-Mail') }}</label>
                                    <input
                                        type="email"
                                        id="inviteEmail"
                                        wire:model="inviteEmail"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                    >
                                    @error('inviteEmail') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="inviteRole" class="block text-sm font-medium text-gray-700">{{ __('Rolle') }}</label>
                                    <select
                                        id="inviteRole"
                                        wire:model="inviteRole"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                    >
                                        <option value="learner">{{ __('Lernende/r') }}</option>
                                        <option value="instructor">{{ __('Dozent/in') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ __('Einladen') }}
                            </button>
                            <button type="button" wire:click="$set('showInviteModal', false)" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 sm:ml-3 sm:mt-0 sm:w-auto sm:text-sm">
                                {{ __('Abbrechen') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
