<?php

namespace App\Enums;

enum StepProgressStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'Nicht begonnen',
            self::InProgress => 'In Bearbeitung',
            self::Completed => 'Abgeschlossen',
            self::Skipped => 'Übersprungen',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NotStarted => 'gray',
            self::InProgress => 'yellow',
            self::Completed => 'green',
            self::Skipped => 'orange',
        };
    }

    public function isComplete(): bool
    {
        return in_array($this, [self::Completed, self::Skipped]);
    }
}
