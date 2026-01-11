<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LearningSettings extends Settings
{
    public int $default_passing_score;

    public int $max_assessment_attempts;

    public int $certificate_validity_years;

    public bool $certificates_enabled;

    public bool $require_all_assessments_passed;

    public bool $gamification_enabled;

    public int $points_step_completion;

    public int $points_assessment_pass;

    public int $points_path_completion;

    public bool $track_time_spent;

    public bool $allow_skip_steps;

    public static function group(): string
    {
        return 'learning';
    }
}
