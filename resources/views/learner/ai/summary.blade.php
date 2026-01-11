<x-layouts.learner :title="__('Zusammenfassung')">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Modul-Zusammenfassung') }}</h1>
            <p class="text-gray-500">{{ __('KI-generierte Zusammenfassung des Moduls') }}</p>
        </div>

        <x-ui.card>
            <x-ui.empty-state
                :title="__('Zusammenfassungen kommen bald')"
                :description="__('KI-generierte Zusammenfassungen werden in einer zukünftigen Version verfügbar sein.')"
            >
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>

                <a href="{{ url()->previous() }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    {{ __('Zurück') }}
                </a>
            </x-ui.empty-state>
        </x-ui.card>
    </div>
</x-layouts.learner>
