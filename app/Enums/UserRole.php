<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Instructor = 'instructor';
    case Learner = 'learner';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Instructor => 'Kursleiter',
            self::Learner => 'Lernender',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Admin => 'red',
            self::Instructor => 'blue',
            self::Learner => 'green',
        };
    }

    public function canAccessAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function canAccessInstructor(): bool
    {
        return in_array($this, [self::Admin, self::Instructor]);
    }
}
