<?php

namespace App\Services\AI;

use App\Enums\AiServiceType;
use App\Models\AiTutorConversation;
use App\Models\AiTutorMessage;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\User;

class AITutorService
{
    public function __construct(
        protected AIClientService $client,
        protected AIContextBuilder $contextBuilder,
        protected AIUsageService $usageService,
    ) {}

    /**
     * Start a new tutor conversation
     */
    public function startConversation(
        User $user,
        ?LearningPath $path = null,
        ?Module $module = null,
        ?LearningStep $step = null,
        ?string $title = null
    ): AiTutorConversation {
        $context = $this->contextBuilder->buildTutorContext($user, $path, $module, $step);

        return AiTutorConversation::create([
            'user_id' => $user->id,
            'learning_path_id' => $path?->id,
            'module_id' => $module?->id,
            'step_id' => $step?->id,
            'title' => $title ?? $this->generateTitle($path, $module, $step),
            'status' => 'active',
            'system_context' => $context,
            'total_messages' => 0,
            'total_tokens_used' => 0,
        ]);
    }

    /**
     * Send a message in the conversation
     */
    public function sendMessage(
        AiTutorConversation $conversation,
        string $userMessage
    ): AiTutorMessage {
        $user = $conversation->user;
        $this->usageService->checkQuota($user, AiServiceType::Tutor);

        AiTutorMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        $messages = $this->buildMessageHistory($conversation, $userMessage);
        $systemPrompt = $this->buildTutorSystemPrompt($conversation);

        $result = $this->client->createMessage(
            AiServiceType::Tutor,
            $systemPrompt,
            $messages
        );

        $this->usageService->logUsage(
            $user,
            AiServiceType::Tutor,
            $result['tokens_input'],
            $result['tokens_output'],
            $result['latency_ms'],
            $conversation
        );

        $assistantMessage = AiTutorMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $result['content'],
            'model' => $result['model'],
            'tokens_input' => $result['tokens_input'],
            'tokens_output' => $result['tokens_output'],
            'latency_ms' => $result['latency_ms'],
        ]);

        $conversation->increment('total_messages', 2);
        $conversation->increment('total_tokens_used', $result['tokens_input'] + $result['tokens_output']);
        $conversation->update(['last_message_at' => now()]);

        return $assistantMessage;
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory(AiTutorConversation $conversation, int $limit = 50): array
    {
        return $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($msg) => [
                'id' => $msg->id,
                'role' => $msg->role,
                'content' => $msg->content,
                'created_at' => $msg->created_at->toISOString(),
            ])
            ->toArray();
    }

    /**
     * Get a conversation by ID.
     */
    public function getConversation(string $conversationId): ?AiTutorConversation
    {
        return AiTutorConversation::with(['messages', 'user'])
            ->find($conversationId);
    }

    /**
     * Archive a conversation
     */
    public function archiveConversation(AiTutorConversation $conversation): void
    {
        $conversation->update(['status' => 'archived']);
    }

    /**
     * Get user's active conversations
     */
    public function getUserConversations(User $user, int $limit = 10): array
    {
        return $user->tutorConversations()
            ->where('status', 'active')
            ->orderByDesc('last_message_at')
            ->limit($limit)
            ->get()
            ->map(fn ($conv) => [
                'id' => $conv->id,
                'title' => $conv->title,
                'last_message_at' => $conv->last_message_at?->toISOString(),
                'total_messages' => $conv->total_messages,
                'context' => [
                    'path' => $conv->learningPath?->title,
                    'module' => $conv->module?->title,
                    'step' => $conv->learningStep?->title,
                ],
            ])
            ->toArray();
    }

    protected function buildMessageHistory(AiTutorConversation $conversation, string $newMessage): array
    {
        $history = $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at')
            ->limit(20)
            ->get()
            ->map(fn ($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->toArray();

        $history[] = ['role' => 'user', 'content' => $newMessage];

        return $history;
    }

    protected function buildTutorSystemPrompt(AiTutorConversation $conversation): string
    {
        $context = $conversation->system_context;

        $contextInfo = '';
        if (isset($context['learning_path'])) {
            $contextInfo .= "Lernpfad: {$context['learning_path']['title']}\n";
            $contextInfo .= "Schwierigkeit: {$context['learning_path']['difficulty']}\n";
        }
        if (isset($context['module'])) {
            $contextInfo .= "Aktuelles Modul: {$context['module']['title']}\n";
        }
        if (isset($context['step'])) {
            $contextInfo .= "Aktueller Schritt: {$context['step']['title']}\n";
        }
        if (isset($context['enrollment'])) {
            $contextInfo .= "Fortschritt: {$context['enrollment']['progress_percent']}%\n";
        }

        return <<<PROMPT
Du bist ein freundlicher und kompetenter Lern-Tutor für ein E-Learning-System.

Dein Name ist LearningPilot-Tutor.

Lernkontext:
{$contextInfo}

Deine Aufgaben:
1. Beantworte Fragen zum Lernmaterial klar und verständlich
2. Erkläre komplexe Konzepte mit einfachen Worten und Beispielen
3. Stelle Rückfragen, um das Verständnis zu überprüfen (Sokratische Methode)
4. Ermutige den Lernenden und bleibe positiv
5. Bleibe beim Thema des Lernkontexts

Regeln:
- Antworte auf Deutsch
- Halte Antworten fokussiert (max 300 Wörter)
- Verwende Markdown für Formatierung
- Gib keine vollständigen Lösungen für Aufgaben
- Leite stattdessen zum selbständigen Denken an
PROMPT;
    }

    protected function generateTitle(
        ?LearningPath $path,
        ?Module $module,
        ?LearningStep $step
    ): string {
        if ($step) {
            return "Hilfe: {$step->title}";
        }
        if ($module) {
            return "Fragen zu: {$module->title}";
        }
        if ($path) {
            return "Lernpfad: {$path->title}";
        }

        return 'Allgemeine Lernhilfe';
    }
}
