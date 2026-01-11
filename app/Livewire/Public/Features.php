<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('Funktionen - LearningPilot')]
class Features extends Component
{
    public function render()
    {
        return view('livewire.public.features', [
            'categories' => $this->getFeatureCategories(),
        ]);
    }

    protected function getFeatureCategories(): array
    {
        return [
            [
                'title' => __('Lernpfad-Erstellung'),
                'description' => __('Erstellen Sie strukturierte Lernpfade mit modularen Inhalten'),
                'features' => [
                    __('Drag-and-Drop Module und Schritte'),
                    __('Videos, PDFs, Texte und interaktive Inhalte'),
                    __('Voraussetzungen und Freischaltbedingungen'),
                    __('Versionierung und Duplizierung'),
                    __('Kategorien und Tags'),
                ],
            ],
            [
                'title' => __('Assessments & Prüfungen'),
                'description' => __('Erstellen Sie Prüfungen mit automatischer Bewertung'),
                'features' => [
                    __('5 Fragetypen (Multiple Choice, Wahr/Falsch, Text, etc.)'),
                    __('Zeitlimits und Versuchsbegrenzung'),
                    __('Automatische Bewertung'),
                    __('Detaillierte Ergebnisauswertung'),
                    __('Bestanden/Nicht bestanden Logik'),
                ],
            ],
            [
                'title' => __('KI-gestützte Funktionen'),
                'description' => __('Nutzen Sie moderne KI für personalisiertes Lernen'),
                'features' => [
                    __('KI-Tutor für individuelle Unterstützung'),
                    __('Automatische Erklärungen bei falschen Antworten'),
                    __('Generierte Übungsfragen'),
                    __('Lernkarten-Generator'),
                    __('Modul-Zusammenfassungen'),
                ],
            ],
            [
                'title' => __('Fortschrittsverfolgung'),
                'description' => __('Behalten Sie den Überblick über alle Lernenden'),
                'features' => [
                    __('Echtzeit-Fortschrittsanzeige'),
                    __('Zeiterfassung pro Schritt'),
                    __('Punktesystem und Gamification'),
                    __('Leistungsanalysen'),
                    __('Export von Berichten'),
                ],
            ],
            [
                'title' => __('Zertifikate'),
                'description' => __('Stellen Sie professionelle Zertifikate aus'),
                'features' => [
                    __('Automatische Ausstellung bei Abschluss'),
                    __('Anpassbare Vorlagen'),
                    __('QR-Code zur Verifizierung'),
                    __('PDF-Download'),
                    __('Gültigkeitsdauer'),
                ],
            ],
            [
                'title' => __('Team-Verwaltung'),
                'description' => __('Verwalten Sie Ihre Schule oder Organisation'),
                'features' => [
                    __('Rollen und Berechtigungen'),
                    __('Einladungslinks'),
                    __('CSV-Import für Lernende'),
                    __('Nutzungsstatistiken'),
                    __('Speicher-Verwaltung'),
                ],
            ],
        ];
    }
}
