<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('learning.default_passing_score', 70);
        $this->migrator->add('learning.max_assessment_attempts', 3);
        $this->migrator->add('learning.certificate_validity_years', 2);
        $this->migrator->add('learning.certificates_enabled', true);
        $this->migrator->add('learning.require_all_assessments_passed', true);
        $this->migrator->add('learning.gamification_enabled', true);
        $this->migrator->add('learning.points_step_completion', 10);
        $this->migrator->add('learning.points_assessment_pass', 50);
        $this->migrator->add('learning.points_path_completion', 200);
        $this->migrator->add('learning.track_time_spent', true);
        $this->migrator->add('learning.allow_skip_steps', false);
    }
};
