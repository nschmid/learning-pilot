<div>
    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="text-base font-semibold text-indigo-600">{{ __('Preise') }}</h2>
                <p class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    {{ __('Einfache, transparente Preise') }}
                </p>
            </div>
            <p class="mx-auto mt-6 max-w-2xl text-center text-lg text-gray-600">
                {{ __('Wählen Sie den Plan, der am besten zu Ihrer Schule passt. Alle Pläne beinhalten 30 Tage kostenlose Testphase.') }}
            </p>

            <!-- Currency Toggle -->
            <div class="mt-10 flex justify-center">
                <div class="inline-flex rounded-lg bg-gray-100 p-1">
                    @foreach($currencies as $code => $currency)
                        <button
                            wire:click="setCurrency('{{ $code }}')"
                            class="rounded-md px-4 py-2 text-sm font-medium transition {{ $this->currency === $code ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-900' }}"
                        >
                            {{ $currency['code'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Pricing Cards -->
            <div class="isolate mx-auto mt-16 grid max-w-md grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                @foreach($plans as $plan)
                    <div class="rounded-3xl p-8 ring-1 {{ $plan['highlighted'] ? 'bg-gray-900 ring-gray-900' : 'bg-white ring-gray-200' }} xl:p-10">
                        <div class="flex items-center justify-between gap-x-4">
                            <h3 class="text-lg font-semibold {{ $plan['highlighted'] ? 'text-white' : 'text-gray-900' }}">
                                {{ $plan['name'] }}
                            </h3>
                            @if($plan['highlighted'])
                                <p class="rounded-full bg-indigo-500 px-2.5 py-1 text-xs font-semibold text-white">
                                    {{ __('Beliebt') }}
                                </p>
                            @endif
                        </div>
                        <p class="mt-4 text-sm {{ $plan['highlighted'] ? 'text-gray-300' : 'text-gray-600' }}">
                            {{ $plan['description'] }}
                        </p>
                        <p class="mt-6 flex items-baseline gap-x-1">
                            <span class="text-4xl font-bold tracking-tight {{ $plan['highlighted'] ? 'text-white' : 'text-gray-900' }}">
                                {{ $plan['formatted_price'] }}
                            </span>
                            <span class="text-sm font-semibold {{ $plan['highlighted'] ? 'text-gray-300' : 'text-gray-600' }}">
                                {{ $plan['interval'] }}
                            </span>
                        </p>
                        <a
                            href="{{ route('register') }}?plan={{ $plan['id'] }}"
                            class="mt-6 block rounded-md px-3 py-2 text-center text-sm font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 {{ $plan['highlighted'] ? 'bg-white text-gray-900 hover:bg-gray-100 focus-visible:outline-white' : 'bg-indigo-600 text-white hover:bg-indigo-500 focus-visible:outline-indigo-600' }}"
                        >
                            {{ __('Kostenlos testen') }}
                        </a>

                        <!-- Features -->
                        <ul role="list" class="mt-8 space-y-3 text-sm {{ $plan['highlighted'] ? 'text-gray-300' : 'text-gray-600' }}">
                            @if(isset($plan['limits']['students']))
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none {{ $plan['highlighted'] ? 'text-white' : 'text-indigo-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Bis zu :count Lernende', ['count' => $plan['limits']['students']]) }}
                                </li>
                            @endif
                            @if(isset($plan['limits']['instructors']))
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none {{ $plan['highlighted'] ? 'text-white' : 'text-indigo-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Bis zu :count Dozenten', ['count' => $plan['limits']['instructors']]) }}
                                </li>
                            @endif
                            @if(isset($plan['limits']['storage_gb']))
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none {{ $plan['highlighted'] ? 'text-white' : 'text-indigo-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __(':count GB Speicher', ['count' => $plan['limits']['storage_gb']]) }}
                                </li>
                            @endif
                            @foreach($plan['features'] as $feature => $enabled)
                                @if($enabled)
                                    <li class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none {{ $plan['highlighted'] ? 'text-white' : 'text-indigo-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                        </svg>
                                        @if($feature === 'learning_paths')
                                            {{ __('Lernpfade') }}
                                        @elseif($feature === 'assessments')
                                            {{ __('Assessments') }}
                                        @elseif($feature === 'certificates')
                                            {{ __('Zertifikate') }}
                                        @elseif($feature === 'ai_tutor')
                                            {{ __('KI-Tutor') }}
                                        @elseif($feature === 'ai_practice')
                                            {{ __('KI-Übungen') }}
                                        @elseif($feature === 'analytics')
                                            {{ __('Erweiterte Statistiken') }}
                                        @elseif($feature === 'custom_branding')
                                            {{ __('Eigenes Branding') }}
                                        @elseif($feature === 'api_access')
                                            {{ __('API-Zugang') }}
                                        @elseif($feature === 'priority_support')
                                            {{ __('Prioritäts-Support') }}
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
                                {{ __('Sie können LearningPilot 30 Tage lang kostenlos und unverbindlich testen. Keine Kreditkarte erforderlich. Nach Ablauf der Testphase können Sie einen passenden Plan wählen oder Ihr Konto wird automatisch pausiert.') }}
                            </p>
                        </dd>
                    </div>
                    <div class="pt-6" x-data="{ open: false }">
                        <dt>
                            <button type="button" class="flex w-full items-start justify-between text-left text-gray-900" @click="open = !open">
                                <span class="text-base font-semibold">{{ __('Kann ich den Plan später wechseln?') }}</span>
                                <span class="ml-6 flex h-7 items-center">
                                    <svg class="size-6" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </span>
                            </button>
                        </dt>
                        <dd class="mt-2 pr-12" x-show="open" x-cloak>
                            <p class="text-base text-gray-600">
                                {{ __('Ja, Sie können jederzeit zu einem höheren Plan upgraden. Bei einem Downgrade wird die Änderung zum nächsten Abrechnungszeitraum wirksam.') }}
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
