<div
    x-data="{
        draggingModule: null,
        draggingStep: null,
        draggingModuleId: null
    }"
>
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
        <span class="text-gray-700">{{ $path->title }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $path->title }}</h1>
                @if($path->is_published)
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                        {{ __('Veröffentlicht') }}
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">
                        {{ __('Entwurf') }}
                    </span>
                @endif
            </div>
            <p class="mt-2 text-gray-500">{{ Str::limit($path->description, 150) }}</p>

            <!-- Stats -->
            <div class="mt-4 flex flex-wrap items-center gap-6 text-sm text-gray-500">
                <span class="flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    {{ $this->modules->count() }} {{ __('Module') }}
                </span>
                <span class="flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    {{ $path->steps()->count() }} {{ __('Schritte') }}
                </span>
                <span class="flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    {{ $path->enrollments()->count() }} {{ __('Teilnehmer') }}
                </span>
                <span class="flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    {{ $path->totalPoints() }} {{ __('Punkte') }}
                </span>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3">
            <a
                href="{{ route('instructor.paths.edit', $path) }}"
                wire:navigate
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                {{ __('Bearbeiten') }}
            </a>
            <button
                wire:click="togglePublish"
                class="inline-flex items-center gap-2 rounded-lg {{ $path->is_published ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }} px-4 py-2 text-sm font-medium text-white"
            >
                @if($path->is_published)
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>
                    {{ __('Verbergen') }}
                @else
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    {{ __('Veröffentlichen') }}
                @endif
            </button>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <!-- Modules List -->
    <div class="space-y-4">
        @forelse($this->modules as $moduleIndex => $module)
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white"
                draggable="true"
                x-on:dragstart="draggingModule = '{{ $module->id }}'"
                x-on:dragend="draggingModule = null"
                x-on:dragover.prevent
                x-on:drop.prevent="
                    if (draggingModule && draggingModule !== '{{ $module->id }}') {
                        const order = [...document.querySelectorAll('[data-module-id]')].map(el => el.dataset.moduleId);
                        $wire.updateModuleOrder(order);
                    }
                "
                data-module-id="{{ $module->id }}"
            >
                <!-- Module Header -->
                <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex items-center gap-4">
                        <div class="cursor-move text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                {{ __('Modul') }} {{ $moduleIndex + 1 }}: {{ $module->title }}
                            </h3>
                            @if($module->description)
                                <p class="text-sm text-gray-500">{{ Str::limit($module->description, 100) }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">{{ $module->steps->count() }} {{ __('Schritte') }}</span>
                        <button
                            wire:click="openModuleModal('{{ $module->id }}')"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-600"
                            title="{{ __('Bearbeiten') }}"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button
                            wire:click="confirmDelete('module', '{{ $module->id }}')"
                            class="rounded-lg p-2 text-gray-400 hover:bg-red-100 hover:text-red-600"
                            title="{{ __('Löschen') }}"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Steps List -->
                <div class="divide-y divide-gray-100">
                    @foreach($module->steps as $stepIndex => $step)
                        <div
                            class="flex items-center justify-between px-6 py-3 hover:bg-gray-50"
                            draggable="true"
                            x-on:dragstart.stop="draggingStep = '{{ $step->id }}'; draggingModuleId = '{{ $module->id }}'"
                            x-on:dragend="draggingStep = null; draggingModuleId = null"
                            x-on:dragover.prevent
                            x-on:drop.prevent.stop="
                                if (draggingStep && draggingStep !== '{{ $step->id }}' && draggingModuleId === '{{ $module->id }}') {
                                    const order = [...document.querySelectorAll('[data-module-{{ $module->id }}]')].map(el => el.dataset.stepId);
                                    $wire.updateStepOrder('{{ $module->id }}', order);
                                }
                            "
                            data-step-id="{{ $step->id }}"
                            data-module-{{ $module->id }}
                        >
                            <div class="flex items-center gap-4">
                                <div class="cursor-move text-gray-300 hover:text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                    </svg>
                                </div>
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{
                                    $step->step_type->value === 'material' ? 'bg-blue-100 text-blue-600' :
                                    ($step->step_type->value === 'task' ? 'bg-yellow-100 text-yellow-600' : 'bg-purple-100 text-purple-600')
                                }}">
                                    @if($step->step_type->value === 'material')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    @elseif($step->step_type->value === 'task')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $step->title }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $step->step_type->label() }} &middot; {{ $step->estimated_minutes }} {{ __('Min') }} &middot; {{ $step->points_value }} {{ __('Pkt') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a
                                    href="{{ route('instructor.steps.edit', $step) }}"
                                    wire:navigate
                                    class="rounded-lg px-3 py-1 text-sm font-medium text-orange-600 hover:bg-orange-50"
                                >
                                    {{ __('Inhalt bearbeiten') }}
                                </a>
                                <button
                                    wire:click="openStepModal('{{ $module->id }}', '{{ $step->id }}')"
                                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-600"
                                    title="{{ __('Einstellungen') }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                                <button
                                    wire:click="confirmDelete('step', '{{ $step->id }}')"
                                    class="rounded-lg p-2 text-gray-400 hover:bg-red-100 hover:text-red-600"
                                    title="{{ __('Löschen') }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <!-- Add Step Button -->
                    <div class="px-6 py-3">
                        <button
                            wire:click="openStepModal('{{ $module->id }}')"
                            class="inline-flex items-center gap-2 text-sm font-medium text-orange-600 hover:text-orange-800"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('Schritt hinzufügen') }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Noch keine Module') }}</h3>
                <p class="mt-2 text-gray-500">{{ __('Erstelle dein erstes Modul, um Lerninhalte hinzuzufügen.') }}</p>
            </div>
        @endforelse

        <!-- Add Module Button -->
        <button
            wire:click="openModuleModal()"
            class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 bg-white px-6 py-8 text-gray-500 hover:border-orange-500 hover:text-orange-600"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ __('Neues Modul hinzufügen') }}
        </button>
    </div>

    <!-- Module Modal -->
    @if($showModuleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <div class="fixed inset-0 bg-black/50" wire:click="closeModuleModal"></div>
            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $editingModuleId ? __('Modul bearbeiten') : __('Neues Modul') }}
                </h3>

                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Titel') }}</label>
                        <input
                            wire:model="moduleTitle"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="{{ __('z.B. Grundlagen') }}"
                        >
                        @error('moduleTitle')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Beschreibung') }}</label>
                        <textarea
                            wire:model="moduleDescription"
                            rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="{{ __('Optional: Kurze Beschreibung des Moduls') }}"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="closeModuleModal"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        {{ __('Abbrechen') }}
                    </button>
                    <button
                        wire:click="saveModule"
                        class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
                    >
                        {{ __('Speichern') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Step Modal -->
    @if($showStepModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <div class="fixed inset-0 bg-black/50" wire:click="closeStepModal"></div>
            <div class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $editingStepId ? __('Schritt bearbeiten') : __('Neuer Schritt') }}
                </h3>

                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Titel') }}</label>
                        <input
                            wire:model="stepTitle"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="{{ __('z.B. Einführung in Variablen') }}"
                        >
                        @error('stepTitle')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Typ') }}</label>
                        <div class="mt-2 grid grid-cols-3 gap-3">
                            <label class="relative cursor-pointer">
                                <input wire:model="stepType" type="radio" value="material" class="peer sr-only">
                                <div class="flex flex-col items-center gap-2 rounded-lg border-2 border-gray-200 p-4 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Material') }}</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input wire:model="stepType" type="radio" value="task" class="peer sr-only">
                                <div class="flex flex-col items-center gap-2 rounded-lg border-2 border-gray-200 p-4 peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Aufgabe') }}</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input wire:model="stepType" type="radio" value="assessment" class="peer sr-only">
                                <div class="flex flex-col items-center gap-2 rounded-lg border-2 border-gray-200 p-4 peer-checked:border-purple-500 peer-checked:bg-purple-50">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Prüfung') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Beschreibung') }}</label>
                        <textarea
                            wire:model="stepDescription"
                            rows="2"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="{{ __('Optional') }}"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Punkte') }}</label>
                            <input
                                wire:model="stepPoints"
                                type="number"
                                min="0"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Geschätzte Minuten') }}</label>
                            <input
                                wire:model="stepMinutes"
                                type="number"
                                min="1"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                            >
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="closeStepModal"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        {{ __('Abbrechen') }}
                    </button>
                    <button
                        wire:click="saveStep"
                        class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
                    >
                        {{ $editingStepId ? __('Speichern') : __('Erstellen & Inhalt bearbeiten') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <div class="fixed inset-0 bg-black/50" wire:click="cancelDelete"></div>
            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 rounded-full bg-red-100 p-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $deleteType === 'module' ? __('Modul löschen') : __('Schritt löschen') }}
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            @if($deleteType === 'module')
                                {{ __('Bist du sicher? Alle Schritte in diesem Modul werden ebenfalls gelöscht.') }}
                            @else
                                {{ __('Bist du sicher, dass du diesen Schritt löschen möchtest?') }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="cancelDelete"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        {{ __('Abbrechen') }}
                    </button>
                    <button
                        wire:click="delete"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                    >
                        {{ __('Löschen') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
