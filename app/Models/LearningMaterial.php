<?php

namespace App\Models;

use App\Enums\MaterialType;
use App\Enums\VideoSourceType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LearningMaterial extends Model implements HasMedia
{
    use HasFactory;
    use HasUuids;
    use InteractsWithMedia;

    protected $fillable = [
        'step_id',
        'material_type',
        'title',
        'content',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'duration_seconds',
        'external_url',
        'video_source_type',
        'video_source_id',
        'position',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'material_type' => MaterialType::class,
            'video_source_type' => VideoSourceType::class,
            'metadata' => 'array',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->singleFile();
    }

    // Relationships

    public function step(): BelongsTo
    {
        return $this->belongsTo(LearningStep::class, 'step_id');
    }

    // Scopes

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    public function scopeByType($query, MaterialType $type)
    {
        return $query->where('material_type', $type);
    }

    // Helpers

    public function isVideo(): bool
    {
        return $this->material_type === MaterialType::Video;
    }

    public function isEmbed(): bool
    {
        return $this->video_source_type?->isEmbed() ?? false;
    }

    public function getDurationFormatted(): ?string
    {
        if (! $this->duration_seconds) {
            return null;
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getEmbedUrl(): ?string
    {
        if (! $this->video_source_type || ! $this->video_source_id) {
            return null;
        }

        return match ($this->video_source_type) {
            VideoSourceType::YouTube => "https://www.youtube.com/embed/{$this->video_source_id}",
            VideoSourceType::Loom => "https://www.loom.com/embed/{$this->video_source_id}",
            VideoSourceType::Vimeo => "https://player.vimeo.com/video/{$this->video_source_id}",
            default => null,
        };
    }
}
