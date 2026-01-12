<?php

namespace App\Models;

use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiPracticeQuestion extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Disable default timestamps - migration only has created_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'question_type',
        'question_text',
        'options',
        'correct_answer',
        'explanation',
        'difficulty',
        'topics',
        'source_material_ids',
        'user_answer',
        'is_correct',
        'answered_at',
        'time_spent_seconds',
        'ai_feedback',
        'position',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'question_type' => QuestionType::class,
            'options' => 'array',
            'topics' => 'array',
            'source_material_ids' => 'array',
            'is_correct' => 'boolean',
            'answered_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // Relationships

    public function session(): BelongsTo
    {
        return $this->belongsTo(AiPracticeSession::class, 'session_id');
    }

    // Scopes

    public function scopeAnswered($query)
    {
        return $query->whereNotNull('answered_at');
    }

    public function scopeUnanswered($query)
    {
        return $query->whereNull('answered_at');
    }

    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    // Helpers

    public function isAnswered(): bool
    {
        return $this->answered_at !== null;
    }

    public function answer(string $userAnswer, ?int $timeSpentSeconds = null): bool
    {
        $isCorrect = $this->checkAnswer($userAnswer);

        $this->update([
            'user_answer' => $userAnswer,
            'is_correct' => $isCorrect,
            'answered_at' => now(),
            'time_spent_seconds' => $timeSpentSeconds,
        ]);

        $this->session->recordAnswer($isCorrect);

        return $isCorrect;
    }

    protected function checkAnswer(string $userAnswer): bool
    {
        return match ($this->question_type) {
            QuestionType::TrueFalse => strtolower($userAnswer) === strtolower($this->correct_answer),
            QuestionType::SingleChoice => $userAnswer === $this->correct_answer,
            QuestionType::MultipleChoice => $this->checkMultipleChoice($userAnswer),
            QuestionType::Text => $this->checkTextAnswer($userAnswer),
            default => $userAnswer === $this->correct_answer,
        };
    }

    protected function checkMultipleChoice(string $userAnswer): bool
    {
        $userAnswers = array_map('trim', explode(',', $userAnswer));
        $correctAnswers = array_map('trim', explode(',', $this->correct_answer));

        sort($userAnswers);
        sort($correctAnswers);

        return $userAnswers === $correctAnswers;
    }

    protected function checkTextAnswer(string $userAnswer): bool
    {
        // Simple case-insensitive comparison for text answers
        // AI can provide more nuanced feedback through explanation
        return strtolower(trim($userAnswer)) === strtolower(trim($this->correct_answer));
    }
}
