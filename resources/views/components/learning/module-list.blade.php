@props([
    'modules',
    'enrollment' => null,
    'currentStepId' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @foreach ($modules as $module)
        @php
            $moduleSteps = $module->steps;
            $completedSteps = $enrollment
                ? $enrollment->stepProgress->whereIn('step_id', $moduleSteps->pluck('id'))->where('status', 'completed')->count()
                : 0;
            $totalSteps = $moduleSteps->count();
            $progressPercent = $totalSteps > 0 ? ($completedSteps / $totalSteps) * 100 : 0;
            $isLocked = false; // Add unlock logic if needed
        @endphp

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
            {{-- Module Header --}}
            <button
                @click="open = !open"
                class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 transition-colors"
                :class="{ 'bg-gray-50': open }"
            >
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center
                        {{ $progressPercent >= 100 ? 'bg-green-100 text-green-600' : 'bg-indigo-100 text-indigo-600' }}">
                        @if ($progressPercent >= 100)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <span class="text-sm font-semibold">{{ $loop->iteration }}</span>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">{{ $module->title }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ $completedSteps }}/{{ $totalSteps }} {{ __('Schritte') }}
                            @if ($module->estimated_minutes)
                                · {{ $module->estimated_minutes }} {{ __('Min.') }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Progress Ring --}}
                    <div class="relative w-10 h-10">
                        <svg class="w-10 h-10 transform -rotate-90">
                            <circle cx="20" cy="20" r="16" stroke="currentColor" stroke-width="3" fill="none" class="text-gray-200"/>
                            <circle cx="20" cy="20" r="16" stroke="currentColor" stroke-width="3" fill="none"
                                class="{{ $progressPercent >= 100 ? 'text-green-500' : 'text-indigo-500' }}"
                                stroke-dasharray="{{ 2 * 3.14159 * 16 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 16 * (1 - $progressPercent / 100) }}"
                                stroke-linecap="round"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-medium text-gray-600">
                            {{ number_format($progressPercent) }}%
                        </span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            {{-- Steps List --}}
            <div x-show="open" x-collapse>
                <div class="border-t border-gray-200 divide-y divide-gray-100">
                    @foreach ($moduleSteps as $step)
                        @php
                            $stepProgress = $enrollment?->stepProgress->firstWhere('step_id', $step->id);
                            $isCompleted = $stepProgress?->status->value === 'completed';
                            $isInProgress = $stepProgress?->status->value === 'in_progress';
                            $isCurrent = $step->id === $currentStepId;
                        @endphp

                        <a
                            href="{{ route('learner.steps.show', $step) }}"
                            class="flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors {{ $isCurrent ? 'bg-indigo-50' : '' }}"
                        >
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                {{ $isCompleted ? 'bg-green-100 text-green-600' : ($isInProgress || $isCurrent ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-400') }}">
                                @if ($isCompleted)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    @switch($step->step_type->value)
                                        @case('material')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            @break
                                        @case('task')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            @break
                                        @case('assessment')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            @break
                                    @endswitch
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate {{ $isCurrent ? 'text-indigo-600' : '' }}">
                                    {{ $step->title }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $step->step_type->label() }}
                                    @if ($step->estimated_minutes)
                                        · {{ $step->estimated_minutes }} {{ __('Min.') }}
                                    @endif
                                    @if ($step->points_value)
                                        · {{ $step->points_value }} {{ __('Punkte') }}
                                    @endif
                                </p>
                            </div>
                            @if ($isCurrent)
                                <x-ui.badge type="primary" size="sm">{{ __('Aktuell') }}</x-ui.badge>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
