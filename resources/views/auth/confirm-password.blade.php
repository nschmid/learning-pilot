<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Passwort bestätigen') }}</h1>
            <p class="mt-2 text-sm text-gray-600">{{ __('Dies ist ein geschützter Bereich. Bitte bestätigen Sie Ihr Passwort, bevor Sie fortfahren.') }}</p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Passwort') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" autofocus
                       class="mt-1 block w-full rounded-xl border-gray-200 px-4 py-3 text-gray-900 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" />
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-teal-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all">
                {{ __('Bestätigen') }}
            </button>
        </form>
    </x-authentication-card>
</x-guest-layout>
