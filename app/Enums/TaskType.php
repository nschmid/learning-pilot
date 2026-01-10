<?php

namespace App\Enums;

enum TaskType: string
{
    case Submission = 'submission';
    case Project = 'project';
    case Discussion = 'discussion';

    public function label(): string
    {
        return match ($this) {
            self::Submission => 'Abgabe',
            self::Project => 'Projekt',
            self::Discussion => 'Diskussion',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Submission => 'Einreichung einer Datei oder eines Textes',
            self::Project => 'Umfangreiches Projekt mit mehreren Teilen',
            self::Discussion => 'Teilnahme an einer Diskussion',
        };
    }
}
