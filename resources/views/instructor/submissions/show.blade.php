<x-layouts.instructor :title="__('Abgabe')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Abgaben'), 'href' => route('instructor.submissions.index')],
            ['label' => __('Details')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Abgabedetails') }}</h2>
        </x-slot:header>

        <x-ui.empty-state
            :title="__('Abgabe wird geladen')"
            :description="__('Die Detailansicht wird in Kürze verfügbar sein.')"
        >
            <a href="{{ route('instructor.submissions.index') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                {{ __('Zurück zur Liste') }}
            </a>
        </x-ui.empty-state>
    </x-ui.card>
</x-layouts.instructor>
