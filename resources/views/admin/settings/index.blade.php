<x-layouts.admin :title="__('Einstellungen')">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Einstellungen') }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ __('Plattform-Konfiguration und Systemeinstellungen') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('admin.settings.general') }}" class="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:shadow-md hover:ring-gray-900/10 transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="flex size-12 items-center justify-center rounded-xl bg-gray-50 ring-1 ring-gray-900/5 group-hover:bg-teal-50 group-hover:ring-teal-600/10 transition">
                    <svg class="size-6 text-gray-600 group-hover:text-teal-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-teal-600 transition">{{ __('Allgemein') }}</h3>
                    <p class="mt-0.5 text-sm text-gray-500">{{ __('Name, Logo, Sprache') }}</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.settings.billing') }}" class="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:shadow-md hover:ring-gray-900/10 transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="flex size-12 items-center justify-center rounded-xl bg-gray-50 ring-1 ring-gray-900/5 group-hover:bg-green-50 group-hover:ring-green-600/10 transition">
                    <svg class="size-6 text-gray-600 group-hover:text-green-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-green-600 transition">{{ __('Abrechnung') }}</h3>
                    <p class="mt-0.5 text-sm text-gray-500">{{ __('Pläne, Zahlungen, Stripe') }}</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.settings.ai') }}" class="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:shadow-md hover:ring-gray-900/10 transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="flex size-12 items-center justify-center rounded-xl bg-gray-50 ring-1 ring-gray-900/5 group-hover:bg-purple-50 group-hover:ring-purple-600/10 transition">
                    <svg class="size-6 text-gray-600 group-hover:text-purple-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-purple-600 transition">{{ __('KI-Einstellungen') }}</h3>
                    <p class="mt-0.5 text-sm text-gray-500">{{ __('Provider, Modelle, Limits') }}</p>
                </div>
            </div>
        </a>
    </div>
</x-layouts.admin>
