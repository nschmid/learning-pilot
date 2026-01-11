<x-layouts.instructor :title="__('Teilnehmer')">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Teilnehmer') }}</h1>
            <p class="text-gray-500">{{ __('Übersicht aller eingeschriebenen Lernenden') }}</p>
        </div>
    </div>

    <x-ui.card>
        <x-ui.empty-state
            :title="__('Keine Teilnehmer')"
            :description="__('Es sind noch keine Teilnehmer in deinen Lernpfaden eingeschrieben.')"
        >
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </x-ui.empty-state>
    </x-ui.card>
</x-layouts.instructor>
