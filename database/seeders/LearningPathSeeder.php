<?php

namespace Database\Seeders;

use App\Enums\AssessmentType;
use App\Enums\Difficulty;
use App\Enums\MaterialType;
use App\Enums\QuestionType;
use App\Enums\StepType;
use App\Enums\TaskType;
use App\Enums\UnlockCondition;
use App\Models\AnswerOption;
use App\Models\Assessment;
use App\Models\Category;
use App\Models\LearningMaterial;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\Question;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class LearningPathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructor = User::where('email', 'instructor@learningpilot.ch')->first();
        $team = Team::where('slug', 'berufsschule-zuerich')->first();
        $webCategory = Category::where('slug', 'webentwicklung')->first();

        // Create tags
        $tags = collect(['Laravel', 'PHP', 'Webentwicklung', 'Backend', 'Anfänger'])
            ->map(fn ($name) => Tag::firstOrCreate(
                ['slug' => \Str::slug($name)],
                ['name' => $name]
            ));

        // Create Learning Path: Laravel Grundlagen
        $path = LearningPath::create([
            'creator_id' => $instructor->id,
            'team_id' => $team->id,
            'category_id' => $webCategory->id,
            'title' => 'Laravel Grundlagen',
            'slug' => 'laravel-grundlagen',
            'description' => 'Lernen Sie die Grundlagen des Laravel PHP Frameworks. Von der Installation bis zur ersten Webanwendung.',
            'difficulty' => Difficulty::Beginner,
            'is_published' => true,
            'published_at' => now(),
            'estimated_hours' => 12,
            'version' => 1,
        ]);

        $path->tags()->attach($tags->pluck('id'));

        // Module 1: Einführung
        $module1 = Module::create([
            'learning_path_id' => $path->id,
            'title' => 'Einführung in Laravel',
            'description' => 'Lernen Sie, was Laravel ist und wie Sie es installieren.',
            'position' => 1,
            'unlock_condition' => UnlockCondition::Manual,
        ]);

        // Step 1.1: Was ist Laravel?
        $step1_1 = LearningStep::create([
            'module_id' => $module1->id,
            'title' => 'Was ist Laravel?',
            'description' => 'Eine Einführung in das Laravel Framework',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 10,
            'estimated_minutes' => 15,
        ]);

        LearningMaterial::create([
            'step_id' => $step1_1->id,
            'material_type' => MaterialType::Text,
            'title' => 'Laravel Einführung',
            'content' => '<h2>Was ist Laravel?</h2>
<p>Laravel ist ein leistungsstarkes PHP-Framework, das die Webentwicklung vereinfacht und beschleunigt. Es wurde 2011 von Taylor Otwell entwickelt und hat sich seitdem zu einem der beliebtesten PHP-Frameworks entwickelt.</p>

<h3>Warum Laravel?</h3>
<ul>
<li><strong>Elegante Syntax</strong> - Laravel bietet eine saubere und ausdrucksstarke Syntax</li>
<li><strong>MVC-Architektur</strong> - Trennung von Logik, Daten und Präsentation</li>
<li><strong>Eloquent ORM</strong> - Einfache Datenbankabfragen</li>
<li><strong>Blade Templates</strong> - Leistungsstarke Template-Engine</li>
<li><strong>Artisan CLI</strong> - Kommandozeilen-Tool für häufige Aufgaben</li>
</ul>

<h3>Voraussetzungen</h3>
<p>Um mit Laravel zu arbeiten, benötigen Sie:</p>
<ul>
<li>PHP 8.1 oder höher</li>
<li>Composer (PHP-Paketmanager)</li>
<li>Node.js und NPM (für Frontend-Assets)</li>
<li>Eine Datenbank (MySQL, PostgreSQL, SQLite)</li>
</ul>',
            'position' => 1,
        ]);

        // Step 1.2: Installation
        $step1_2 = LearningStep::create([
            'module_id' => $module1->id,
            'title' => 'Installation',
            'description' => 'Laravel installieren und einrichten',
            'step_type' => StepType::Material,
            'position' => 2,
            'points_value' => 10,
            'estimated_minutes' => 20,
        ]);

        LearningMaterial::create([
            'step_id' => $step1_2->id,
            'material_type' => MaterialType::Text,
            'title' => 'Laravel installieren',
            'content' => '<h2>Laravel Installation</h2>
<p>Die Installation von Laravel ist dank Composer sehr einfach.</p>

<h3>Schritt 1: Composer installieren</h3>
<p>Falls Sie Composer noch nicht installiert haben, laden Sie es von <a href="https://getcomposer.org">getcomposer.org</a> herunter.</p>

<h3>Schritt 2: Laravel Projekt erstellen</h3>
<pre><code>composer create-project laravel/laravel mein-projekt</code></pre>

<h3>Schritt 3: Entwicklungsserver starten</h3>
<pre><code>cd mein-projekt
php artisan serve</code></pre>

<p>Öffnen Sie nun <code>http://localhost:8000</code> in Ihrem Browser.</p>',
            'position' => 1,
        ]);

        // Step 1.3: Quiz - Einführung
        $step1_3 = LearningStep::create([
            'module_id' => $module1->id,
            'title' => 'Quiz: Einführung',
            'description' => 'Testen Sie Ihr Wissen über die Laravel Grundlagen',
            'step_type' => StepType::Assessment,
            'position' => 3,
            'points_value' => 50,
            'estimated_minutes' => 10,
        ]);

        $assessment1 = Assessment::create([
            'step_id' => $step1_3->id,
            'assessment_type' => AssessmentType::Quiz,
            'title' => 'Einführung Quiz',
            'description' => 'Überprüfen Sie Ihr Verständnis der Laravel Grundlagen',
            'time_limit_minutes' => 10,
            'passing_score_percent' => 70,
            'max_attempts' => 3,
            'shuffle_questions' => true,
        ]);

        // Question 1
        $q1 = Question::create([
            'assessment_id' => $assessment1->id,
            'question_type' => QuestionType::SingleChoice,
            'question_text' => 'Wer hat Laravel entwickelt?',
            'explanation' => 'Taylor Otwell ist der Erfinder und Lead-Entwickler von Laravel.',
            'points' => 10,
            'position' => 1,
        ]);

        AnswerOption::create(['question_id' => $q1->id, 'option_text' => 'Taylor Otwell', 'is_correct' => true, 'position' => 1]);
        AnswerOption::create(['question_id' => $q1->id, 'option_text' => 'Rasmus Lerdorf', 'is_correct' => false, 'position' => 2]);
        AnswerOption::create(['question_id' => $q1->id, 'option_text' => 'Jeffrey Way', 'is_correct' => false, 'position' => 3]);
        AnswerOption::create(['question_id' => $q1->id, 'option_text' => 'Adam Wathan', 'is_correct' => false, 'position' => 4]);

        // Question 2
        $q2 = Question::create([
            'assessment_id' => $assessment1->id,
            'question_type' => QuestionType::TrueFalse,
            'question_text' => 'Laravel verwendet die MVC-Architektur.',
            'explanation' => 'Richtig! Laravel basiert auf dem Model-View-Controller Architekturpattern.',
            'points' => 10,
            'position' => 2,
        ]);

        AnswerOption::create(['question_id' => $q2->id, 'option_text' => 'Wahr', 'is_correct' => true, 'position' => 1]);
        AnswerOption::create(['question_id' => $q2->id, 'option_text' => 'Falsch', 'is_correct' => false, 'position' => 2]);

        // Question 3
        $q3 = Question::create([
            'assessment_id' => $assessment1->id,
            'question_type' => QuestionType::MultipleChoice,
            'question_text' => 'Welche PHP-Versionen werden von Laravel 12 unterstützt? (Mehrfachauswahl möglich)',
            'explanation' => 'Laravel 12 benötigt PHP 8.2 oder höher.',
            'points' => 15,
            'position' => 3,
        ]);

        AnswerOption::create(['question_id' => $q3->id, 'option_text' => 'PHP 7.4', 'is_correct' => false, 'position' => 1]);
        AnswerOption::create(['question_id' => $q3->id, 'option_text' => 'PHP 8.0', 'is_correct' => false, 'position' => 2]);
        AnswerOption::create(['question_id' => $q3->id, 'option_text' => 'PHP 8.2', 'is_correct' => true, 'position' => 3]);
        AnswerOption::create(['question_id' => $q3->id, 'option_text' => 'PHP 8.3', 'is_correct' => true, 'position' => 4]);

        // Module 2: Routing
        $module2 = Module::create([
            'learning_path_id' => $path->id,
            'title' => 'Routing in Laravel',
            'description' => 'Lernen Sie, wie Routing in Laravel funktioniert.',
            'position' => 2,
            'unlock_condition' => UnlockCondition::Sequential,
        ]);

        $step2_1 = LearningStep::create([
            'module_id' => $module2->id,
            'title' => 'Grundlagen des Routings',
            'description' => 'HTTP-Methoden und Route-Definitionen',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 10,
            'estimated_minutes' => 20,
        ]);

        LearningMaterial::create([
            'step_id' => $step2_1->id,
            'material_type' => MaterialType::Text,
            'title' => 'Routing Grundlagen',
            'content' => '<h2>Routing in Laravel</h2>
<p>Routes definieren, wie Ihre Anwendung auf HTTP-Anfragen reagiert.</p>

<h3>Einfache Routes</h3>
<pre><code>// routes/web.php
Route::get(\'/\', function () {
    return view(\'welcome\');
});

Route::get(\'/about\', function () {
    return \'Über uns\';
});</code></pre>

<h3>Route mit Parametern</h3>
<pre><code>Route::get(\'/user/{id}\', function ($id) {
    return "Benutzer: " . $id;
});</code></pre>

