<x-layouts.admin :title="__('Berichte')">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Berichte') }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ __('Plattform-Statistiken und Auswertungen') }}</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('Benutzer') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format(\App\Models\User::count()) }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('Lernpfade') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format(\App\Models\LearningPath::count()) }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('Einschreibungen') }}</p>
            <p class="mt-2 text-3xl font-bold text-teal-600">{{ number_format(\App\Models\Enrollment::count()) }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('Zertifikate') }}</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format(\App\Models\Certificate::count()) }}</p>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('admin.reports.users') }}" class="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:shadow-md hover:ring-gray-900/10 transition-all duration-200">
            <div class="flex items-start gap-4">
                <div class="flex size-12 items-center justify-center rounded-xl bg-teal-50 ring-1 ring-teal-600/10">
                    <svg class="size-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 group-hover:text-teal-600 transition">{{ __('Benutzer-Bericht') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Registrierungen, Aktivität, Rollen-Verteilung') }}</p>
                </div>
                <svg class="size-5 text-gray-400 group-hover:text-teal-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('admin.reports.paths') }}" class="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:shadow-md hover:ring-gray-900/10 transition-all duration-200">
            <div class="flex items-start gap-4">
                <div class="flex size-12 items-center justify-center rounded-xl bg-purple-50 ring-1 ring-purple-600/10">
                    <svg class="size-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 group-hover:text-purple-600 transition">{{ __('Lernpfad-Bericht') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Beliebtheit, Abschlussraten, Kategorien') }}</p>
                </div>
                <svg class="size-5 text-gray-400 group-hover:text-purple-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('admin.reports.enrollments') }}" class="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:shadow-md hover:ring-gray-900/10 transition-all duration-200">
            <div class="flex items-start gap-4">
                <div class="flex size-12 items-center justify-center rounded-xl bg-amber-50 ring-1 ring-amber-600/10">
                    <svg class="size-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 group-hover:text-amber-600 transition">{{ __('Einschreibungs-Bericht') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Fortschritt, Abbruchraten, Trends') }}</p>
                </div>
                <svg class="size-5 text-gray-400 group-hover:text-amber-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('admin.reports.ai-usage') }}" class="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:shadow-md hover:ring-gray-900/10 transition-all duration-200">
            <div class="flex items-start gap-4">
                <div class="flex size-12 items-center justify-center rounded-xl bg-sky-50 ring-1 ring-sky-600/10">
                    <svg class="size-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 group-hover:text-sky-600 transition">{{ __('KI-Nutzung') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Token-Verbrauch, Anfragen, Kosten') }}</p>
                </div>
                <svg class="size-5 text-gray-400 group-hover:text-sky-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
    </div>
</x-layouts.admin>
