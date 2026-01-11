<div>
    @if($show)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="$el.querySelector('dialog').showModal()">
            <dialog class="fixed inset-0 z-50 m-auto w-full max-w-lg rounded-xl bg-white p-0 shadow-xl backdrop:bg-gray-900/50" @close="$wire.close()">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Erklärung') }}</h3>
                        <button wire:click="close" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Question -->
                    @if($this->question)
                        <div class="mt-4 rounded-lg bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Frage') }}</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $this->question->question_text }}</p>
                        </div>
                    @endif

                    <!-- User's Answer -->
                    @if($userAnswer)
                        <div class="mt-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Ihre Antwort') }}</p>
                            <p class="mt-1 text-sm text-red-600">{{ $userAnswer }}</p>
                        </div>
                    @endif

                    <!-- Explanation -->
                    <div class="mt-4">
                        @if($isLoading)
                            <div class="flex items-center justify-center py-8">
                                <svg class="size-8 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        @elseif($explanation)
                            <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4">
                                <p class="text-xs font-medium uppercase tracking-wider text-indigo-600">{{ __('KI-Erklärung') }}</p>
                                <div class="mt-2 prose prose-sm prose-indigo max-w-none">
                                    {!! \Illuminate\Support\Str::markdown($explanation) !!}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Close button -->
                    <div class="mt-6">
                        <button
                            wire:click="close"
                            class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
                        >
                            {{ __('Verstanden') }}
                        </button>
                    </div>
                </div>
            </dialog>
        </div>
    @endif
</div>
