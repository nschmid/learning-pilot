<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AISettings extends Settings
{
    public bool $enabled;

    public string $provider;

    public ?string $model_default;

    public ?string $model_tutor;

    public ?string $model_practice;

    public ?string $model_summary;

    public int $default_monthly_tokens;

    public int $default_daily_requests;

    public bool $cache_enabled;

    public int $cache_ttl_hours;

    public bool $feature_explanations;

    public bool $feature_tutor;

    public bool $feature_practice;

    public bool $feature_hints;

    public bool $feature_summaries;

    public bool $feature_flashcards;

    public static function group(): string
    {
        return 'ai';
    }
}
