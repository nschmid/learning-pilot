<?php

namespace App\Services\AI;

use App\Enums\AiContentType;
use App\Enums\AiServiceType;
use App\Models\AiGeneratedContent;
use App\Models\QuestionResponse;
use App\Models\StepProgress;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AIExplanationService
{
    public function __construct(
        protected AIClientService $client,
        protected AIContextBuilder $contextBuilder,
        protected AIUsageService $usageService,
    ) {}

    /**
     * Generate explanation for wrong answer
     */
    public function generateExplanation(QuestionResponse $response, User $user): AiGeneratedContent
    {
        $this->usageService->checkQuota($user, AiServiceType::Explanation);

        // Check for cached content using composite lookup
        $cached = AiGeneratedContent::query()
            ->where('contentable_type', QuestionResponse::class)
            ->where('contentable_id', $response->id)
            ->where('content_type', AiContentType::Explanation)
            ->where('user_id', $user->id)
            ->cached()
            ->first();

        if ($cached) {
            return $cached;
        }

        $context = $this->contextBuilder->buildQuestionContext($response);
        $systemPrompt = $this->buildExplanationSystemPrompt();
        $userMessage = $this->buildExplanationUserMessage($context);

        $result = $this->client->createMessage(
            AiServiceType::Explanation,
            $systemPrompt,
            [['role' => 'user', 'content' => $userMessage]]
        );

        $this->usageService->logUsage(
            $user,
            AiServiceType::Explanation,
            $result['tokens_input'],
            $result['tokens_output'],
            $result['latency_ms'],
            $response
        );

        return AiGeneratedContent::create([
            'contentable_type' => QuestionResponse::class,
            'contentable_id' => $response->id,
            'user_id' => $user->id,
            'content_type' => AiContentType::Explanation,
            'content' => $result['content'],
            'content_metadata' => [
                'model' => $result['model'],
                'tokens_input' => $result['tokens_input'],
                'tokens_output' => $result['tokens_output'],
                'latency_ms' => $result['latency_ms'],
            ],
            'context_snapshot' => $context,
            'cache_key' => "explanation:{$response->id}",
            'expires_at' => now()->addDays(30),
        ]);
    }

    /**
     * Generate progressive hint for step
     */
    public function generateHint(StepProgress $progress, User $user, int $hintLevel = 1): AiGeneratedContent
    {
        $this->usageService->checkQuota($user, AiServiceType::Explanation);

        // Check for cached hint at this level
        $cacheKey = "hint:{$progress->id}:level:{$hintLevel}";
        $cached = AiGeneratedContent::query()
            ->where('cache_key', $cacheKey)
            ->cached()
            ->first();

        if ($cached) {
            return $cached;
        }

        $context = $this->contextBuilder->buildStepContext($progress);
        $context['hint_level'] = $hintLevel;

        $systemPrompt = $this->buildHintSystemPrompt($hintLevel);
        $userMessage = $this->buildHintUserMessage($context);

        $result = $this->client->createMessage(
            AiServiceType::Explanation,
            $systemPrompt,
            [['role' => 'user', 'content' => $userMessage]]
        );

        $this->usageService->logUsage(
            $user,
            AiServiceType::Explanation,
            $result['tokens_input'],
            $result['tokens_output'],
            $result['latency_ms'],
            $progress
        );

        return AiGeneratedContent::create([
            'contentable_type' => StepProgress::class,
            'contentable_id' => $progress->id,
            'user_id' => $user->id,
            'content_type' => AiContentType::Hint,
            'content' => $result['content'],
            'content_metadata' => [
                'model' => $result['model'],
                'tokens_input' => $result['tokens_input'],
                'tokens_output' => $result['tokens_output'],
                'latency_ms' => $result['latency_ms'],
                'hint_level' => $hintLevel,
            ],
            'context_snapshot' => $context,
            'cache_key' => "hint:{$progress->id}:level:{$hintLevel}",
            'expires_at' => now()->addDays(7),
        ]);
    }

    protected function buildExplanationSystemPrompt(): string
    {
        return <<<'PROMPT'
Du bist ein geduldiger Tutor, der Lernenden hilft, ihre Fehler zu verstehen.

Deine Aufgabe:
1. Erkläre freundlich, warum die gegebene Antwort falsch ist
2. Erkläre die richtige Antwort und warum sie korrekt ist
3. Gib einen hilfreichen Tipp, um ähnliche Fehler zu vermeiden
4. Verwende einfache Sprache und konkrete Beispiele

Formatierung:
- Verwende Absätze für bessere Lesbarkeit
- Halte die Erklärung unter 300 Wörtern
- Sei ermutigend, nicht kritisch
PROMPT;
    }

    protected function buildExplanationUserMessage(array $context): string
    {
        $question = $context['question'];
        $userAnswer = is_array($context['user_answer'])
            ? implode(', ', $context['user_answer'])
            : $context['user_answer'];
        $correctAnswer = is_array($context['correct_answer'])
            ? implode(', ', $context['correct_answer'])
            : $context['correct_answer'];

        return <<<MESSAGE
Frage: {$question['text']}
Typ: {$question['type']}

Antwort des Lernenden: {$userAnswer}
Richtige Antwort: {$correctAnswer}

Kontext:
- Lernschritt: {$context['learning_context']['step_title']}
- Modul: {$context['learning_context']['module_title']}
- Schwierigkeitsgrad: {$context['learning_context']['difficulty']}

Bitte erkläre dem Lernenden, warum seine Antwort falsch war und hilf ihm, das Konzept zu verstehen.
MESSAGE;
    }

    protected function buildHintSystemPrompt(int $hintLevel): string
    {
        $specificity = match ($hintLevel) {
            1 => 'Gib einen allgemeinen Hinweis, der in die richtige Richtung weist, ohne die Lösung zu verraten.',
            2 => 'Gib einen spezifischeren Hinweis, der das relevante Konzept oder die Methode benennt.',
            3 => 'Gib einen detaillierten Hinweis mit konkreten Schritten, aber verrate noch nicht die vollständige Lösung.',
            default => 'Erkläre den Lösungsweg schrittweise und ausführlich.',
        };

        return <<<PROMPT
Du bist ein hilfreicher Tutor, der Lernenden bei Aufgaben hilft.

Hinweis-Level: {$hintLevel} von 4
{$specificity}

Regeln:
- Sei ermutigend und unterstützend
- Verwende einfache Sprache
- Beziehe dich auf das Lernmaterial
- Halte den Hinweis unter 150 Wörtern
PROMPT;
    }

    protected function buildHintUserMessage(array $context): string
    {
        $step = $context['step'];
        $progress = $context['progress'];

        $materialsText = '';
        foreach ($context['materials'] as $material) {
            $materialsText .= "- {$material['title']}: {$material['content_preview']}\n";
        }

        return <<<MESSAGE
Der Lernende arbeitet an folgendem Schritt:

Titel: {$step['title']}
Beschreibung: {$step['description']}
Typ: {$step['type']}

Bisheriger Fortschritt:
- Status: {$progress['status']}
- Zeit verbracht: {$progress['time_spent_minutes']} Minuten

Verfügbare Materialien:
{$materialsText}

Bitte gib einen Hinweis auf Level {$context['hint_level']}.
MESSAGE;
    }
}
