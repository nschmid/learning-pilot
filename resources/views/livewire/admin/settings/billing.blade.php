<div>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-teal-600 transition">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('admin.settings.index') }}" wire:navigate class="hover:text-teal-600 transition">{{ __('Einstellungen') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ __('Abrechnung') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Abrechnungseinstellungen') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Stripe-Integration und Abrechnungsübersicht.') }}</p>
    </div>

    <!-- Stripe Status -->
    <div class="mb-8 rounded-xl border {{ $this->stripeConfigured ? 'border-green-200 bg-green-50' : 'border-yellow-200 bg-yellow-50' }} p-6">
        <div class="flex items-start gap-4">
            @if($this->stripeConfigured)
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-green-900">{{ __('Stripe ist konfiguriert') }}</h3>
                    <p class="mt-1 text-sm text-green-700">{{ __('Die Stripe-Integration ist aktiv und bereit für Zahlungen.') }}</p>
                </div>
            @else
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-yellow-900">{{ __('Stripe nicht konfiguriert') }}</h3>
                    <p class="mt-1 text-sm text-yellow-700">{{ __('Setze STRIPE_KEY und STRIPE_SECRET in der .env Datei.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="mb-8 grid gap-6 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Teams gesamt') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->billingStats['total_teams'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Mit Stripe-Konto') }}</p>
            <p class="text-3xl font-bold text-teal-600">{{ $this->billingStats['teams_with_billing'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Aktive Abonnements') }}</p>
            <p class="text-3xl font-bold text-green-600">{{ $this->billingStats['active_subscriptions'] }}</p>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Configuration -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Konfiguration') }}</h2>
            </div>
            <div class="p-6">
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Stripe Public Key') }}</dt>
                        <dd class="text-sm font-medium {{ config('cashier.key') ? 'text-green-600' : 'text-red-600' }}">
                            {{ config('cashier.key') ? __('Konfiguriert') : __('Nicht gesetzt') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Stripe Secret Key') }}</dt>
                        <dd class="text-sm font-medium {{ config('cashier.secret') ? 'text-green-600' : 'text-red-600' }}">
                            {{ config('cashier.secret') ? __('Konfiguriert') : __('Nicht gesetzt') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Webhook Secret') }}</dt>
                        <dd class="text-sm font-medium {{ $this->webhookStatus['configured'] ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $this->webhookStatus['configured'] ? __('Konfiguriert') : __('Optional') }}
                        </dd>
                    </div>
                    <div class="border-t border-gray-200 pt-4">
                        <dt class="text-sm text-gray-500">{{ __('Webhook URL') }}</dt>
                        <dd class="mt-1 rounded bg-gray-100 p-2 text-xs font-mono text-gray-700">
                            {{ $this->webhookStatus['url'] }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Teams with Billing -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Teams mit Abrechnung') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($this->recentTransactions as $team)
                    <div class="flex items-center justify-between px-6 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-900">{{ $team['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $team['owner'] }}</p>
                        </div>
                        <div class="ml-4">
                            @if($team['has_subscription'])
                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">{{ __('Aktiv') }}</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">{{ __('Inaktiv') }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        {{ __('Keine Teams mit Abrechnungsdaten.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
