<div class="mx-auto max-w-3xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('instructor.dashboard') }}" class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.paths.index') }}" class="hover:text-gray-700">{{ __('Lernpfade') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ __('Neu') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Neuen Lernpfad erstellen') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Gib die grundlegenden Informationen für deinen Kurs ein.') }}</p>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- Basic Info Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Grundinformationen') }}</h2>

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700">
                    {{ __('Titel') }} <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="title"
                    type="text"
                    id="title"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                    placeholder="{{ __('z.B. Einführung in Python') }}"
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700">
                    {{ __('Beschreibung') }} <span class="text-red-500">*</span>
                </label>
                <textarea
                    wire:model="description"
                    id="description"
                    rows="4"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                    placeholder="{{ __('Beschreibe, worum es in diesem Lernpfad geht...') }}"
                ></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Thumbnail -->
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Vorschaubild') }}</label>
                <div class="mt-1 flex items-center gap-4">
                    @if($thumbnail)
                        <img src="{{ $thumbnail->temporaryUrl() }}" alt="Preview" class="h-24 w-40 rounded-lg object-cover">
                    @else
                        <div class="flex h-24 w-40 items-center justify-center rounded-lg bg-gray-100">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <label class="cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('Bild auswählen') }}
                            <input wire:model="thumbnail" type="file" accept="image/*" class="hidden">
                        </label>
                        <p class="mt-1 text-xs text-gray-500">{{ __('PNG, JPG bis 2MB') }}</p>
                    </div>
                </div>
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Objectives Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Lernziele') }}</h2>
            <p class="mb-4 text-sm text-gray-500">{{ __('Was werden Teilnehmer nach Abschluss dieses Lernpfads können?') }}</p>

            <div class="space-y-3">
                @foreach($objectives as $index => $objective)
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">{{ $index + 1 }}.</span>
                                <input
                                    wire:model="objectives.{{ $index }}"
                                    type="text"
                                    class="block w-full rounded-lg border-gray-300 pl-8 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                    placeholder="{{ __('z.B. Grundlagen der Programmierung verstehen') }}"
                                >
                            </div>
                        </div>
                        @if(count($objectives) > 1)
                            <button
                                wire:click="removeObjective({{ $index }})"
                                type="button"
                                class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-red-500"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            <button
                wire:click="addObjective"
                type="button"
                class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-orange-600 hover:text-orange-800"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('Weiteres Lernziel hinzufügen') }}
            </button>
        </div>

        <!-- Details Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Details') }}</h2>

            <div class="grid gap-6 sm:grid-cols-2">
                <!-- Difficulty -->
                <div>
                    <label for="difficulty" class="block text-sm font-medium text-gray-700">
                        {{ __('Schwierigkeitsgrad') }}
                    </label>
                    <select
                        wire:model="difficulty"
                        id="difficulty"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                    >
                        @foreach($this->difficulties as $diff)
                            <option value="{{ $diff['value'] }}">{{ $diff['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Estimated Hours -->
                <div>
                    <label for="estimatedHours" class="block text-sm font-medium text-gray-700">
                        {{ __('Geschätzte Dauer (Stunden)') }}
                    </label>
                    <input
                        wire:model="estimatedHours"
                        type="number"
                        id="estimatedHours"
                        min="1"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        placeholder="{{ __('z.B. 10') }}"
                    >
                </div>

                <!-- Category -->
                <div>
                    <label for="categoryId" class="block text-sm font-medium text-gray-700">
                        {{ __('Kategorie') }}
                    </label>
                    <select
                        wire:model="categoryId"
                        id="categoryId"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                    >
                        <option value="">{{ __('Keine Kategorie') }}</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @foreach($category->children as $child)
                                <option value="{{ $child->id }}">&nbsp;&nbsp;{{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Tags') }}</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($this->tags as $tag)
                            <label class="inline-flex cursor-pointer items-center">
                                <input
                                    type="checkbox"
                                    wire:model="selectedTags"
                                    value="{{ $tag->id }}"
                                    class="rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a
                href="{{ route('instructor.paths.index') }}"
                class="text-gray-600 hover:text-gray-900"
            >
                {{ __('Abbrechen') }}
            </a>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-6 py-2 font-medium text-white hover:bg-orange-600 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="save">
                    {{ __('Lernpfad erstellen') }}
                </span>
                <span wire:loading wire:target="save">
                    {{ __('Wird erstellt...') }}
                </span>
            </button>
        </div>
    </form>
</div>
