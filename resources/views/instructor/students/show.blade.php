<x-layouts.instructor :title="__('Teilnehmer')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Teilnehmer'), 'href' => route('instructor.students.index')],
            ['label' => __('Details')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Teilnehmerdetails') }}</h2>
        </x-slot:header>

        <x-ui.empty-state
            :title="__('Profil wird geladen')"
            :description="__('Die Detailansicht wird in Kürze verfügbar sein.')"
        >
            <a href="{{ route('instructor.students.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ __('Zurück zur Liste') }}
            </a>
        </x-ui.empty-state>
    </x-ui.card>
</x-layouts.instructor>
