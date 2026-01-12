<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('E-Mail bestätigen') }}</h1>
            <p class="mt-2 text-sm text-gray-600">{{ __('Vielen Dank für Ihre Registrierung! Bitte bestätigen Sie Ihre E-Mail-Adresse, indem Sie auf den Link klicken, den wir Ihnen gesendet haben.') }}</p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">
                {{ __('Ein neuer Bestätigungslink wurde an Ihre E-Mail-Adresse gesendet.') }}
            </div>
        @endif

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="w-full rounded-xl bg-teal-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all">
                    {{ __('Bestätigungslink erneut senden') }}
                </button>
            </form>

            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('profile.show') }}" class="text-sm font-medium text-teal-600 hover:text-teal-500">
                    {{ __('Profil bearbeiten') }}
                </a>
                <span class="text-gray-300">|</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-teal-600 hover:text-teal-500">
                        {{ __('Abmelden') }}
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
