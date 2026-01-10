<?php

namespace App\Livewire\Shared;

use App\Services\MediaProcessingService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileUploader extends Component
{
    use WithFileUploads;

    public array $files = [];

    public array $uploadedFiles = [];

    public bool $multiple = false;

    public ?string $accept = null;

    public int $maxFiles = 5;

    public int $maxFileSize = 10240; // KB

    public string $collection = 'uploads';

    public bool $showPreview = true;

    public array $errors = [];

    protected MediaProcessingService $mediaService;

    public function boot(MediaProcessingService $mediaService): void
    {
        $this->mediaService = $mediaService;
    }

    public function mount(
        bool $multiple = false,
        ?string $accept = null,
        int $maxFiles = 5,
        int $maxFileSize = 10240,
        string $collection = 'uploads',
        bool $showPreview = true,
        array $existingFiles = []
    ): void {
        $this->multiple = $multiple;
        $this->accept = $accept;
        $this->maxFiles = $maxFiles;
        $this->maxFileSize = $maxFileSize;
        $this->collection = $collection;
        $this->showPreview = $showPreview;
        $this->uploadedFiles = $existingFiles;
    }

    public function updatedFiles(): void
    {
        $this->errors = [];

        foreach ($this->files as $index => $file) {
            // Validate file count
            if (count($this->uploadedFiles) >= $this->maxFiles) {
                $this->errors[] = __('Maximale Anzahl von :max Dateien erreicht.', ['max' => $this->maxFiles]);
                break;
            }

            // Validate file size
            if ($file->getSize() > $this->maxFileSize * 1024) {
                $this->errors[] = __(':file ist zu groß. Maximum: :size MB', [
                    'file' => $file->getClientOriginalName(),
                    'size' => round($this->maxFileSize / 1024, 1),
                ]);

                continue;
            }

            // Add to uploaded files
            $this->uploadedFiles[] = [
                'id' => uniqid(),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
                'path' => $file->getRealPath(),
                'preview' => $this->isImage($file) ? $file->temporaryUrl() : null,
                'temp' => true,
                'file' => $file,
            ];
        }

        $this->files = [];
        $this->dispatch('files-updated', files: $this->getFileData());
    }

    public function removeFile(string $fileId): void
    {
        $this->uploadedFiles = array_filter($this->uploadedFiles, fn ($f) => $f['id'] !== $fileId);
        $this->uploadedFiles = array_values($this->uploadedFiles);
        $this->dispatch('files-updated', files: $this->getFileData());
    }

    public function getFileData(): array
    {
        return collect($this->uploadedFiles)->map(fn ($f) => [
            'id' => $f['id'],
            'name' => $f['name'],
            'size' => $f['size'],
            'type' => $f['type'],
            'temp' => $f['temp'] ?? false,
        ])->toArray();
    }

    public function getFiles(): Collection
    {
        return collect($this->uploadedFiles)
            ->filter(fn ($f) => isset($f['file']))
            ->map(fn ($f) => $f['file']);
    }

    protected function isImage($file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    public function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1).' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    public function render()
    {
        return view('livewire.shared.file-uploader');
    }
}
