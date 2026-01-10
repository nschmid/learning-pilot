<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Paused = 'paused';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktiv',
            self::Completed => 'Abgeschlossen',
            self::Paused => 'Pausiert',
            self::Expired => 'Abgelaufen',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Completed => 'blue',
            self::Paused => 'yellow',
            self::Expired => 'red',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Active => 'play',
            self::Completed => 'check',
            self::Paused => 'pause',
            self::Expired => 'clock',
        };
    }
}
