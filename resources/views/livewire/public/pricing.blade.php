<div>
    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="text-base font-semibold text-teal-600">{{ __('Preise') }}</h2>
                <p class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    {{ __('Bezahlen Sie pro Lernenden') }}
                </p>
            </div>
            <p class="mx-auto mt-6 max-w-2xl text-center text-lg text-gray-600">
                {{ __('Einfache, transparente Preise. Zahlen Sie nur für die Lernenden, die Sie haben. 14 Tage kostenlose Testphase.') }}
            </p>

            <!-- Student Count Calculator -->
            <div class="mx-auto mt-10 max-w-xl">
                <div class="rounded-2xl bg-gray-50 p-6 ring-1 ring-gray-200">
                    <label class="block text-center text-sm font-medium text-gray-700">
                        {{ __('Wie viele Lernende haben Sie?') }}
                    </label>
                    <div class="mt-4 flex items-center gap-4">
                        <input
                            type="range"
                            min="10"
                            max="2000"
                            step="10"
                            wire:model.live="studentCount"
                            class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-200 accent-teal-600"
                        >
                    </div>
                    <div class="mt-3 flex items-center justify-center gap-2">
                        <input
                            type="number"
                            min="10"
                            max="2000"
                            wire:model.live.debounce.300ms="studentCount"
                            class="w-24 rounded-lg border-gray-300 text-center text-xl font-bold text-gray-900 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        >
                        <span class="text-gray-600">{{ __('Lernende') }}</span>
                    </div>
                </div>
            </div>

            <!-- Billing & Currency Toggle -->
            <div class="mt-8 flex flex-col items-center gap-4 sm:flex-row sm:justify-center sm:gap-8">
                <div class="inline-flex items-center rounded-lg bg-gray-100 p-1">
                    <button
                        wire:click="setBilling('monthly')"
                        class="rounded-md px-4 py-2 text-sm font-medium transition {{ $billing === 'monthly' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-900' }}"
                    >
                        {{ __('Monatlich') }}
                    </button>
                    <button
                        wire:click="setBilling('yearly')"
                        class="rounded-md px-4 py-2 text-sm font-medium transition {{ $billing === 'yearly' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-900' }}"
                    >
                        {{ __('Jährlich') }}
                        <span class="ml-1 rounded-full bg-teal-100 px-2 py-0.5 text-xs font-semibold text-teal-700">-17%</span>
                    </button>
                </div>

                <div class="inline-flex rounded-lg bg-gray-100 p-1">
                    @foreach($currencies as $code => $currency)
                        <button
                            wire:click="setCurrency('{{ $code }}')"
                            class="rounded-md px-3 py-2 text-sm font-medium transition {{ $this->currency === $code ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-900' }}"
                        >
                            {{ $currency['code'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Pricing Cards -->
            <div class="isolate mx-auto mt-12 grid max-w-md grid-cols-1 gap-6 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                @foreach($plans as $plan)
                    <div class="relative rounded-3xl p-8 ring-1 {{ $plan['highlighted'] ? 'bg-gray-900 ring-gray-900' : 'bg-white ring-gray-200' }} xl:p-10">
                        @if($plan['highlighted'])
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                                <span class="inline-flex items-center rounded-full bg-teal-500 px-4 py-1 text-sm font-semibold text-white">
                                    {{ __('Empfohlen') }}
                                </span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-x-4">
                            <h3 class="text-lg font-semibold {{ $plan['highlighted'] ? 'text-white' : 'text-gray-900' }}">
                                {{ $plan['name'] }}
                            </h3>
                        </div>
                        <p class="mt-4 text-sm {{ $plan['highlighted'] ? 'text-gray-300' : 'text-gray-600' }}">
                            {{ $plan['description'] }}
                        </p>

                        <!-- Per-Student Price -->
                        @if($plan['contact_sales'] ?? false)
                            <div class="mt-6">
                                <span class="text-2xl font-bold tracking-tight {{ $plan['highlighted'] ? 'text-white' : 'text-gray-900' }}">
                                    {{ __('Auf Anfrage') }}
                                </span>
                                <p class="mt-1 text-sm {{ $plan['highlighted'] ? 'text-gray-400' : 'text-gray-500' }}">
                                    {{ __('Ab 500 Lernenden') }}
                                </p>
                            </div>
                        @else
                            <div class="mt-6">
                                <div class="flex items-baseline gap-x-1">
                                    <span class="text-4xl font-bold tracking-tight {{ $plan['highlighted'] ? 'text-white' : 'text-gray-900' }}">
                                        {{ $plan['formatted_per_student'] }}
                                    </span>
                                    <span class="text-sm font-semibold {{ $plan['highlighted'] ? 'text-gray-300' : 'text-gray-600' }}">
                                        {{ __('/ Lernender / Monat') }}
                                    </span>
                                </div>
                                <div class="mt-3 rounded-lg {{ $plan['highlighted'] ? 'bg-gray-800' : 'bg-gray-50' }} p-3">
                                    <p class="text-xs {{ $plan['highlighted'] ? 'text-gray-400' : 'text-gray-500' }}">
                                        {{ __('Für :count Lernende:', ['count' => $studentCount]) }}
                                    </p>
                                    <p class="mt-1 text-lg font-bold {{ $plan['highlighted'] ? 'text-white' : 'text-gray-900' }}">
                                        {{ $plan['formatted_total'] }} <span class="text-sm font-normal {{ $plan['highlighted'] ? 'text-gray-400' : 'text-gray-500' }}">{{ $plan['interval'] }}</span>
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($plan['contact_sales'] ?? false)
                            <a
                                href="{{ route('contact') }}"
                                class="mt-6 block rounded-md px-3 py-2 text-center text-sm font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 bg-gray-800 text-white hover:bg-gray-700 focus-visible:outline-gray-800"
                            >
                                {{ __('Kontakt aufnehmen') }}
                            </a>
                        @else
                            <a
                                href="{{ route('register') }}?plan={{ $plan['id'] }}"
                                class="mt-6 block rounded-md px-3 py-2 text-center text-sm font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 {{ $plan['highlighted'] ? 'bg-white text-gray-900 hover:bg-gray-100 focus-visible:outline-white' : 'bg-teal-600 text-white hover:bg-teal-500 focus-visible:outline-teal-600' }}"
                            >
                                {{ __('14 Tage kostenlos testen') }}
                            </a>
                        @endif

                        <!-- Features -->
                        <ul role="list" class="mt-8 space-y-3 text-sm {{ $plan['highlighted'] ? 'text-gray-300' : 'text-gray-600' }}">
                            <!-- Limits -->
                            <li class="flex gap-x-3">
                                <svg class="h-6 w-5 flex-none {{ $plan['highlighted'] ? 'text-white' : 'text-teal-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Unbegrenzte Lernpfade & Kurse') }}
                            </li>
                            <li class="flex gap-x-3">
                                <svg class="h-6 w-5 flex-none {{ $plan['highlighted'] ? 'text-white' : 'text-teal-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Assessments & Zertifikate') }}
                            </li>

                            @foreach($plan['features'] as $feature => $enabled)
                                @if($enabled)
                                    <li class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none {{ $plan['highlighted'] ? 'text-white' : 'text-teal-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                        </svg>
                                        @if($feature === 'ai_tutor')
                                            {{ __('KI-Tutor für personalisiertes Lernen') }}
                                        @elseif($feature === 'ai_practice')
                                            {{ __('KI-generierte Übungsfragen') }}
                                        @elseif($feature === 'ai_explanations')
                                            {{ __('KI-Erklärungen bei Fehlern') }}
                                        @elseif($feature === 'advanced_analytics')
                                            {{ __('Erweiterte Lernstatistiken') }}
                                        @elseif($feature === 'custom_branding')
                                            {{ __('Eigenes Logo & Branding') }}
                                        @elseif($feature === 'api_access')
                                            {{ __('API-Zugang') }}
                                        @elseif($feature === 'sso')
                                            {{ __('Single Sign-On (SSO)') }}
                                        @elseif($feature === 'priority_support')
                                            {{ __('Prioritäts-Support') }}
                                        @elseif($feature === 'dedicated_support')
                                            {{ __('Dedizierter Ansprechpartner') }}
                                        @elseif($feature === 'custom_integrations')
                                            {{ __('Individuelle Integrationen') }}
                                        @elseif($feature === 'sla')
                                            {{ __('Service Level Agreement (SLA)') }}
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $feature)) }}
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            <!-- Comparison Note -->
            <p class="mx-auto mt-12 max-w-2xl text-center text-sm text-gray-500">
                {{ __('Alle Preise exkl. MwSt. Jährliche Abrechnung bietet 2 Monate gratis. Mengenrabatte ab 500 Lernenden verfügbar.') }}
            </p>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-gray-50">
        <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-4xl divide-y divide-gray-900/10">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('Häufige Fragen') }}</h2>
                <dl class="mt-10 space-y-6 divide-y divide-gray-900/10">
                    <div class="pt-6" x-data="{ open: false }">
                        <dt>
                            <button type="button" class="flex w-full items-start justify-between text-left text-gray-900" @click="open = !open">
                                <span class="text-base font-semibold">{{ __('Was zählt als "Lernender"?') }}</span>
                                <span class="ml-6 flex h-7 items-center">
                                    <svg class="size-6" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </span>
                            </button>
                        </dt>
                        <dd class="mt-2 pr-12" x-show="open" x-cloak>
                            <p class="text-base text-gray-600">
                                {{ __('Ein Lernender ist jeder aktive Benutzer, der auf Kursinhalte zugreifen kann. Dozenten und Administratoren zählen nicht als Lernende. Sie können Lernende jederzeit hinzufügen oder entfernen.') }}
                            </p>
                        </dd>
                    </div>
                    <div class="pt-6" x-data="{ open: false }">
                        <dt>
                            <button type="button" class="flex w-full items-start justify-between text-left text-gray-900" @click="open = !open">
                                <span class="text-base font-semibold">{{ __('Wie funktioniert die Testphase?') }}</span>
                                <span class="ml-6 flex h-7 items-center">
                                    <svg class="size-6" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </span>
                            </button>
                        </dt>
                        <dd class="mt-2 pr-12" x-show="open" x-cloak>
                            <p class="text-base text-gray-600">
                                {{ __('Sie können LearningPilot 14 Tage lang kostenlos und unverbindlich testen - mit allen Funktionen. Keine Kreditkarte erforderlich. Nach Ablauf der Testphase wählen Sie einen Plan oder Ihr Konto wird automatisch pausiert.') }}
                            </p>
                        </dd>
                    </div>
                    <div class="pt-6" x-data="{ open: false }">
                        <dt>
                            <button type="button" class="flex w-full items-start justify-between text-left text-gray-900" @click="open = !open">
                                <span class="text-base font-semibold">{{ __('Kann ich die Anzahl Lernende ändern?') }}</span>
                                <span class="ml-6 flex h-7 items-center">
                                    <svg class="size-6" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </span>
                            </button>
                        </dt>
                        <dd class="mt-2 pr-12" x-show="open" x-cloak>
                            <p class="text-base text-gray-600">
                                {{ __('Ja, Sie können jederzeit Lernende hinzufügen oder entfernen. Bei Erhöhung wird anteilig abgerechnet. Bei Reduzierung wird die Änderung zum nächsten Abrechnungszeitraum wirksam.') }}
                            </p>
                        </dd>
                    </div>
                    <div class="pt-6" x-data="{ open: false }">
                        <dt>
                            <button type="button" class="flex w-full items-start justify-between text-left text-gray-900" @click="open = !open">
                                <span class="text-base font-semibold">{{ __('Welche Zahlungsmethoden akzeptieren Sie?') }}</span>
                                <span class="ml-6 flex h-7 items-center">
                                    <svg class="size-6" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </span>
                            </button>
                        </dt>
                        <dd class="mt-2 pr-12" x-show="open" x-cloak>
                            <p class="text-base text-gray-600">
                                {{ __('Wir akzeptieren alle gängigen Kreditkarten (Visa, Mastercard, American Express) sowie SEPA-Lastschrift für Kunden in der EU. Für Schulen bieten wir auch Rechnungszahlung an.') }}
                            </p>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
