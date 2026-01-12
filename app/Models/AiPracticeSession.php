<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiPracticeSession extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'learning_path_id',
        'module_id',
        'step_id',
        'difficulty',
        'question_count',
        'focus_areas',
        'questions_generated',
        'questions_answered',
        'correct_answers',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'focus_areas' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
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

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(LearningStep::class, 'step_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AiPracticeQuestion::class, 'session_id');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAbandoned($query)
    {
        return $query->where('status', 'abandoned');
    }

    public function scopeForPath($query, LearningPath $path)
    {
        return $query->where('learning_path_id', $path->id);
    }

    public function scopeForModule($query, Module $module)
    {
        return $query->where('module_id', $module->id);
    }

    public function scopeForStep($query, LearningStep $step)
    {
        return $query->where('step_id', $step->id);
    }

    // Helpers

    public function scorePercent(): float
    {
        if ($this->questions_answered === 0) {
            return 0;
        }

        return round(($this->correct_answers / $this->questions_answered) * 100, 2);
    }

    public function remainingQuestions(): int
    {
        return max(0, $this->question_count - $this->questions_answered);
    }

    public function progressPercent(): float
    {
        if ($this->question_count === 0) {
            return 0;
        }

        return round(($this->questions_answered / $this->question_count) * 100, 2);
    }

    public function recordAnswer(bool $isCorrect): void
    {
        $this->increment('questions_answered');

        if ($isCorrect) {
            $this->increment('correct_answers');
        }

        if ($this->questions_answered >= $this->question_count) {
            $this->complete();
        }
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function abandon(): void
    {
        $this->update([
            'status' => 'abandoned',
        ]);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function adjustDifficulty(): string
    {
        $score = $this->scorePercent();
        $difficulties = ['beginner', 'intermediate', 'advanced'];
        $currentIndex = array_search($this->difficulty, $difficulties);

        if ($currentIndex === false || $this->difficulty === 'adaptive') {
            return $this->difficulty;
        }

        if ($score >= 80 && $currentIndex < count($difficulties) - 1) {
            return $difficulties[$currentIndex + 1];
        } elseif ($score < 50 && $currentIndex > 0) {
            return $difficulties[$currentIndex - 1];
        }

        return $this->difficulty;
    }
}
