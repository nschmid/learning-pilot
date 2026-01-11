<x-layouts.learner :title="__('KI-Tutor')">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('KI-Tutor') }}</h1>
            <p class="text-gray-500">{{ __('Stelle Fragen und erhalte personalisierte Unterstützung') }}</p>
        </div>

        <x-ui.card>
            <x-ui.empty-state
                :title="__('KI-Tutor kommt bald')"
                :description="__('Der KI-Tutor wird in einer zukünftigen Version verfügbar sein.')"
            >
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>

                <a href="{{ route('learner.dashboard') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    {{ __('Zurück zum Dashboard') }}
                </a>
            </x-ui.empty-state>
        </x-ui.card>
    </div>
</x-layouts.learner>
