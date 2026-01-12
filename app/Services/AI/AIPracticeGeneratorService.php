<?php

namespace App\Services\AI;

use App\Enums\AiServiceType;
use App\Enums\Difficulty;
use App\Enums\QuestionType;
use App\Models\AiPracticeQuestion;
use App\Models\AiPracticeSession;
use App\Models\LearningPath;
use App\Models\Module;
use App\Models\User;

class AIPracticeGeneratorService
{
    public function __construct(
        protected AIClientService $client,
        protected AIContextBuilder $contextBuilder,
        protected AIUsageService $usageService,
    ) {}

    /**
     * Start a new practice session
     */
    public function startSession(
        User $user,
        LearningPath $path,
        ?Module $module = null,
        ?Difficulty $difficulty = null,
        int $questionCount = 5
    ): AiPracticeSession {
        $this->usageService->checkQuota($user, AiServiceType::PracticeGen);

        $context = $this->contextBuilder->buildPracticeContext(
            $user,
            $path,
            $module,
            $difficulty?->value
        );

        $session = AiPracticeSession::create([
            'user_id' => $user->id,
            'learning_path_id' => $path->id,
            'module_id' => $module?->id,
            'difficulty' => ($difficulty ?? $path->difficulty)->value ?? 'beginner',
            'question_count' => $questionCount,
            'questions_answered' => 0,
            'correct_answers' => 0,
            'status' => 'active',
            'started_at' => now(),
            'focus_areas' => $context,
        ]);

        $this->generateQuestionsForSession($session, $user, $questionCount);

        return $session;
    }

    /**
     * Answer a practice question
     */
    public function answerQuestion(
        AiPracticeQuestion $question,
        mixed $userAnswer
    ): array {
        $isCorrect = $this->evaluateAnswer($question, $userAnswer);

        $question->update([
            'user_answer' => $userAnswer,
            'is_correct' => $isCorrect,
            'answered_at' => now(),
        ]);

        $session = $question->session;
        $session->increment('questions_answered');
        if ($isCorrect) {
            $session->increment('correct_answers');
        }

        if ($session->questions_answered >= $session->question_count) {
            $this->completeSession($session);
        }

        return [
            'is_correct' => $isCorrect,
            'correct_answer' => $question->correct_answer,
            'explanation' => $question->explanation,
            'session_progress' => [
                'answered' => $session->questions_answered,
                'total' => $session->question_count,
                'correct' => $session->correct_answers,
            ],
        ];
    }

    /**
     * Get next question for session
     */
    public function getNextQuestion(AiPracticeSession $session): ?AiPracticeQuestion
    {
        return $session->questions()
            ->whereNull('answered_at')
            ->orderBy('created_at')
            ->first();
    }

    /**
     * Get session results
     */
    public function getSessionResults(AiPracticeSession $session): array
    {
        $questions = $session->questions()
            ->orderBy('created_at')
            ->get();

        return [
            'session' => [
                'id' => $session->id,
                'is_completed' => $session->isCompleted(),
                'difficulty' => $session->difficulty,
                'score_percent' => $session->scorePercent(),
                'correct_answers' => $session->correct_answers,
                'question_count' => $session->question_count,
                'completed_at' => $session->completed_at?->toISOString(),
            ],
            'questions' => $questions->map(fn ($q) => [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'question_type' => $q->question_type->value,
                'options' => $q->options,
                'user_answer' => $q->user_answer,
                'correct_answer' => $q->correct_answer,
                'is_correct' => $q->is_correct,
                'explanation' => $q->explanation,
            ])->toArray(),
        ];
    }

    protected function generateQuestionsForSession(
        AiPracticeSession $session,
        User $user,
        int $count
    ): void {
        $systemPrompt = $this->buildGeneratorSystemPrompt($session);
        $userMessage = $this->buildGeneratorUserMessage($session, $count);

        $result = $this->client->createMessage(
            AiServiceType::PracticeGen,
            $systemPrompt,
            [['role' => 'user', 'content' => $userMessage]]
        );

        $this->usageService->logUsage(
            $user,
            AiServiceType::PracticeGen,
            $result['tokens_input'],
            $result['tokens_output'],
            $result['latency_ms'],
            $result['model'],
            $session
        );

        $questions = $this->parseGeneratedQuestions($result['content']);

        if (empty($questions)) {
            throw new \RuntimeException('Die KI konnte keine gültigen Übungsfragen generieren. Bitte versuche es erneut.');
        }

        foreach ($questions as $index => $questionData) {
            AiPracticeQuestion::create([
                'session_id' => $session->id,
                'question_type' => QuestionType::from($questionData['type'] ?? 'single_choice'),
                'question_text' => $questionData['question'],
                'options' => $questionData['options'] ?? null,
                'correct_answer' => $questionData['correct_answer'],
                'explanation' => $questionData['explanation'] ?? null,
                'difficulty' => $session->difficulty,
                'position' => $index + 1,
            ]);
        }
    }

