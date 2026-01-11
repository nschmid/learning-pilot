<?php

namespace App\Enums;

enum AIPracticeDifficulty: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Adaptive = 'adaptive';

    public function label(): string
    {
        return match ($this) {
            self::Beginner => __('Anfänger'),
            self::Intermediate => __('Fortgeschritten'),
            self::Advanced => __('Experte'),
            self::Adaptive => __('Adaptiv'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Beginner => __('Grundlegende Fragen zum Einstieg'),
            self::Intermediate => __('Fragen mit moderatem Schwierigkeitsgrad'),
            self::Advanced => __('Anspruchsvolle Fragen für Fortgeschrittene'),
            self::Adaptive => __('Passt sich automatisch Ihrem Niveau an'),
        };
    }
}
