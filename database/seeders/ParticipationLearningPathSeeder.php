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

class ParticipationLearningPathSeeder extends Seeder
{
    private LearningPath $path;

    public function run(): void
    {
        $instructor = User::where('email', 'instructor@learningpilot.ch')->first();
        $team = Team::first();

        if (! $instructor || ! $team) {
            $this->command->warn('Instructor or Team not found. Run UserSeeder and TeamSeeder first.');
            return;
        }

        // Create or get Pädagogik category
        $category = $this->createCategory();

        // Create tags
        $tags = collect(['Partizipation', 'Kinderrechte', 'Betreuung', 'Pädagogik', 'Inklusion'])
            ->map(fn ($name) => Tag::firstOrCreate(
                ['slug' => \Str::slug($name)],
                ['name' => $name]
            ));

        // Create Learning Path
        $this->path = LearningPath::create([
            'creator_id' => $instructor->id,
            'team_id' => $team->id,
            'category_id' => $category->id,
            'title' => 'Partizipation in der Betreuungsarbeit',
            'slug' => 'partizipation-betreuungsarbeit',
            'description' => 'Dieser Lernpfad vermittelt fundiertes Wissen und praktische Kompetenzen zur Umsetzung von Partizipation in der Betreuungsarbeit mit Kindern und Jugendlichen. Nach Abschluss können Teilnehmende Partizipation fachlich begründen, Beteiligungsprozesse altersgerecht gestalten und Kinderrechte im Alltag umsetzen.',
            'difficulty' => Difficulty::Intermediate,
            'is_published' => true,
            'published_at' => now(),
            'estimated_hours' => 24,
            'version' => 1,
        ]);

        $this->path->tags()->attach($tags->pluck('id'));

        // Create all 8 modules
        $this->createModule1();
        $this->createModule2();
        $this->createModule3();
        $this->createModule4();
        $this->createModule5();
        $this->createModule6();
        $this->createModule7();
        $this->createModule8();

        $this->command->info('Partizipation learning path seeded successfully with 8 modules!');
    }

    private function createCategory(): Category
    {
        $parent = Category::firstOrCreate(
            ['slug' => 'paedagogik'],
            ['name' => 'Pädagogik', 'description' => 'Erziehung, Betreuung und Bildung']
        );

        return Category::firstOrCreate(
            ['slug' => 'kinderbetreuung'],
            ['name' => 'Kinderbetreuung', 'description' => 'Betreuung von Kindern und Jugendlichen', 'parent_id' => $parent->id]
        );
    }

