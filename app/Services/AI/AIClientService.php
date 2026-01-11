<?php

namespace App\Services\AI;

use App\Enums\AiServiceType;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIClientService
{
    protected string $baseUrl = 'https://api.anthropic.com/v1';

    public function __construct(
        protected string $apiKey,
    ) {}

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
            $response = $this->client()->post('/messages', [
                'model' => $model,
                'max_tokens' => $maxTokens,
                'system' => $systemPrompt,
                'messages' => $messages,
            ]);

            $latencyMs = (int) ((microtime(true) - $startTime) * 1000);

            if (! $response->successful()) {
                Log::error('AI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('AI API request failed: '.$response->body());
            }

            $data = $response->json();

            return [
                'content' => $data['content'][0]['text'] ?? '',
                'model' => $model,
                'tokens_input' => $data['usage']['input_tokens'] ?? 0,
                'tokens_output' => $data['usage']['output_tokens'] ?? 0,
                'latency_ms' => $latencyMs,
                'stop_reason' => $data['stop_reason'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('AI Client Exception', [
                'message' => $e->getMessage(),
                'service_type' => $serviceType->value,
            ]);
            throw $e;
        }
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->timeout(60);
    }

    protected function getDefaultModel(AiServiceType $serviceType): string
    {
        return match ($serviceType) {
            AiServiceType::Tutor, AiServiceType::Practice => config('lernpfad.ai.models.tutor', 'claude-sonnet-4-5-20250929'),
            default => config('lernpfad.ai.models.default', 'claude-haiku-4-5-20251001'),
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
}
