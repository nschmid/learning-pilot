<div>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('admin.reports.index') }}" wire:navigate class="hover:text-gray-700">{{ __('Berichte') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ __('Benutzer') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Benutzer-Bericht') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Übersicht aller registrierten Benutzer.') }}</p>
        </div>
        <select wire:model.live="period" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="week">{{ __('Letzte Woche') }}</option>
            <option value="month">{{ __('Letzter Monat') }}</option>
            <option value="quarter">{{ __('Letztes Quartal') }}</option>
            <option value="year">{{ __('Letztes Jahr') }}</option>
            <option value="all">{{ __('Alle Zeit') }}</option>
        </select>
    </div>

    <!-- Stats -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Gesamt') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($this->stats['total']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Aktiv') }}</p>
            <p class="text-3xl font-bold text-green-600">{{ number_format($this->stats['active']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Inaktiv') }}</p>
            <p class="text-3xl font-bold text-gray-400">{{ number_format($this->stats['inactive']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Lernende') }}</p>
            <p class="text-3xl font-bold text-indigo-600">{{ number_format($this->stats['learners']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Instruktoren') }}</p>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($this->stats['instructors']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Admins') }}</p>
            <p class="text-3xl font-bold text-orange-600">{{ number_format($this->stats['admins']) }}</p>
        </div>
    </div>

    <!-- Top Users -->
    <div class="mb-8 grid gap-8 lg:grid-cols-2">
        <!-- Top Learners -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Top Lernende') }}</h2>
                <p class="text-sm text-gray-500">{{ __('Nach Punkten') }}</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->topLearners as $index => $learner)
                    <div class="flex items-center justify-between px-6 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }} text-xs font-bold">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $learner['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $learner['completed'] }}/{{ $learner['enrollments'] }} {{ __('abgeschlossen') }}</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-sm font-semibold text-indigo-800">
                            {{ number_format($learner['points']) }} {{ __('Pkt.') }}
                        </span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        {{ __('Keine Lernenden.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Top Instructors -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Top Instruktoren') }}</h2>
                <p class="text-sm text-gray-500">{{ __('Nach Lernpfaden') }}</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->topInstructors as $index => $instructor)
                    <div class="flex items-center justify-between px-6 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $index < 3 ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-600' }} text-xs font-bold">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $instructor['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $instructor['published'] }}/{{ $instructor['paths'] }} {{ __('veröffentlicht') }}</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-600">
                            {{ number_format($instructor['enrollments']) }} {{ __('Einschr.') }}
                        </span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        {{ __('Keine Instruktoren.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Alle Benutzer') }}</h2>
                <div class="flex flex-wrap items-center gap-4">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Suchen...') }}"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    <select wire:model.live="role" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Alle Rollen') }}</option>
                        <option value="learner">{{ __('Lernende') }}</option>
                        <option value="instructor">{{ __('Instruktoren') }}</option>
                        <option value="admin">{{ __('Admins') }}</option>
                    </select>
                    <select wire:model.live="status" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Alle Status') }}</option>
                        <option value="active">{{ __('Aktiv') }}</option>
                        <option value="inactive">{{ __('Inaktiv') }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('name')" class="flex items-center gap-1 hover:text-gray-700">
                                {{ __('Benutzer') }}
                                @if($sortBy === 'name')
                                    <svg class="h-4 w-4 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Rolle') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Einschreibungen') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('created_at')" class="flex items-center gap-1 hover:text-gray-700">
                                {{ __('Registriert') }}
                                @if($sortBy === 'created_at')
                                    <svg class="h-4 w-4 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($this->users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center">
                                    @if($user->profile_photo_url)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $user->profile_photo_url }}" alt="">
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-medium text-indigo-600">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @switch($user->role->value)
                                    @case('learner')
                                        <span class="rounded-full bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-800">{{ __('Lernende/r') }}</span>
                                        @break
                                    @case('instructor')
                                        <span class="rounded-full bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-800">{{ __('Instruktor') }}</span>
                                        @break
                                    @case('admin')
                                        <span class="rounded-full bg-orange-100 px-2 py-1 text-xs font-semibold text-orange-800">{{ __('Admin') }}</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1 text-sm text-green-600">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('Aktiv') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-sm text-gray-400">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('Inaktiv') }}
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $user->enrollments_count }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $user->created_at->format('d.m.Y') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('admin.users.show', $user->id) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">
                                    {{ __('Details') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Benutzer gefunden') }}</h3>
                                <p class="mt-2 text-gray-500">{{ __('Versuche es mit anderen Filterkriterien.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->users->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $this->users->links() }}
            </div>
        @endif
    </div>
</div>
