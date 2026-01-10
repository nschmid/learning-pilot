<?php

namespace App\Models;

use App\Enums\UnlockCondition;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'learning_path_id',
        'title',
        'description',
        'position',
        'unlock_condition',
        'unlock_value',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'unlock_condition' => UnlockCondition::class,
            'is_required' => 'boolean',
        ];
    }

    // Relationships

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(LearningStep::class)->orderBy('position');
    }

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Module::class,
            'module_dependencies',
            'module_id',
            'required_module_id'
        );
    }

    public function dependentModules(): BelongsToMany
    {
        return $this->belongsToMany(
            Module::class,
            'module_dependencies',
            'required_module_id',
            'module_id'
        );
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

    // Helpers

    public function totalSteps(): int
    {
        return $this->steps()->count();
    }

    public function totalPoints(): int
    {
        return $this->steps()->sum('points_value');
    }

    public function estimatedMinutes(): int
    {
        return $this->steps()->sum('estimated_minutes') ?? 0;
    }

    public function previousModule(): ?Module
    {
        return $this->learningPath->modules()
            ->where('position', '<', $this->position)
            ->orderByDesc('position')
            ->first();
    }

    public function nextModule(): ?Module
    {
        return $this->learningPath->modules()
            ->where('position', '>', $this->position)
            ->orderBy('position')
            ->first();
    }
}
