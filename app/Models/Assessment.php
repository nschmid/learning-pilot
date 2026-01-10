<?php

namespace App\Models;

use App\Enums\AssessmentType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'step_id',
        'assessment_type',
        'title',
        'description',
        'instructions',
        'time_limit_minutes',
        'passing_score_percent',
        'max_attempts',
        'shuffle_questions',
        'shuffle_answers',
        'show_correct_answers',
        'show_score_immediately',
    ];

    protected function casts(): array
    {
        return [
            'assessment_type' => AssessmentType::class,
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_correct_answers' => 'boolean',
            'show_score_immediately' => 'boolean',
        ];
    }

    // Relationships

    public function step(): BelongsTo
    {
        return $this->belongsTo(LearningStep::class, 'step_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    // Helpers

    public function totalPoints(): int
    {
        return $this->questions()->sum('points');
    }

    public function questionCount(): int
    {
        return $this->questions()->count();
    }

    public function hasTimeLimit(): bool
    {
        return $this->time_limit_minutes > 0;
    }

    public function hasAttemptLimit(): bool
    {
        return $this->max_attempts !== null;
    }
}