    private function createModule1(): void
    {
        $module = Module::create([
            'learning_path_id' => $this->path->id,
            'title' => 'Modul 1: Grundlagen & Haltung',
            'description' => 'Verstehen, was Partizipation bedeutet und warum sie zentral ist.',
            'position' => 1,
            'unlock_condition' => UnlockCondition::Manual,
        ]);

        // Step 1.1: Definition von Partizipation
        $step1 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Was ist Partizipation?',
            'description' => 'Definition und Abgrenzung von Partizipation',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 10,
            'estimated_minutes' => 20,
        ]);

        LearningMaterial::create([
            'step_id' => $step1->id,
            'material_type' => MaterialType::Text,
            'title' => 'Definition von Partizipation',
            'content' => '<h2>Was bedeutet Partizipation?</h2>
<p>Partizipation (lat. participare = teilnehmen) bezeichnet die aktive Einbeziehung von Kindern und Jugendlichen in Entscheidungsprozesse, die ihr Leben betreffen. Es geht dabei um mehr als nur "mitmachen dürfen" – es geht um echte Teilhabe an Entscheidungen.</p>

<h3>Partizipation vs. Mitbestimmung vs. Mitwirkung</h3>
<table>
<tr><th>Begriff</th><th>Bedeutung</th><th>Beispiel</th></tr>
<tr><td><strong>Mitwirkung</strong></td><td>Beteiligung an der Umsetzung</td><td>Kinder helfen beim Aufräumen</td></tr>
<tr><td><strong>Mitbestimmung</strong></td><td>Stimme bei Entscheidungen</td><td>Kinder stimmen über Spielregeln ab</td></tr>
<tr><td><strong>Partizipation</strong></td><td>Umfassende Teilhabe am gesamten Prozess</td><td>Kinder identifizieren Probleme, entwickeln Lösungen und setzen diese um</td></tr>
</table>

<h3>Warum ist Partizipation wichtig?</h3>
<ul>
<li><strong>Demokratiebildung:</strong> Kinder lernen demokratische Prozesse kennen</li>
<li><strong>Selbstwirksamkeit:</strong> Kinder erfahren, dass ihre Meinung zählt</li>
<li><strong>Entwicklungsförderung:</strong> Fördert Verantwortungsbewusstsein und Sozialkompetenz</li>
<li><strong>Schutzfunktion:</strong> Beteiligte Kinder können sich besser äussern, wenn etwas nicht stimmt</li>
</ul>',
            'position' => 1,
        ]);

        // Step 1.2: Kinderrechte
        $step2 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Kinderrechte und UN-Kinderrechtskonvention',
            'description' => 'Rechtliche Grundlagen der Partizipation',
            'step_type' => StepType::Material,
            'position' => 2,
            'points_value' => 10,
            'estimated_minutes' => 25,
        ]);

        LearningMaterial::create([
            'step_id' => $step2->id,
            'material_type' => MaterialType::Text,
            'title' => 'UN-Kinderrechtskonvention Artikel 12',
            'content' => '<h2>UN-Kinderrechtskonvention – Artikel 12</h2>
<blockquote>
<p>"Die Vertragsstaaten sichern dem Kind, das fähig ist, sich eine eigene Meinung zu bilden, das Recht zu, diese Meinung in allen das Kind berührenden Angelegenheiten frei zu äussern, und berücksichtigen die Meinung des Kindes angemessen und entsprechend seinem Alter und seiner Reife."</p>
</blockquote>

<h3>Was bedeutet das konkret?</h3>
<ul>
<li><strong>Recht auf Meinungsäusserung:</strong> Jedes Kind darf seine Meinung sagen</li>
<li><strong>Berücksichtigungspflicht:</strong> Erwachsene müssen die Meinung ernst nehmen</li>
<li><strong>Altersangemessenheit:</strong> Die Form der Beteiligung passt sich dem Entwicklungsstand an</li>
<li><strong>Alle Angelegenheiten:</strong> Gilt für Familie, Schule, Betreuung und Gesellschaft</li>
</ul>

<h3>Weitere relevante Artikel</h3>
<ul>
<li><strong>Artikel 13:</strong> Recht auf freie Meinungsäusserung</li>
<li><strong>Artikel 14:</strong> Gedanken-, Gewissens- und Religionsfreiheit</li>
<li><strong>Artikel 15:</strong> Vereinigungs- und Versammlungsfreiheit</li>
</ul>

<h3>Reflexionsfrage</h3>
<p><em>Wie wird Artikel 12 in Ihrer Einrichtung bereits umgesetzt? Wo sehen Sie Potenzial?</em></p>',
            'position' => 1,
        ]);

        // Step 1.3: Macht und Haltung
        $step3 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Macht, Verantwortung und professionelle Haltung',
            'description' => 'Reflexion der eigenen Rolle',
            'step_type' => StepType::Material,
            'position' => 3,
            'points_value' => 10,
            'estimated_minutes' => 20,
        ]);

        LearningMaterial::create([
            'step_id' => $step3->id,
            'material_type' => MaterialType::Text,
            'title' => 'Professionelle Haltung zur Partizipation',
            'content' => '<h2>Macht und Verantwortung in der Betreuung</h2>
<p>Als Betreuungsperson haben Sie Macht über Kinder – Macht über Zeit, Raum, Regeln und Ressourcen. Diese Macht verantwortungsvoll zu nutzen bedeutet, sie bewusst zu teilen.</p>

<h3>Partizipative Grundhaltung</h3>
<ul>
<li><strong>Wertschätzung:</strong> Jede Meinung ist wertvoll, auch wenn sie unbequem ist</li>
<li><strong>Offenheit:</strong> Bereitschaft, Entscheidungen zu teilen und zu überdenken</li>
<li><strong>Geduld:</strong> Partizipation braucht Zeit – das ist keine verschwendete Zeit</li>
<li><strong>Vertrauen:</strong> Kinder können mehr, als wir ihnen oft zutrauen</li>
<li><strong>Transparenz:</strong> Erklären, warum manche Entscheidungen nicht verhandelbar sind</li>
</ul>

<h3>Grenzen der eigenen Macht</h3>
<p>Partizipation bedeutet nicht, alle Macht abzugeben. Erwachsene behalten die Verantwortung für:</p>
<ul>
<li>Sicherheit und Schutz der Kinder</li>
<li>Einhaltung gesetzlicher Vorgaben</li>
<li>Rahmenentscheidungen der Einrichtung</li>
</ul>',
            'position' => 1,
        ]);

        // Step 1.4: Selbstreflexion
        $step4 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Selbstreflexion: Partizipation in meinem Alltag',
            'description' => 'Reflexion der eigenen Praxis',
            'step_type' => StepType::Task,
            'position' => 4,
            'points_value' => 25,
            'estimated_minutes' => 30,
        ]);

        Task::create([
            'step_id' => $step4->id,
            'task_type' => TaskType::Submission,
            'title' => 'Selbstreflexion zur Partizipation',
            'instructions' => '<h3>Reflexionsaufgabe</h3>
<p>Beantworten Sie folgende Fragen schriftlich (mindestens 300 Wörter):</p>
<ol>
<li><strong>Bestandsaufnahme:</strong> In welchen Situationen dürfen Kinder bei Ihnen bereits mitentscheiden? Listen Sie mindestens 5 konkrete Beispiele auf.</li>
<li><strong>Grenzen:</strong> Wo setzen Sie aktuell Grenzen der Mitbestimmung? Sind diese Grenzen alle notwendig oder könnten manche erweitert werden?</li>
<li><strong>Hürden:</strong> Was hindert Sie manchmal daran, Kinder stärker zu beteiligen? (z.B. Zeitdruck, Unsicherheit, institutionelle Vorgaben)</li>
<li><strong>Vision:</strong> Wie würde ein partizipativer Alltag in Ihrer Arbeit idealerweise aussehen?</li>
</ol>

<h4>Tipps für die Reflexion</h4>
<ul>
<li>Seien Sie ehrlich zu sich selbst – es gibt keine falschen Antworten</li>
<li>Denken Sie an konkrete Situationen aus Ihrem Alltag</li>
<li>Überlegen Sie auch, was Sie von den Kindern lernen können</li>
</ul>',
            'max_points' => 25,
            'due_days' => 7,
        ]);

        // Step 1.5: Quiz
        $step5 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Quiz: Grundlagen der Partizipation',
            'description' => 'Überprüfen Sie Ihr Wissen',
            'step_type' => StepType::Assessment,
            'position' => 5,
            'points_value' => 50,
            'estimated_minutes' => 15,
        ]);

        $assessment = Assessment::create([
            'step_id' => $step5->id,
            'assessment_type' => AssessmentType::Quiz,
            'title' => 'Quiz Modul 1: Grundlagen',
            'description' => 'Testen Sie Ihr Verständnis der Partizipationsgrundlagen',
            'time_limit_minutes' => 15,
            'passing_score_percent' => 70,
            'max_attempts' => 3,
            'shuffle_questions' => true,
        ]);

        $this->createQuizQuestions($assessment, [
            [
                'type' => QuestionType::SingleChoice,
                'text' => 'Was unterscheidet Partizipation von Mitwirkung?',
                'explanation' => 'Partizipation umfasst die Beteiligung am gesamten Prozess, von der Problemerkennung bis zur Umsetzung.',
                'points' => 10,
                'options' => [
                    ['text' => 'Partizipation bedeutet Teilhabe am gesamten Entscheidungsprozess', 'correct' => true],
                    ['text' => 'Es gibt keinen Unterschied', 'correct' => false],
                    ['text' => 'Mitwirkung ist umfassender als Partizipation', 'correct' => false],
                    ['text' => 'Partizipation ist nur für Erwachsene möglich', 'correct' => false],
                ],
            ],
            [
                'type' => QuestionType::TrueFalse,
                'text' => 'Laut UN-Kinderrechtskonvention müssen Kinder erst ab 12 Jahren an Entscheidungen beteiligt werden.',
                'explanation' => 'Falsch. Artikel 12 spricht von Kindern, die "fähig sind, sich eine eigene Meinung zu bilden" – dies ist keine Altersfrage.',
                'points' => 10,
                'options' => [
                    ['text' => 'Wahr', 'correct' => false],
                    ['text' => 'Falsch', 'correct' => true],
                ],
            ],
            [
                'type' => QuestionType::MultipleChoice,
                'text' => 'Welche Aspekte gehören zu einer partizipativen Grundhaltung?',
                'explanation' => 'Eine partizipative Grundhaltung umfasst Wertschätzung, Offenheit, Geduld und Vertrauen.',
                'points' => 15,
                'options' => [
                    ['text' => 'Wertschätzung jeder Meinung', 'correct' => true],
                    ['text' => 'Offenheit für Veränderungen', 'correct' => true],
                    ['text' => 'Strikte Einhaltung aller Regeln ohne Ausnahme', 'correct' => false],
                    ['text' => 'Vertrauen in die Fähigkeiten der Kinder', 'correct' => true],
                ],
            ],
            [
                'type' => QuestionType::SingleChoice,
                'text' => 'Welcher Artikel der UN-Kinderrechtskonvention regelt das Recht auf Beteiligung?',
                'explanation' => 'Artikel 12 der UN-KRK regelt das Recht des Kindes auf Berücksichtigung seiner Meinung.',
                'points' => 10,
                'options' => [
                    ['text' => 'Artikel 12', 'correct' => true],
                    ['text' => 'Artikel 3', 'correct' => false],
                    ['text' => 'Artikel 19', 'correct' => false],
                    ['text' => 'Artikel 28', 'correct' => false],
                ],
            ],
        ]);
    }

    private function createModule2(): void
    {
        $module = Module::create([
            'learning_path_id' => $this->path->id,
            'title' => 'Modul 2: Rechtliche & institutionelle Rahmenbedingungen',
            'description' => 'Sicherheit im rechtlichen Kontext gewinnen.',
            'position' => 2,
            'unlock_condition' => UnlockCondition::Sequential,
        ]);

        // Step 2.1: SGB VIII
        $step1 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Kinder- und Jugendhilfegesetz (SGB VIII)',
            'description' => 'Rechtliche Grundlagen in Deutschland',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 10,
            'estimated_minutes' => 25,
        ]);

        LearningMaterial::create([
            'step_id' => $step1->id,
            'material_type' => MaterialType::Text,
            'title' => 'SGB VIII und Partizipation',
            'content' => '<h2>Partizipation im SGB VIII</h2>
<p>Das Sozialgesetzbuch VIII (Kinder- und Jugendhilfe) verankert Partizipation als grundlegendes Prinzip der Kinder- und Jugendhilfe.</p>

<h3>Zentrale Paragraphen</h3>

<h4>§ 8 SGB VIII – Beteiligung von Kindern und Jugendlichen</h4>
<blockquote>
<p>(1) Kinder und Jugendliche sind entsprechend ihrem Entwicklungsstand an allen sie betreffenden Entscheidungen der öffentlichen Jugendhilfe zu beteiligen.</p>
</blockquote>

<h4>§ 45 SGB VIII – Erlaubnis für den Betrieb einer Einrichtung</h4>
<p>Die Betriebserlaubnis setzt voraus, dass die Einrichtung geeignete Verfahren der Selbstvertretung und Beteiligung sowie der Möglichkeit der Beschwerde in persönlichen Angelegenheiten Anwendung findet.</p>

<h3>Was bedeutet das für Ihre Einrichtung?</h3>
<ul>
<li><strong>Pflicht zur Beteiligung:</strong> Partizipation ist keine freiwillige Zusatzleistung</li>
<li><strong>Beschwerdeverfahren:</strong> Kinder müssen sich beschweren können</li>
<li><strong>Qualitätsmerkmal:</strong> Partizipation wird bei Betriebserlaubnissen geprüft</li>
<li><strong>Dokumentation:</strong> Beteiligungsverfahren sollten schriftlich fixiert sein</li>
</ul>',
            'position' => 1,
        ]);

        // Step 2.2: Fallbeispiele
        $step2 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Fallbeispiele: Pflicht oder Möglichkeit?',
            'description' => 'Praktische Anwendung der rechtlichen Grundlagen',
            'step_type' => StepType::Task,
            'position' => 2,
            'points_value' => 30,
            'estimated_minutes' => 40,
        ]);

        Task::create([
            'step_id' => $step2->id,
            'task_type' => TaskType::Submission,
            'title' => 'Zuordnungsaufgabe: Rechtliche Einordnung',
            'instructions' => '<h3>Aufgabe: Pflicht oder Möglichkeit?</h3>
<p>Ordnen Sie die folgenden Situationen ein und begründen Sie Ihre Einschätzung:</p>

<ol>
<li><strong>Situation A:</strong> Die Kinder möchten die Mittagsruhe abschaffen. Müssen Sie darüber abstimmen lassen?</li>
<li><strong>Situation B:</strong> Ein Kind beschwert sich, dass es immer als Letztes beim Essen drankommt. Wie gehen Sie damit um?</li>
<li><strong>Situation C:</strong> Die Gruppe möchte den Tagesablauf umstellen. Eltern sind dagegen. Wer entscheidet?</li>
<li><strong>Situation D:</strong> Ein Kind möchte nicht am Ausflug teilnehmen. Muss es trotzdem mit?</li>
<li><strong>Situation E:</strong> Die Kinder wollen nur noch Süsses essen. Wie reagieren Sie?</li>
</ol>

<h4>Für jede Situation:</h4>
<ul>
<li>Ist Beteiligung hier Pflicht, Möglichkeit oder gibt es einen Schutzaspekt?</li>
<li>Welche rechtliche Grundlage ist relevant?</li>
<li>Wie würden Sie konkret vorgehen?</li>
</ul>',
            'max_points' => 30,
            'due_days' => 5,
            'rubric' => [
                ['criterion' => 'Rechtliche Einordnung', 'max_points' => 10, 'description' => 'Korrekte Unterscheidung zwischen Pflicht und Möglichkeit'],
                ['criterion' => 'Begründung', 'max_points' => 10, 'description' => 'Nachvollziehbare Argumentation mit Bezug auf Rechtsgrundlagen'],
                ['criterion' => 'Praxisbezug', 'max_points' => 10, 'description' => 'Realistische und umsetzbare Handlungsvorschläge'],
            ],
        ]);

        // Step 2.3: Quiz
        $step3 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Quiz: Rechtliche Grundlagen',
            'description' => 'Überprüfen Sie Ihr Wissen',
            'step_type' => StepType::Assessment,
            'position' => 3,
            'points_value' => 40,
            'estimated_minutes' => 10,
        ]);

        $assessment = Assessment::create([
            'step_id' => $step3->id,
            'assessment_type' => AssessmentType::Quiz,
            'title' => 'Quiz Modul 2: Rechtliche Grundlagen',
            'description' => 'Testen Sie Ihr Wissen zu den rechtlichen Rahmenbedingungen',
            'time_limit_minutes' => 10,
            'passing_score_percent' => 70,
            'max_attempts' => 3,
            'shuffle_questions' => true,
        ]);

        $this->createQuizQuestions($assessment, [
            [
                'type' => QuestionType::SingleChoice,
                'text' => 'In welchem Gesetz ist die Beteiligung von Kindern in der Jugendhilfe geregelt?',
                'explanation' => '§ 8 SGB VIII regelt die Beteiligung von Kindern und Jugendlichen.',
                'points' => 10,
                'options' => [
                    ['text' => 'SGB VIII (Kinder- und Jugendhilfe)', 'correct' => true],
                    ['text' => 'SGB XII (Sozialhilfe)', 'correct' => false],
                    ['text' => 'Bürgerliches Gesetzbuch (BGB)', 'correct' => false],
                    ['text' => 'Grundgesetz Artikel 6', 'correct' => false],
                ],
            ],
            [
                'type' => QuestionType::TrueFalse,
                'text' => 'Ein Beschwerdeverfahren für Kinder ist laut SGB VIII Voraussetzung für die Betriebserlaubnis einer Einrichtung.',
                'explanation' => 'Richtig. § 45 SGB VIII fordert geeignete Verfahren der Beteiligung und Beschwerde.',
                'points' => 10,
                'options' => [
                    ['text' => 'Wahr', 'correct' => true],
                    ['text' => 'Falsch', 'correct' => false],
                ],
            ],
        ]);
    }

    private function createModule3(): void
    {
        $module = Module::create([
            'learning_path_id' => $this->path->id,
            'title' => 'Modul 3: Formen und Ebenen der Partizipation',
            'description' => 'Partizipation differenziert anwenden können.',
            'position' => 3,
            'unlock_condition' => UnlockCondition::Sequential,
        ]);

        // Step 3.1: Partizipationsstufen
        $step1 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Partizipationsstufen nach Schröder',
            'description' => 'Verschiedene Grade der Beteiligung verstehen',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 15,
            'estimated_minutes' => 30,
        ]);

        LearningMaterial::create([
            'step_id' => $step1->id,
            'material_type' => MaterialType::Text,
            'title' => 'Die Partizipationsleiter',
            'content' => '<h2>Partizipationsstufen nach Richard Schröder</h2>
<p>Richard Schröder hat ein Stufenmodell entwickelt, das verschiedene Grade der Beteiligung unterscheidet – von Scheinpartizipation bis zur echten Selbstbestimmung.</p>

<h3>Die Stufen (von unten nach oben)</h3>

<h4>Stufe 1-3: Keine echte Partizipation</h4>
<ul>
<li><strong>1. Fremdbestimmung:</strong> Kinder werden nicht einbezogen</li>
<li><strong>2. Dekoration:</strong> Kinder sind anwesend, aber ohne Einfluss</li>
<li><strong>3. Alibi-Teilnahme:</strong> Kinder dürfen etwas sagen, es hat aber keine Wirkung</li>
</ul>

<h4>Stufe 4-6: Vorstufen der Partizipation</h4>
<ul>
<li><strong>4. Teilhabe:</strong> Kinder werden informiert</li>
<li><strong>5. Zugewiesen, informiert:</strong> Erwachsene entscheiden, aber erklären warum</li>
<li><strong>6. Mitwirkung:</strong> Kinder können Einfluss nehmen auf vorgegebene Themen</li>
</ul>

<h4>Stufe 7-9: Echte Partizipation</h4>
<ul>
<li><strong>7. Mitbestimmung:</strong> Kinder entscheiden gemeinsam mit Erwachsenen</li>
<li><strong>8. Selbstbestimmung:</strong> Kinder entscheiden selbst, Erwachsene unterstützen</li>
<li><strong>9. Selbstverwaltung:</strong> Kinder gestalten eigenverantwortlich</li>
</ul>

<h3>Praxisbeispiel</h3>
<table>
<tr><th>Stufe</th><th>Beispiel: Ausflugsziel</th></tr>
<tr><td>Fremdbestimmung</td><td>Betreuende entscheiden: "Wir gehen in den Zoo."</td></tr>
<tr><td>Alibi-Teilnahme</td><td>"Wollt ihr in den Zoo?" (Antwort spielt keine Rolle)</td></tr>
<tr><td>Mitbestimmung</td><td>Kinder wählen aus 3 Vorschlägen</td></tr>
<tr><td>Selbstbestimmung</td><td>Kinder entwickeln und planen den Ausflug selbst</td></tr>
</table>',
            'position' => 1,
        ]);

        // Step 3.2: Alltagsentscheidungen
        $step2 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Alltagsentscheidungen vs. strukturelle Beteiligung',
            'description' => 'Unterschiedliche Ebenen der Partizipation',
            'step_type' => StepType::Material,
            'position' => 2,
            'points_value' => 10,
            'estimated_minutes' => 20,
        ]);

        LearningMaterial::create([
            'step_id' => $step2->id,
            'material_type' => MaterialType::Text,
            'title' => 'Ebenen der Partizipation',
            'content' => '<h2>Alltagsentscheidungen und strukturelle Beteiligung</h2>

<h3>Alltagsentscheidungen</h3>
<p>Kleine Entscheidungen im täglichen Ablauf:</p>
<ul>
<li>Was möchte ich spielen?</li>
<li>Mit wem möchte ich zusammen sein?</li>
<li>Wann und was esse ich?</li>
<li>Wann brauche ich Ruhe?</li>
</ul>

<h3>Strukturelle Beteiligung</h3>
<p>Einfluss auf Regeln, Abläufe und Strukturen:</p>
<ul>
<li>Welche Regeln gelten in unserer Gruppe?</li>
<li>Wie sieht unser Tagesablauf aus?</li>
<li>Wie gestalten wir unsere Räume?</li>
<li>Welche Projekte führen wir durch?</li>
</ul>

<h3>Alters- und entwicklungsangemessene Beteiligung</h3>
<table>
<tr><th>Alter</th><th>Mögliche Beteiligung</th></tr>
<tr><td>0-3 Jahre</td><td>Körpersprache beobachten, Wahlmöglichkeiten anbieten (2 Optionen), Tempo respektieren</td></tr>
<tr><td>3-6 Jahre</td><td>Abstimmungen mit Symbolen, Kinderkonferenzen mit Visualisierung, Projektbeteiligung</td></tr>
<tr><td>6-10 Jahre</td><td>Regeln gemeinsam entwickeln, Gruppenrat mit Protokoll, Beschwerdebriefkasten</td></tr>
<tr><td>10+ Jahre</td><td>Gremienarbeit, eigene Projekte leiten, Peer-Mediation</td></tr>
</table>',
            'position' => 1,
        ]);

        // Step 3.3: Ampelübung
        $step3 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Ampelübung: Was geht – was nicht?',
            'description' => 'Praxisnahe Einschätzung von Partizipationsmöglichkeiten',
            'step_type' => StepType::Task,
            'position' => 3,
            'points_value' => 25,
            'estimated_minutes' => 35,
        ]);

        Task::create([
            'step_id' => $step3->id,
            'task_type' => TaskType::Submission,
            'title' => 'Ampelübung zur Partizipation',
            'instructions' => '<h3>Ampelübung: Grenzen der Partizipation</h3>
<p>Ordnen Sie die folgenden Situationen nach dem Ampelprinzip ein:</p>
<ul>
<li><strong style="color:green;">GRÜN:</strong> Volle Partizipation möglich – Kinder können selbst entscheiden</li>
<li><strong style="color:orange;">GELB:</strong> Eingeschränkte Partizipation – Kinder werden beteiligt, aber nicht allein entscheiden</li>
<li><strong style="color:red;">ROT:</strong> Keine Partizipation – Erwachsene müssen entscheiden (Schutz, Sicherheit, Gesetz)</li>
</ul>

<h4>Situationen zur Einordnung:</h4>
<ol>
<li>Welches Spiel spielen wir heute?</li>
<li>Ob ein Kind zum Arzt muss</li>
<li>Wie wir Konflikte in der Gruppe lösen</li>
<li>Wer heute den Tisch deckt</li>
<li>Ob wir bei Gewitter draussen spielen</li>
<li>Welche Regeln im Gruppenraum gelten</li>
<li>Ob ein Kind Medikamente bekommt</li>
<li>Wie die Geburtstagsfeier gestaltet wird</li>
<li>Wann das Kind abgeholt wird</li>
<li>Welche Projekte wir durchführen</li>
</ol>

<h4>Ihre Aufgabe:</h4>
<p>Ordnen Sie jede Situation einer Ampelfarbe zu und begründen Sie kurz (1-2 Sätze) Ihre Entscheidung.</p>',
            'max_points' => 25,
            'due_days' => 5,
        ]);
    }

    private function createModule4(): void
    {
        $module = Module::create([
            'learning_path_id' => $this->path->id,
            'title' => 'Modul 4: Partizipation im Betreuungsalltag',
            'description' => 'Konkrete Umsetzung im Alltag.',
            'position' => 4,
            'unlock_condition' => UnlockCondition::Sequential,
        ]);

        // Step 4.1: Beteiligung bei Tagesabläufen
        $step1 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Beteiligung bei Tagesabläufen',
            'description' => 'Partizipation in Routinen integrieren',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 10,
            'estimated_minutes' => 20,
        ]);

        LearningMaterial::create([
            'step_id' => $step1->id,
            'material_type' => MaterialType::Text,
            'title' => 'Partizipation im Tagesablauf',
            'content' => '<h2>Beteiligung bei Tagesabläufen</h2>

<h3>Ankommen</h3>
<ul>
<li>Kinder wählen, wo sie spielen möchten</li>
<li>Flexible Übergänge ermöglichen</li>
<li>Begrüssungsrituale mitgestalten lassen</li>
</ul>

<h3>Mahlzeiten</h3>
<ul>
<li>Selbstbedienung ermöglichen</li>
<li>Portionsgrösse selbst bestimmen</li>
<li>Tischdienste gemeinsam organisieren</li>
<li>Menüwünsche berücksichtigen</li>
</ul>

<h3>Freispiel</h3>
<ul>
<li>Freie Wahl von Spielort, -partner und -material</li>
<li>Eigene Spielideen umsetzen</li>
<li>Rückzugsmöglichkeiten respektieren</li>
</ul>

<h3>Ruhezeit</h3>
<ul>
<li>Individuelle Ruhebedürfnisse berücksichtigen</li>
<li>Alternativen zum Schlafen anbieten</li>
<li>Ritualen gemeinsam entwickeln</li>
</ul>

<h3>Abholzeit</h3>
<ul>
<li>Kinder über Abholzeit informieren</li>
<li>Zeit zum Verabschieden geben</li>
<li>Spiele zu Ende bringen lassen</li>
</ul>',
            'position' => 1,
        ]);

        // Step 4.2: Regeln und Konflikte
        $step2 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Regeln und Konfliktlösung',
            'description' => 'Gemeinsam Regeln entwickeln und Konflikte lösen',
            'step_type' => StepType::Material,
            'position' => 2,
            'points_value' => 10,
            'estimated_minutes' => 25,
        ]);

        LearningMaterial::create([
            'step_id' => $step2->id,
            'material_type' => MaterialType::Text,
            'title' => 'Partizipative Regelentwicklung',
            'content' => '<h2>Regeln gemeinsam entwickeln</h2>

<h3>Prinzipien partizipativer Regeln</h3>
<ul>
<li><strong>Weniger ist mehr:</strong> Nur so viele Regeln wie nötig</li>
<li><strong>Positiv formuliert:</strong> "Wir gehen langsam" statt "Nicht rennen"</li>
<li><strong>Verständlich:</strong> Kindgerechte Sprache und Visualisierung</li>
<li><strong>Veränderbar:</strong> Regeln können überprüft und angepasst werden</li>
</ul>

<h3>Prozess der Regelentwicklung</h3>
<ol>
<li><strong>Problem benennen:</strong> Was stört uns? Was ist das Problem?</li>
<li><strong>Bedürfnisse klären:</strong> Was brauchen wir alle?</li>
<li><strong>Ideen sammeln:</strong> Welche Lösungen gibt es?</li>
<li><strong>Abstimmung:</strong> Für welche Regel entscheiden wir uns?</li>
<li><strong>Visualisierung:</strong> Regel sichtbar machen</li>
<li><strong>Evaluation:</strong> Funktioniert die Regel? Anpassen?</li>
</ol>

<h3>Partizipative Konfliktlösung</h3>
<p>Kinder lernen Konflikte zu lösen, wenn sie dabei unterstützt werden:</p>
<ol>
<li><strong>Stopp-Signal:</strong> Alle Beteiligten stoppen</li>
<li><strong>Jeder erzählt:</strong> Was ist passiert? (Ich-Botschaften)</li>
<li><strong>Gefühle benennen:</strong> Wie geht es dir dabei?</li>
<li><strong>Lösung suchen:</strong> Was könnten wir tun?</li>
<li><strong>Vereinbarung:</strong> Worauf einigen wir uns?</li>
</ol>',
            'position' => 1,
        ]);

        // Step 4.3: Gesprächsführung
        $step3 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Gesprächsführung & Fragetechniken',
            'description' => 'Wie wir mit Kindern über ihre Meinung sprechen',
            'step_type' => StepType::Material,
            'position' => 3,
            'points_value' => 10,
            'estimated_minutes' => 20,
        ]);

        LearningMaterial::create([
            'step_id' => $step3->id,
            'material_type' => MaterialType::Text,
            'title' => 'Mit Kindern ins Gespräch kommen',
            'content' => '<h2>Partizipative Gesprächsführung</h2>

<h3>Offene Fragen stellen</h3>
<ul>
<li><strong>Statt:</strong> "Hat es dir gefallen?" → <strong>Besser:</strong> "Was hat dir gefallen?"</li>
<li><strong>Statt:</strong> "Willst du mitspielen?" → <strong>Besser:</strong> "Was möchtest du jetzt tun?"</li>
<li><strong>Statt:</strong> "War das schön?" → <strong>Besser:</strong> "Wie war das für dich?"</li>
</ul>

<h3>Fragetechniken</h3>
<ul>
<li><strong>W-Fragen:</strong> Was, Wie, Wo, Wer, Warum (vorsichtig!)</li>
<li><strong>Skalenfragen:</strong> "Von 1 bis 5, wie gut hat es dir gefallen?"</li>
<li><strong>Bildgestützt:</strong> Smileys, Daumen hoch/runter, Ampelkarten</li>
<li><strong>Hypothetische Fragen:</strong> "Stell dir vor, du könntest entscheiden..."</li>
</ul>

<h3>Aktives Zuhören</h3>
<ul>
<li>Augenhöhe einnehmen (hinknien)</li>
<li>Ausreden lassen</li>
<li>Zusammenfassen: "Du meinst also..."</li>
<li>Nachfragen: "Erzähl mir mehr davon"</li>
<li>Gefühle spiegeln: "Das hat dich geärgert"</li>
</ul>

<h3>Was Partizipation verhindert</h3>
<ul>
<li>Suggestivfragen: "Das findest du doch auch toll, oder?"</li>
<li>Unterbrechen</li>
<li>Bewertungen: "Das ist aber eine komische Idee"</li>
<li>Ignorieren von Einwänden</li>
</ul>',
            'position' => 1,
        ]);

        // Step 4.4: Rollenspiel
        $step4 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Praxisübung: Rollenspiel',
            'description' => 'Partizipative Gesprächsführung üben',
            'step_type' => StepType::Task,
            'position' => 4,
            'points_value' => 35,
            'estimated_minutes' => 45,
        ]);

        Task::create([
            'step_id' => $step4->id,
            'task_type' => TaskType::Project,
            'title' => 'Rollenspiel zur Gesprächsführung',
            'instructions' => '<h3>Praxisaufgabe: Partizipative Gespräche führen</h3>

<h4>Szenario</h4>
<p>In Ihrer Gruppe gibt es seit Wochen Streit darüber, wer im Bau-Ecken spielen darf. Die Kinder beschweren sich täglich. Sie möchten das Problem partizipativ lösen.</p>

<h4>Ihre Aufgabe</h4>
<ol>
<li><strong>Vorbereitung:</strong> Schreiben Sie auf, welche offenen Fragen Sie den Kindern stellen würden, um das Problem zu verstehen.</li>
<li><strong>Durchführung:</strong> Beschreiben Sie Schritt für Schritt, wie Sie ein Gruppengespräch mit 4-5 Kindern (5-6 Jahre alt) gestalten würden.</li>
<li><strong>Lösungsfindung:</strong> Wie begleiten Sie die Kinder dabei, selbst eine Lösung zu finden?</li>
<li><strong>Dokumentation:</strong> Wie halten Sie die Ergebnisse fest?</li>
</ol>

<h4>Reflexion</h4>
<p>Beantworten Sie zusätzlich:</p>
<ul>
<li>Welche Herausforderungen erwarten Sie?</li>
<li>Wie gehen Sie damit um, wenn ein Kind nicht mitmachen möchte?</li>
<li>Was tun Sie, wenn die Kinder keine Lösung finden?</li>
</ul>',
            'max_points' => 35,
            'due_days' => 7,
            'rubric' => [
                ['criterion' => 'Fragetechnik', 'max_points' => 10, 'description' => 'Offene, altersgerechte Fragen formuliert'],
                ['criterion' => 'Prozessgestaltung', 'max_points' => 15, 'description' => 'Strukturierter, partizipativer Ablauf'],
                ['criterion' => 'Reflexionstiefe', 'max_points' => 10, 'description' => 'Herausforderungen erkannt und Lösungen entwickelt'],
            ],
        ]);
    }

    private function createModule5(): void
    {
        $module = Module::create([
            'learning_path_id' => $this->path->id,
            'title' => 'Modul 5: Methoden & Instrumente',
            'description' => 'Handwerkszeug für echte Beteiligung erwerben.',
            'position' => 5,
            'unlock_condition' => UnlockCondition::Sequential,
        ]);

        // Step 5.1: Kinderkonferenzen
        $step1 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Kinderkonferenzen & Gruppenrat',
            'description' => 'Regelmässige Beteiligungsgremien einführen',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 15,
            'estimated_minutes' => 30,
        ]);

        LearningMaterial::create([
            'step_id' => $step1->id,
            'material_type' => MaterialType::Text,
            'title' => 'Kinderkonferenz gestalten',
            'content' => '<h2>Kinderkonferenzen & Gruppenrat</h2>

<h3>Was ist eine Kinderkonferenz?</h3>
<p>Ein regelmässiges Treffen aller Kinder und Betreuenden, bei dem gemeinsam besprochen, geplant und entschieden wird.</p>

<h3>Rahmenbedingungen</h3>
<ul>
<li><strong>Regelmässigkeit:</strong> Wöchentlich oder 14-täglich, fester Termin</li>
<li><strong>Dauer:</strong> 15-30 Minuten je nach Alter</li>
<li><strong>Ort:</strong> Sitzkreis, gemütlicher Rahmen</li>
<li><strong>Rituale:</strong> Anfangs- und Schlussritual</li>
</ul>

<h3>Möglicher Ablauf</h3>
<ol>
<li><strong>Begrüssung & Ritual:</strong> Lied, Kerze anzünden, Sprechstein</li>
<li><strong>Rückblick:</strong> Was war letzte Woche? Was haben wir beschlossen?</li>
<li><strong>Themen sammeln:</strong> Was möchten wir besprechen?</li>
<li><strong>Diskussion:</strong> Ein Thema vertiefen</li>
<li><strong>Abstimmung/Entscheidung:</strong> Was machen wir?</li>
<li><strong>Abschluss:</strong> Zusammenfassung, Ritual</li>
</ol>

<h3>Dokumentation</h3>
<p>Ergebnisse kindgerecht festhalten:</p>
<ul>
<li>Fotos der Abstimmung</li>
<li>Gemalte Protokolle</li>
<li>Symbolkarten</li>
<li>Plakate mit Vereinbarungen</li>
</ul>

<h3>Tipps für die Moderation</h3>
<ul>
<li>Redestein verwenden</li>
<li>Alle Kinder einbeziehen (auch die Stillen)</li>
<li>Visualisieren statt nur reden</li>
<li>Konkretes Thema wählen</li>
<li>Ergebnisse umsetzen!</li>
</ul>',
            'position' => 1,
        ]);

        // Step 5.2: Abstimmungsmethoden
        $step2 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Abstimmungen & Feedbackmethoden',
            'description' => 'Verschiedene Wege, Meinungen einzuholen',
            'step_type' => StepType::Material,
            'position' => 2,
            'points_value' => 10,
            'estimated_minutes' => 20,
        ]);

        LearningMaterial::create([
            'step_id' => $step2->id,
            'material_type' => MaterialType::Text,
            'title' => 'Abstimmungsmethoden für Kinder',
            'content' => '<h2>Abstimmungsmethoden</h2>

<h3>Hand heben</h3>
<p>Klassisch, aber problematisch: Kinder orientieren sich an anderen. Besser: Augen schliessen.</p>

<h3>Muggelsteine / Klebepunkte</h3>
<p>Jedes Kind erhält Steine/Punkte und verteilt sie auf Optionen. Ermöglicht auch gewichtete Abstimmungen.</p>

<h3>Aufstellen / Positionslinie</h3>
<p>Kinder stellen sich zu ihrer Meinung: Links = stimme zu, Rechts = stimme nicht zu, Mitte = weiss nicht.</p>

<h3>Daumen-Voting</h3>
<p>Daumen hoch, runter oder zur Seite – schnell und eindeutig.</p>

<h3>Vier-Ecken-Methode</h3>
<p>Jede Ecke steht für eine Option. Kinder stellen sich in ihre Ecke.</p>

<h2>Feedbackmethoden</h2>

<h3>Smiley-Skala</h3>
<p>Kinder zeigen mit Smileys ihre Zufriedenheit: 😊 😐 😢</p>

<h3>Blitzlicht</h3>
<p>Reihum sagt jedes Kind einen kurzen Satz zu einem Thema.</p>

<h3>Wunsch-Box</h3>
<p>Kinder können jederzeit Wünsche/Ideen einwerfen (auch gemalt).</p>

<h3>Stimmungsbarometer</h3>
<p>Wäscheklammern auf einer Skala von "super" bis "nicht gut".</p>',
            'position' => 1,
        ]);

        // Step 5.3: Beschwerdeverfahren
        $step3 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Beschwerdeverfahren für Kinder',
            'description' => 'Wie Kinder sich beschweren können',
            'step_type' => StepType::Material,
            'position' => 3,
            'points_value' => 10,
            'estimated_minutes' => 20,
        ]);

        LearningMaterial::create([
            'step_id' => $step3->id,
            'material_type' => MaterialType::Text,
            'title' => 'Kindgerechtes Beschwerdeverfahren',
            'content' => '<h2>Beschwerdeverfahren für Kinder</h2>

<h3>Warum ist das wichtig?</h3>
<ul>
<li>Gesetzliche Pflicht (§ 45 SGB VIII)</li>
<li>Schutz vor Grenzüberschreitungen</li>
<li>Stärkt Selbstwirksamkeit der Kinder</li>
<li>Ermöglicht Qualitätsverbesserung</li>
</ul>

<h3>Kriterien für ein gutes Beschwerdeverfahren</h3>
<ul>
<li><strong>Niederschwellig:</strong> Kinder wissen, wie und wo sie sich beschweren können</li>
<li><strong>Altersgerecht:</strong> Auch ohne Schreiben möglich (malen, erzählen)</li>
<li><strong>Vertraulich:</strong> Option, sich an Vertrauensperson zu wenden</li>
<li><strong>Ernst genommen:</strong> Jede Beschwerde wird bearbeitet</li>
<li><strong>Rückmeldung:</strong> Kinder erfahren, was mit ihrer Beschwerde passiert</li>
</ul>

<h3>Konkrete Umsetzungsideen</h3>
<ul>
<li><strong>Kummer-/Wunschkasten:</strong> Für gemalte oder diktierte Beschwerden</li>
<li><strong>Vertrauensperson:</strong> Ein/e fest benannte/r Ansprechpartner/in</li>
<li><strong>Beschwerde-Ecke:</strong> Fester Ort für Gespräche</li>
<li><strong>Beschwerde-Tier:</strong> Stofftier, dem man "erzählen" kann</li>
<li><strong>Sprechstunde:</strong> Feste Zeiten für Einzelgespräche</li>
</ul>

<h3>Bearbeitung von Beschwerden</h3>
<ol>
<li>Beschwerde ernst nehmen und danken</li>
<li>Situation verstehen (Nachfragen)</li>
<li>Kind einbeziehen in Lösungssuche</li>
<li>Massnahme umsetzen</li>
<li>Rückmeldung an das Kind geben</li>
<li>Dokumentation (intern)</li>
</ol>',
            'position' => 1,
        ]);

        // Step 5.4: Methodenkoffer
        $step4 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Praxisaufgabe: Methodenkoffer erstellen',
            'description' => 'Eigene Methoden für die Praxis entwickeln',
            'step_type' => StepType::Task,
            'position' => 4,
            'points_value' => 40,
            'estimated_minutes' => 60,
        ]);

        Task::create([
            'step_id' => $step4->id,
            'task_type' => TaskType::Project,
            'title' => 'Methodenkoffer Partizipation',
            'instructions' => '<h3>Projekt: Ihren Methodenkoffer erstellen</h3>

<h4>Aufgabe</h4>
<p>Entwickeln Sie einen "Methodenkoffer Partizipation" für Ihre Einrichtung. Dieser soll konkret einsetzbare Materialien und Anleitungen enthalten.</p>

<h4>Mindestinhalt (wählen Sie 4 aus):</h4>
<ol>
<li><strong>Kinderkonferenz-Leitfaden:</strong> Ablaufplan mit Zeitangaben, Ritualen, Visualisierungen</li>
<li><strong>Abstimmungsmaterial:</strong> Gestaltete Karten, Steine, Poster zum Abstimmen</li>
<li><strong>Beschwerdeverfahren:</strong> Kindgerechte Erklärung + Material (z.B. Kummerkasten)</li>
<li><strong>Gesprächskarten:</strong> Bildkarten für Gefühle, Wünsche, Meinungen</li>
<li><strong>Regelplakat-Vorlage:</strong> Template für partizipativ entwickelte Regeln</li>
<li><strong>Stimmungsbarometer:</strong> Visualisierung für tägliches Feedback</li>
</ol>

<h4>Abgabe</h4>
<p>Reichen Sie ein:</p>
<ul>
<li>Beschreibung jeder Methode (inkl. Zielgruppe, Material, Anleitung)</li>
<li>Fotos oder Skizzen der erstellten Materialien</li>
<li>Kurze Reflexion: Wie werden Sie den Koffer einsetzen?</li>
</ul>',
            'max_points' => 40,
            'due_days' => 14,
            'rubric' => [
                ['criterion' => 'Vollständigkeit', 'max_points' => 10, 'description' => 'Mindestens 4 Methoden ausgearbeitet'],
                ['criterion' => 'Praxistauglichkeit', 'max_points' => 15, 'description' => 'Materialien sind direkt einsetzbar'],
                ['criterion' => 'Altersangemessenheit', 'max_points' => 10, 'description' => 'Methoden passen zur Zielgruppe'],
                ['criterion' => 'Kreativität', 'max_points' => 5, 'description' => 'Eigene Ideen und ansprechende Gestaltung'],
            ],
        ]);
    }

    private function createModule6(): void
    {
        $module = Module::create([
            'learning_path_id' => $this->path->id,
            'title' => 'Modul 6: Partizipation, Vielfalt & Inklusion',
            'description' => 'Alle Kinder beteiligen – unabhängig von Voraussetzungen.',
            'position' => 6,
            'unlock_condition' => UnlockCondition::Sequential,
        ]);

        // Step 6.1: Inklusive Partizipation
        $step1 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Partizipation bei besonderen Bedürfnissen',
            'description' => 'Alle Kinder einbeziehen',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 15,
            'estimated_minutes' => 30,
        ]);

        LearningMaterial::create([
            'step_id' => $step1->id,
            'material_type' => MaterialType::Text,
            'title' => 'Inklusive Partizipation gestalten',
            'content' => '<h2>Partizipation für alle Kinder</h2>

<h3>Partizipation bei Sprachbarrieren</h3>
<ul>
<li>Visualisierung nutzen (Bilder, Symbole, Piktogramme)</li>
<li>Dolmetscher-Kinder einbeziehen (vorsichtig!)</li>
<li>Einfache Sprache verwenden</li>
<li>Gebärden ergänzend nutzen</li>
<li>Zeit geben für Übersetzung und Verstehen</li>
</ul>

<h3>Partizipation bei Behinderungen</h3>
<ul>
<li><strong>Körperliche Behinderung:</strong> Räume barrierefrei gestalten, alternative Abstimmungsmethoden</li>
<li><strong>Sinnesbehinderung:</strong> Mehrkanalige Kommunikation (visuell UND auditiv)</li>
<li><strong>Kognitive Beeinträchtigung:</strong> Einfache Sprache, konkrete Beispiele, mehr Zeit</li>
<li><strong>Verhaltensauffälligkeiten:</strong> Struktur geben, Rückzugsmöglichkeiten, 1:1 Begleitung</li>
</ul>

<h3>Partizipation und kulturelle Vielfalt</h3>
<ul>
<li>Verschiedene Perspektiven als Bereicherung sehen</li>
<li>Kultursensibel vorgehen (z.B. Blickkontakt, Geschlechterrollen)</li>
<li>Mehrsprachigkeit als Ressource nutzen</li>
<li>Feste und Traditionen gemeinsam erkunden</li>
</ul>

<h3>Grundprinzip</h3>
<p><em>Nicht fragen: "Kann dieses Kind partizipieren?" Sondern: "Wie kann dieses Kind partizipieren?"</em></p>',
            'position' => 1,
        ]);

        // Step 6.2: Adultismus
        $step2 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Adultismus & Machtkritik',
            'description' => 'Erwachsenenmacht kritisch reflektieren',
            'step_type' => StepType::Material,
            'position' => 2,
            'points_value' => 15,
            'estimated_minutes' => 25,
        ]);

        LearningMaterial::create([
            'step_id' => $step2->id,
            'material_type' => MaterialType::Text,
            'title' => 'Was ist Adultismus?',
            'content' => '<h2>Adultismus verstehen</h2>

<h3>Definition</h3>
<p>Adultismus bezeichnet die Diskriminierung von Kindern und Jugendlichen durch Erwachsene aufgrund ihres Alters. Es ist ein Machtungleichgewicht, das oft unbewusst reproduziert wird.</p>

<h3>Beispiele für Adultismus im Alltag</h3>
<ul>
<li>"Du verstehst das noch nicht."</li>
<li>"Weil ich es sage!"</li>
<li>"Wenn du gross bist, darfst du mitreden."</li>
<li>Über Kinder sprechen, als wären sie nicht anwesend</li>
<li>Kindern Gefühle absprechen: "Das tut doch gar nicht weh."</li>
<li>Entscheidungen "für" statt "mit" Kindern treffen</li>
</ul>

<h3>Warum ist das problematisch?</h3>
<ul>
<li>Kinder lernen, dass ihre Meinung nicht zählt</li>
<li>Selbstwirksamkeit wird geschwächt</li>
<li>Demokratische Bildung wird verhindert</li>
<li>Schutz vor Übergriffen wird erschwert (Kinder trauen sich nicht zu widersprechen)</li>
</ul>

<h3>Adultismus überwinden</h3>
<ul>
<li><strong>Bewusstsein schaffen:</strong> Eigene adultistische Muster erkennen</li>
<li><strong>Sprache überdenken:</strong> Auf Augenhöhe kommunizieren</li>
<li><strong>Macht teilen:</strong> Bewusst Entscheidungsräume öffnen</li>
<li><strong>Fehler zugeben:</strong> Auch Erwachsene liegen mal falsch</li>
<li><strong>Zuhören:</strong> Kinderperspektiven ernst nehmen</li>
</ul>',
            'position' => 1,
        ]);

        // Step 6.3: Perspektivwechsel
        $step3 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Übung: Perspektivwechsel',
            'description' => 'Die Kinderperspektive einnehmen',
            'step_type' => StepType::Task,
            'position' => 3,
            'points_value' => 30,
            'estimated_minutes' => 40,
        ]);

        Task::create([
            'step_id' => $step3->id,
            'task_type' => TaskType::Submission,
            'title' => 'Perspektivwechsel-Übung',
            'instructions' => '<h3>Perspektivwechsel-Übung</h3>

<h4>Teil 1: Kindheitserinnerungen</h4>
<p>Erinnern Sie sich an eine Situation aus Ihrer eigenen Kindheit, in der Sie sich nicht gehört oder übergangen fühlten. Beschreiben Sie:</p>
<ul>
<li>Was ist passiert?</li>
<li>Wie haben Sie sich gefühlt?</li>
<li>Was hätten Sie sich von den Erwachsenen gewünscht?</li>
</ul>

<h4>Teil 2: Alltagsanalyse</h4>
<p>Beobachten Sie einen Tag lang Ihren Betreuungsalltag aus der Perspektive eines Kindes. Stellen Sie sich vor, Sie wären 4 Jahre alt und erleben diesen Tag:</p>
<ul>
<li>Was wird ohne meine Zustimmung entschieden?</li>
<li>Wann werde ich gefragt?</li>
<li>Gibt es Situationen, in denen ich mich machtlos fühle?</li>
<li>Wann fühle ich mich ernst genommen?</li>
</ul>

<h4>Teil 3: Reflexion</h4>
<p>Verbinden Sie beide Teile:</p>
<ul>
<li>Welche Parallelen sehen Sie?</li>
<li>Erkennen Sie adultistische Muster in Ihrem Alltag?</li>
<li>Was möchten Sie verändern?</li>
</ul>',
            'max_points' => 30,
            'due_days' => 7,
        ]);
    }

    private function createModule7(): void
    {
        $module = Module::create([
            'learning_path_id' => $this->path->id,
            'title' => 'Modul 7: Zusammenarbeit mit Eltern & Team',
            'description' => 'Partizipation gemeinsam tragen.',
            'position' => 7,
            'unlock_condition' => UnlockCondition::Sequential,
        ]);

        // Step 7.1: Elternarbeit
        $step1 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Partizipation gegenüber Eltern kommunizieren',
            'description' => 'Transparenz und Dialog mit Familien',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 10,
            'estimated_minutes' => 25,
        ]);

        LearningMaterial::create([
            'step_id' => $step1->id,
            'material_type' => MaterialType::Text,
            'title' => 'Elternkommunikation zu Partizipation',
            'content' => '<h2>Mit Eltern über Partizipation sprechen</h2>

<h3>Warum Transparenz wichtig ist</h3>
<ul>
<li>Eltern verstehen, warum Kinder mitentscheiden</li>
<li>Vermeidung von Missverständnissen ("Die lassen die Kinder ja alles machen!")</li>
<li>Einheitliche Haltung zwischen Einrichtung und Zuhause</li>
<li>Eltern als Partner für Partizipation gewinnen</li>
</ul>

<h3>Kommunikationsstrategien</h3>

<h4>Im Aufnahmegespräch</h4>
<ul>
<li>Partizipationskonzept vorstellen</li>
<li>Konkrete Beispiele nennen</li>
<li>Fragen beantworten, Sorgen ernst nehmen</li>
</ul>

<h4>Regelmässige Information</h4>
<ul>
<li>Dokumentation partizipativer Prozesse (Fotos, Plakate)</li>
<li>Newsletter / Elternbriefe mit Beispielen</li>
<li>Tür-und-Angel-Gespräche nutzen</li>
</ul>

<h4>Bei Widerständen</h4>
<ul>
<li>Sorgen ernst nehmen, nicht belehren</li>
<li>Wissenschaftliche Hintergründe erklären</li>
<li>Grenzen der Partizipation verdeutlichen (Sicherheit)</li>
<li>Erfolgsbeispiele teilen</li>
</ul>

<h3>Häufige Elternfragen</h3>
<ul>
<li><em>"Mein Kind soll doch Regeln lernen!"</em> → Partizipativ entwickelte Regeln werden besser eingehalten</li>
<li><em>"Wer hat hier das Sagen?"</em> → Erwachsene behalten Verantwortung für Schutz und Rahmen</li>
<li><em>"Ist das nicht überfordernden?"</em> → Beteiligung ist altersangemessen und freiwillig</li>
</ul>',
            'position' => 1,
        ]);

        // Step 7.2: Teamarbeit
        $step2 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Partizipation im Team',
            'description' => 'Gemeinsame Haltung entwickeln',
            'step_type' => StepType::Material,
            'position' => 2,
            'points_value' => 10,
            'estimated_minutes' => 20,
        ]);

        LearningMaterial::create([
            'step_id' => $step2->id,
            'material_type' => MaterialType::Text,
            'title' => 'Partizipation als Teamaufgabe',
            'content' => '<h2>Partizipation im Team verankern</h2>

<h3>Gemeinsame Haltung entwickeln</h3>
<ul>
<li>Partizipationsverständnis im Team klären</li>
<li>Gemeinsame Werte formulieren</li>
<li>Verbindliche Absprachen treffen</li>
<li>Regelmässige Reflexion einplanen</li>
</ul>

<h3>Vorbildfunktion</h3>
<p>Partizipation muss auch im Team gelebt werden:</p>
<ul>
<li>Wie werden Teamentscheidungen getroffen?</li>
<li>Werden alle Stimmen gehört?</li>
<li>Gibt es Raum für unterschiedliche Meinungen?</li>
</ul>

<h3>Umgang mit unterschiedlichen Haltungen</h3>
<ul>
<li>Unterschiede offen ansprechen</li>
<li>Gemeinsamen Nenner finden</li>
<li>Verbindlichkeit durch Konzept herstellen</li>
<li>Fortbildungen gemeinsam besuchen</li>
</ul>

<h3>Partizipation im Konzept verankern</h3>
<ul>
<li>Partizipation als Qualitätsmerkmal festschreiben</li>
<li>Konkrete Methoden benennen</li>
<li>Zuständigkeiten klären</li>
<li>Regelmässige Evaluation vorsehen</li>
</ul>',
            'position' => 1,
        ]);

        // Step 7.3: Praxisaufgabe
        $step3 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Praxisaufgabe: Elterngespräch simulieren',
            'description' => 'Partizipation erklären und begründen',
            'step_type' => StepType::Task,
            'position' => 3,
            'points_value' => 30,
            'estimated_minutes' => 40,
        ]);

        Task::create([
            'step_id' => $step3->id,
            'task_type' => TaskType::Submission,
            'title' => 'Elterngespräch simulieren',
            'instructions' => '<h3>Praxisaufgabe: Elterngespräch</h3>

<h4>Szenario</h4>
<p>Eine Mutter kommt zu Ihnen und sagt: "Ich habe gehört, dass die Kinder hier selbst entscheiden, was sie essen und wann sie schlafen. Das kann doch nicht richtig sein! Kinder brauchen Grenzen und Regeln!"</p>

<h4>Ihre Aufgabe</h4>
<p>Schreiben Sie einen Gesprächsleitfaden, wie Sie auf diese Mutter reagieren würden. Berücksichtigen Sie:</p>

<ol>
<li><strong>Aktives Zuhören:</strong> Wie zeigen Sie Verständnis für die Sorgen?</li>
<li><strong>Erklärung:</strong> Wie erklären Sie Partizipation verständlich?</li>
<li><strong>Grenzen:</strong> Wie verdeutlichen Sie, dass es sehr wohl Regeln gibt?</li>
<li><strong>Beispiele:</strong> Welche konkreten Beispiele nutzen Sie?</li>
<li><strong>Einbeziehung:</strong> Wie können Sie die Mutter als Partnerin gewinnen?</li>
</ol>

<h4>Format</h4>
<p>Schreiben Sie das Gespräch als Dialog (mindestens 15 Wechsel) oder als ausführlichen Gesprächsleitfaden mit Formulierungsvorschlägen.</p>',
            'max_points' => 30,
            'due_days' => 7,
            'rubric' => [
                ['criterion' => 'Empathie', 'max_points' => 8, 'description' => 'Verständnis für Elternsorgen wird gezeigt'],
                ['criterion' => 'Fachliche Argumentation', 'max_points' => 10, 'description' => 'Partizipation wird korrekt erklärt'],
                ['criterion' => 'Praxisnähe', 'max_points' => 7, 'description' => 'Konkrete, überzeugende Beispiele'],
                ['criterion' => 'Dialogqualität', 'max_points' => 5, 'description' => 'Respektvoller, partnerschaftlicher Ton'],
            ],
        ]);
    }

    private function createModule8(): void
    {
        $module = Module::create([
            'learning_path_id' => $this->path->id,
            'title' => 'Modul 8: Reflexion & Qualitätsentwicklung',
            'description' => 'Partizipation nachhaltig verankern.',
            'position' => 8,
            'unlock_condition' => UnlockCondition::Sequential,
        ]);

        // Step 8.1: Qualitätsindikatoren
        $step1 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Qualitätsindikatoren für Partizipation',
            'description' => 'Woran erkennt man gelungene Partizipation?',
            'step_type' => StepType::Material,
            'position' => 1,
            'points_value' => 15,
            'estimated_minutes' => 25,
        ]);

        LearningMaterial::create([
            'step_id' => $step1->id,
            'material_type' => MaterialType::Text,
            'title' => 'Qualitätsmerkmale von Partizipation',
            'content' => '<h2>Qualitätsindikatoren für Partizipation</h2>

<h3>Strukturelle Indikatoren</h3>
<ul>
<li>Partizipation ist im Konzept verankert</li>
<li>Regelmässige Beteiligungsgremien existieren (Kinderkonferenz etc.)</li>
<li>Beschwerdeverfahren ist implementiert</li>
<li>Mitarbeitende sind fortgebildet</li>
<li>Zeit für Partizipation ist eingeplant</li>
</ul>

<h3>Prozessindikatoren</h3>
<ul>
<li>Kinder werden regelmässig nach ihrer Meinung gefragt</li>
<li>Entscheidungen werden begründet und transparent gemacht</li>
<li>Ergebnisse werden umgesetzt und dokumentiert</li>
<li>Alle Kinder werden einbezogen (auch die Stillen)</li>
<li>Partizipation findet im Alltag statt, nicht nur bei Events</li>
</ul>

<h3>Ergebnisindikatoren</h3>
<ul>
<li>Kinder kennen ihre Rechte und können diese benennen</li>
<li>Kinder wissen, wie und wo sie sich beschweren können</li>
<li>Kinder erleben, dass ihre Meinung etwas bewirkt</li>
<li>Konflikte werden gemeinsam gelöst</li>
<li>Regeln werden gemeinsam entwickelt und eingehalten</li>
</ul>

<h3>Selbsteinschätzungsfragen</h3>
<ul>
<li>Wie oft frage ich Kinder nach ihrer Meinung?</li>
<li>Bei welchen Themen entscheide ich allein, obwohl Beteiligung möglich wäre?</li>
<li>Kennen die Kinder das Beschwerdeverfahren?</li>
<li>Wie reagiere ich, wenn Kinder "unbequeme" Wünsche äussern?</li>
</ul>',
            'position' => 1,
        ]);

        // Step 8.2: Selbsteinschätzung
        $step2 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Selbsteinschätzungsbogen',
            'description' => 'Eigene Partizipationspraxis reflektieren',
            'step_type' => StepType::Task,
            'position' => 2,
            'points_value' => 25,
            'estimated_minutes' => 30,
        ]);

        Task::create([
            'step_id' => $step2->id,
            'task_type' => TaskType::Submission,
            'title' => 'Selbsteinschätzung zur Partizipation',
            'instructions' => '<h3>Selbsteinschätzungsbogen</h3>

<p>Bewerten Sie Ihre aktuelle Praxis ehrlich (1 = trifft nicht zu, 5 = trifft voll zu):</p>

<h4>Haltung</h4>
<ol>
<li>Ich sehe Kinder als kompetente Gesprächspartner.</li>
<li>Ich bin bereit, Macht zu teilen.</li>
<li>Ich nehme Kindermeinungen ernst, auch wenn sie unbequem sind.</li>
<li>Ich erkläre Kindern, warum manche Entscheidungen nicht verhandelbar sind.</li>
</ol>

<h4>Alltag</h4>
<ol start="5">
<li>Kinder können bei Alltagsentscheidungen mitbestimmen (Essen, Spielen, Ruhen).</li>
<li>Regeln werden gemeinsam mit Kindern entwickelt.</li>
<li>Ich führe regelmässig Kinderkonferenzen oder ähnliche Formate durch.</li>
<li>Konflikte werden partizipativ gelöst.</li>
</ol>

<h4>Strukturen</h4>
<ol start="9">
<li>Es gibt ein funktionierendes Beschwerdeverfahren.</li>
<li>Partizipation ist im Einrichtungskonzept verankert.</li>
<li>Das Team hat eine gemeinsame Haltung zur Partizipation.</li>
<li>Eltern werden über Partizipation informiert.</li>
</ol>

<h4>Auswertung</h4>
<p>Für jede Kategorie:</p>
<ul>
<li>Wo liegen Ihre Stärken?</li>
<li>Wo sehen Sie Entwicklungspotenzial?</li>
<li>Was ist Ihr nächster konkreter Schritt?</li>
</ul>',
            'max_points' => 25,
            'due_days' => 5,
        ]);

        // Step 8.3: Entwicklungsplan
        $step3 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Entwicklungsplan erstellen',
            'description' => 'Konkrete Schritte für mehr Partizipation planen',
            'step_type' => StepType::Task,
            'position' => 3,
            'points_value' => 40,
            'estimated_minutes' => 45,
        ]);

        Task::create([
            'step_id' => $step3->id,
            'task_type' => TaskType::Project,
            'title' => 'Persönlicher Entwicklungsplan',
            'instructions' => '<h3>Abschlussaufgabe: Entwicklungsplan</h3>

<p>Erstellen Sie einen konkreten Entwicklungsplan für mehr Partizipation in Ihrer Einrichtung.</p>

<h4>Teil 1: Bestandsaufnahme</h4>
<ul>
<li>Wo stehen wir aktuell in Sachen Partizipation?</li>
<li>Was läuft bereits gut?</li>
<li>Wo gibt es Entwicklungsbedarf?</li>
</ul>

<h4>Teil 2: Zielsetzung</h4>
<p>Formulieren Sie 3 konkrete, messbare Ziele für die nächsten 3 Monate:</p>
<ul>
<li>Was genau möchten Sie erreichen?</li>
<li>Woran erkennen Sie, dass Sie das Ziel erreicht haben?</li>
</ul>

<h4>Teil 3: Massnahmenplanung</h4>
<p>Für jedes Ziel:</p>
<ul>
<li>Welche konkreten Schritte sind nötig?</li>
<li>Wer ist verantwortlich?</li>
<li>Was brauchen Sie dafür (Material, Zeit, Unterstützung)?</li>
<li>Bis wann soll es umgesetzt sein?</li>
</ul>

<h4>Teil 4: Evaluation</h4>
<ul>
<li>Wie werden Sie den Erfolg messen?</li>
<li>Wann findet die Überprüfung statt?</li>
<li>Wie beziehen Sie die Kinder in die Evaluation ein?</li>
</ul>',
            'max_points' => 40,
            'due_days' => 10,
            'rubric' => [
                ['criterion' => 'Bestandsaufnahme', 'max_points' => 8, 'description' => 'Ehrliche, differenzierte Analyse der Ist-Situation'],
                ['criterion' => 'Zielsetzung', 'max_points' => 10, 'description' => 'SMART-Ziele formuliert'],
                ['criterion' => 'Massnahmen', 'max_points' => 15, 'description' => 'Konkrete, umsetzbare Schritte mit Verantwortlichkeiten'],
                ['criterion' => 'Evaluation', 'max_points' => 7, 'description' => 'Nachvollziehbares Evaluationskonzept'],
            ],
        ]);

        // Step 8.4: Abschlussreflexion
        $step4 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Abschlussreflexion',
            'description' => 'Rückblick auf den gesamten Lernpfad',
            'step_type' => StepType::Task,
            'position' => 4,
            'points_value' => 20,
            'estimated_minutes' => 30,
        ]);

        Task::create([
            'step_id' => $step4->id,
            'task_type' => TaskType::Discussion,
            'title' => 'Abschlussreflexion',
            'instructions' => '<h3>Abschlussreflexion</h3>

<p>Sie haben den Lernpfad "Partizipation in der Betreuungsarbeit" absolviert. Reflektieren Sie Ihren Lernprozess:</p>

<h4>Lerngewinn</h4>
<ul>
<li>Was war für Sie die wichtigste Erkenntnis?</li>
<li>Welches Thema hat Sie am meisten zum Nachdenken gebracht?</li>
<li>Was hat Ihre Haltung zu Partizipation verändert?</li>
</ul>

<h4>Praxistransfer</h4>
<ul>
<li>Was haben Sie bereits in Ihrer Praxis umgesetzt?</li>
<li>Was hat gut funktioniert, was war herausfordernd?</li>
<li>Welche Reaktionen haben Sie bei Kindern, Team oder Eltern beobachtet?</li>
</ul>

<h4>Ausblick</h4>
<ul>
<li>Was sind Ihre nächsten Schritte?</li>
<li>Welche Unterstützung brauchen Sie noch?</li>
<li>Wie werden Sie dranbleiben am Thema?</li>
</ul>

<h4>Offenes Feedback</h4>
<p>Gibt es etwas, das Sie noch loswerden möchten? Feedback zum Lernpfad ist willkommen!</p>',
            'max_points' => 20,
            'due_days' => 7,
        ]);

        // Step 8.5: Abschlussprüfung
        $step5 = LearningStep::create([
            'module_id' => $module->id,
            'title' => 'Abschlussprüfung',
            'description' => 'Umfassende Prüfung zum gesamten Lernpfad',
            'step_type' => StepType::Assessment,
            'position' => 5,
            'points_value' => 100,
            'estimated_minutes' => 45,
        ]);

        $assessment = Assessment::create([
            'step_id' => $step5->id,
            'assessment_type' => AssessmentType::Exam,
            'title' => 'Abschlussprüfung Partizipation',
            'description' => 'Umfassende Prüfung zu allen Modulen des Lernpfads',
            'time_limit_minutes' => 45,
            'passing_score_percent' => 70,
            'max_attempts' => 2,
            'shuffle_questions' => true,
        ]);

        $this->createQuizQuestions($assessment, [
            [
                'type' => QuestionType::SingleChoice,
                'text' => 'Was unterscheidet Partizipation von Mitwirkung?',
                'explanation' => 'Partizipation umfasst die Beteiligung am gesamten Prozess, von der Problemerkennung bis zur Umsetzung.',
                'points' => 10,
                'options' => [
                    ['text' => 'Partizipation bedeutet Teilhabe am gesamten Entscheidungsprozess', 'correct' => true],
                    ['text' => 'Es gibt keinen Unterschied', 'correct' => false],
                    ['text' => 'Mitwirkung ist umfassender als Partizipation', 'correct' => false],
                    ['text' => 'Partizipation ist nur für Erwachsene möglich', 'correct' => false],
                ],
            ],
            [
                'type' => QuestionType::MultipleChoice,
                'text' => 'Welche Stufen gehören nach Schröder zur echten Partizipation?',
                'explanation' => 'Echte Partizipation umfasst Mitbestimmung, Selbstbestimmung und Selbstverwaltung.',
                'points' => 15,
                'options' => [
                    ['text' => 'Mitbestimmung', 'correct' => true],
                    ['text' => 'Selbstbestimmung', 'correct' => true],
                    ['text' => 'Selbstverwaltung', 'correct' => true],
                    ['text' => 'Dekoration', 'correct' => false],
                    ['text' => 'Alibi-Teilnahme', 'correct' => false],
                ],
            ],
            [
                'type' => QuestionType::TrueFalse,
                'text' => 'Laut SGB VIII müssen Einrichtungen ein Beschwerdeverfahren für Kinder haben, um eine Betriebserlaubnis zu erhalten.',
                'explanation' => 'Richtig. § 45 SGB VIII fordert geeignete Verfahren der Beteiligung und Beschwerde.',
                'points' => 10,
                'options' => [
                    ['text' => 'Wahr', 'correct' => true],
                    ['text' => 'Falsch', 'correct' => false],
                ],
            ],
            [
                'type' => QuestionType::SingleChoice,
                'text' => 'Was versteht man unter Adultismus?',
                'explanation' => 'Adultismus bezeichnet die Diskriminierung von Kindern durch Erwachsene aufgrund ihres Alters.',
                'points' => 10,
                'options' => [
                    ['text' => 'Die Diskriminierung von Kindern durch Erwachsene aufgrund ihres Alters', 'correct' => true],
                    ['text' => 'Eine pädagogische Methode', 'correct' => false],
                    ['text' => 'Die Bevorzugung von Erwachsenenbildung', 'correct' => false],
                    ['text' => 'Ein Erziehungsstil', 'correct' => false],
                ],
            ],
            [
                'type' => QuestionType::MultipleChoice,
                'text' => 'Welche Prinzipien gehören zu einer partizipativen Gesprächsführung?',
                'explanation' => 'Partizipative Gesprächsführung umfasst offene Fragen, aktives Zuhören und altersgerechte Methoden.',
                'points' => 15,
                'options' => [
                    ['text' => 'Offene Fragen stellen', 'correct' => true],
                    ['text' => 'Aktives Zuhören', 'correct' => true],
                    ['text' => 'Suggestivfragen verwenden', 'correct' => false],
                    ['text' => 'Auf Augenhöhe kommunizieren', 'correct' => true],
                    ['text' => 'Schnell Lösungen vorgeben', 'correct' => false],
                ],
            ],
            [
                'type' => QuestionType::SingleChoice,
                'text' => 'Bei welchen Entscheidungen können Kinder NICHT mitbestimmen?',
                'explanation' => 'Bei Fragen der Sicherheit und des Schutzes liegt die Verantwortung bei den Erwachsenen.',
                'points' => 10,
                'options' => [
                    ['text' => 'Bei Fragen der Sicherheit und des Kindesschutzes', 'correct' => true],
                    ['text' => 'Bei der Gestaltung des Tagesablaufs', 'correct' => false],
                    ['text' => 'Bei der Entwicklung von Gruppenregeln', 'correct' => false],
                    ['text' => 'Bei der Auswahl von Spielen', 'correct' => false],
                ],
            ],
            [
                'type' => QuestionType::TrueFalse,
                'text' => 'Partizipation bedeutet, dass Kinder alles selbst entscheiden dürfen.',
                'explanation' => 'Falsch. Partizipation bedeutet altersangemessene Beteiligung, nicht grenzenlose Entscheidungsfreiheit.',
                'points' => 10,
                'options' => [
                    ['text' => 'Wahr', 'correct' => false],
                    ['text' => 'Falsch', 'correct' => true],
                ],
            ],
            [
                'type' => QuestionType::SingleChoice,
                'text' => 'Welcher Artikel der UN-Kinderrechtskonvention regelt das Recht auf Beteiligung?',
                'explanation' => 'Artikel 12 der UN-KRK regelt das Recht des Kindes auf Berücksichtigung seiner Meinung.',
                'points' => 10,
                'options' => [
                    ['text' => 'Artikel 12', 'correct' => true],
                    ['text' => 'Artikel 3', 'correct' => false],
                    ['text' => 'Artikel 19', 'correct' => false],
                    ['text' => 'Artikel 28', 'correct' => false],
                ],
            ],
        ]);
    }

    private function createQuizQuestions(Assessment $assessment, array $questions): void
    {
        foreach ($questions as $index => $questionData) {
            $question = Question::create([
                'assessment_id' => $assessment->id,
                'question_type' => $questionData['type'],
                'question_text' => $questionData['text'],
                'explanation' => $questionData['explanation'],
                'points' => $questionData['points'],
                'position' => $index + 1,
            ]);

            foreach ($questionData['options'] as $optionIndex => $option) {
                AnswerOption::create([
                    'question_id' => $question->id,
                    'option_text' => $option['text'],
                    'is_correct' => $option['correct'],
                    'position' => $optionIndex + 1,
                ]);
            }
        }
    }
}
