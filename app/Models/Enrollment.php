<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'learning_path_id',
        'status',
        'progress_percent',
        'started_at',
        'completed_at',
        'last_activity_at',
        'total_time_spent_seconds',
        'points_earned',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => EnrollmentStatus::class,
            'progress_percent' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function stepProgress(): HasMany
    {
        return $this->hasMany(StepProgress::class);
    }

    public function taskSubmissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function assessmentAttempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', EnrollmentStatus::Active);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', EnrollmentStatus::Completed);
    }

    // Helpers

    public function isActive(): bool
    {
        return $this->status === EnrollmentStatus::Active;
    }

    public function isCompleted(): bool
    {
        return $this->status === EnrollmentStatus::Completed;
    }

    public function recalculateProgress(): void
    {
        $totalSteps = $this->learningPath->steps()->count();
        $completedSteps = $this->stepProgress()
            ->where('status', 'completed')
            ->count();

        $this->update([
            'progress_percent' => $totalSteps > 0
                ? round(($completedSteps / $totalSteps) * 100, 2)
                : 0,
        ]);
    }

    public function getFormattedTimeSpent(): string
    {
        $hours = floor($this->total_time_spent_seconds / 3600);
        $minutes = floor(($this->total_time_spent_seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }
}
