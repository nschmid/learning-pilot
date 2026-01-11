<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('LearningPilot - Die moderne Lernplattform')]
class Landing extends Component
{
    public function render()
    {
        return view('livewire.public.landing', [
            'features' => $this->getFeatures(),
            'stats' => $this->getStats(),
        ]);
    }

    protected function getFeatures(): array
    {
        return [
            [
                'title' => __('Lernpfade erstellen'),
                'description' => __('Strukturieren Sie Ihre Lerninhalte in modulare Lernpfade mit Videos, Texten und interaktiven Elementen.'),
                'icon' => 'path',
            ],
            [
                'title' => __('KI-gestützte Unterstützung'),
                'description' => __('Nutzen Sie KI-Tutoren, automatische Erklärungen und personalisierte Übungsfragen für jeden Lernenden.'),
                'icon' => 'ai',
            ],
            [
                'title' => __('Assessments & Zertifikate'),
                'description' => __('Erstellen Sie Prüfungen mit automatischer Bewertung und stellen Sie Zertifikate bei erfolgreichem Abschluss aus.'),
                'icon' => 'certificate',
            ],
            [
                'title' => __('Fortschrittsverfolgung'),
                'description' => __('Behalten Sie den Überblick über den Lernfortschritt aller Teilnehmer mit detaillierten Statistiken.'),
                'icon' => 'progress',
            ],
            [
                'title' => __('Aufgabenverwaltung'),
                'description' => __('Vergeben Sie Aufgaben, sammeln Sie Einreichungen und geben Sie individuelles Feedback.'),
                'icon' => 'task',
            ],
            [
                'title' => __('Team-Verwaltung'),
                'description' => __('Verwalten Sie Ihre Schule oder Organisation mit Rollen, Einladungen und Nutzungsstatistiken.'),
                'icon' => 'team',
            ],
        ];
    }

    protected function getStats(): array
    {
        return [
            ['value' => '500+', 'label' => __('Aktive Schulen')],
            ['value' => '50\'000+', 'label' => __('Lernende')],
            ['value' => '10\'000+', 'label' => __('Lernpfade')],
            ['value' => '99.9%', 'label' => __('Verfügbarkeit')],
        ];
    }
}
