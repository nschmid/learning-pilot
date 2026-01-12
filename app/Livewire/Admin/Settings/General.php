<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Category;
use App\Models\LearningPath;
use App\Models\User;
use App\Settings\GeneralSettings;
use App\Settings\LearningSettings;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class General extends Component
{
    #[Validate('required|string|max:255')]
    public string $site_name = '';

    #[Validate('required|string|max:500')]
    public string $site_description = '';

    #[Validate('required|string|in:de,en,fr')]
    public string $default_locale = 'de';

    #[Validate('required|string')]
    public string $timezone = 'Europe/Zurich';

    #[Validate('required|string')]
    public string $date_format = 'd.m.Y';

    public bool $maintenance_mode = false;

    public ?string $maintenance_message = null;

    // Learning settings
    #[Validate('required|integer|min:1|max:100')]
    public int $default_passing_score = 70;

    #[Validate('required|integer|min:1|max:10')]
    public int $max_assessment_attempts = 3;

    public bool $gamification_enabled = true;

    public bool $track_time_spent = true;

    public function mount(GeneralSettings $generalSettings, LearningSettings $learningSettings): void
    {
        $this->site_name = $generalSettings->site_name;
        $this->site_description = $generalSettings->site_description;
        $this->default_locale = $generalSettings->default_locale;
        $this->timezone = $generalSettings->timezone;
        $this->date_format = $generalSettings->date_format;
        $this->maintenance_mode = $generalSettings->maintenance_mode;
        $this->maintenance_message = $generalSettings->maintenance_message;

        $this->default_passing_score = $learningSettings->default_passing_score;
        $this->max_assessment_attempts = $learningSettings->max_assessment_attempts;
        $this->gamification_enabled = $learningSettings->gamification_enabled;
        $this->track_time_spent = $learningSettings->track_time_spent;
    }

    public function save(GeneralSettings $generalSettings, LearningSettings $learningSettings): void
    {
        $this->validate();

        $generalSettings->site_name = $this->site_name;
        $generalSettings->site_description = $this->site_description;
        $generalSettings->default_locale = $this->default_locale;
        $generalSettings->timezone = $this->timezone;
        $generalSettings->date_format = $this->date_format;
        $generalSettings->maintenance_mode = $this->maintenance_mode;
        $generalSettings->maintenance_message = $this->maintenance_message;
        $generalSettings->save();

        $learningSettings->default_passing_score = $this->default_passing_score;
        $learningSettings->max_assessment_attempts = $this->max_assessment_attempts;
        $learningSettings->gamification_enabled = $this->gamification_enabled;
        $learningSettings->track_time_spent = $this->track_time_spent;
        $learningSettings->save();

        session()->flash('success', __('Einstellungen wurden gespeichert.'));
    }

    #[Computed]
    public function systemStats(): array
    {
        return [
            'users' => User::count(),
            'paths' => LearningPath::count(),
            'categories' => Category::count(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
    }

    #[Computed]
    public function appInfo(): array
    {
        return [
            'name' => config('app.name'),
            'url' => config('app.url'),
            'env' => config('app.env'),
            'debug' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];
    }

    #[Computed]
    public function timezones(): array
    {
        return [
            'Europe/Zurich' => 'Zürich (Europe/Zurich)',
            'Europe/Berlin' => 'Berlin (Europe/Berlin)',
            'Europe/Vienna' => 'Wien (Europe/Vienna)',
            'Europe/Paris' => 'Paris (Europe/Paris)',
            'Europe/London' => 'London (Europe/London)',
            'UTC' => 'UTC',
        ];
    }

    #[Computed]
    public function locales(): array
    {
        return [
            'de' => 'Deutsch',
            'en' => 'English',
            'fr' => 'Français',
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.general')
            ->layout('layouts.admin', ['title' => __('Allgemeine Einstellungen')]);
    }
}
