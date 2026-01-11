<x-layouts.instructor :title="__('Lernpfad-Analytics')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Analytics'), 'href' => route('instructor.analytics.index')],
            ['label' => __('Lernpfade')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Lernpfad-Statistiken') }}</h2>
        </x-slot:header>

        <x-ui.empty-state
            :title="__('Keine Daten')"
            :description="__('Detaillierte Lernpfad-Statistiken werden hier angezeigt.')"
        />
    </x-ui.card>
</x-layouts.instructor>
