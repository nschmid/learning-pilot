<div class="mx-auto max-w-4xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('instructor.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.paths.show', $task->step->module->learningPath->slug) }}" wire:navigate class="hover:text-gray-700">
            {{ $task->step->module->learningPath->title }}
        </a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.tasks.show', $task->id) }}" wire:navigate class="hover:text-gray-700">{{ $task->title }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ __('Bearbeiten') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Aufgabe bearbeiten') }}</h1>
        <p class="mt-1 text-gray-500">{{ $task->step->module->title }} &bull; {{ $task->step->title }}</p>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- Basic Info -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Grundinformationen') }}</h2>

            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">{{ __('Titel') }}</label>
                    <input
                        type="text"
                        id="title"
                        wire:model="title"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                    >
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="task_type" class="block text-sm font-medium text-gray-700">{{ __('Aufgabentyp') }}</label>
                    <select
                        id="task_type"
                        wire:model="task_type"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                    >
                        @foreach($taskTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    @error('task_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700">{{ __('Aufgabenstellung') }}</label>
                    <textarea
                        id="instructions"
                        wire:model="instructions"
                        rows="6"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        placeholder="{{ __('Beschreibe die Aufgabe im Detail...') }}"
                    ></textarea>
                    @error('instructions') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Scoring -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Bewertung') }}</h2>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="max_points" class="block text-sm font-medium text-gray-700">{{ __('Maximale Punkte') }}</label>
                    <input
                        type="number"
                        id="max_points"
                        wire:model="max_points"
                        min="1"
                        max="1000"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                    >
                    @error('max_points') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="due_days" class="block text-sm font-medium text-gray-700">{{ __('Fällig in (Tage)') }}</label>
                    <input
                        type="number"
                        id="due_days"
                        wire:model="due_days"
                        min="1"
                        placeholder="{{ __('Optional') }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                    >
                    @error('due_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Rubric -->
            <div class="mt-6">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Bewertungskriterien') }}</label>
                    <button type="button" wire:click="addRubricItem" class="text-sm text-teal-600 hover:text-teal-800">
                        + {{ __('Kriterium hinzufügen') }}
                    </button>
                </div>
                @if(count($rubric) > 0)
                    <div class="mt-3 space-y-3">
                        @foreach($rubric as $index => $item)
                            <div class="flex items-center gap-3">
                                <input
                                    type="text"
                                    wire:model="rubric.{{ $index }}.name"
                                    placeholder="{{ __('Kriteriumsname') }}"
                                    class="flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                >
                                <input
                                    type="number"
                                    wire:model="rubric.{{ $index }}.points"
                                    min="0"
                                    placeholder="{{ __('Punkte') }}"
                                    class="w-24 rounded-lg border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                >
                                <button
                                    type="button"
                                    wire:click="removeRubricItem({{ $index }})"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-2 text-sm text-gray-500">{{ __('Noch keine Bewertungskriterien definiert.') }}</p>
                @endif
            </div>
        </div>

        <!-- Submission Options -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Abgabeoptionen') }}</h2>

            <div class="space-y-4">
                <label class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        wire:model="allow_late"
                        class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                    >
                    <span class="text-sm text-gray-700">{{ __('Verspätete Abgaben erlauben') }}</span>
                </label>

                <label class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        wire:model="allow_resubmit"
                        class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                    >
                    <span class="text-sm text-gray-700">{{ __('Erneute Einreichung erlauben') }}</span>
                </label>
            </div>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Erlaubte Dateitypen') }}</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($fileTypes as $type)
                            <label class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-sm {{ in_array($type, $allowed_file_types) ? 'border-teal-300 bg-teal-50 text-teal-700' : 'border-gray-300 bg-white text-gray-700' }}">
                                <input
                                    type="checkbox"
                                    value="{{ $type }}"
                                    wire:model="allowed_file_types"
                                    class="hidden"
                                >
                                .{{ $type }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="max_file_size_mb" class="block text-sm font-medium text-gray-700">{{ __('Max. Dateigröße (MB)') }}</label>
                    <input
                        type="number"
                        id="max_file_size_mb"
                        wire:model="max_file_size_mb"
                        min="1"
                        max="100"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                    >
                    @error('max_file_size_mb') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('instructor.tasks.show', $task->id) }}" wire:navigate class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                {{ __('Abbrechen') }}
            </a>
            <button
                type="submit"
                class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700"
            >
                {{ __('Speichern') }}
            </button>
        </div>
    </form>
</div>
