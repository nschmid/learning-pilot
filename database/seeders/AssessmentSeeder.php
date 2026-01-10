<?php

namespace Database\Seeders;

use App\Enums\AssessmentType;
use App\Enums\QuestionType;
use App\Enums\StepType;
use App\Models\AnswerOption;
use App\Models\Assessment;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\Question;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
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

        // Create a final exam step
        $examStep = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Abschlussprüfung: Controllers',
            'description' => 'Umfassende Prüfung zu Controllers in Laravel',
            'step_type' => StepType::Assessment,
            'position' => 10,
            'points_value' => 100,
            'estimated_minutes' => 30,
        ]);

        $exam = Assessment::create([
            'step_id' => $examStep->id,
            'assessment_type' => AssessmentType::Exam,
            'title' => 'Controller-Prüfung',
            'description' => 'Diese Prüfung testet Ihr Verständnis von Laravel Controllers.',
            'instructions' => 'Lesen Sie jede Frage sorgfältig. Sie haben 30 Minuten Zeit.',
            'time_limit_minutes' => 30,
            'passing_score_percent' => 70,
            'max_attempts' => 2,
            'shuffle_questions' => true,
            'shuffle_answers' => true,
            'show_correct_answers' => true,
            'show_score_immediately' => true,
        ]);

        // Question 1: Single Choice
        $q1 = Question::create([
            'assessment_id' => $exam->id,
            'question_type' => QuestionType::SingleChoice,
            'question_text' => 'Welcher Artisan-Befehl erstellt einen neuen Controller?',
            'explanation' => 'Der Befehl "php artisan make:controller" erstellt einen neuen Controller.',
            'points' => 10,
            'position' => 1,
        ]);

        $this->createOptions($q1, [
            ['text' => 'php artisan make:controller', 'correct' => true],
            ['text' => 'php artisan create:controller', 'correct' => false],
            ['text' => 'php artisan new:controller', 'correct' => false],
            ['text' => 'php artisan generate:controller', 'correct' => false],
        ]);

        // Question 2: Multiple Choice
        $q2 = Question::create([
            'assessment_id' => $exam->id,
            'question_type' => QuestionType::MultipleChoice,
            'question_text' => 'Welche HTTP-Methoden werden typischerweise in einem Resource Controller verwendet?',
            'explanation' => 'Resource Controller verwenden GET, POST, PUT/PATCH und DELETE.',
            'points' => 15,
            'position' => 2,
        ]);

        $this->createOptions($q2, [
            ['text' => 'GET', 'correct' => true],
            ['text' => 'POST', 'correct' => true],
            ['text' => 'PUT', 'correct' => true],
            ['text' => 'DELETE', 'correct' => true],
            ['text' => 'CONNECT', 'correct' => false],
        ]);

        // Question 3: True/False
        $q3 = Question::create([
            'assessment_id' => $exam->id,
            'question_type' => QuestionType::TrueFalse,
            'question_text' => 'Controller sollten möglichst viel Geschäftslogik enthalten.',
            'explanation' => 'Falsch! Controller sollten "thin" sein. Geschäftslogik gehört in Services oder Actions.',
            'points' => 10,
            'position' => 3,
        ]);

        $this->createOptions($q3, [
            ['text' => 'Wahr', 'correct' => false],
            ['text' => 'Falsch', 'correct' => true],
        ]);

        // Question 4: Single Choice
        $q4 = Question::create([
            'assessment_id' => $exam->id,
            'question_type' => QuestionType::SingleChoice,
            'question_text' => 'In welchem Verzeichnis befinden sich standardmässig die Controller?',
            'explanation' => 'Controller befinden sich im Verzeichnis app/Http/Controllers.',
            'points' => 10,
            'position' => 4,
        ]);

        $this->createOptions($q4, [
            ['text' => 'app/Http/Controllers', 'correct' => true],
            ['text' => 'app/Controllers', 'correct' => false],
            ['text' => 'Http/Controllers', 'correct' => false],
            ['text' => 'controllers/', 'correct' => false],
        ]);

        // Question 5: Multiple Choice
        $q5 = Question::create([
            'assessment_id' => $exam->id,
            'question_type' => QuestionType::MultipleChoice,
            'question_text' => 'Welche Methoden hat ein Resource Controller standardmässig?',
            'explanation' => 'Ein Resource Controller hat: index, create, store, show, edit, update, destroy.',
            'points' => 20,
            'position' => 5,
        ]);

        $this->createOptions($q5, [
            ['text' => 'index', 'correct' => true],
            ['text' => 'store', 'correct' => true],
            ['text' => 'update', 'correct' => true],
            ['text' => 'destroy', 'correct' => true],
            ['text' => 'remove', 'correct' => false],
        ]);

        // Question 6: Text answer (manual grading)
        $q6 = Question::create([
            'assessment_id' => $exam->id,
            'question_type' => QuestionType::Text,
            'question_text' => 'Erklären Sie kurz, was Dependency Injection in Controllern bedeutet und warum sie nützlich ist.',
            'explanation' => 'Dependency Injection ermöglicht es, Abhängigkeiten über den Konstruktor oder Methodenparameter zu übergeben, was Testing und Wartbarkeit verbessert.',
            'points' => 25,
            'position' => 6,
        ]);

        // Question 7: Single Choice
        $q7 = Question::create([
            'assessment_id' => $exam->id,
            'question_type' => QuestionType::SingleChoice,
            'question_text' => 'Wie erstellt man einen Resource Controller mit dem Model?',
            'explanation' => 'Mit --model=ModelName wird der Controller mit dem entsprechenden Model verknüpft.',
            'points' => 10,
            'position' => 7,
        ]);

        $this->createOptions($q7, [
            ['text' => 'php artisan make:controller --resource --model=Product', 'correct' => true],
            ['text' => 'php artisan make:controller --crud --model=Product', 'correct' => false],
            ['text' => 'php artisan make:controller --model=Product', 'correct' => false],
            ['text' => 'php artisan make:controller Product --auto', 'correct' => false],
        ]);

        $this->command->info('Assessment seeded successfully! Created exam with ' . $exam->questions()->count() . ' questions.');
    }

    /**
     * Create answer options for a question.
     */
    private function createOptions(Question $question, array $options): void
    {
        foreach ($options as $index => $option) {
            AnswerOption::create([
                'question_id' => $question->id,
                'option_text' => $option['text'],
                'is_correct' => $option['correct'],
                'position' => $index + 1,
            ]);
        }
    }
}
