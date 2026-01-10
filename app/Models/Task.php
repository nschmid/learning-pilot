<?php

namespace App\Models;

use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'step_id',
        'task_type',
        'title',
        'instructions',
        'max_points',
        'due_days',
        'allow_late',
        'allow_resubmit',
        'rubric',
        'allowed_file_types',
        'max_file_size_mb',
    ];

    protected function casts(): array
    {
        return [
            'task_type' => TaskType::class,
            'allow_late' => 'boolean',
            'allow_resubmit' => 'boolean',
            'rubric' => 'array',
            'allowed_file_types' => 'array',
        ];
    }

    // Relationships

    public function step(): BelongsTo
    {
        return $this->belongsTo(LearningStep::class, 'step_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class);
    }
}
