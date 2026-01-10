<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TaskSubmission extends Model implements HasMedia
{
    use HasFactory;
    use HasUuids;
    use InteractsWithMedia;

    protected $fillable = [
        'task_id',
        'enrollment_id',
        'content',
        'file_paths',
        'status',
        'score',
        'feedback',
        'submitted_at',
        'reviewed_at',
        'reviewer_id',
    ];

    protected function casts(): array
    {
        return [
            'file_paths' => 'array',
            'status' => SubmissionStatus::class,
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('submissions');
    }

    // Relationships

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', SubmissionStatus::Pending);
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', SubmissionStatus::Reviewed);
    }

    // Helpers

    public function isPending(): bool
    {
        return $this->status === SubmissionStatus::Pending;
    }

    public function isReviewed(): bool
    {
        return $this->status === SubmissionStatus::Reviewed;
    }

    public function needsRevision(): bool
    {
        return $this->status === SubmissionStatus::RevisionRequested;
    }

    public function scorePercent(): float
    {
        if (! $this->score) {
            return 0;
        }

        return round(($this->score / $this->task->max_points) * 100, 2);
    }
}
