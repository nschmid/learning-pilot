<div>
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li><a href="{{ route('instructor.paths.index') }}" class="text-gray-500 hover:text-gray-700">{{ __('Lernpfade') }}</a></li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        <span class="ml-1 text-gray-700 font-medium">{{ $assessment->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Prüfung erstellen') }}</h1>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Settings Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">{{ __('Einstellungen') }}</h2>

                <form wire:submit="saveSettings" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Titel') }}</label>
                        <input wire:model="title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Typ') }}</label>
                        <select wire:model="assessmentType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                            <option value="quiz">{{ __('Quiz') }}</option>
                            <option value="exam">{{ __('Prüfung') }}</option>
                            <option value="survey">{{ __('Umfrage') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Beschreibung') }}</label>
                        <textarea wire:model="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Zeitlimit (Min.)') }}</label>
                            <input wire:model="timeLimit" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Bestehensgrenze (%)') }}</label>
                            <input wire:model="passingScore" type="number" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Max. Versuche') }}</label>
                        <input wire:model="maxAttempts" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="{{ __('Unbegrenzt') }}">
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input wire:model="shuffleQuestions" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Fragen mischen') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input wire:model="shuffleAnswers" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Antworten mischen') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input wire:model="showCorrectAnswers" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Richtige Antworten zeigen') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input wire:model="showScoreImmediately" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Ergebnis sofort zeigen') }}</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-sm">{{ __('Einstellungen speichern') }}</button>
                </form>

                <!-- Stats -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $this->questionCount }}</p>
                            <p class="text-sm text-gray-500">{{ __('Fragen') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $this->totalPoints }}</p>
                            <p class="text-sm text-gray-500">{{ __('Punkte') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Panel -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('Fragen') }}</h2>
                    <div class="flex gap-2">
                        <button wire:click="addTrueFalseQuestion" class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            {{ __('Richtig/Falsch') }}
                        </button>
                        <button wire:click="openQuestionModal" class="inline-flex items-center px-3 py-1.5 text-sm bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Frage hinzufügen') }}
                        </button>
                    </div>
                </div>

                <div class="p-4 space-y-3">
                    @forelse ($this->questions as $index => $question)
                        <div class="border border-gray-200 rounded-lg p-4" draggable="true">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 font-medium text-sm">{{ $index + 1 }}</span>
                                    <div>
                                        <p class="text-gray-900">{{ Str::limit($question->question_text, 150) }}</p>
                                        <div class="mt-2 flex items-center gap-3 text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100">{{ $question->question_type->label() }}</span>
                                            <span>{{ $question->points }} {{ __('Punkte') }}</span>
                                            @if ($question->options->count() > 0)
                                                <span>{{ $question->options->count() }} {{ __('Optionen') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button wire:click="duplicateQuestion('{{ $question->id }}')" class="p-2 text-gray-400 hover:text-gray-600" title="{{ __('Duplizieren') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                    <button wire:click="openQuestionModal('{{ $question->id }}')" class="p-2 text-gray-400 hover:text-gray-600" title="{{ __('Bearbeiten') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteQuestion('{{ $question->id }}')" wire:confirm="{{ __('Möchtest du diese Frage wirklich löschen?') }}" class="p-2 text-red-400 hover:text-red-600" title="{{ __('Löschen') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Keine Fragen') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Füge deine erste Frage hinzu.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Question Modal -->
    @if ($showQuestionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeQuestionModal"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit="saveQuestion">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 max-h-[70vh] overflow-y-auto">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editingQuestionId ? __('Frage bearbeiten') : __('Neue Frage') }}</h3>

                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Fragetyp') }}</label>
                                        <select wire:model.live="questionType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                            <option value="single_choice">{{ __('Einzelauswahl') }}</option>
                                            <option value="multiple_choice">{{ __('Mehrfachauswahl') }}</option>
                                            <option value="true_false">{{ __('Richtig/Falsch') }}</option>
                                            <option value="text">{{ __('Freitext') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Punkte') }}</label>
                                        <input wire:model="questionPoints" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Frage') }}</label>
                                    <textarea wire:model="questionText" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required></textarea>
                                    @error('questionText') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @if (in_array($questionType, ['single_choice', 'multiple_choice', 'true_false']))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Antwortoptionen') }}</label>
                                        @error('questionOptions') <span class="text-red-500 text-sm block mb-2">{{ $message }}</span> @enderror

                                        <div class="space-y-2">
                                            @foreach ($questionOptions as $index => $option)
                                                <div class="flex items-center gap-2">
                                                    @if ($questionType === 'single_choice')
                                                        <input type="radio" wire:click="setCorrectOption({{ $index }})" {{ $option['is_correct'] ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-teal-600">
                                                    @else
                                                        <input type="checkbox" wire:model="questionOptions.{{ $index }}.is_correct" class="h-4 w-4 rounded border-gray-300 text-teal-600">
                                                    @endif
                                                    <input wire:model="questionOptions.{{ $index }}.text" type="text" placeholder="{{ __('Antwortoption') }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                                    @if (count($questionOptions) > 2 && $questionType !== 'true_false')
                                                        <button type="button" wire:click="removeOption({{ $index }})" class="p-1 text-red-400 hover:text-red-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        @if ($questionType !== 'true_false' && count($questionOptions) < 10)
                                            <button type="button" wire:click="addOption" class="mt-2 text-sm text-teal-600 hover:text-teal-800">+ {{ __('Option hinzufügen') }}</button>
                                        @endif
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Erklärung (optional)') }}</label>
                                    <textarea wire:model="questionExplanation" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" placeholder="{{ __('Wird nach Beantwortung angezeigt') }}"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700">{{ __('Speichern') }}</button>
                            <button type="button" wire:click="closeQuestionModal" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50">{{ __('Abbrechen') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
