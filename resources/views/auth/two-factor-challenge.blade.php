<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div x-data="{ recovery: false }">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Zwei-Faktor-Authentifizierung') }}</h1>
                <p class="mt-2 text-sm text-gray-600" x-show="! recovery">
                    {{ __('Bitte bestätigen Sie den Zugriff mit dem Code aus Ihrer Authenticator-App.') }}
                </p>
                <p class="mt-2 text-sm text-gray-600" x-cloak x-show="recovery">
                    {{ __('Bitte bestätigen Sie den Zugriff mit einem Ihrer Wiederherstellungscodes.') }}
                </p>
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
                @csrf

                <div x-show="! recovery">
                    <label for="code" class="block text-sm font-medium text-gray-700">{{ __('Code') }}</label>
                    <input id="code" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code"
                           class="mt-1 block w-full rounded-xl border-gray-200 px-4 py-3 text-gray-900 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" />
                </div>

                <div x-cloak x-show="recovery">
                    <label for="recovery_code" class="block text-sm font-medium text-gray-700">{{ __('Wiederherstellungscode') }}</label>
                    <input id="recovery_code" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code"
                           class="mt-1 block w-full rounded-xl border-gray-200 px-4 py-3 text-gray-900 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" />
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" class="text-sm font-medium text-teal-600 hover:text-teal-500"
                            x-show="! recovery"
                            x-on:click="
                                recovery = true;
                                $nextTick(() => { $refs.recovery_code.focus() })
                            ">
                        {{ __('Wiederherstellungscode verwenden') }}
                    </button>

                    <button type="button" class="text-sm font-medium text-teal-600 hover:text-teal-500"
                            x-cloak
                            x-show="recovery"
                            x-on:click="
                                recovery = false;
                                $nextTick(() => { $refs.code.focus() })
                            ">
                        {{ __('Authentifizierungscode verwenden') }}
                    </button>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-teal-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all">
                    {{ __('Anmelden') }}
                </button>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
