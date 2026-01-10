<?php

namespace App\Enums;

enum QuestionType: string
{
    case SingleChoice = 'single_choice';
    case MultipleChoice = 'multiple_choice';
    case TrueFalse = 'true_false';
    case Text = 'text';
    case Matching = 'matching';

    public function label(): string
    {
        return match ($this) {
            self::SingleChoice => 'Einzelauswahl',
            self::MultipleChoice => 'Mehrfachauswahl',
            self::TrueFalse => 'Richtig/Falsch',
            self::Text => 'Freitext',
            self::Matching => 'Zuordnung',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SingleChoice => 'circle',
            self::MultipleChoice => 'squares-2x2',
            self::TrueFalse => 'check-circle',
            self::Text => 'pencil',
            self::Matching => 'arrows-right-left',
        };
    }

    public function isAutoGradable(): bool
    {
        return $this !== self::Text;
    }

    public function hasOptions(): bool
    {
        return in_array($this, [self::SingleChoice, self::MultipleChoice, self::TrueFalse]);
    }
}
