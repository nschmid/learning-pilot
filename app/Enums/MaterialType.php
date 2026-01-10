<?php

namespace App\Enums;

enum MaterialType: string
{
    case Text = 'text';
    case Video = 'video';
    case Audio = 'audio';
    case Pdf = 'pdf';
    case Image = 'image';
    case Link = 'link';
    case Interactive = 'interactive';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Text',
            self::Video => 'Video',
            self::Audio => 'Audio',
            self::Pdf => 'PDF-Dokument',
            self::Image => 'Bild',
            self::Link => 'Externer Link',
            self::Interactive => 'Interaktiv',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Text => 'document-text',
            self::Video => 'video-camera',
            self::Audio => 'musical-note',
            self::Pdf => 'document',
            self::Image => 'photo',
            self::Link => 'link',
            self::Interactive => 'puzzle-piece',
        };
    }

    public function allowedExtensions(): array
    {
        return match ($this) {
            self::Video => ['mp4', 'webm', 'mov'],
            self::Audio => ['mp3', 'wav', 'ogg'],
            self::Pdf => ['pdf'],
            self::Image => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            default => [],
        };
    }

    public function requiresFile(): bool
    {
        return in_array($this, [self::Video, self::Audio, self::Pdf, self::Image]);
    }
}
