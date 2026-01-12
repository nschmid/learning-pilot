<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Plan wählen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Currency Toggle -->
            <div class="mb-10 flex justify-center">
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

            <!-- Plans Grid -->
            <div class="grid gap-8 lg:grid-cols-3">
                @foreach($plans as $plan)
                    <div class="relative rounded-2xl {{ $plan['highlighted'] ?? false ? 'bg-gray-900 ring-2 ring-teal-500' : 'bg-white ring-1 ring-gray-200' }} p-8 shadow-sm">
                        @if($plan['highlighted'] ?? false)
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                                <span class="inline-flex items-center rounded-full bg-teal-500 px-4 py-1 text-xs font-semibold text-white">
                                    {{ __('Beliebt') }}
                                </span>
                            </div>
                        @endif

                        @if($currentPlanId === $plan['id'])
                            <div class="absolute top-4 right-4">
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    {{ __('Aktuell') }}
                                </span>
                            </div>
                        @endif

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold {{ $plan['highlighted'] ?? false ? 'text-white' : 'text-gray-900' }}">
                                {{ $plan['name'] }}
                            </h3>
                            <p class="mt-2 text-sm {{ $plan['highlighted'] ?? false ? 'text-gray-300' : 'text-gray-500' }}">
                                {{ $plan['description'] ?? '' }}
                            </p>
                        </div>

                        <div class="mb-6">
                            <span class="text-4xl font-bold {{ $plan['highlighted'] ?? false ? 'text-white' : 'text-gray-900' }}">
                                {{ $plan['formatted_price'] }}
                            </span>
                            <span class="text-sm {{ $plan['highlighted'] ?? false ? 'text-gray-300' : 'text-gray-500' }}">
                                {{ $plan['interval'] }}
                            </span>
                        </div>

                        @if($currentPlanId === $plan['id'])
                            <button
                                disabled
                                class="w-full rounded-lg bg-gray-300 px-4 py-3 text-sm font-semibold text-gray-500 cursor-not-allowed"
                            >
                                {{ __('Aktueller Plan') }}
                            </button>
                        @else
                            <button
                                wire:click="checkout('{{ $plan['id'] }}')"
                                class="w-full rounded-lg {{ $plan['highlighted'] ?? false ? 'bg-white text-gray-900 hover:bg-gray-100' : 'bg-teal-600 text-white hover:bg-teal-500' }} px-4 py-3 text-sm font-semibold transition"
                            >
                                {{ $currentPlanId ? __('Wechseln') : __('Auswählen') }}
                            </button>
                        @endif

                        <ul class="mt-8 space-y-3">
                            @if(isset($plan['limits']['students']))
                                <li class="flex items-center gap-x-3">
                                    <svg class="size-5 flex-none {{ $plan['highlighted'] ?? false ? 'text-white' : 'text-teal-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm {{ $plan['highlighted'] ?? false ? 'text-gray-300' : 'text-gray-600' }}">
                                        {{ __('Bis zu :count Lernende', ['count' => $plan['limits']['students']]) }}
                                    </span>
                                </li>
                            @endif

                            @if(isset($plan['limits']['instructors']))
                                <li class="flex items-center gap-x-3">
                                    <svg class="size-5 flex-none {{ $plan['highlighted'] ?? false ? 'text-white' : 'text-teal-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm {{ $plan['highlighted'] ?? false ? 'text-gray-300' : 'text-gray-600' }}">
                                        {{ __('Bis zu :count Dozenten', ['count' => $plan['limits']['instructors']]) }}
                                    </span>
                                </li>
                            @endif

                            @if(isset($plan['limits']['storage_gb']))
                                <li class="flex items-center gap-x-3">
                                    <svg class="size-5 flex-none {{ $plan['highlighted'] ?? false ? 'text-white' : 'text-teal-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm {{ $plan['highlighted'] ?? false ? 'text-gray-300' : 'text-gray-600' }}">
                                        {{ __(':count GB Speicher', ['count' => $plan['limits']['storage_gb']]) }}
                                    </span>
                                </li>
                            @endif

                            @foreach($plan['features'] ?? [] as $feature => $enabled)
                                @if($enabled)
                                    <li class="flex items-center gap-x-3">
                                        <svg class="size-5 flex-none {{ $plan['highlighted'] ?? false ? 'text-white' : 'text-teal-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm {{ $plan['highlighted'] ?? false ? 'text-gray-300' : 'text-gray-600' }}">
                                            @switch($feature)
                                                @case('learning_paths') {{ __('Lernpfade') }} @break
                                                @case('assessments') {{ __('Assessments') }} @break
                                                @case('certificates') {{ __('Zertifikate') }} @break
                                                @case('ai_tutor') {{ __('KI-Tutor') }} @break
                                                @case('ai_practice') {{ __('KI-Übungen') }} @break
                                                @case('analytics') {{ __('Erweiterte Statistiken') }} @break
                                                @case('custom_branding') {{ __('Eigenes Branding') }} @break
                                                @case('api_access') {{ __('API-Zugang') }} @break
                                                @case('priority_support') {{ __('Prioritäts-Support') }} @break
                                                @default {{ ucfirst(str_replace('_', ' ', $feature)) }}
                                            @endswitch
                                        </span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            <!-- FAQ -->
            <div class="mt-16">
                <h2 class="text-center text-2xl font-bold text-gray-900">{{ __('Häufige Fragen') }}</h2>
                <div class="mx-auto mt-8 max-w-3xl divide-y divide-gray-200">
                    <div class="py-6" x-data="{ open: false }">
                        <button @click="open = !open" class="flex w-full items-center justify-between text-left">
                            <span class="text-base font-medium text-gray-900">{{ __('Kann ich jederzeit wechseln?') }}</span>
                            <svg class="size-5 text-gray-500" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <p x-show="open" x-cloak class="mt-4 text-sm text-gray-600">
                            {{ __('Ja, Sie können jederzeit zu einem höheren Plan upgraden. Die Differenz wird anteilig berechnet. Bei einem Downgrade wird die Änderung zum nächsten Abrechnungszeitraum wirksam.') }}
                        </p>
                    </div>
                    <div class="py-6" x-data="{ open: false }">
                        <button @click="open = !open" class="flex w-full items-center justify-between text-left">
                            <span class="text-base font-medium text-gray-900">{{ __('Was passiert nach der Testphase?') }}</span>
                            <svg class="size-5 text-gray-500" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <p x-show="open" x-cloak class="mt-4 text-sm text-gray-600">
                            {{ __('Nach Ablauf der 30-tägigen Testphase wird Ihr Konto pausiert. Ihre Daten bleiben erhalten, aber Sie können keine neuen Inhalte erstellen. Wählen Sie einfach einen Plan, um fortzufahren.') }}
                        </p>
                    </div>
                    <div class="py-6" x-data="{ open: false }">
                        <button @click="open = !open" class="flex w-full items-center justify-between text-left">
                            <span class="text-base font-medium text-gray-900">{{ __('Gibt es Rabatte für Schulen?') }}</span>
                            <svg class="size-5 text-gray-500" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <p x-show="open" x-cloak class="mt-4 text-sm text-gray-600">
                            {{ __('Ja, für öffentliche Bildungseinrichtungen bieten wir Sonderkonditionen an.') }}
                            <a href="{{ route('contact') }}" class="text-teal-600 hover:text-teal-500">{{ __('Kontaktieren Sie uns') }}</a>
                            {{ __('für ein individuelles Angebot.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
