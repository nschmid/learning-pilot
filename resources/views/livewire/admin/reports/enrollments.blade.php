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
        <span class="text-gray-700">{{ __('Einschreibungen') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Einschreibungs-Bericht') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Übersicht aller Kurs-Einschreibungen.') }}</p>
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
            <p class="text-sm text-gray-500">{{ __('Abgeschlossen') }}</p>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($this->stats['completed']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Pausiert') }}</p>
            <p class="text-3xl font-bold text-yellow-600">{{ number_format($this->stats['paused']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Ø Fortschritt') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->stats['avg_progress'] }}%</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Ø Lernzeit') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->stats['avg_time'] }}</p>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Top Paths -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Beliebteste Lernpfade') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->topPathsByEnrollments as $index => $item)
                    <div class="flex items-center justify-between px-6 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-sm text-gray-900">{{ $item['path_title'] }}</span>
                        </div>
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-sm font-medium text-gray-700">{{ $item['count'] }}</span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        {{ __('Keine Daten.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Enrollments Table -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Einschreibungen') }}</h2>
                <select wire:model.live="status" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('Alle Status') }}</option>
                    <option value="active">{{ __('Aktiv') }}</option>
                    <option value="completed">{{ __('Abgeschlossen') }}</option>
                    <option value="paused">{{ __('Pausiert') }}</option>
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Teilnehmer') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Lernpfad') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Fortschritt') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Datum') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($this->recentEnrollments as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-medium text-indigo-600">
                                            {{ substr($enrollment->user->name ?? '', 0, 2) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $enrollment->user->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ \Illuminate\Support\Str::limit($enrollment->learningPath->title ?? '-', 30) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @switch($enrollment->status->value)
                                        @case('active')
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">{{ __('Aktiv') }}</span>
                                            @break
                                        @case('completed')
                                            <span class="rounded-full bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-800">{{ __('Abgeschlossen') }}</span>
                                            @break
                                        @case('paused')
                                            <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">{{ __('Pausiert') }}</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-16 overflow-hidden rounded-full bg-gray-200">
                                            <div class="h-full rounded-full bg-indigo-600" style="width: {{ $enrollment->progress_percent }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $enrollment->progress_percent }}%</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ $enrollment->created_at->format('d.m.Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    {{ __('Keine Einschreibungen im gewählten Zeitraum.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->recentEnrollments->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $this->recentEnrollments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
