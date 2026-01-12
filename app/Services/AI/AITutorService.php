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
        // Determine the context - prefer step, then module, then path
        $contextable = $step ?? $module ?? $path;

        return AiTutorConversation::create([
            'user_id' => $user->id,
            'contextable_type' => $contextable ? get_class($contextable) : null,
            'contextable_id' => $contextable?->id,
            'title' => $title ?? $this->generateTitle($path, $module, $step),
            'is_active' => true,
            'total_tokens_used' => 0,
            'message_count' => 0,
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
        $this->usageService->checkQuota($user, AiServiceType::TutorChat);

        AiTutorMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        $messages = $this->buildMessageHistory($conversation, $userMessage);
        $systemPrompt = $this->buildTutorSystemPrompt($conversation);

        $result = $this->client->createMessage(
            AiServiceType::TutorChat,
            $systemPrompt,
            $messages
        );

        $this->usageService->logUsage(
            $user,
            AiServiceType::TutorChat,
            $result['tokens_input'],
            $result['tokens_output'],
            $result['latency_ms'],
            $result['model'],
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

        $conversation->increment('message_count', 2);
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
        $conversation->update(['is_active' => false]);
    }

    /**
     * Get user's active conversations
     */
    public function getUserConversations(User $user, int $limit = 10): array
    {
        return $user->tutorConversations()
            ->where('is_active', true)
            ->orderByDesc('last_message_at')
            ->limit($limit)
            ->get()
            ->map(fn ($conv) => [
                'id' => $conv->id,
                'title' => $conv->title,
                'last_message_at' => $conv->last_message_at?->toISOString(),
                'total_messages' => $conv->message_count,
                'context_type' => $conv->contextable_type ? class_basename($conv->contextable_type) : null,
                'context_title' => $conv->contextable?->title ?? null,
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
        $contextInfo = '';
        $contextable = $conversation->contextable;

        if ($contextable) {
            $contextInfo .= "Kontext: {$contextable->title}\n";

            // Try to get more context from relationships
            if ($contextable instanceof LearningStep) {
                $contextInfo .= "Typ: Lernschritt\n";
                if ($contextable->module) {
                    $contextInfo .= "Modul: {$contextable->module->title}\n";
                }
            } elseif ($contextable instanceof Module) {
                $contextInfo .= "Typ: Modul\n";
            } elseif ($contextable instanceof LearningPath) {
                $contextInfo .= "Typ: Lernpfad\n";
                $contextInfo .= "Schwierigkeit: {$contextable->difficulty->value}\n";
            }
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
