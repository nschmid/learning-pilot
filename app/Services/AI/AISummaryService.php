<?php

namespace App\Services\AI;

use App\Enums\AiContentType;
use App\Enums\AiServiceType;
use App\Models\AiGeneratedContent;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\User;

class AISummaryService
{
    public function __construct(
        protected AIClientService $client,
        protected AIContextBuilder $contextBuilder,
        protected AIUsageService $usageService,
    ) {}

    /**
     * Generate module summary
     */
    public function generateModuleSummary(Module $module, User $user): AiGeneratedContent
    {
        $this->usageService->checkQuota($user, AiServiceType::Summary);

        // Check for cached content using composite lookup
        $cached = AiGeneratedContent::query()
            ->where('contentable_type', Module::class)
            ->where('contentable_id', $module->id)
            ->where('content_type', AiContentType::Summary)
            ->where('user_id', $user->id)
            ->cached()
            ->first();

        if ($cached) {
            return $cached;
        }

        $context = $this->contextBuilder->buildModuleContext($module, $user);
        $systemPrompt = $this->buildSummarySystemPrompt();
        $userMessage = $this->buildModuleSummaryMessage($context);

        $result = $this->client->createMessage(
            AiServiceType::Summary,
            $systemPrompt,
            [['role' => 'user', 'content' => $userMessage]]
        );

        $this->usageService->logUsage(
            $user,
            AiServiceType::Summary,
            $result['tokens_input'],
            $result['tokens_output'],
            $result['latency_ms'],
            $module
        );

        return AiGeneratedContent::create([
            'contentable_type' => Module::class,
            'contentable_id' => $module->id,
            'user_id' => $user->id,
            'content_type' => AiContentType::Summary,
            'content' => $result['content'],
            'model_used' => $result['model'],
            'tokens_used' => $result['tokens_input'] + $result['tokens_output'],
            'generation_time_ms' => $result['latency_ms'],
            'is_cached' => true,
            'cache_expires_at' => now()->addDays(14),
            'metadata' => [
                'tokens_input' => $result['tokens_input'],
                'tokens_output' => $result['tokens_output'],
                'context' => $context,
            ],
        ]);
    }

    /**
     * Generate flashcards for a module
     */
    public function generateFlashcards(Module $module, User $user, int $count = 10): AiGeneratedContent
    {
        $this->usageService->checkQuota($user, AiServiceType::Summary);

        // Check for cached flashcards using composite lookup
        $cached = AiGeneratedContent::query()
            ->where('contentable_type', Module::class)
            ->where('contentable_id', $module->id)
            ->where('content_type', AiContentType::Flashcard)
            ->where('user_id', $user->id)
            ->whereJsonContains('metadata->count', $count)
            ->cached()
            ->first();

        if ($cached) {
            return $cached;
        }

        $context = $this->contextBuilder->buildModuleContext($module, $user);
        $systemPrompt = $this->buildFlashcardSystemPrompt();
        $userMessage = $this->buildFlashcardMessage($context, $count);

        $result = $this->client->createMessage(
            AiServiceType::Summary,
            $systemPrompt,
            [['role' => 'user', 'content' => $userMessage]]
        );

        $this->usageService->logUsage(
            $user,
            AiServiceType::Summary,
            $result['tokens_input'],
            $result['tokens_output'],
            $result['latency_ms'],
            $module
        );

        return AiGeneratedContent::create([
            'contentable_type' => Module::class,
            'contentable_id' => $module->id,
            'user_id' => $user->id,
            'content_type' => AiContentType::Flashcard,
            'content' => $result['content'],
            'model_used' => $result['model'],
            'tokens_used' => $result['tokens_input'] + $result['tokens_output'],
            'generation_time_ms' => $result['latency_ms'],
            'is_cached' => true,
            'cache_expires_at' => now()->addDays(14),
            'metadata' => [
                'tokens_input' => $result['tokens_input'],
                'tokens_output' => $result['tokens_output'],
                'count' => $count,
                'context' => $context,
            ],
        ]);
    }

    /**
     * Generate key concepts for a step
     */
    public function generateKeyConceptsForStep(LearningStep $step, User $user): AiGeneratedContent
    {
        $this->usageService->checkQuota($user, AiServiceType::Summary);

        // Check for cached concepts using composite lookup
        $cached = AiGeneratedContent::query()
            ->where('contentable_type', LearningStep::class)
            ->where('contentable_id', $step->id)
            ->where('content_type', AiContentType::ConceptBreakdown)
            ->where('user_id', $user->id)
            ->cached()
            ->first();

        if ($cached) {
            return $cached;
        }

        $context = [
            'step' => [
                'title' => $step->title,
                'description' => $step->description,
            ],
            'materials' => $step->materials->map(fn ($m) => [
                'title' => $m->title,
                'content' => $this->truncateContent($m->content, 2000),
            ])->toArray(),
            'module' => [
                'title' => $step->module->title,
            ],
            'path' => [
                'title' => $step->module->learningPath->title,
                'difficulty' => $step->module->learningPath->difficulty->value,
            ],
        ];

        $systemPrompt = $this->buildConceptsSystemPrompt();
        $userMessage = $this->buildConceptsMessage($context);

        $result = $this->client->createMessage(
            AiServiceType::Summary,
            $systemPrompt,
            [['role' => 'user', 'content' => $userMessage]]
        );

        $this->usageService->logUsage(
            $user,
            AiServiceType::Summary,
            $result['tokens_input'],
            $result['tokens_output'],
            $result['latency_ms'],
            $step
        );

        return AiGeneratedContent::create([
            'contentable_type' => LearningStep::class,
            'contentable_id' => $step->id,
            'user_id' => $user->id,
            'content_type' => AiContentType::ConceptBreakdown,
            'content' => $result['content'],
            'model_used' => $result['model'],
            'tokens_used' => $result['tokens_input'] + $result['tokens_output'],
            'generation_time_ms' => $result['latency_ms'],
            'is_cached' => true,
            'cache_expires_at' => now()->addDays(30),
            'metadata' => [
                'tokens_input' => $result['tokens_input'],
                'tokens_output' => $result['tokens_output'],
                'context' => $context,
            ],
        ]);
    }

