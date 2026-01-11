<div>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('admin.settings.index') }}" wire:navigate class="hover:text-gray-700">{{ __('Einstellungen') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ __('KI') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('KI-Einstellungen') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Konfiguration und Nutzungsübersicht der KI-Funktionen.') }}</p>
    </div>

    <!-- API Status -->
    <div class="mb-8 rounded-xl border {{ $this->aiConfigured ? 'border-green-200 bg-green-50' : 'border-yellow-200 bg-yellow-50' }} p-6">
        <div class="flex items-start gap-4">
            @if($this->aiConfigured)
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-green-900">{{ __('KI-API ist konfiguriert') }}</h3>
                    <p class="mt-1 text-sm text-green-700">{{ __('Die Anthropic API ist aktiv und KI-Funktionen sind verfügbar.') }}</p>
                </div>
            @else
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-yellow-900">{{ __('KI-API nicht konfiguriert') }}</h3>
                    <p class="mt-1 text-sm text-yellow-700">{{ __('Setze ANTHROPIC_API_KEY in der .env Datei.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Usage Stats -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Anfragen (Monat)') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($this->usageStats['total_requests']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Tokens verbraucht') }}</p>
            <p class="text-3xl font-bold text-indigo-600">{{ number_format($this->usageStats['total_tokens']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Aktive Nutzer') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->usageStats['unique_users'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Ø Tokens/Anfrage') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($this->usageStats['avg_tokens_per_request']) }}</p>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Model Configuration -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Modell-Konfiguration') }}</h2>
            </div>
            <div class="p-6">
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Provider') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ ucfirst($this->aiConfig['provider']) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Standard-Modell') }}</dt>
                        <dd class="rounded bg-gray-100 px-2 py-1 text-xs font-mono text-gray-700">{{ $this->aiConfig['default_model'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Tutor-Modell') }}</dt>
                        <dd class="rounded bg-gray-100 px-2 py-1 text-xs font-mono text-gray-700">{{ $this->aiConfig['tutor_model'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Übungs-Modell') }}</dt>
                        <dd class="rounded bg-gray-100 px-2 py-1 text-xs font-mono text-gray-700">{{ $this->aiConfig['practice_model'] }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Default Quotas -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Standard-Limits') }}</h2>
            </div>
            <div class="p-6">
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Monatliches Token-Limit') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ number_format($defaultMonthlyTokens) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Tägliches Anfragen-Limit') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ number_format($defaultDailyRequests) }}</dd>
                    </div>
                    <div class="border-t border-gray-200 pt-4">
                        <dt class="text-sm text-gray-500">{{ __('Nutzer mit Quota') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->quotaStats['users_with_quota'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Am Limit') }}</dt>
                        <dd class="text-sm font-medium {{ $this->quotaStats['users_at_limit'] > 0 ? 'text-orange-600' : 'text-green-600' }}">
                            {{ $this->quotaStats['users_at_limit'] }}
                        </dd>
                    </div>
                </dl>
                <div class="mt-6">
                    <a href="{{ route('admin.ai.quotas') }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-800">
                        {{ __('Quotas verwalten') }} &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Top Features -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Top KI-Funktionen (diesen Monat)') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Funktion') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Anfragen') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Tokens') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($this->topFeatures as $feature)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $feature['feature'] }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ number_format($feature['count']) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ number_format($feature['tokens']) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                    {{ __('Noch keine KI-Nutzung in diesem Monat.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 px-6 py-3">
                <a href="{{ route('admin.ai.usage') }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-800">
                    {{ __('Vollständige Nutzungsstatistik') }} &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
