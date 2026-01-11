<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', config('app.name', 'LearningPilot'));
        $this->migrator->add('general.site_description', 'Die moderne Lernplattform für Schulen und Bildungseinrichtungen');
        $this->migrator->add('general.default_locale', 'de');
        $this->migrator->add('general.supported_locales', ['de', 'en', 'fr']);
        $this->migrator->add('general.timezone', 'Europe/Zurich');
        $this->migrator->add('general.date_format', 'd.m.Y');
        $this->migrator->add('general.maintenance_mode', false);
        $this->migrator->add('general.maintenance_message', null);
    }
};
