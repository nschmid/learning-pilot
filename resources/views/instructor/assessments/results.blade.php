<x-layouts.instructor :title="__('Prüfungsergebnisse')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Prüfungen')],
            ['label' => __('Ergebnisse')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Prüfungsergebnisse') }}</h2>
        </x-slot:header>

        <x-ui.empty-state
            :title="__('Keine Ergebnisse')"
            :description="__('Es wurden noch keine Prüfungsversuche abgeschlossen.')"
        >
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ __('Zurück') }}
            </a>
        </x-ui.empty-state>
    </x-ui.card>
</x-layouts.instructor>
