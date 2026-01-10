<?php

namespace App\Livewire\Instructor\Materials;

use App\Enums\MaterialType;
use App\Enums\VideoSourceType;
use App\Models\LearningMaterial;
use App\Models\LearningStep;
use App\Services\MediaProcessingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class MaterialUploader extends Component
{
    use WithFileUploads;

    public LearningStep $step;

    // Modal state
    public bool $showModal = false;
    public ?string $editingMaterialId = null;

    // Form fields
    public string $materialType = 'text';
    public string $title = '';
    public string $content = '';
    public $file = null;
    public ?string $externalUrl = null;
    public ?string $videoSourceType = null;
    public ?string $videoSourceId = null;
    public ?int $durationSeconds = null;

    protected MediaProcessingService $mediaService;

    public function boot(MediaProcessingService $mediaService): void
    {
        $this->mediaService = $mediaService;
    }

    public function mount(LearningStep $step): void
    {
        $path = $step->module->learningPath;
        if ($path->creator_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->step = $step;
    }

    #[Computed]
    public function materials()
    {
        return $this->step->materials()->ordered()->get();
    }

    public function openModal(?string $materialId = null): void
    {
        if ($materialId) {
            $material = LearningMaterial::find($materialId);
            $this->editingMaterialId = $materialId;
            $this->materialType = $material->material_type->value;
            $this->title = $material->title;
            $this->content = $material->content ?? '';
            $this->externalUrl = $material->external_url;
            $this->videoSourceType = $material->video_source_type?->value;
            $this->videoSourceId = $material->video_source_id;
            $this->durationSeconds = $material->duration_seconds;
        } else {
            $this->resetForm();
        }
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->editingMaterialId = null;
        $this->materialType = 'text';
        $this->title = '';
        $this->content = '';
        $this->file = null;
        $this->externalUrl = null;
        $this->videoSourceType = null;
        $this->videoSourceId = null;
        $this->durationSeconds = null;
    }

    public function updatedMaterialType(): void
    {
        // Reset file when type changes
        $this->file = null;
        $this->externalUrl = null;
        $this->videoSourceType = null;
        $this->videoSourceId = null;
    }

    public function updatedExternalUrl(): void
    {
        // Try to detect video source from URL
        if ($this->materialType === 'video' && $this->externalUrl) {
            $parsed = $this->parseVideoUrl($this->externalUrl);
            if ($parsed) {
                $this->videoSourceType = $parsed['type'];
                $this->videoSourceId = $parsed['id'];
            }
        }
    }

    protected function parseVideoUrl(string $url): ?array
    {
        // YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return ['type' => 'youtube', 'id' => $matches[1]];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return ['type' => 'vimeo', 'id' => $matches[1]];
        }

        // Loom
        if (preg_match('/loom\.com\/share\/([a-zA-Z0-9]+)/', $url, $matches)) {
            return ['type' => 'loom', 'id' => $matches[1]];
        }

        return null;
    }

    public function save(): void
    {
        $type = MaterialType::tryFrom($this->materialType);

        $rules = [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'materialType' => ['required', 'in:text,video,audio,pdf,image,link,interactive'],
        ];

        // Type-specific validation
        if ($type === MaterialType::Text) {
            $rules['content'] = ['required', 'string', 'min:10'];
        } elseif ($type === MaterialType::Link) {
            $rules['externalUrl'] = ['required', 'url'];
        } elseif ($type === MaterialType::Video) {
            if (!$this->editingMaterialId && !$this->file && !$this->externalUrl) {
                $rules['externalUrl'] = ['required_without:file', 'nullable', 'url'];
                $rules['file'] = ['required_without:externalUrl', 'nullable', 'file', 'mimes:mp4,webm,mov', 'max:512000'];
            }
        } elseif ($type === MaterialType::Audio) {
            if (!$this->editingMaterialId) {
                $rules['file'] = ['required', 'file', 'mimes:mp3,wav,ogg', 'max:102400'];
            }
        } elseif ($type === MaterialType::Pdf) {
            if (!$this->editingMaterialId) {
                $rules['file'] = ['required', 'file', 'mimes:pdf', 'max:51200'];
            }
        } elseif ($type === MaterialType::Image) {
            if (!$this->editingMaterialId) {
                $rules['file'] = ['required', 'image', 'max:10240'];
            }
        }

        $this->validate($rules);

        $data = [
            'step_id' => $this->step->id,
            'material_type' => $type,
            'title' => $this->title,
            'content' => $type === MaterialType::Text ? $this->content : null,
            'external_url' => in_array($this->materialType, ['video', 'link']) ? $this->externalUrl : null,
            'video_source_type' => $this->videoSourceType ? VideoSourceType::from($this->videoSourceType) : null,
            'video_source_id' => $this->videoSourceId,
            'duration_seconds' => $this->durationSeconds,
        ];

        // Handle file upload
        if ($this->file) {
            $result = $this->mediaService->processUpload(
                $this->file,
                'materials',
                'public',
                ['type' => $type]
            );

            $data['file_path'] = $result['path'];
            $data['file_name'] = $result['original_name'];
            $data['mime_type'] = $result['mime_type'];
            $data['file_size'] = $result['size'];

            if (isset($result['duration'])) {
                $data['duration_seconds'] = $result['duration'];
            }
        }

        if ($this->editingMaterialId) {
            LearningMaterial::where('id', $this->editingMaterialId)->update($data);
        } else {
            $maxPosition = $this->step->materials()->max('position') ?? 0;
            $data['position'] = $maxPosition + 1;
            LearningMaterial::create($data);
        }

        unset($this->materials);
        $this->closeModal();
        session()->flash('success', __('Material gespeichert.'));
    }

    public function delete(string $materialId): void
    {
        $material = LearningMaterial::find($materialId);
        if ($material && $material->step_id === $this->step->id) {
            // Delete file if exists
            if ($material->file_path) {
                \Storage::disk('public')->delete($material->file_path);
            }
            $material->delete();
            unset($this->materials);
            session()->flash('success', __('Material gelöscht.'));
        }
    }

    public function updateOrder(array $order): void
    {
        foreach ($order as $position => $materialId) {
            LearningMaterial::where('id', $materialId)
                ->where('step_id', $this->step->id)
                ->update(['position' => $position + 1]);
        }
        unset($this->materials);
    }

    public function render()
    {
        return view('livewire.instructor.materials.material-uploader')
            ->layout('layouts.instructor', ['title' => __('Materialien') . ' - ' . $this->step->title]);
    }
}
