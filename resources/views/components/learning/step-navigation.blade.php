@props([
    'previousStep' => null,
    'nextStep' => null,
    'currentStep',
    'enrollment',
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-between py-4 border-t border-gray-200']) }}>
    {{-- Previous Button --}}
    <div>
        @if ($previousStep)
            <a href="{{ route('learner.steps.show', $previousStep) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="hidden sm:inline">{{ Str::limit($previousStep->title, 20) }}</span>
                <span class="sm:hidden">{{ __('Zurück') }}</span>
            </a>
        @else
            <a href="{{ route('learner.paths.show', $currentStep->module->learningPath) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('Übersicht') }}
            </a>
        @endif
    </div>

    {{-- Progress Indicator --}}
    <div class="hidden md:flex items-center gap-2 text-sm text-gray-500">
        @php
            $allSteps = $currentStep->module->learningPath->modules->flatMap->steps;
            $currentIndex = $allSteps->search(fn($s) => $s->id === $currentStep->id);
            $totalSteps = $allSteps->count();
        @endphp
        <span>{{ $currentIndex + 1 }} / {{ $totalSteps }}</span>
    </div>

    {{-- Next/Complete Button --}}
    <div>
        @if ($nextStep)
            <a href="{{ route('learner.steps.show', $nextStep) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <span class="hidden sm:inline">{{ Str::limit($nextStep->title, 20) }}</span>
                <span class="sm:hidden">{{ __('Weiter') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <a href="{{ route('learner.paths.show', $currentStep->module->learningPath) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('Abschliessen') }}
            </a>
        @endif
    </div>
</div>
