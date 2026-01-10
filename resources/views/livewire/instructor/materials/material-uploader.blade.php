<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Materialien') }}</h1>
            <p class="text-gray-500">{{ $step->title }}</p>
        </div>
        <button wire:click="openModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('Material hinzufügen') }}
        </button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Materials List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 space-y-3">
            @forelse ($this->materials as $material)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg" draggable="true">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center {{ match($material->material_type->value) {
                            'text' => 'bg-blue-100 text-blue-600',
                            'video' => 'bg-red-100 text-red-600',
                            'audio' => 'bg-purple-100 text-purple-600',
                            'pdf' => 'bg-orange-100 text-orange-600',
                            'image' => 'bg-green-100 text-green-600',
                            'link' => 'bg-gray-100 text-gray-600',
                            default => 'bg-gray-100 text-gray-600'
                        } }}">
                            @switch($material->material_type->value)
                                @case('text')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @break
                                @case('video')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    @break
                                @case('audio')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                    @break
                                @case('pdf')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    @break
                                @case('image')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @break
                                @default
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            @endswitch
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $material->title }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ $material->material_type->label() }}
                                @if ($material->duration_seconds)
                                    · {{ $material->getDurationFormatted() }}
                                @endif
                                @if ($material->file_size)
                                    · {{ number_format($material->file_size / 1024 / 1024, 1) }} MB
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="openModal('{{ $material->id }}')" class="p-2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button wire:click="delete('{{ $material->id }}')" wire:confirm="{{ __('Möchtest du dieses Material wirklich löschen?') }}" class="p-2 text-red-400 hover:text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Keine Materialien') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Füge dein erstes Lernmaterial hinzu.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Material Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editingMaterialId ? __('Material bearbeiten') : __('Neues Material') }}</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Typ') }}</label>
                                    <select wire:model.live="materialType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $editingMaterialId ? 'disabled' : '' }}>
                                        <option value="text">{{ __('Text') }}</option>
                                        <option value="video">{{ __('Video') }}</option>
                                        <option value="audio">{{ __('Audio') }}</option>
                                        <option value="pdf">{{ __('PDF') }}</option>
                                        <option value="image">{{ __('Bild') }}</option>
                                        <option value="link">{{ __('Link') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Titel') }}</label>
                                    <input wire:model="title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @if ($materialType === 'text')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Inhalt') }}</label>
                                        <textarea wire:model="content" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                        @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                @elseif ($materialType === 'link')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('URL') }}</label>
                                        <input wire:model="externalUrl" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://">
                                        @error('externalUrl') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                @elseif ($materialType === 'video')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Video-URL (YouTube, Vimeo, Loom)') }}</label>
                                        <input wire:model.live="externalUrl" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://youtube.com/watch?v=...">
                                        @if ($videoSourceType)
                                            <p class="mt-1 text-sm text-green-600">{{ ucfirst($videoSourceType) }} {{ __('erkannt') }}</p>
                                        @endif
                                    </div>
                                    <div class="text-center text-gray-500 text-sm">{{ __('oder') }}</div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Video hochladen') }}</label>
                                        <input wire:model="file" type="file" accept="video/mp4,video/webm,video/quicktime" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <p class="mt-1 text-xs text-gray-500">MP4, WebM, MOV · {{ __('Max.') }} 500MB</p>
                                    </div>
                                @elseif (in_array($materialType, ['audio', 'pdf', 'image']))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Datei hochladen') }}</label>
                                        <input wire:model="file" type="file" accept="{{ match($materialType) {
                                            'audio' => 'audio/mpeg,audio/wav,audio/ogg',
                                            'pdf' => 'application/pdf',
                                            'image' => 'image/*',
                                            default => '*'
                                        } }}" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        @error('file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                <div wire:loading wire:target="file" class="text-sm text-indigo-600">
                                    {{ __('Datei wird hochgeladen...') }}
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700" wire:loading.attr="disabled">{{ __('Speichern') }}</button>
                            <button type="button" wire:click="closeModal" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50">{{ __('Abbrechen') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
