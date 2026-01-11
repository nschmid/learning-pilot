<x-layouts.instructor :title="__('Analytics')">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Analytics') }}</h1>
        <p class="text-gray-500">{{ __('Übersicht deiner Lernpfad-Statistiken') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.stat-card :label="__('Lernpfade')" value="0" />
        <x-ui.stat-card :label="__('Einschreibungen')" value="0" />
        <x-ui.stat-card :label="__('Abschlüsse')" value="0" />
        <x-ui.stat-card :label="__('Durchschnittl. Bewertung')" value="—" />
    </div>

    <x-ui.card>
        <x-ui.empty-state
            :title="__('Keine Daten vorhanden')"
            :description="__('Sobald Lernende deine Kurse besuchen, werden hier Statistiken angezeigt.')"
        />
    </x-ui.card>
</x-layouts.instructor>
