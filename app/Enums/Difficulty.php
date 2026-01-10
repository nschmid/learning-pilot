<?php

namespace App\Enums;

enum Difficulty: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Expert = 'expert';

    public function label(): string
    {
        return match ($this) {
            self::Beginner => 'Anfänger',
            self::Intermediate => 'Fortgeschritten',
            self::Advanced => 'Weit Fortgeschritten',
            self::Expert => 'Experte',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Beginner => 'green',
            self::Intermediate => 'blue',
            self::Advanced => 'orange',
            self::Expert => 'red',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::Beginner => 1,
            self::Intermediate => 2,
            self::Advanced => 3,
            self::Expert => 4,
        };
    }
}