    protected function evaluateAnswer(AiPracticeQuestion $question, mixed $userAnswer): bool
    {
        $correctAnswer = $question->correct_answer;

        if ($question->question_type === QuestionType::MultipleChoice) {
            $userAnswers = is_array($userAnswer) ? $userAnswer : [$userAnswer];
            $correctAnswers = is_array($correctAnswer) ? $correctAnswer : [$correctAnswer];

            sort($userAnswers);
            sort($correctAnswers);

            return $userAnswers === $correctAnswers;
        }

        if ($question->question_type === QuestionType::TrueFalse) {
            return (bool) $userAnswer === (bool) $correctAnswer;
        }

        if (is_string($userAnswer) && is_string($correctAnswer)) {
            return strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer));
        }

        return $userAnswer === $correctAnswer;
    }

    protected function completeSession(AiPracticeSession $session): void
    {
        $session->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    protected function buildGeneratorSystemPrompt(AiPracticeSession $session): string
    {
        $difficulty = $session->difficulty;

        return <<<PROMPT
Du bist ein Experte für die Erstellung von Übungsfragen.

Generiere Fragen auf dem Schwierigkeitsgrad: {$difficulty}

Antworte NUR mit einem JSON-Array im folgenden Format:
[
  {
    "type": "single_choice",
    "question": "Die Fragestellung",
    "options": ["Option A", "Option B", "Option C", "Option D"],
    "correct_answer": "Option A",
    "explanation": "Kurze Erklärung warum das richtig ist"
  },
  {
    "type": "true_false",
    "question": "Aussage die wahr oder falsch ist",
    "correct_answer": true,
    "explanation": "Erklärung"
  }
]

Fragetypen: single_choice, multiple_choice, true_false
Sprache: Deutsch
PROMPT;
    }

    protected function buildGeneratorUserMessage(AiPracticeSession $session, int $count): string
    {
        $context = $session->focus_areas;
        $pathTitle = $context['learning_path']['title'] ?? 'Unbekannt';
        $moduleTitle = $context['module']['title'] ?? null;

        $contentPreview = '';
        if (isset($context['content'])) {
            foreach (array_slice($context['content'], 0, 3) as $item) {
                $contentPreview .= "- {$item['title']}: {$item['content']}\n";
            }
        }

        $message = "Generiere {$count} Übungsfragen zum Thema:\n\n";
        $message .= "Lernpfad: {$pathTitle}\n";
        if ($moduleTitle) {
            $message .= "Modul: {$moduleTitle}\n";
        }
        $message .= "Schwierigkeit: {$context['target_difficulty']}\n\n";

        if ($contentPreview) {
            $message .= "Basierend auf diesem Inhalt:\n{$contentPreview}\n";
        }

        if (isset($context['user_performance']['weak_areas']) && ! empty($context['user_performance']['weak_areas'])) {
            $message .= "\nSchwächen des Lernenden (mehr Fragen dazu):\n";
            foreach ($context['user_performance']['weak_areas'] as $area) {
                $message .= "- {$area['module_title']}\n";
            }
        }

        return $message;
    }

    protected function parseGeneratedQuestions(string $content): array
    {
        $jsonMatch = [];
        preg_match('/\[[\s\S]*\]/m', $content, $jsonMatch);

        if (empty($jsonMatch)) {
            return [];
        }

        try {
            $questions = json_decode($jsonMatch[0], true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($questions)) {
                return [];
            }

            // Filter out questions missing required fields
            return array_filter($questions, function ($question) {
                return is_array($question)
                    && isset($question['question'])
                    && isset($question['correct_answer']);
            });
        } catch (\JsonException) {
            return [];
        }
    }
}
