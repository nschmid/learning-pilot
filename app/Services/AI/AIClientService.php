<?php

namespace App\Services\AI;

use App\Enums\AiServiceType;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

class AIClientService
{
    protected string $provider;

    public function __construct()
    {
        $this->provider = config('lernpfad.ai.provider', 'anthropic');
    }

    public function createMessage(
        AiServiceType $serviceType,
        string $systemPrompt,
        array $messages,
        ?string $model = null,
        ?int $maxTokens = null,
    ): array {
        $model = $model ?? $this->getDefaultModel($serviceType);
        $maxTokens = $maxTokens ?? $this->getMaxTokens($serviceType);

        $startTime = microtime(true);

        try {
            $prismMessages = $this->convertMessages($messages);

            $response = Prism::text()
                ->using($this->provider, $model)
                ->withSystemPrompt($systemPrompt)
                ->withMessages($prismMessages)
                ->withMaxTokens($maxTokens)
                ->asText();

            $latencyMs = (int) ((microtime(true) - $startTime) * 1000);

            return [
                'content' => $response->text,
                'model' => $model,
                'tokens_input' => $response->usage->promptTokens,
                'tokens_output' => $response->usage->completionTokens,
                'latency_ms' => $latencyMs,
                'stop_reason' => $response->finishReason->value ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('AI Client Exception', [
                'message' => $e->getMessage(),
                'service_type' => $serviceType->value,
                'provider' => $this->provider,
                'model' => $model,
            ]);
            throw $e;
        }
    }

    /**
     * Convert array messages to Prism message objects.
     */
    protected function convertMessages(array $messages): array
    {
        return array_map(function ($message) {
            $role = $message['role'] ?? 'user';
            $content = $message['content'] ?? '';

            return match ($role) {
                'assistant' => new AssistantMessage($content),
                default => new UserMessage($content),
            };
        }, $messages);
    }

    /**
     * Get the default model for a service type based on the configured provider.
     */
    protected function getDefaultModel(AiServiceType $serviceType): string
    {
        $configKey = match ($serviceType) {
            AiServiceType::Tutor, AiServiceType::Practice => 'tutor',
            AiServiceType::Summary => 'summary',
            default => 'default',
        };

        // First, check for explicit model override in config
        $model = config("lernpfad.ai.models.{$configKey}");
        if ($model) {
            return $model;
        }

        // Then, use provider-specific defaults from config
        $providerModel = config("lernpfad.ai.provider_models.{$this->provider}.{$configKey}");
        if ($providerModel) {
            return $providerModel;
        }

        // Fallback to hardcoded defaults if config is missing
        return match ($this->provider) {
            'anthropic' => 'claude-haiku-4-5-20251001',
            'openai' => 'gpt-4o-mini',
            'mistral' => 'mistral-small-latest',
            'groq' => 'llama-3.3-70b-versatile',
            'gemini' => 'gemini-1.5-flash',
            'deepseek' => 'deepseek-chat',
            'ollama' => 'llama3.2',
            default => 'claude-haiku-4-5-20251001',
        };
    }

    protected function getMaxTokens(AiServiceType $serviceType): int
    {
        return match ($serviceType) {
            AiServiceType::Tutor => 2048,
            AiServiceType::Practice => 4096,
            AiServiceType::Summary => 2048,
            default => 1024,
        };
    }

    /**
     * Get the currently configured provider.
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Get list of supported providers.
     */
    public static function getSupportedProviders(): array
    {
        return [
            'anthropic' => 'Anthropic (Claude)',
            'openai' => 'OpenAI (GPT)',
            'mistral' => 'Mistral AI',
            'groq' => 'Groq',
            'gemini' => 'Google Gemini',
            'deepseek' => 'DeepSeek',
            'ollama' => 'Ollama (Local)',
            'openrouter' => 'OpenRouter',
        ];
    }
}
