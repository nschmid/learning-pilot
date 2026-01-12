<div>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-teal-600 transition">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('admin.reports.index') }}" wire:navigate class="hover:text-teal-600 transition">{{ __('Berichte') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ __('Lernpfade') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Lernpfad-Bericht') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Übersicht und Statistiken aller Lernpfade.') }}</p>
    </div>

    <!-- Stats -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Lernpfade gesamt') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->stats['total_paths'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Veröffentlicht') }}</p>
            <p class="text-3xl font-bold text-green-600">{{ $this->stats['published'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Entwurf') }}</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $this->stats['draft'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Module gesamt') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->stats['total_modules'] }}</p>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <!-- By Category -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Nach Kategorie') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->pathsByCategory as $item)
                    <div class="flex items-center justify-between px-6 py-3">
                        <span class="text-sm text-gray-900">{{ $item['category'] }}</span>
                        <span class="rounded-full bg-teal-100 px-2.5 py-1 text-sm font-medium text-teal-800">{{ $item['count'] }}</span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        {{ __('Keine Daten.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- By Difficulty -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Nach Schwierigkeit') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->pathsByDifficulty as $item)
                    <div class="flex items-center justify-between px-6 py-3">
                        <span class="text-sm text-gray-900">{{ ucfirst($item['difficulty']) }}</span>
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-sm font-medium text-gray-700">{{ $item['count'] }}</span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        {{ __('Keine Daten.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Schnellzugriff') }}</h2>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('admin.paths.index') }}" wire:navigate class="flex items-center justify-between rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                    <span class="text-sm font-medium text-gray-900">{{ __('Alle Lernpfade verwalten') }}</span>
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="{{ route('admin.categories.index') }}" wire:navigate class="flex items-center justify-between rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                    <span class="text-sm font-medium text-gray-900">{{ __('Kategorien verwalten') }}</span>
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Top Paths Table -->
    <div class="mt-8 rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Top Lernpfade') }}</h2>
            <select wire:model.live="sortBy" class="rounded-lg border-0 bg-gray-50 ring-1 ring-gray-200 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500">
                <option value="enrollments">{{ __('Nach Einschreibungen') }}</option>
                <option value="completion">{{ __('Nach Abschlüssen') }}</option>
                <option value="newest">{{ __('Neueste zuerst') }}</option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Lernpfad') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Ersteller') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Einschreibungen') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Abschlüsse') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Inhalt') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Erstellt') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($this->topPaths as $path)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-900">{{ $path['title'] }}</span>
                                    @if(!$path['is_published'])
                                        <span class="rounded bg-yellow-100 px-1.5 py-0.5 text-xs text-yellow-700">{{ __('Entwurf') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $path['creator'] }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $path['enrollments'] }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $path['completed'] }}</span>
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $path['completion_rate'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $path['completion_rate'] }}%
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $path['modules'] }} {{ __('Module') }}, {{ $path['steps'] }} {{ __('Schritte') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $path['created_at'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                {{ __('Keine Lernpfade vorhanden.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
