<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiPracticeSession extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'sourceable_type',
        'sourceable_id',
        'difficulty_level',
        'question_count',
        'questions_answered',
        'correct_answers',
        'total_tokens_used',
        'is_completed',
        'started_at',
        'completed_at',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AiPracticeQuestion::class, 'session_id');
    }

    // Scopes

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeInProgress($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeForSource($query, Model $source)
    {
        return $query->where('sourceable_type', get_class($source))
            ->where('sourceable_id', $source->getKey());
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
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    public function adjustDifficulty(): void
    {
        $score = $this->scorePercent();

        if ($score >= 80 && $this->difficulty_level < 5) {
            $this->increment('difficulty_level');
        } elseif ($score < 50 && $this->difficulty_level > 1) {
            $this->decrement('difficulty_level');
        }
    }
}
