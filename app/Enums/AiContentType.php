<?php

namespace App\Enums;

enum AiContentType: string
{
    case Explanation = 'explanation';
    case Hint = 'hint';
    case Summary = 'summary';
    case PracticeQuestion = 'practice_question';
    case Feedback = 'feedback';
    case Recommendation = 'recommendation';
    case Flashcard = 'flashcard';
    case ConceptBreakdown = 'concept_breakdown';

    public function label(): string
    {
        return match ($this) {
            self::Explanation => 'Erklärung',
            self::Hint => 'Hinweis',
            self::Summary => 'Zusammenfassung',
            self::PracticeQuestion => 'Übungsfrage',
            self::Feedback => 'Feedback',
            self::Recommendation => 'Empfehlung',
            self::Flashcard => 'Lernkarte',
            self::ConceptBreakdown => 'Konzeptaufschlüsselung',
        };
    }

    /**
     * Get the icon identifier for this content type.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Explanation => 'academic-cap',
            self::Hint => 'light-bulb',
            self::Summary => 'document-text',
            self::PracticeQuestion => 'question-mark-circle',
            self::Feedback => 'chat-bubble-left-right',
            self::Recommendation => 'sparkles',
            self::Flashcard => 'rectangle-stack',
            self::ConceptBreakdown => 'puzzle-piece',
        };
    }

    /**
     * Get the cache duration in minutes for this content type.
     */
    public function cacheDuration(): int
    {
        return match ($this) {
            self::Explanation => 60 * 24 * 7,    // 7 days - stable content
            self::Hint => 60 * 24 * 7,           // 7 days - stable content
            self::Summary => 60 * 24 * 30,       // 30 days - rarely changes
            self::PracticeQuestion => 60 * 24,   // 1 day - should vary
            self::Feedback => 60 * 24 * 7,       // 7 days - stable
            self::Recommendation => 60,          // 1 hour - personalized
            self::Flashcard => 60 * 24 * 30,     // 30 days - stable
            self::ConceptBreakdown => 60 * 24 * 30, // 30 days - stable
        };
    }
}
