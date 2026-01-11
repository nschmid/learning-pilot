<?php

namespace App\Enums;

enum AiServiceType: string
{
    case Explanation = 'explanation';
    case Hint = 'hint';
    case Summary = 'summary';
    case PracticeGen = 'practice_gen';
    case Feedback = 'feedback';
    case TutorChat = 'tutor_chat';
    case Recommendation = 'recommendation';

    public function label(): string
    {
        return match ($this) {
            self::Explanation => 'Erklärung',
            self::Hint => 'Hinweis',
            self::Summary => 'Zusammenfassung',
            self::PracticeGen => 'Übungsgenerierung',
            self::Feedback => 'Feedback',
            self::TutorChat => 'Tutor-Chat',
            self::Recommendation => 'Empfehlung',
        };
    }

    /**
     * Get the default AI model for this service type.
     */
    public function defaultModel(): string
    {
        return match ($this) {
            self::Explanation => config('lernpfad.ai.models.default', 'claude-haiku-4-5-20251001'),
            self::Hint => config('lernpfad.ai.models.default', 'claude-haiku-4-5-20251001'),
            self::Summary => config('lernpfad.ai.models.default', 'claude-haiku-4-5-20251001'),
            self::PracticeGen => config('lernpfad.ai.models.practice', 'claude-sonnet-4-5-20250929'),
            self::Feedback => config('lernpfad.ai.models.default', 'claude-haiku-4-5-20251001'),
            self::TutorChat => config('lernpfad.ai.models.tutor', 'claude-sonnet-4-5-20250929'),
            self::Recommendation => config('lernpfad.ai.models.default', 'claude-haiku-4-5-20251001'),
        };
    }

    /**
     * Get the maximum tokens for this service type.
     */
    public function maxTokens(): int
    {
        return match ($this) {
            self::Explanation => 1000,
            self::Hint => 500,
            self::Summary => 2000,
            self::PracticeGen => 3000,
            self::Feedback => 800,
            self::TutorChat => 1500,
            self::Recommendation => 500,
        };
    }

    /**
     * Get the rate limit configuration [requests per minute, requests per hour].
     */
    public function rateLimit(): array
    {
        return match ($this) {
            self::Explanation => [20, 100],
            self::Hint => [30, 150],
            self::Summary => [10, 50],
            self::PracticeGen => [10, 60],
            self::Feedback => [20, 100],
            self::TutorChat => [30, 200],
            self::Recommendation => [20, 100],
        };
    }
}