<h3>HTTP-Methoden</h3>
<pre><code>Route::get($uri, $callback);
Route::post($uri, $callback);
Route::put($uri, $callback);
Route::patch($uri, $callback);
Route::delete($uri, $callback);</code></pre>',
            'position' => 1,
        ]);

        // Step 2.2: Praktische Aufgabe
        $step2_2 = LearningStep::create([
            'module_id' => $module2->id,
            'title' => 'Aufgabe: Routes erstellen',
            'description' => 'Erstellen Sie eigene Routes',
            'step_type' => StepType::Task,
            'position' => 2,
            'points_value' => 30,
            'estimated_minutes' => 30,
        ]);

        Task::create([
            'step_id' => $step2_2->id,
            'task_type' => TaskType::Submission,
            'title' => 'Routes erstellen',
            'instructions' => '<h3>Aufgabe</h3>
<p>Erstellen Sie folgende Routes in Ihrer Laravel-Anwendung:</p>
<ol>
<li>Eine GET-Route für <code>/kontakt</code> die eine Kontaktseite anzeigt</li>
<li>Eine POST-Route für <code>/kontakt</code> die das Kontaktformular verarbeitet</li>
<li>Eine GET-Route für <code>/produkte/{kategorie}</code> die Produkte einer Kategorie anzeigt</li>
</ol>

<h3>Abgabe</h3>
<p>Fügen Sie Ihren Code aus <code>routes/web.php</code> hier ein.</p>',
            'max_points' => 30,
            'due_days' => 7,
        ]);

        // Module 3: Controllers
        $module3 = Module::create([
            'learning_path_id' => $path->id,
            'title' => 'Controllers',
            'description' => 'Lernen Sie, wie Sie Controller in Laravel verwenden.',
            'position' => 3,
            'unlock_condition' => UnlockCondition::Sequential,
        ]);

        $step3_1 = LearningStep::create([
            'module_id' => $module3->id,
            'title' => 'Controller erstellen',
            'description' => 'Controller-Klassen in Laravel',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 10,
            'estimated_minutes' => 25,
        ]);

        LearningMaterial::create([
            'step_id' => $step3_1->id,
            'material_type' => MaterialType::Text,
            'title' => 'Controllers in Laravel',
            'content' => '<h2>Controllers</h2>
<p>Controller organisieren Ihre Anwendungslogik in Klassen.</p>

<h3>Controller erstellen</h3>
<pre><code>php artisan make:controller ProductController</code></pre>

<h3>Controller-Klasse</h3>
<pre><code>namespace App\Http\Controllers;

class ProductController extends Controller
{
    public function index()
    {
        return view(\'products.index\');
    }

    public function show($id)
    {
        return view(\'products.show\', [\'id\' => $id]);
    }
}</code></pre>

<h3>Route zu Controller</h3>
<pre><code>use App\Http\Controllers\ProductController;

Route::get(\'/products\', [ProductController::class, \'index\']);
Route::get(\'/products/{id}\', [ProductController::class, \'show\']);</code></pre>',
            'position' => 1,
        ]);

        $this->command->info('Learning paths seeded successfully!');
    }
}
