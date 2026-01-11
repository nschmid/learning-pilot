<x-layouts.admin :title="__('KI-Nutzung')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Berichte'), 'href' => route('admin.reports.index')],
            ['label' => __('KI-Nutzung')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('KI-Nutzungsstatistiken') }}</h2>
        </x-slot:header>

        <x-ui.empty-state
            :title="__('Keine KI-Nutzung')"
            :description="__('KI-Funktionen wurden noch nicht genutzt oder sind nicht aktiviert.')"
        >
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </x-ui.empty-state>
    </x-ui.card>
</x-layouts.admin>
