<x-layouts.learner :title="__('Lernkarten')">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Lernkarten') }}</h1>
            <p class="text-gray-500">{{ __('KI-generierte Lernkarten zum Wiederholen') }}</p>
        </div>

        <x-ui.card>
            <x-ui.empty-state
                :title="__('Lernkarten kommen bald')"
                :description="__('KI-generierte Lernkarten werden in einer zukünftigen Version verfügbar sein.')"
            >
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>

                <a href="{{ url()->previous() }}" class="mt-4 inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                    {{ __('Zurück') }}
                </a>
            </x-ui.empty-state>
        </x-ui.card>
    </div>
</x-layouts.learner>
