<x-layouts.admin :title="__('Allgemeine Einstellungen')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Einstellungen'), 'href' => route('admin.settings.index')],
            ['label' => __('Allgemein')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Allgemeine Einstellungen') }}</h2>
        </x-slot:header>

        <x-ui.alert type="info">
            {{ __('Allgemeine Einstellungen können über die Konfigurationsdateien angepasst werden.') }}
        </x-ui.alert>

        <div class="mt-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('App-Name') }}</label>
                <p class="mt-1 text-gray-900">{{ config('app.name') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Umgebung') }}</label>
                <p class="mt-1 text-gray-900">{{ config('app.env') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('URL') }}</label>
                <p class="mt-1 text-gray-900">{{ config('app.url') }}</p>
            </div>
        </div>
    </x-ui.card>
</x-layouts.admin>
