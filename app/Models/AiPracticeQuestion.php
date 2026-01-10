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

    protected $fillable = [
        'session_id',
        'question_type',
        'question_text',
        'options',
        'correct_answer',
        'explanation',
        'user_answer',
        'is_correct',
        'answered_at',
        'difficulty_level',
        'tokens_used',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'question_type' => QuestionType::class,
            'options' => 'array',
            'is_correct' => 'boolean',
            'answered_at' => 'datetime',
            'metadata' => 'array',
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

    // Helpers

    public function isAnswered(): bool
    {
        return $this->answered_at !== null;
    }

    public function answer(string $userAnswer): bool
    {
        $isCorrect = $this->checkAnswer($userAnswer);

        $this->update([
            'user_answer' => $userAnswer,
            'is_correct' => $isCorrect,
            'answered_at' => now(),
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
