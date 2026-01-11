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
        <span class="text-gray-700">{{ __('KI-Nutzung') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('KI-Nutzungsbericht') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Detaillierte Analyse der KI-Funktionsnutzung.') }}</p>
        </div>
        <div class="flex items-center gap-4">
            <select wire:model.live="period" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="week">{{ __('Letzte Woche') }}</option>
                <option value="month">{{ __('Letzter Monat') }}</option>
                <option value="quarter">{{ __('Letztes Quartal') }}</option>
                <option value="year">{{ __('Letztes Jahr') }}</option>
            </select>
        </div>
    </div>

    <!-- Stats -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Anfragen') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($this->stats['total_requests']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Tokens verbraucht') }}</p>
            <p class="text-3xl font-bold text-indigo-600">{{ number_format($this->stats['total_tokens']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Eindeutige Nutzer') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->stats['unique_users'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Ø Antwortzeit') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->stats['avg_response_time'] }}ms</p>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Feature Breakdown -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Nach Funktion') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->featureBreakdown as $item)
                    <div class="px-6 py-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900">{{ $item['feature'] }}</span>
                            <span class="text-sm text-gray-500">{{ number_format($item['count']) }}</span>
                        </div>
                        <div class="mt-1 text-xs text-gray-400">{{ number_format($item['tokens']) }} {{ __('Tokens') }}</div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        {{ __('Keine Daten vorhanden.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Usage Logs -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Nutzungsverlauf') }}</h2>
                <select wire:model.live="feature" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('Alle Funktionen') }}</option>
                    @foreach($this->availableFeatures as $f)
                        <option value="{{ $f }}">{{ $f }}</option>
                    @endforeach
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Nutzer') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Funktion') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Tokens') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Zeit') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Datum') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($this->logs as $log)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    {{ $log->user?->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="rounded-full bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-800">
                                        {{ $log->feature }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ number_format($log->tokens_used) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ $log->response_time_ms ?? '-' }}ms
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ $log->created_at->format('d.m.Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    {{ __('Keine KI-Nutzung im gewählten Zeitraum.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->logs->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $this->logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
