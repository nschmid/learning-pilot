<?php

namespace App\Enums;

enum UnlockCondition: string
{
    case Sequential = 'sequential';
    case CompletionPercent = 'completion_percent';
    case Manual = 'manual';
    case Date = 'date';

    public function label(): string
    {
        return match ($this) {
            self::Sequential => 'Nach Abschluss des vorherigen Moduls',
            self::CompletionPercent => 'Bei X% Fortschritt',
            self::Manual => 'Manuell durch Kursleiter',
            self::Date => 'Ab bestimmtem Datum',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Sequential => 'Das Modul wird freigeschaltet, sobald das vorherige Modul abgeschlossen ist',
            self::CompletionPercent => 'Das Modul wird bei einem bestimmten Fortschritt freigeschaltet',
            self::Manual => 'Der Kursleiter schaltet das Modul manuell frei',
            self::Date => 'Das Modul wird ab einem bestimmten Datum freigeschaltet',
        };
    }
}
