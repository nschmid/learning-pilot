<?php

namespace App\Enums;

enum AiFeedbackType: string
{
    case Inaccurate = 'inaccurate';
    case Unhelpful = 'unhelpful';
    case TooComplex = 'too_complex';
    case TooSimple = 'too_simple';
    case OffTopic = 'off_topic';
    case Inappropriate = 'inappropriate';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Inaccurate => 'Ungenau',
            self::Unhelpful => 'Nicht hilfreich',
            self::TooComplex => 'Zu komplex',
            self::TooSimple => 'Zu einfach',
            self::OffTopic => 'Themenfremd',
            self::Inappropriate => 'Unangemessen',
            self::Other => 'Andere',
        };
    }
}
