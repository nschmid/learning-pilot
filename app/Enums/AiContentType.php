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
}
