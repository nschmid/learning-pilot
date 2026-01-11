<x-layouts.learner :title="__('Übungsfragen')">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Übungsfragen') }}</h1>
            <p class="text-gray-500">{{ __('KI-generierte Übungsfragen zum Vertiefen') }}</p>
        </div>

        <x-ui.card>
            <x-ui.empty-state
                :title="__('Übungsfragen kommen bald')"
                :description="__('KI-generierte Übungsfragen werden in einer zukünftigen Version verfügbar sein.')"
            >
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>

                <a href="{{ url()->previous() }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    {{ __('Zurück') }}
                </a>
            </x-ui.empty-state>
        </x-ui.card>
    </div>
</x-layouts.learner>
