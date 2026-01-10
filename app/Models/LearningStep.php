<?php

namespace App\Models;

use App\Enums\StepType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningStep extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'module_id',
        'title',
        'description',
        'step_type',
        'position',
        'points_value',
        'estimated_minutes',
        'is_required',
        'is_preview',
    ];

    protected function casts(): array
    {
        return [
            'step_type' => StepType::class,
            'is_required' => 'boolean',
            'is_preview' => 'boolean',
        ];
    }

    // Relationships

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function learningPath(): BelongsTo
    {
        return $this->module->learningPath();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LearningMaterial::class, 'step_id')->orderBy('position');
    }

    public function task(): HasOne
    {
        return $this->hasOne(Task::class, 'step_id');
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class, 'step_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(StepProgress::class, 'step_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(UserNote::class, 'step_id');
    }

    // Scopes

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopePreviewable($query)
    {
        return $query->where('is_preview', true);
    }

    public function scopeByType($query, StepType $type)
    {
        return $query->where('step_type', $type);
    }

    // Helpers

    public function isMaterial(): bool
    {
        return $this->step_type === StepType::Material;
    }

    public function isTask(): bool
    {
        return $this->step_type === StepType::Task;
    }

    public function isAssessment(): bool
    {
        return $this->step_type === StepType::Assessment;
    }

    public function previousStep(): ?LearningStep
    {
        return $this->module->steps()
            ->where('position', '<', $this->position)
            ->orderByDesc('position')
            ->first();
    }

    public function nextStep(): ?LearningStep
    {
        return $this->module->steps()
            ->where('position', '>', $this->position)
            ->orderBy('position')
            ->first();
    }
}
