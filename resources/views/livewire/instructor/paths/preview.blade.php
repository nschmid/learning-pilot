<div class="mx-auto max-w-7xl">
    <!-- Breadcrumb & Header -->
    <div class="mb-6 flex items-center justify-between">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('instructor.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('instructor.paths.show', $path->slug) }}" wire:navigate class="hover:text-gray-700">{{ $path->title }}</a>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-700">{{ __('Vorschau') }}</span>
        </nav>
        <div class="flex items-center gap-3">
            <span class="rounded-full bg-purple-100 px-3 py-1 text-sm font-medium text-purple-800">
                <svg class="mr-1 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                {{ __('Vorschaumodus') }}
            </span>
            <a href="{{ route('instructor.paths.show', $path->slug) }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                {{ __('Beenden') }}
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-4">
        <!-- Sidebar - Module Navigation -->
        <div class="lg:col-span-1">
            <div class="sticky top-6 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-4 py-3">
                    <h2 class="font-semibold text-gray-900">{{ __('Inhalt') }}</h2>
                </div>
                <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
                    @forelse($path->modules as $module)
                        <div class="border-b border-gray-100 last:border-b-0">
                            <button
                                wire:click="selectModule('{{ $module->id }}')"
                                class="flex w-full items-center justify-between px-4 py-3 text-left hover:bg-gray-50 {{ $selectedModuleId === $module->id ? 'bg-teal-50' : '' }}"
                            >
                                <span class="text-sm font-medium {{ $selectedModuleId === $module->id ? 'text-teal-700' : 'text-gray-900' }}">
                                    {{ $module->title }}
                                </span>
                                <svg class="h-5 w-5 text-gray-400 transition {{ $selectedModuleId === $module->id ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            @if($selectedModuleId === $module->id)
                                <div class="border-t border-gray-100 bg-gray-50 px-2 py-2">
                                    @forelse($module->steps as $step)
                                        <button
                                            wire:click="selectStep('{{ $step->id }}')"
                                            class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm {{ $selectedStepId === $step->id ? 'bg-white text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}"
                                        >
                                            @switch($step->step_type->value)
                                                @case('material')
                                                    <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                    @break
                                                @case('task')
                                                    <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                    </svg>
                                                    @break
                                                @case('assessment')
                                                    <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    @break
                                            @endswitch
                                            <span class="truncate">{{ $step->title }}</span>
                                        </button>
                                    @empty
                                        <p class="px-3 py-2 text-sm text-gray-400">{{ __('Keine Schritte') }}</p>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center text-gray-500">
                            <p>{{ __('Keine Module vorhanden.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            @if($this->selectedStep)
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <!-- Step Header -->
                    <div class="border-b border-gray-200 px-6 py-4">
                        <div class="flex items-center gap-2">
                            @switch($this->selectedStep->step_type->value)
                                @case('material')
                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-800">{{ __('Lerninhalt') }}</span>
                                    @break
                                @case('task')
                                    <span class="rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-800">{{ __('Aufgabe') }}</span>
                                    @break
                                @case('assessment')
                                    <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-800">{{ __('Test') }}</span>
                                    @break
                            @endswitch
                            @if($this->selectedStep->points_value)
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                    {{ $this->selectedStep->points_value }} {{ __('Punkte') }}
                                </span>
                            @endif
                            @if($this->selectedStep->estimated_minutes)
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                    ~{{ $this->selectedStep->estimated_minutes }} {{ __('Min.') }}
                                </span>
                            @endif
                        </div>
                        <h1 class="mt-2 text-xl font-bold text-gray-900">{{ $this->selectedStep->title }}</h1>
                        @if($this->selectedStep->description)
                            <p class="mt-1 text-gray-500">{{ $this->selectedStep->description }}</p>
                        @endif
                    </div>

                    <!-- Step Content -->
                    <div class="p-6">
                        @switch($this->selectedStep->step_type->value)
                            @case('material')
                                @if($this->selectedStep->materials->count() > 0)
                                    <div class="space-y-6">
                                        @foreach($this->selectedStep->materials as $material)
                                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                                <h3 class="font-medium text-gray-900">{{ $material->title }}</h3>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ __('Typ') }}: {{ $material->material_type->label() }}
                                                </p>
                                                @if($material->content)
                                                    <div class="prose prose-indigo mt-4 max-w-none">
                                                        {!! $material->content !!}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <p class="mt-4 text-gray-500">{{ __('Keine Materialien hinzugefügt.') }}</p>
                                        <a href="{{ route('instructor.steps.materials', $this->selectedStep->id) }}" wire:navigate class="mt-2 inline-block text-sm text-teal-600 hover:text-teal-800">
                                            {{ __('Materialien hinzufügen') }} &rarr;
                                        </a>
                                    </div>
                                @endif
                                @break

                            @case('task')
                                @if($this->selectedStep->task)
                                    <div class="space-y-4">
                                        <div class="rounded-lg bg-orange-50 p-4">
                                            <h3 class="font-medium text-orange-900">{{ $this->selectedStep->task->title }}</h3>
                                            <p class="mt-1 text-sm text-orange-700">{{ $this->selectedStep->task->task_type->label() }} &bull; {{ $this->selectedStep->task->max_points }} {{ __('Punkte') }}</p>
                                        </div>
                                        @if($this->selectedStep->task->instructions)
                                            <div class="prose prose-indigo max-w-none">
                                                {!! nl2br(e($this->selectedStep->task->instructions)) !!}
                                            </div>
                                        @endif
                                        @if($this->selectedStep->task->rubric && count($this->selectedStep->task->rubric) > 0)
                                            <div class="mt-6">
                                                <h4 class="mb-2 font-medium text-gray-900">{{ __('Bewertungskriterien') }}</h4>
                                                <ul class="list-inside list-disc space-y-1 text-sm text-gray-600">
                                                    @foreach($this->selectedStep->task->rubric as $criterion)
                                                        <li>{{ $criterion['name'] ?? $criterion }} ({{ $criterion['points'] ?? '-' }} {{ __('Punkte') }})</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p class="mt-4 text-gray-500">{{ __('Keine Aufgabe konfiguriert.') }}</p>
                                    </div>
                                @endif
                                @break

                            @case('assessment')
                                @if($this->selectedStep->assessment)
                                    <div class="space-y-4">
                                        <div class="rounded-lg bg-purple-50 p-4">
                                            <h3 class="font-medium text-purple-900">{{ $this->selectedStep->assessment->title }}</h3>
                                            <p class="mt-1 text-sm text-purple-700">
                                                {{ $this->selectedStep->assessment->questions->count() }} {{ __('Fragen') }}
                                                @if($this->selectedStep->assessment->time_limit_minutes)
                                                    &bull; {{ $this->selectedStep->assessment->time_limit_minutes }} {{ __('Min. Zeitlimit') }}
                                                @endif
                                                &bull; {{ $this->selectedStep->assessment->passing_score_percent }}% {{ __('zum Bestehen') }}
                                            </p>
                                        </div>
                                        @if($this->selectedStep->assessment->description)
                                            <p class="text-gray-600">{{ $this->selectedStep->assessment->description }}</p>
                                        @endif
                                        <div class="rounded-lg border border-gray-200 p-4">
                                            <h4 class="mb-3 font-medium text-gray-900">{{ __('Fragenvorschau') }}</h4>
                                            <div class="space-y-3">
                                                @foreach($this->selectedStep->assessment->questions->take(3) as $index => $question)
                                                    <div class="rounded bg-gray-50 p-3">
                                                        <p class="text-sm font-medium text-gray-700">{{ $index + 1 }}. {{ \Illuminate\Support\Str::limit($question->question_text, 100) }}</p>
                                                        <p class="mt-1 text-xs text-gray-500">{{ $question->question_type->label() }} &bull; {{ $question->points }} {{ __('Punkte') }}</p>
                                                    </div>
                                                @endforeach
                                                @if($this->selectedStep->assessment->questions->count() > 3)
                                                    <p class="text-center text-sm text-gray-500">
                                                        {{ __('... und') }} {{ $this->selectedStep->assessment->questions->count() - 3 }} {{ __('weitere Fragen') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="mt-4 text-gray-500">{{ __('Kein Test konfiguriert.') }}</p>
                                    </div>
                                @endif
                                @break
                        @endswitch
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-24 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Wähle einen Schritt aus') }}</h3>
                    <p class="mt-2 text-gray-500">{{ __('Klicke auf einen Schritt im Menü links, um die Vorschau zu sehen.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
