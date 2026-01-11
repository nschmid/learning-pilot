<x-layouts.instructor :title="__('Vorschau')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Lernpfade'), 'href' => route('instructor.paths.index')],
            ['label' => __('Vorschau')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Lernpfad-Vorschau') }}</h2>
        </x-slot:header>

        <x-ui.empty-state
            :title="__('Vorschau wird geladen')"
            :description="__('Die Vorschau-Funktion wird in Kürze verfügbar sein.')"
        >
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ __('Zurück') }}
            </a>
        </x-ui.empty-state>
    </x-ui.card>
</x-layouts.instructor>
