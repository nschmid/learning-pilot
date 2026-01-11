@props([
    'path',
    'enrollment',
    'currentStepId' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden']) }}>
    {{-- Path Header --}}
    <div class="p-4 border-b border-gray-200 bg-gray-50">
        <h2 class="font-semibold text-gray-900 line-clamp-2">{{ $path->title }}</h2>
        <div class="mt-2 flex items-center gap-2">
            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-indigo-600 rounded-full transition-all duration-300" style="width: {{ $enrollment->progress_percent }}%"></div>
            </div>
            <span class="text-sm font-medium text-gray-600">{{ number_format($enrollment->progress_percent) }}%</span>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 divide-x divide-gray-200 border-b border-gray-200">
        <div class="p-3 text-center">
            <p class="text-lg font-semibold text-gray-900">{{ $enrollment->points_earned ?? 0 }}</p>
            <p class="text-xs text-gray-500">{{ __('Punkte') }}</p>
        </div>
        <div class="p-3 text-center">
            @php
                $timeSpent = $enrollment->total_time_spent_seconds ?? 0;
                $hours = floor($timeSpent / 3600);
                $minutes = floor(($timeSpent % 3600) / 60);
            @endphp
            <p class="text-lg font-semibold text-gray-900">
                @if ($hours > 0)
                    {{ $hours }}h {{ $minutes }}m
                @else
                    {{ $minutes }}m
                @endif
            </p>
            <p class="text-xs text-gray-500">{{ __('Lernzeit') }}</p>
        </div>
    </div>

    {{-- Modules List --}}
    <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
        @foreach ($path->modules as $module)
            @php
                $moduleSteps = $module->steps;
                $completedSteps = $enrollment->stepProgress->whereIn('step_id', $moduleSteps->pluck('id'))->where('status', 'completed')->count();
                $totalSteps = $moduleSteps->count();
                $isComplete = $completedSteps >= $totalSteps && $totalSteps > 0;
            @endphp

            <div x-data="{ open: {{ $moduleSteps->pluck('id')->contains($currentStepId) ? 'true' : 'false' }} }">
                <button
                    @click="open = !open"
                    class="w-full flex items-center gap-2 p-3 text-left hover:bg-gray-50 transition-colors"
                >
                    <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs
                        {{ $isComplete ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                        @if ($isComplete)
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $loop->iteration }}
                        @endif
                    </div>
                    <span class="flex-1 text-sm font-medium text-gray-700 truncate">{{ $module->title }}</span>
                    <span class="text-xs text-gray-400">{{ $completedSteps }}/{{ $totalSteps }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-collapse class="bg-gray-50">
                    @foreach ($moduleSteps as $step)
                        @php
                            $stepProgress = $enrollment->stepProgress->firstWhere('step_id', $step->id);
                            $isCompleted = $stepProgress?->status->value === 'completed';
                            $isCurrent = $step->id === $currentStepId;
                        @endphp

                        <a
                            href="{{ route('learner.steps.show', $step) }}"
                            class="flex items-center gap-2 px-3 py-2 pl-11 text-sm hover:bg-gray-100 transition-colors {{ $isCurrent ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600' }}"
                        >
                            <div class="flex-shrink-0 w-4 h-4 rounded-full flex items-center justify-center
                                {{ $isCompleted ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-indigo-500 text-white' : 'border-2 border-gray-300') }}">
                                @if ($isCompleted)
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @elseif ($isCurrent)
                                    <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                                @endif
                            </div>
                            <span class="truncate">{{ $step->title }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
