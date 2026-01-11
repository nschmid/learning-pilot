<div>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('school.students') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Lernende importieren') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <!-- Result -->
            @if($importResult)
                <div class="mb-6 rounded-lg {{ $importResult['success'] ? 'bg-green-50' : 'bg-red-50' }} p-4">
                    <div class="flex">
                        @if($importResult['success'])
                            <svg class="size-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg class="size-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        @endif
                        <div class="ml-3">
                            <p class="text-sm font-medium {{ $importResult['success'] ? 'text-green-800' : 'text-red-800' }}">
                                {{ $importResult['message'] }}
                            </p>
                            @if(!empty($importResult['errors']))
                                <ul class="mt-2 list-disc list-inside text-sm {{ $importResult['success'] ? 'text-green-700' : 'text-red-700' }}">
                                    @foreach(array_slice($importResult['errors'], 0, 5) as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    @if(count($importResult['errors']) > 5)
                                        <li>{{ __('... und :count weitere Fehler', ['count' => count($importResult['errors']) - 5]) }}</li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('CSV-Import') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Importieren Sie mehrere Lernende auf einmal mit einer CSV-Datei.') }}
                    </p>
                </div>

                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <!-- CSV Format Info -->
                    <div class="mb-6 rounded-lg bg-gray-50 p-4">
                        <h4 class="text-sm font-medium text-gray-900">{{ __('CSV-Format') }}</h4>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Die CSV-Datei muss folgende Spalten enthalten (Semikolon-getrennt):') }}</p>
                        <ul class="mt-2 list-disc list-inside text-sm text-gray-600">
                            <li><strong>Vorname</strong> - {{ __('Pflichtfeld') }}</li>
                            <li><strong>Nachname</strong> - {{ __('Pflichtfeld') }}</li>
                            <li><strong>Email</strong> - {{ __('Pflichtfeld') }}</li>
                            <li><strong>Klasse</strong> - {{ __('Optional') }}</li>
                            <li><strong>Rolle</strong> - {{ __('Optional (learner oder instructor, Standard: learner)') }}</li>
                        </ul>
                        <button wire:click="downloadTemplate" class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            {{ __('Vorlage herunterladen') }}
                        </button>
                    </div>

                    <!-- Upload Form -->
                    <form wire:submit="import">
                        <div
                            x-data="{ isDragging: false }"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="isDragging = false"
                            class="mt-1 flex justify-center rounded-lg border-2 border-dashed px-6 py-10 transition"
                            :class="isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'"
                        >
                            <div class="text-center">
                                <svg class="mx-auto size-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <div class="mt-4 flex text-sm text-gray-600">
                                    <label class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                        <span>{{ __('Datei auswählen') }}</span>
                                        <input type="file" wire:model="csvFile" accept=".csv,.txt" class="sr-only">
                                    </label>
                                    <p class="pl-1">{{ __('oder hierher ziehen') }}</p>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">{{ __('CSV bis zu 2MB') }}</p>

                                @if($csvFile)
                                    <p class="mt-2 text-sm font-medium text-indigo-600">
                                        {{ $csvFile->getClientOriginalName() }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        @error('csvFile') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

                        <div class="mt-6">
                            <button
                                type="submit"
                                @disabled(!$csvFile || $isImporting)
                                class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="import">{{ __('Importieren') }}</span>
                                <span wire:loading wire:target="import">{{ __('Wird importiert...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
