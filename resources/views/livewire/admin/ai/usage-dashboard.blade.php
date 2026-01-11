<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('KI-Nutzungsstatistiken') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Übersicht über die KI-Nutzung auf der Plattform') }}</p>
    </div>

    <!-- Period Filter -->
    <div class="mb-6 flex gap-2">
        <button wire:click="setPeriod('week')"
            class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $period === 'week' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
            {{ __('Letzte Woche') }}
        </button>
        <button wire:click="setPeriod('month')"
            class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $period === 'month' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
            {{ __('Letzter Monat') }}
        </button>
        <button wire:click="setPeriod('year')"
            class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $period === 'year' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
            {{ __('Letztes Jahr') }}
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Tokens -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Tokens verbraucht') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($this->totalTokensUsed) }}</p>
                </div>
                <div class="rounded-full bg-indigo-100 p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Requests -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Anfragen') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($this->totalRequests) }}</p>
                </div>
                <div class="rounded-full bg-purple-100 p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Erfolgsrate') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $this->successRate }}%</p>
                </div>
                <div class="rounded-full bg-green-100 p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Response Time -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Durchschn. Antwortzeit') }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($this->averageResponseTime) }}ms</p>
                </div>
                <div class="rounded-full bg-orange-100 p-3">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="mb-8 grid gap-6 lg:grid-cols-3">
        <!-- Quick Stats -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Schnellstatistiken') }}</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">{{ __('Aktive Quotas') }}</span>
                    <span class="font-semibold text-gray-900">{{ $this->activeQuotas }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">{{ __('Quotas nahe Limit') }}</span>
                    <span class="font-semibold text-yellow-600">{{ $this->quotasNearLimit }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">{{ __('Offenes Feedback') }}</span>
                    <span class="font-semibold text-red-600">{{ $this->unresolvedFeedback }}</span>
                </div>
            </div>
        </div>

        <!-- Usage by Service -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Nutzung nach Service') }}</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="pb-3 text-left text-sm font-medium text-gray-500">{{ __('Service') }}</th>
                            <th class="pb-3 text-right text-sm font-medium text-gray-500">{{ __('Anfragen') }}</th>
                            <th class="pb-3 text-right text-sm font-medium text-gray-500">{{ __('Tokens') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($this->usageByService as $usage)
                            <tr>
                                <td class="py-3 text-gray-900">{{ $usage['service'] }}</td>
                                <td class="py-3 text-right text-gray-700">{{ number_format($usage['count']) }}</td>
                                <td class="py-3 text-right text-gray-700">{{ number_format($usage['tokens']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-sm text-gray-500">{{ __('Keine Daten vorhanden') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Third Row -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Top Users -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Top-Nutzer (nach Tokens)') }}</h2>
                <a href="{{ route('admin.ai.quotas') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ __('Alle anzeigen') }}</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->topUsers as $usage)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="font-medium text-gray-900">{{ $usage->user->name ?? __('Gelöscht') }}</p>
                            <p class="text-sm text-gray-500">{{ $usage->user->email ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">{{ number_format($usage->tokens) }}</p>
                            <p class="text-sm text-gray-500">{{ number_format($usage->requests) }} {{ __('Anfragen') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="py-4 text-center text-sm text-gray-500">{{ __('Keine Daten vorhanden') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Errors -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Letzte Fehler') }}</h2>
            <div class="divide-y divide-gray-100">
                @forelse($this->recentErrors as $error)
                    <div class="py-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ $error->user->name ?? __('Unbekannt') }}</span>
                            <span class="text-sm text-gray-500">{{ $error->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1 text-sm text-red-600">{{ Str::limit($error->error_message, 80) }}</p>
                        <span class="mt-1 inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{ $error->service_type->label() }}</span>
                    </div>
                @empty
                    <div class="flex items-center justify-center py-8 text-gray-500">
                        <svg class="mr-2 h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('Keine Fehler im Zeitraum') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
