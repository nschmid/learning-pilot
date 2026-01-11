<div class="mx-auto max-w-4xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('instructor.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.paths.index') }}" wire:navigate class="hover:text-gray-700">{{ __('Lernpfade') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.paths.show', $path) }}" wire:navigate class="hover:text-gray-700">{{ $path->title }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ __('Einstellungen') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Lernpfad-Einstellungen') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Verwalte Sichtbarkeit, Kategorisierung und erweiterte Optionen.') }}</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4">
            <div class="flex items-center gap-2 text-green-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <form wire:submit="save" class="space-y-8">
        <!-- Publishing Settings -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Veröffentlichung') }}</h2>
            </div>
            <div class="p-6 space-y-6">
                <!-- Published Toggle -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900">{{ __('Veröffentlicht') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Wenn aktiviert, ist der Lernpfad für Lernende sichtbar.') }}</p>
                    </div>
                    <button
                        type="button"
                        wire:click="$toggle('isPublished')"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $isPublished ? 'bg-indigo-600' : 'bg-gray-200' }}"
                        role="switch"
                        aria-checked="{{ $isPublished ? 'true' : 'false' }}"
                    >
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $isPublished ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                <!-- Featured Toggle -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900">{{ __('Empfohlen') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Empfohlene Lernpfade werden prominent im Katalog angezeigt.') }}</p>
                    </div>
                    <button
                        type="button"
                        wire:click="$toggle('isFeatured')"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $isFeatured ? 'bg-indigo-600' : 'bg-gray-200' }}"
                        role="switch"
                        aria-checked="{{ $isFeatured ? 'true' : 'false' }}"
                    >
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $isFeatured ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                @if($path->published_at)
                    <p class="text-sm text-gray-500">
                        {{ __('Erstmals veröffentlicht am') }}: {{ $path->published_at->format('d.m.Y H:i') }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Categorization -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Kategorisierung') }}</h2>
            </div>
            <div class="p-6 space-y-6">
                <!-- Difficulty -->
                <div>
                    <label for="difficulty" class="block text-sm font-medium text-gray-700">{{ __('Schwierigkeitsgrad') }}</label>
                    <select
                        wire:model="difficulty"
                        id="difficulty"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        @foreach($this->difficulties as $diff)
                            <option value="{{ $diff['value'] }}">{{ $diff['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label for="categoryId" class="block text-sm font-medium text-gray-700">{{ __('Kategorie') }}</label>
                    <select
                        wire:model="categoryId"
                        id="categoryId"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">{{ __('Keine Kategorie') }}</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Tags') }}</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($this->tags as $tag)
                            <label class="inline-flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="selectedTags"
                                    value="{{ $tag->id }}"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @if($this->tags->isEmpty())
                        <p class="mt-2 text-sm text-gray-500">{{ __('Keine Tags verfügbar.') }}</p>
                    @endif
                </div>

                <!-- Estimated Hours -->
                <div>
                    <label for="estimatedHours" class="block text-sm font-medium text-gray-700">{{ __('Geschätzte Dauer (Stunden)') }}</label>
                    <input
                        type="number"
                        wire:model="estimatedHours"
                        id="estimatedHours"
                        min="1"
                        class="mt-1 block w-32 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="z.B. 10"
                    >
                    <p class="mt-1 text-sm text-gray-500">{{ __('Wird automatisch berechnet, wenn leer.') }}</p>
                </div>
            </div>
        </div>

        <!-- Advanced Settings -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Erweiterte Einstellungen') }}</h2>
            </div>
            <div class="p-6 space-y-6">
                <!-- Requires Approval -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900">{{ __('Genehmigung erforderlich') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Einschreibungen müssen manuell genehmigt werden.') }}</p>
                    </div>
                    <button
                        type="button"
                        wire:click="$toggle('requiresApproval')"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $requiresApproval ? 'bg-indigo-600' : 'bg-gray-200' }}"
                        role="switch"
                    >
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $requiresApproval ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                <!-- Max Enrollments -->
                <div>
                    <label for="maxEnrollments" class="block text-sm font-medium text-gray-700">{{ __('Maximale Teilnehmer') }}</label>
                    <input
                        type="number"
                        wire:model="maxEnrollments"
                        id="maxEnrollments"
                        min="1"
                        class="mt-1 block w-32 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="{{ __('Unbegrenzt') }}"
                    >
                    <p class="mt-1 text-sm text-gray-500">{{ __('Leer lassen für unbegrenzte Teilnahme.') }}</p>
                </div>

                <!-- Certificate Settings -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900">{{ __('Zertifikat aktiviert') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Teilnehmer erhalten ein Zertifikat bei Abschluss.') }}</p>
                    </div>
                    <button
                        type="button"
                        wire:click="$toggle('certificateEnabled')"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $certificateEnabled ? 'bg-indigo-600' : 'bg-gray-200' }}"
                        role="switch"
                    >
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $certificateEnabled ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                @if($certificateEnabled)
                    <div>
                        <label for="certificateValidityDays" class="block text-sm font-medium text-gray-700">{{ __('Zertifikat-Gültigkeit (Tage)') }}</label>
                        <input
                            type="number"
                            wire:model="certificateValidityDays"
                            id="certificateValidityDays"
                            min="1"
                            class="mt-1 block w-32 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="{{ __('Unbegrenzt') }}"
                        >
                        <p class="mt-1 text-sm text-gray-500">{{ __('Leer lassen für unbegrenzte Gültigkeit.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics (Read-only) -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Statistiken') }}</h2>
            </div>
            <div class="p-6">
                <div class="grid gap-4 sm:grid-cols-4">
                    <div class="rounded-lg bg-gray-50 p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $path->enrollmentCount() }}</div>
                        <div class="text-sm text-gray-500">{{ __('Einschreibungen') }}</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $path->completionCount() }}</div>
                        <div class="text-sm text-gray-500">{{ __('Abschlüsse') }}</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $path->totalSteps() }}</div>
                        <div class="text-sm text-gray-500">{{ __('Lektionen') }}</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($path->averageRating(), 1) }}</div>
                        <div class="text-sm text-gray-500">{{ __('Ø Bewertung') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a
                href="{{ route('instructor.paths.show', $path) }}"
                wire:navigate
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Zurück zur Übersicht') }}
            </a>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2 font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-wait disabled:opacity-75"
            >
                <span wire:loading.remove wire:target="save">{{ __('Speichern') }}</span>
                <span wire:loading wire:target="save">{{ __('Wird gespeichert...') }}</span>
            </button>
        </div>
    </form>
</div>
