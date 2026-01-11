<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ai.enabled', true);
        $this->migrator->add('ai.provider', 'anthropic');
        $this->migrator->add('ai.model_default', null);
        $this->migrator->add('ai.model_tutor', null);
        $this->migrator->add('ai.model_practice', null);
        $this->migrator->add('ai.model_summary', null);
        $this->migrator->add('ai.default_monthly_tokens', 100000);
        $this->migrator->add('ai.default_daily_requests', 100);
        $this->migrator->add('ai.cache_enabled', true);
        $this->migrator->add('ai.cache_ttl_hours', 24);
        $this->migrator->add('ai.feature_explanations', true);
        $this->migrator->add('ai.feature_tutor', true);
        $this->migrator->add('ai.feature_practice', true);
        $this->migrator->add('ai.feature_hints', true);
        $this->migrator->add('ai.feature_summaries', true);
        $this->migrator->add('ai.feature_flashcards', true);
    }
};
