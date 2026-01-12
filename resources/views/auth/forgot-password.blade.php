<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Passwort vergessen?') }}</h1>
            <p class="mt-2 text-sm text-gray-600">{{ __('Kein Problem. Geben Sie Ihre E-Mail-Adresse ein und wir senden Ihnen einen Link zum Zurücksetzen.') }}</p>
        </div>

        @session('status')
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('E-Mail-Adresse') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="mt-1 block w-full rounded-xl border-gray-200 px-4 py-3 text-gray-900 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" />
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-teal-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all">
                {{ __('Link senden') }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            <a href="{{ route('login') }}" class="font-medium text-teal-600 hover:text-teal-500">
                {{ __('Zurück zur Anmeldung') }}
            </a>
        </p>
    </x-authentication-card>
</x-guest-layout>
