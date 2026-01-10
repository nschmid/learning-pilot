<?php

namespace App\Enums;

enum AssessmentType: string
{
    case Quiz = 'quiz';
    case Exam = 'exam';
    case Survey = 'survey';

    public function label(): string
    {
        return match ($this) {
            self::Quiz => 'Quiz',
            self::Exam => 'Prüfung',
            self::Survey => 'Umfrage',
        };
    }

    public function isGraded(): bool
    {
        return $this !== self::Survey;
    }
}
