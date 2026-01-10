<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentAttempt extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'assessment_id',
        'enrollment_id',
        'attempt_number',
        'started_at',
        'completed_at',
        'score_percent',
        'points_earned',
        'passed',
        'time_spent_seconds',
        'answers',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'score_percent' => 'decimal:2',
            'passed' => 'boolean',
            'answers' => 'array',
        ];
    }

    // Relationships

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QuestionResponse::class, 'attempt_id');
    }

    // Scopes

    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    // Helpers

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isInProgress(): bool
    {
        return $this->started_at !== null && $this->completed_at === null;
    }

    public function hasPassed(): bool
    {
        return $this->passed;
    }

    public function getFormattedTimeSpent(): string
    {
        if (! $this->time_spent_seconds) {
            return '0:00';
        }

        $minutes = floor($this->time_spent_seconds / 60);
        $seconds = $this->time_spent_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function timeRemaining(): ?int
    {
        if (! $this->assessment->time_limit_minutes || ! $this->isInProgress()) {
            return null;
        }

        $elapsedSeconds = now()->diffInSeconds($this->started_at);
        $limitSeconds = $this->assessment->time_limit_minutes * 60;

        return max(0, $limitSeconds - $elapsedSeconds);
    }
}
