<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Notizen') }}</h1>
        <p class="mt-2 text-gray-600">{{ __('Deine persönlichen Lernnotizen') }}</p>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <div class="relative max-w-md">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Notizen durchsuchen...') }}"
                class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-gray-900 placeholder-gray-500 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500"
            >
        </div>
    </div>

    @if($this->notes->count() > 0)
        <!-- Notes List -->
        <div class="space-y-4">
            @foreach($this->notes as $note)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <!-- Header -->
                    <div class="mb-3 flex items-start justify-between">
                        <div>
                            <a
                                href="{{ route('learner.learn.step', ['path' => $note->step->module->learningPath->slug, 'step' => $note->step->position]) }}"
                                wire:navigate
                                class="font-medium text-gray-900 hover:text-teal-600"
                            >
                                {{ $note->step->title }}
                            </a>
                            <p class="text-sm text-gray-500">
                                {{ $note->step->module->learningPath->title }} &bull; {{ $note->step->module->title }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($editingNoteId !== $note->id)
                                <button
                                    wire:click="startEditing('{{ $note->id }}')"
                                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-teal-600"
                                    title="{{ __('Bearbeiten') }}"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button
                                    wire:click="deleteNote('{{ $note->id }}')"
                                    wire:confirm="{{ __('Notiz wirklich löschen?') }}"
                                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-red-500"
                                    title="{{ __('Löschen') }}"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    @if($editingNoteId === $note->id)
                        <div class="space-y-3">
                            <textarea
                                wire:model="editingContent"
                                rows="4"
                                class="w-full rounded-lg border border-gray-300 p-3 text-gray-900 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500"
                                placeholder="{{ __('Notiz eingeben...') }}"
                            ></textarea>
                            <div class="flex items-center gap-2">
                                <button
                                    wire:click="saveNote"
                                    class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700"
                                >
                                    {{ __('Speichern') }}
                                </button>
                                <button
                                    wire:click="cancelEditing"
                                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    {{ __('Abbrechen') }}
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! nl2br(e($note->content)) !!}
                        </div>
                    @endif

                    <!-- Footer -->
                    <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 text-xs text-gray-400">
                        <span>{{ __('Erstellt') }} {{ $note->created_at->diffForHumans() }}</span>
                        @if($note->created_at->ne($note->updated_at))
                            <span>{{ __('Bearbeitet') }} {{ $note->updated_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $this->notes->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 py-16 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Notizen vorhanden') }}</h3>
            <p class="mt-2 text-gray-500">{{ __('Erstelle Notizen zu Lektionen, um wichtige Informationen festzuhalten.') }}</p>
            <a
                href="{{ route('learner.catalog') }}"
                wire:navigate
                class="mt-4 inline-flex items-center rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700"
            >
                {{ __('Kurskatalog durchsuchen') }}
            </a>
        </div>
    @endif
</div>
