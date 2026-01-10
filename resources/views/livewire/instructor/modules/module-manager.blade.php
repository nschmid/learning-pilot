<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li><a href="{{ route('instructor.paths.index') }}" class="text-gray-500 hover:text-gray-700">{{ __('Lernpfade') }}</a></li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            <a href="{{ route('instructor.paths.edit', $path) }}" class="ml-1 text-gray-500 hover:text-gray-700">{{ $path->title }}</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            <span class="ml-1 text-gray-700 font-medium">{{ __('Module') }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ __('Module verwalten') }}</h1>
        </div>
        <button wire:click="openModuleModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('Modul hinzufügen') }}
        </button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Modules List -->
    <div class="space-y-4" x-data="{ dragModule: null }" x-on:drop.prevent="$wire.updateModuleOrder(Array.from($event.target.closest('.space-y-4').children).map(el => el.dataset.moduleId).filter(Boolean))">
        @forelse ($this->modules as $module)
            <div data-module-id="{{ $module->id }}" class="bg-white rounded-lg shadow" draggable="true" x-on:dragstart="dragModule = '{{ $module->id }}'" x-on:dragover.prevent x-on:dragend="dragModule = null">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="cursor-move text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $module->title }}</h3>
                                @if ($module->description)
                                    <p class="text-sm text-gray-500">{{ Str::limit($module->description, 100) }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">{{ $module->steps->count() }} {{ __('Schritte') }}</span>
                            <button wire:click="openModuleModal('{{ $module->id }}')" class="p-2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="deleteModule('{{ $module->id }}')" wire:confirm="{{ __('Möchtest du dieses Modul wirklich löschen?') }}" class="p-2 text-red-400 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Steps -->
                <div class="p-4 space-y-2">
                    @foreach ($module->steps as $step)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg {{ $step->step_type->value === 'material' ? 'bg-blue-100 text-blue-600' : ($step->step_type->value === 'task' ? 'bg-orange-100 text-orange-600' : 'bg-purple-100 text-purple-600') }}">
                                    @if ($step->step_type->value === 'material')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    @elseif ($step->step_type->value === 'task')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $step->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $step->step_type->label() }} · {{ $step->points_value }} {{ __('Punkte') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="editStep('{{ $step->id }}')" class="p-2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="deleteStep('{{ $step->id }}')" wire:confirm="{{ __('Möchtest du diesen Schritt wirklich löschen?') }}" class="p-2 text-red-400 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <button wire:click="openStepModal('{{ $module->id }}')" class="w-full p-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-indigo-500 hover:text-indigo-500 transition-colors">
                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span class="text-sm">{{ __('Schritt hinzufügen') }}</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Keine Module') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('Füge dein erstes Modul hinzu, um Inhalte zu strukturieren.') }}</p>
                <button wire:click="openModuleModal" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    {{ __('Modul hinzufügen') }}
                </button>
            </div>
        @endforelse
    </div>

    <!-- Module Modal -->
    @if ($showModuleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModuleModal"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveModule">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editingModuleId ? __('Modul bearbeiten') : __('Neues Modul') }}</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Titel') }}</label>
                                    <input wire:model="moduleTitle" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @error('moduleTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Beschreibung') }}</label>
                                    <textarea wire:model="moduleDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Entsperrbedingung') }}</label>
                                    <select wire:model.live="unlockCondition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="sequential">{{ __('Sequentiell (vorheriges Modul abschließen)') }}</option>
                                        <option value="completion_percent">{{ __('Nach Fortschritt') }}</option>
                                        <option value="manual">{{ __('Immer verfügbar') }}</option>
                                    </select>
                                </div>

                                @if ($unlockCondition === 'completion_percent')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Erforderlicher Fortschritt (%)') }}</label>
                                        <input wire:model="unlockValue" type="number" min="1" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                @endif

                                <div class="flex items-center">
                                    <input wire:model="moduleIsRequired" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label class="ml-2 text-sm text-gray-700">{{ __('Pflichtmodul') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ __('Speichern') }}</button>
                            <button type="button" wire:click="closeModuleModal" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50">{{ __('Abbrechen') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Step Modal -->
    @if ($showStepModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeStepModal"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveStep">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editingStepId ? __('Schritt bearbeiten') : __('Neuer Schritt') }}</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Titel') }}</label>
                                    <input wire:model="stepTitle" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @error('stepTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Typ') }}</label>
                                    <select wire:model="stepType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="material">{{ __('Lernmaterial') }}</option>
                                        <option value="task">{{ __('Aufgabe') }}</option>
                                        <option value="assessment">{{ __('Prüfung') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Beschreibung') }}</label>
                                    <textarea wire:model="stepDescription" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Punkte') }}</label>
                                        <input wire:model="stepPointsValue" type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Geschätzte Zeit (Min.)') }}</label>
                                        <input wire:model="stepEstimatedMinutes" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <label class="flex items-center">
                                        <input wire:model="stepIsRequired" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ __('Pflicht') }}</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="stepIsPreview" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ __('Vorschau') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ $editingStepId ? __('Speichern') : __('Erstellen & Bearbeiten') }}</button>
                            <button type="button" wire:click="closeStepModal" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50">{{ __('Abbrechen') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
