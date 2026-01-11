<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Category;
use App\Models\LearningPath;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class General extends Component
{
    #[Computed]
    public function appInfo(): array
    {
        return [
            'name' => config('app.name', 'LearningPilot'),
            'url' => config('app.url'),
            'env' => config('app.env'),
            'debug' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];
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
    public function lernpfadConfig(): array
    {
        return [
            'passing_score' => config('lernpfad.defaults.passing_score', 70),
            'max_attempts' => config('lernpfad.defaults.max_assessment_attempts', 3),
            'certificate_validity' => config('lernpfad.defaults.certificate_validity_years', 2),
            'max_file_size' => config('lernpfad.materials.max_file_size', 100 * 1024 * 1024) / (1024 * 1024),
            'gamification_enabled' => config('lernpfad.gamification.enabled', true),
        ];
    }

    #[Computed]
    public function mailConfig(): array
    {
        return [
            'mailer' => config('mail.default'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.general')
            ->layout('layouts.admin', ['title' => __('Allgemeine Einstellungen')]);
    }
}
