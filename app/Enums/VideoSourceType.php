<?php

namespace App\Enums;

enum VideoSourceType: string
{
    case Upload = 'upload';
    case YouTube = 'youtube';
    case Loom = 'loom';
    case Vimeo = 'vimeo';

    public function label(): string
    {
        return match ($this) {
            self::Upload => 'Hochgeladen',
            self::YouTube => 'YouTube',
            self::Loom => 'Loom',
            self::Vimeo => 'Vimeo',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Upload => 'cloud-arrow-up',
            self::YouTube => 'play-circle',
            self::Loom => 'video-camera',
            self::Vimeo => 'play',
        };
    }

    public function isEmbed(): bool
    {
        return in_array($this, [self::YouTube, self::Loom, self::Vimeo]);
    }

    public function requiresStorage(): bool
    {
        return $this === self::Upload;
    }
}
