<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Zahlungsmethoden') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Ihre Zahlungsmethoden') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Verwalten Sie Ihre Kreditkarten und Zahlungsmethoden') }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('billing.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                            {{ __('Zurück') }}
                        </a>
                        <button
                            wire:click="redirectToPortal"
                            class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-500"
                        >
                            {{ __('Verwalten') }}
                        </button>
                    </div>
                </div>

                <div class="border-t border-gray-200">
                    @if(count($paymentMethods) > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($paymentMethods as $method)
                                <li class="flex items-center justify-between px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <!-- Card Brand Icon -->
                                        <div class="flex size-10 items-center justify-center rounded-lg bg-gray-100">
                                            @if($method['brand'] === 'visa')
                                                <svg class="h-6" viewBox="0 0 24 24" fill="none">
                                                    <path d="M9.5 15.5L10.5 8.5H12.5L11.5 15.5H9.5Z" fill="#1434CB"/>
                                                    <path d="M16 8.5C15.5 8.3 14.8 8 14 8C12 8 10.5 9.2 10.5 10.8C10.5 12 11.5 12.6 12.3 13C13 13.4 13.3 13.6 13.3 14C13.3 14.5 12.7 14.8 12.1 14.8C11.3 14.8 10.8 14.6 10.2 14.4L10 14.3L9.7 16.1C10.3 16.4 11.2 16.6 12.2 16.6C14.3 16.6 15.8 15.4 15.8 13.7C15.8 12.7 15.2 12 14 11.4C13.3 11 12.9 10.8 12.9 10.4C12.9 10 13.3 9.7 14 9.7C14.6 9.7 15.1 9.8 15.5 10L15.7 10.1L16 8.5Z" fill="#1434CB"/>
                                                </svg>
                                            @elseif($method['brand'] === 'mastercard')
                                                <svg class="h-6" viewBox="0 0 24 24" fill="none">
                                                    <circle cx="9" cy="12" r="5" fill="#EB001B"/>
                                                    <circle cx="15" cy="12" r="5" fill="#F79E1B"/>
                                                    <path d="M12 8.5C13.1 9.4 13.8 10.6 13.8 12C13.8 13.4 13.1 14.6 12 15.5C10.9 14.6 10.2 13.4 10.2 12C10.2 10.6 10.9 9.4 12 8.5Z" fill="#FF5F00"/>
                                                </svg>
                                            @else
                                                <svg class="size-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                                </svg>
                                            @endif
                                        </div>

                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ ucfirst($method['brand']) }} •••• {{ $method['last4'] }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ __('Läuft ab') }} {{ $method['exp_month'] }}/{{ $method['exp_year'] }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        @if($method['is_default'])
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                {{ __('Standard') }}
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="px-4 py-12 text-center">
                            <svg class="mx-auto size-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Keine Zahlungsmethoden') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Sie haben noch keine Zahlungsmethode hinzugefügt.') }}</p>
                            <button
                                wire:click="redirectToPortal"
                                class="mt-4 inline-flex items-center rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-500"
                            >
                                {{ __('Zahlungsmethode hinzufügen') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-6 rounded-lg bg-gray-50 p-6">
                <h4 class="text-sm font-medium text-gray-900">{{ __('Hilfe') }}</h4>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('Zahlungsmethoden werden sicher über Stripe verwaltet. Klicken Sie auf "Verwalten" um Karten hinzuzufügen, zu ändern oder zu entfernen.') }}
                </p>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('Wir akzeptieren Visa, Mastercard, American Express und SEPA-Lastschrift.') }}
                </p>
            </div>
        </div>
    </div>
</div>
