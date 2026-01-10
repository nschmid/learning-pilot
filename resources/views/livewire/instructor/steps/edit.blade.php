<div>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('instructor.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('instructor.paths.show', $step->module->learningPath) }}" wire:navigate class="hover:text-gray-700">
            {{ $step->module->learningPath->title }}
        </a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $step->title }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg {{
                $step->step_type->value === 'material' ? 'bg-blue-100 text-blue-600' :
                ($step->step_type->value === 'task' ? 'bg-yellow-100 text-yellow-600' : 'bg-purple-100 text-purple-600')
            }}">
                @if($step->step_type->value === 'material')
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                @elseif($step->step_type->value === 'task')
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                @else
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $step->title }}</h1>
                <p class="text-gray-500">{{ $step->step_type->label() }} &middot; {{ $step->module->title }}</p>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Material Step --}}
    @if($step->step_type->value === 'material')
        <div class="space-y-6">
            <!-- Materials List -->
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h2 class="font-semibold text-gray-900">{{ __('Lernmaterialien') }}</h2>
                    <button
                        wire:click="openMaterialModal()"
                        class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Material hinzufügen') }}
                    </button>
                </div>

                @if($this->materials->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-4 text-gray-500">{{ __('Noch keine Materialien. Füge Text, Videos oder Dateien hinzu.') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($this->materials as $material)
                            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">
                                        @switch($material->material_type->value)
                                            @case('text')
                                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                @break
                                            @case('video')
                                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                @break
                                            @case('link')
                                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                </svg>
                                                @break
                                            @default
                                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                        @endswitch
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $material->title }}</p>
                                        <p class="text-sm text-gray-500">{{ ucfirst($material->material_type->value) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        wire:click="openMaterialModal('{{ $material->id }}')"
                                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-600"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="deleteMaterial('{{ $material->id }}')"
                                        wire:confirm="{{ __('Material wirklich löschen?') }}"
                                        class="rounded-lg p-2 text-gray-400 hover:bg-red-100 hover:text-red-600"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Material Modal -->
        @if($showMaterialModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
                <div class="fixed inset-0 bg-black/50" wire:click="closeMaterialModal"></div>
                <div class="relative w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $editingMaterialId ? __('Material bearbeiten') : __('Neues Material') }}
                    </h3>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Typ') }}</label>
                            <select wire:model.live="materialType" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                                <option value="text">{{ __('Text') }}</option>
                                <option value="video">{{ __('Video (URL)') }}</option>
                                <option value="link">{{ __('Link') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Titel') }}</label>
                            <input wire:model="materialTitle" type="text" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                            @error('materialTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        @if($materialType === 'text')
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Inhalt') }}</label>
                                <textarea wire:model="materialContent" rows="10" class="mt-1 block w-full rounded-lg border-gray-300 font-mono text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                                <p class="mt-1 text-xs text-gray-500">{{ __('Markdown wird unterstützt.') }}</p>
                                @error('materialContent') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @elseif(in_array($materialType, ['video', 'link']))
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('URL') }}</label>
                                <input wire:model="materialUrl" type="url" placeholder="https://..." class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                                @error('materialUrl') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button wire:click="closeMaterialModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('Abbrechen') }}
                        </button>
                        <button wire:click="saveMaterial" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600">
                            {{ __('Speichern') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Task Step --}}
    @if($step->step_type->value === 'task')
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Aufgabe konfigurieren') }}</h2>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Titel') }}</label>
                    <input wire:model="taskTitle" type="text" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                    @error('taskTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Aufgabenstellung') }}</label>
                    <textarea wire:model="taskInstructions" rows="6" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" placeholder="{{ __('Beschreibe die Aufgabe ausführlich...') }}"></textarea>
                    @error('taskInstructions') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-6 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Max. Punkte') }}</label>
                        <input wire:model="taskMaxPoints" type="number" min="1" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Fällig in (Tage)') }}</label>
                        <input wire:model="taskDueDays" type="number" min="1" placeholder="{{ __('Optional') }}" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Max. Dateigröße (MB)') }}</label>
                        <input wire:model="taskMaxFileSizeMb" type="number" min="1" max="100" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-3">
                        <input wire:model="taskAllowResubmit" type="checkbox" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                        <span class="text-sm text-gray-700">{{ __('Erneute Einreichung nach Überarbeitung erlauben') }}</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button wire:click="saveTask" class="rounded-lg bg-orange-500 px-6 py-2 font-medium text-white hover:bg-orange-600">
                        {{ __('Aufgabe speichern') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Assessment Step --}}
    @if($step->step_type->value === 'assessment')
        <div class="space-y-6">
            <!-- Assessment Settings -->
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Prüfungseinstellungen') }}</h2>

                <div class="space-y-6">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Titel') }}</label>
                            <input wire:model="assessmentTitle" type="text" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Zeitlimit (Minuten)') }}</label>
                            <input wire:model="assessmentTimeLimit" type="number" min="1" placeholder="{{ __('Kein Limit') }}" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Beschreibung') }}</label>
                        <textarea wire:model="assessmentDescription" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"></textarea>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Bestehensgrenze (%)') }}</label>
                            <input wire:model="assessmentPassingScore" type="number" min="1" max="100" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Max. Versuche') }}</label>
                            <input wire:model="assessmentMaxAttempts" type="number" min="1" max="10" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-3">
                            <input wire:model="assessmentShuffleQuestions" type="checkbox" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                            <span class="text-sm text-gray-700">{{ __('Fragen zufällig anordnen') }}</span>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button wire:click="saveAssessment" class="rounded-lg bg-orange-500 px-6 py-2 font-medium text-white hover:bg-orange-600">
                            {{ __('Einstellungen speichern') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Questions List -->
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h2 class="font-semibold text-gray-900">{{ __('Fragen') }} ({{ $this->questions->count() }})</h2>
                    <button
                        wire:click="openQuestionModal()"
                        class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Frage hinzufügen') }}
                    </button>
                </div>

                @if($this->questions->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="mt-4 text-gray-500">{{ __('Noch keine Fragen. Füge Multiple-Choice oder andere Fragen hinzu.') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($this->questions as $index => $question)
                            <div class="flex items-start justify-between px-6 py-4 hover:bg-gray-50">
                                <div class="flex items-start gap-4">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-medium text-gray-600">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ Str::limit($question->question_text, 80) }}</p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ $question->question_type->label() }} &middot; {{ $question->points }} {{ __('Punkte') }}
                                            @if($question->options->count() > 0)
                                                &middot; {{ $question->options->count() }} {{ __('Optionen') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        wire:click="openQuestionModal('{{ $question->id }}')"
                                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-600"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="deleteQuestion('{{ $question->id }}')"
                                        wire:confirm="{{ __('Frage wirklich löschen?') }}"
                                        class="rounded-lg p-2 text-gray-400 hover:bg-red-100 hover:text-red-600"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Question Modal -->
        @if($showQuestionModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
                <div class="fixed inset-0 bg-black/50" wire:click="closeQuestionModal"></div>
                <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $editingQuestionId ? __('Frage bearbeiten') : __('Neue Frage') }}
                    </h3>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Fragetyp') }}</label>
                            <select wire:model.live="questionType" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                                <option value="single_choice">{{ __('Single Choice') }}</option>
                                <option value="multiple_choice">{{ __('Multiple Choice') }}</option>
                                <option value="true_false">{{ __('Wahr/Falsch') }}</option>
                                <option value="text">{{ __('Freitext') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Frage') }}</label>
                            <textarea wire:model="questionText" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"></textarea>
                            @error('questionText') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Punkte') }}</label>
                            <input wire:model="questionPoints" type="number" min="1" class="mt-1 block w-32 rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        @if($questionType !== 'text')
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Antwortoptionen') }}</label>
                                <p class="mb-2 text-xs text-gray-500">
                                    @if($questionType === 'single_choice')
                                        {{ __('Wähle die eine richtige Antwort aus.') }}
                                    @elseif($questionType === 'multiple_choice')
                                        {{ __('Wähle alle richtigen Antworten aus.') }}
                                    @elseif($questionType === 'true_false')
                                        {{ __('Wähle die richtige Antwort aus.') }}
                                    @endif
                                </p>

                                <div class="space-y-2">
                                    @foreach($questionOptions as $index => $option)
                                        <div class="flex items-center gap-2">
                                            @if($questionType === 'single_choice' || $questionType === 'true_false')
                                                <input
                                                    type="radio"
                                                    wire:model="questionOptions.{{ $index }}.is_correct"
                                                    wire:click="$set('questionOptions', {{ json_encode(collect($questionOptions)->map(fn($o, $i) => [...$o, 'is_correct' => $i === $index])->toArray()) }})"
                                                    class="border-gray-300 text-orange-500 focus:ring-orange-500"
                                                    {{ $option['is_correct'] ? 'checked' : '' }}
                                                >
                                            @else
                                                <input
                                                    type="checkbox"
                                                    wire:model="questionOptions.{{ $index }}.is_correct"
                                                    class="rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                                                >
                                            @endif
                                            <input
                                                wire:model="questionOptions.{{ $index }}.text"
                                                type="text"
                                                placeholder="{{ __('Antwortoption') }}"
                                                class="flex-1 rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                                            >
                                            @if(count($questionOptions) > 2)
                                                <button
                                                    wire:click="removeQuestionOption({{ $index }})"
                                                    type="button"
                                                    class="rounded-lg p-2 text-gray-400 hover:bg-red-100 hover:text-red-600"
                                                >
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if($questionType !== 'true_false')
                                    <button
                                        wire:click="addQuestionOption"
                                        type="button"
                                        class="mt-2 inline-flex items-center gap-1 text-sm text-orange-600 hover:text-orange-800"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        {{ __('Option hinzufügen') }}
                                    </button>
                                @endif
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Erklärung (optional)') }}</label>
                            <textarea wire:model="questionExplanation" rows="2" placeholder="{{ __('Wird nach Beantwortung angezeigt') }}" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button wire:click="closeQuestionModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('Abbrechen') }}
                        </button>
                        <button wire:click="saveQuestion" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600">
                            {{ __('Speichern') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Back Link -->
    <div class="mt-8">
        <a
            href="{{ route('instructor.paths.show', $step->module->learningPath) }}"
            wire:navigate
            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ __('Zurück zum Lernpfad') }}
        </a>
    </div>
</div>
