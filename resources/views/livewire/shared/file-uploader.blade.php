<div>
    <!-- Drop Zone -->
    <div
        x-data="{ dragging: false }"
        x-on:dragover.prevent="dragging = true"
        x-on:dragleave.prevent="dragging = false"
        x-on:drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
        :class="{ 'border-indigo-500 bg-indigo-50': dragging }"
        class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 p-8 transition-colors hover:border-gray-400"
    >
        <input
            x-ref="fileInput"
            wire:model="files"
            type="file"
            class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
            @if($multiple) multiple @endif
            @if($accept) accept="{{ $accept }}" @endif
        >

        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-600">
                <span class="font-medium text-indigo-600">{{ __('Datei auswählen') }}</span>
                {{ __('oder per Drag & Drop') }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
                @if($accept)
                    {{ $accept }}
                @endif
                {{ __('bis zu :size MB', ['size' => round($maxFileSize / 1024, 1)]) }}
            </p>
        </div>

        <!-- Loading Indicator -->
        <div wire:loading wire:target="files" class="absolute inset-0 flex items-center justify-center rounded-xl bg-white/80">
            <svg class="h-8 w-8 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <!-- Errors -->
    @if(count($errors) > 0)
        <div class="mt-4 rounded-lg bg-red-50 p-4">
            <ul class="list-inside list-disc space-y-1 text-sm text-red-600">
                @foreach($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Uploaded Files -->
    @if($showPreview && count($uploadedFiles) > 0)
        <div class="mt-4 space-y-2">
            @foreach($uploadedFiles as $file)
                <div wire:key="file-{{ $file['id'] }}" class="flex items-center gap-4 rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <!-- Preview -->
                    @if($file['preview'] ?? null)
                        <img src="{{ $file['preview'] }}" alt="{{ $file['name'] }}" class="h-12 w-12 rounded-lg object-cover">
                    @else
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-200">
                            <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    @endif

                    <!-- File Info -->
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium text-gray-900">{{ $file['name'] }}</p>
                        <p class="text-sm text-gray-500">{{ $this->formatSize($file['size']) }}</p>
                    </div>

                    <!-- Remove Button -->
                    <button
                        wire:click="removeFile('{{ $file['id'] }}')"
                        type="button"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-200 hover:text-red-500"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>
