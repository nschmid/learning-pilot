<?php

namespace App\Enums;

enum SchoolType: string
{
    case Primary = 'primary';
    case Secondary = 'secondary';
    case Vocational = 'vocational';
    case University = 'university';
    case Corporate = 'corporate';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Primary => 'Grundschule',
            self::Secondary => 'Sekundarschule',
            self::Vocational => 'Berufsschule',
            self::University => 'Universität/Hochschule',
            self::Corporate => 'Unternehmen',
            self::Other => 'Andere',
        };
    }
}
