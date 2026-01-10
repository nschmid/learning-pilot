<?php

namespace App\Models;

use App\Enums\StepProgressStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StepProgress extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'step_progress';

    protected $fillable = [
        'enrollment_id',
        'step_id',
        'status',
        'started_at',
        'completed_at',
        'time_spent_seconds',
        'points_earned',
        'attempts',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'status' => StepProgressStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'data' => 'array',
        ];
    }

    // Relationships

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(LearningStep::class, 'step_id');
    }

    // Scopes

    public function scopeCompleted($query)
    {
        return $query->where('status', StepProgressStatus::Completed);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', StepProgressStatus::InProgress);
    }

    // Helpers

    public function isCompleted(): bool
    {
        return $this->status === StepProgressStatus::Completed;
    }

    public function isInProgress(): bool
    {
        return $this->status === StepProgressStatus::InProgress;
    }

    public function getFormattedTimeSpent(): string
    {
        $minutes = floor($this->time_spent_seconds / 60);
        $seconds = $this->time_spent_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
