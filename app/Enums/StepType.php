<?php

namespace App\Enums;

enum StepType: string
{
    case Material = 'material';
    case Task = 'task';
    case Assessment = 'assessment';

    public function label(): string
    {
        return match ($this) {
            self::Material => 'Lernmaterial',
            self::Task => 'Aufgabe',
            self::Assessment => 'Prüfung',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Material => 'book-open',
            self::Task => 'clipboard-document-check',
            self::Assessment => 'academic-cap',
        };
    }
}
