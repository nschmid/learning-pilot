<x-layouts.admin :title="__('Berichte')">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Berichte') }}</h1>
        <p class="text-gray-500">{{ __('Plattform-Statistiken und Auswertungen') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.stat-card :label="__('Benutzer')" value="{{ \App\Models\User::count() }}" />
        <x-ui.stat-card :label="__('Lernpfade')" value="{{ \App\Models\LearningPath::count() }}" />
        <x-ui.stat-card :label="__('Einschreibungen')" value="{{ \App\Models\Enrollment::count() }}" />
        <x-ui.stat-card :label="__('Zertifikate')" value="{{ \App\Models\Certificate::count() }}" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-ui.card :hover="true">
            <a href="{{ route('admin.reports.users') }}" class="block">
                <h3 class="font-semibold text-gray-900">{{ __('Benutzer-Bericht') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Registrierungen, Aktivität, Rollen') }}</p>
            </a>
        </x-ui.card>

        <x-ui.card :hover="true">
            <a href="{{ route('admin.reports.paths') }}" class="block">
                <h3 class="font-semibold text-gray-900">{{ __('Lernpfad-Bericht') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Beliebtheit, Abschlussraten') }}</p>
            </a>
        </x-ui.card>

        <x-ui.card :hover="true">
            <a href="{{ route('admin.reports.enrollments') }}" class="block">
                <h3 class="font-semibold text-gray-900">{{ __('Einschreibungs-Bericht') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Fortschritt, Abbruchraten') }}</p>
            </a>
        </x-ui.card>

        <x-ui.card :hover="true">
            <a href="{{ route('admin.reports.ai-usage') }}" class="block">
                <h3 class="font-semibold text-gray-900">{{ __('KI-Nutzung') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Token-Verbrauch, Anfragen') }}</p>
            </a>
        </x-ui.card>
    </div>
</x-layouts.admin>
