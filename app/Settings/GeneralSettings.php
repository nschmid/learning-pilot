<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public string $site_description;

    public string $default_locale;

    public array $supported_locales;

    public string $timezone;

    public string $date_format;

    public bool $maintenance_mode;

    public ?string $maintenance_message;

    public static function group(): string
    {
        return 'general';
    }
}
