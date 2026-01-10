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
}
