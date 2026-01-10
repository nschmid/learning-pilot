<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Pending = 'pending';
    case Reviewed = 'reviewed';
    case RevisionRequested = 'revision_requested';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ausstehend',
            self::Reviewed => 'Bewertet',
            self::RevisionRequested => 'Überarbeitung angefordert',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Reviewed => 'green',
            self::RevisionRequested => 'orange',
        };
    }
}
