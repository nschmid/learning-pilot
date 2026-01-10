<?php

namespace Database\Seeders;

use App\Enums\StepType;
use App\Enums\TaskType;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = LearningPath::where('slug', 'laravel-grundlagen')->first();

        if (! $path) {
            $this->command->warn('Learning path not found. Run LearningPathSeeder first.');

            return;
        }

        // Find the Controllers module
        $module = Module::where('learning_path_id', $path->id)
            ->where('title', 'Controllers')
            ->first();

        if (! $module) {
            $this->command->warn('Controllers module not found.');

            return;
        }

        // Task 1: Code Review Task
        $step1 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Praktische Aufgabe: CRUD Controller',
            'description' => 'Erstellen Sie einen vollständigen CRUD Controller',
            'step_type' => StepType::Task,
            'position' => 5,
            'points_value' => 50,
            'estimated_minutes' => 45,
        ]);

        Task::create([
            'step_id' => $step1->id,
            'task_type' => TaskType::Submission,
            'title' => 'CRUD Controller erstellen',
            'instructions' => '<h3>Aufgabe</h3>
<p>Erstellen Sie einen vollständigen CRUD Controller für ein "Artikel"-System.</p>

<h4>Anforderungen</h4>
<ol>
    <li>Erstellen Sie ein Model "Article" mit Migration (Felder: title, content, published_at, author_id)</li>
    <li>Erstellen Sie einen Resource Controller "ArticleController"</li>
    <li>Implementieren Sie alle CRUD-Methoden:
        <ul>
            <li>index() - Liste aller Artikel</li>
            <li>create() - Formular zum Erstellen</li>
            <li>store() - Artikel speichern</li>
            <li>show() - Einzelnen Artikel anzeigen</li>
            <li>edit() - Formular zum Bearbeiten</li>
            <li>update() - Artikel aktualisieren</li>
            <li>destroy() - Artikel löschen</li>
        </ul>
    </li>
    <li>Verwenden Sie Form Requests für die Validierung</li>
    <li>Implementieren Sie Authorization mit Policies</li>
</ol>

<h4>Bonuspunkte</h4>
<ul>
    <li>Soft Deletes implementieren</li>
    <li>API Resource Controller hinzufügen</li>
    <li>Feature Tests schreiben</li>
</ul>

<h4>Abgabe</h4>
<p>Laden Sie alle relevanten Dateien als ZIP-Archiv hoch oder verlinken Sie Ihr GitHub Repository.</p>',
            'max_points' => 50,
            'due_days' => 7,
            'rubric' => [
                [
                    'criterion' => 'Model & Migration',
                    'max_points' => 10,
                    'description' => 'Korrekte Implementierung des Models mit allen Feldern und Beziehungen',
                ],
                [
                    'criterion' => 'Controller Methoden',
                    'max_points' => 20,
                    'description' => 'Alle CRUD-Methoden sind korrekt implementiert',
                ],
                [
                    'criterion' => 'Validierung',
                    'max_points' => 10,
                    'description' => 'Form Requests werden korrekt verwendet',
                ],
                [
                    'criterion' => 'Authorization',
                    'max_points' => 10,
                    'description' => 'Policies sind implementiert und werden verwendet',
                ],
            ],
        ]);

        // Task 2: Reflection Task
        $step2 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Reflexion: Controller Best Practices',
            'description' => 'Reflektieren Sie über Controller-Architektur',
            'step_type' => StepType::Task,
            'position' => 6,
            'points_value' => 20,
            'estimated_minutes' => 20,
        ]);

        Task::create([
            'step_id' => $step2->id,
            'task_type' => TaskType::Discussion,
            'title' => 'Best Practices Reflexion',
            'instructions' => '<h3>Reflexionsaufgabe</h3>

<p>Beantworten Sie die folgenden Fragen in eigenen Worten (mindestens 200 Wörter gesamt):</p>

<ol>
    <li><strong>Thin Controllers:</strong> Warum ist es wichtig, dass Controller "dünn" bleiben? Was sind die Vorteile?</li>
    <li><strong>Service Layer:</strong> Wann würden Sie einen Service Layer zwischen Controller und Model einfügen?</li>
    <li><strong>Single Responsibility:</strong> Wie wenden Sie das Single Responsibility Principle bei Controllern an?</li>
    <li><strong>Erfahrungen:</strong> Beschreiben Sie eine Situation aus Ihrer Praxis, in der ein "fat Controller" zu Problemen geführt hat oder führen könnte.</li>
</ol>

<h4>Bewertungskriterien</h4>
<ul>
    <li>Vollständigkeit: Alle Fragen beantwortet</li>
    <li>Tiefe: Durchdachte, fundierte Antworten</li>
    <li>Praxisbezug: Eigene Erfahrungen eingebracht</li>
</ul>',
            'max_points' => 20,
            'due_days' => 5,
        ]);

        // Task 3: Peer Review Task
        $step3 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Peer Review: Controller Code',
            'description' => 'Überprüfen Sie den Code eines Mitlernenden',
            'step_type' => StepType::Task,
            'position' => 7,
            'points_value' => 30,
            'estimated_minutes' => 30,
        ]);

        Task::create([
            'step_id' => $step3->id,
            'task_type' => TaskType::Project,
            'title' => 'Code Review durchführen',
            'instructions' => '<h3>Peer Review Aufgabe</h3>

<p>Sie erhalten den Controller-Code eines anderen Teilnehmenden zur Überprüfung.</p>

<h4>Ihre Aufgabe</h4>
<ol>
    <li>Lesen Sie den Code gründlich durch</li>
    <li>Identifizieren Sie:
        <ul>
            <li>Stärken des Codes</li>
            <li>Verbesserungsmöglichkeiten</li>
            <li>Potentielle Bugs oder Sicherheitslücken</li>
            <li>Verstösse gegen Best Practices</li>
        </ul>
    </li>
    <li>Formulieren Sie konstruktives Feedback</li>
    <li>Schlagen Sie konkrete Verbesserungen vor</li>
</ol>

<h4>Richtlinien für gutes Feedback</h4>
<ul>
    <li>Seien Sie spezifisch - zeigen Sie auf Zeilennummern oder Code-Abschnitte</li>
    <li>Bleiben Sie konstruktiv - nicht nur kritisieren, sondern auch loben</li>
    <li>Begründen Sie Ihre Vorschläge mit Best Practices</li>
    <li>Unterscheiden Sie zwischen "muss" und "könnte" verbessert werden</li>
</ul>

<h4>Abgabe</h4>
<p>Schreiben Sie Ihr Review mit mindestens 3 positiven Punkten und 3 Verbesserungsvorschlägen.</p>',
            'max_points' => 30,
            'due_days' => 3,
            'rubric' => [
                [
                    'criterion' => 'Gründlichkeit',
                    'max_points' => 10,
                    'description' => 'Code wurde vollständig analysiert',
                ],
                [
                    'criterion' => 'Qualität des Feedbacks',
                    'max_points' => 10,
                    'description' => 'Feedback ist konstruktiv und spezifisch',
                ],
                [
                    'criterion' => 'Verbesserungsvorschläge',
                    'max_points' => 10,
                    'description' => 'Vorschläge sind umsetzbar und begründet',
                ],
            ],
        ]);

        $this->command->info('Tasks seeded successfully! Created 3 additional tasks.');
    }
}