    /**
     * Parse flashcards from generated content
     */
    public function parseFlashcards(AiGeneratedContent $content): array
    {
        $jsonMatch = [];
        preg_match('/\[[\s\S]*\]/m', $content->content, $jsonMatch);

        if (empty($jsonMatch)) {
            return [];
        }

        try {
            $cards = json_decode($jsonMatch[0], true, 512, JSON_THROW_ON_ERROR);

            return is_array($cards) ? $cards : [];
        } catch (\JsonException) {
            return [];
        }
    }

    protected function buildSummarySystemPrompt(): string
    {
        return <<<'PROMPT'
Du bist ein Experte für das Erstellen von Lernzusammenfassungen.

Erstelle eine klare, gut strukturierte Zusammenfassung die:
1. Die wichtigsten Konzepte hervorhebt
2. Kernpunkte in Aufzählungen darstellt
3. Zusammenhänge zwischen Themen erklärt
4. Praktische Anwendungen nennt

Formatierung:
- Verwende Markdown
- Nutze Überschriften (##) für Hauptthemen
- Verwende Aufzählungen für Kernpunkte
- Halte die Zusammenfassung unter 500 Wörtern
- Schreibe auf Deutsch
PROMPT;
    }

    protected function buildModuleSummaryMessage(array $context): string
    {
        $module = $context['module'];
        $steps = $context['steps'] ?? [];

        $stepsContent = '';
        foreach ($steps as $step) {
            $stepsContent .= "\n### {$step['title']} ({$step['type']})\n";
            foreach ($step['materials'] ?? [] as $material) {
                $stepsContent .= "- {$material['title']}: {$material['content']}\n";
            }
        }

        return <<<MESSAGE
Erstelle eine Zusammenfassung für das folgende Modul:

# {$module['title']}
{$module['description']}

Lernpfad: {$context['learning_path']['title']}
Schwierigkeit: {$context['learning_path']['difficulty']}

Enthaltene Schritte:
{$stepsContent}
MESSAGE;
    }

    protected function buildFlashcardSystemPrompt(): string
    {
        return <<<'PROMPT'
Du bist ein Experte für das Erstellen von Lernkarten (Flashcards).

Erstelle Lernkarten im JSON-Format:
[
  {
    "front": "Frage oder Begriff",
    "back": "Antwort oder Definition",
    "hint": "Optional: Ein kleiner Hinweis"
  }
]

Regeln:
- Fragen sollten präzise und eindeutig sein
- Antworten sollten kurz und merkbar sein
- Variiere die Fragetypen (Definition, Anwendung, Vergleich)
- Antworte NUR mit dem JSON-Array
PROMPT;
    }

    protected function buildFlashcardMessage(array $context, int $count): string
    {
        $module = $context['module'];
        $contentPreview = '';

        foreach ($context['steps'] ?? [] as $step) {
            foreach ($step['materials'] ?? [] as $material) {
                $contentPreview .= "- {$material['title']}: {$material['content']}\n";
            }
        }

        return <<<MESSAGE
Erstelle {$count} Lernkarten für das Modul: {$module['title']}

Basierend auf folgendem Inhalt:
{$contentPreview}

Gib das Ergebnis als JSON-Array zurück.
MESSAGE;
    }

    protected function buildConceptsSystemPrompt(): string
    {
        return <<<'PROMPT'
Du bist ein Experte für das Erklären von Lernkonzepten.

Extrahiere und erkläre die wichtigsten Konzepte aus dem Lernmaterial:
1. Identifiziere 3-5 Kernkonzepte
2. Erkläre jedes Konzept kurz und verständlich
3. Gib praktische Beispiele
4. Zeige Zusammenhänge auf

Formatierung:
- Verwende Markdown
- Ein Abschnitt pro Konzept
- Kurze, klare Erklärungen
- Auf Deutsch
PROMPT;
    }

    protected function buildConceptsMessage(array $context): string
    {
        $step = $context['step'];
        $materialsContent = '';

        foreach ($context['materials'] ?? [] as $material) {
            $materialsContent .= "\n## {$material['title']}\n{$material['content']}\n";
        }

        return <<<MESSAGE
Extrahiere die Schlüsselkonzepte aus diesem Lernschritt:

# {$step['title']}
{$step['description']}

Modul: {$context['module']['title']}
Lernpfad: {$context['path']['title']} ({$context['path']['difficulty']})

Materialien:
{$materialsContent}
MESSAGE;
    }

    protected function truncateContent(?string $content, int $maxLength): string
    {
        if (! $content) {
            return '';
        }

        $stripped = strip_tags($content);

        if (strlen($stripped) <= $maxLength) {
            return $stripped;
        }

        return substr($stripped, 0, $maxLength).'...';
    }
}
