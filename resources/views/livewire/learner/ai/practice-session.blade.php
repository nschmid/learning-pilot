<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('KI-Übungen') }}</h2>
                @if($this->module)
                    <p class="mt-1 text-sm text-gray-500">{{ $this->module->title }}</p>
                @endif
            </div>
            <livewire:learner.ai.usage-stats />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-6 rounded-lg bg-red-50 p-4">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            @if(!$sessionId)
                <!-- Session Configuration -->
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Übungssession starten') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Konfigurieren Sie Ihre Übungssession und lassen Sie KI-generierte Fragen erstellen.') }}</p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Schwierigkeitsgrad') }}</label>
                                <select wire:model="difficulty" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($this->difficulties as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Anzahl Fragen') }}</label>
                                <input
                                    type="number"
                                    wire:model="questionCount"
                                    min="3"
                                    max="20"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            <button
                                wire:click="startSession"
                                wire:loading.attr="disabled"
                                class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="startSession">{{ __('Session starten') }}</span>
                                <span wire:loading wire:target="startSession">{{ __('Fragen werden generiert...') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($sessionComplete)
                <!-- Session Complete -->
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-8 text-center sm:px-6">
                        <svg class="mx-auto size-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ __('Session abgeschlossen!') }}</h3>

                        @if($this->session)
                            <p class="mt-2 text-4xl font-bold text-indigo-600">{{ $this->session->score_percent }}%</p>
                            <p class="text-sm text-gray-500">{{ __('Richtige Antworten') }}</p>
                        @endif

                        <div class="mt-8 flex justify-center gap-4">
                            <a href="{{ route('learner.ai.practice', $moduleId) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                                {{ __('Neue Session') }}
                            </a>
                            <a href="{{ route('learner.modules.show', $moduleId) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ __('Zurück zum Modul') }}
                            </a>
                        </div>
                    </div>
                </div>
            @elseif($currentQuestion)
                <!-- Question Card -->
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <!-- Progress -->
                    <div class="border-b border-gray-200 px-4 py-3 sm:px-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">
                                {{ __('Frage :current von :total', ['current' => $currentQuestion['position'], 'total' => $currentQuestion['total']]) }}
                            </span>
                            <div class="h-2 w-32 overflow-hidden rounded-full bg-gray-200">
                                <div class="h-2 bg-indigo-600" style="width: {{ ($currentQuestion['position'] / $currentQuestion['total']) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-6 sm:px-6">
                        <!-- Question -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ $currentQuestion['text'] }}</h3>
                        </div>

                        @if(!$showResult)
                            <!-- Answer Options -->
                            @if(in_array($currentQuestion['type'], ['single_choice', 'multiple_choice']))
                                <div class="space-y-3">
                                    @foreach($currentQuestion['options'] as $index => $option)
                                        <label class="flex cursor-pointer items-center rounded-lg border p-4 hover:bg-gray-50 {{ $userAnswer === $option ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                            <input
                                                type="radio"
                                                wire:model="userAnswer"
                                                value="{{ $option }}"
                                                class="size-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            >
                                            <span class="ml-3 text-sm text-gray-700">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($currentQuestion['type'] === 'true_false')
                                <div class="flex gap-4">
                                    <label class="flex-1 cursor-pointer rounded-lg border p-4 text-center hover:bg-gray-50 {{ $userAnswer === 'true' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                        <input type="radio" wire:model="userAnswer" value="true" class="sr-only">
                                        <span class="text-sm font-medium text-gray-700">{{ __('Wahr') }}</span>
                                    </label>
                                    <label class="flex-1 cursor-pointer rounded-lg border p-4 text-center hover:bg-gray-50 {{ $userAnswer === 'false' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                        <input type="radio" wire:model="userAnswer" value="false" class="sr-only">
                                        <span class="text-sm font-medium text-gray-700">{{ __('Falsch') }}</span>
                                    </label>
                                </div>
                            @else
                                <textarea
                                    wire:model="userAnswer"
                                    rows="4"
                                    class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="{{ __('Ihre Antwort...') }}"
                                ></textarea>
                            @endif

                            <button
                                wire:click="submitAnswer"
                                @disabled(!$userAnswer)
                                class="mt-6 w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{ __('Antwort prüfen') }}
                            </button>
                        @else
                            <!-- Result -->
                            <div class="rounded-lg {{ $isCorrect ? 'bg-green-50' : 'bg-red-50' }} p-4">
                                <div class="flex items-start gap-3">
                                    @if($isCorrect)
                                        <svg class="size-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-green-800">{{ __('Richtig!') }}</h4>
                                        </div>
                                    @else
                                        <svg class="size-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-red-800">{{ __('Leider falsch') }}</h4>
                                        </div>
                                    @endif
                                </div>

                                @if($explanation)
                                    <div class="mt-4 border-t {{ $isCorrect ? 'border-green-200' : 'border-red-200' }} pt-4">
                                        <h5 class="text-sm font-medium {{ $isCorrect ? 'text-green-800' : 'text-red-800' }}">{{ __('Erklärung') }}</h5>
                                        <p class="mt-1 text-sm {{ $isCorrect ? 'text-green-700' : 'text-red-700' }}">{{ $explanation }}</p>
                                    </div>
                                @endif
                            </div>

                            <button
                                wire:click="nextQuestion"
                                class="mt-6 w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-500"
                            >
                                {{ __('Nächste Frage') }}
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
