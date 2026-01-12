<x-layouts.instructor :title="__('Aufgabe bearbeiten')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Aufgaben')],
            ['label' => __('Bearbeiten')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Aufgabe bearbeiten') }}</h2>
        </x-slot:header>

        <x-ui.empty-state
            :title="__('Editor wird geladen')"
            :description="__('Der Aufgabeneditor wird in Kürze verfügbar sein.')"
        >
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                {{ __('Zurück') }}
            </a>
        </x-ui.empty-state>
    </x-ui.card>
</x-layouts.instructor>
