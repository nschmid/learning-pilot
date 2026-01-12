<?php

namespace App\Services\AI;

use App\Enums\AiServiceType;
use App\Exceptions\AIQuotaExceededException;
use App\Models\AiUsageLog;
use App\Models\AiUserQuota;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AIUsageService
{
    /**
     * Check if user has quota available
     */
    public function checkQuota(User $user, AiServiceType $serviceType): void
    {
        $quota = $this->getOrCreateQuota($user);

        if (! $this->isFeatureEnabled($quota, $serviceType)) {
            throw new AIQuotaExceededException(
                "Feature {$serviceType->value} is not enabled for this user."
            );
        }

        if ($this->isMonthlyTokenLimitReached($quota)) {
            throw new AIQuotaExceededException(
                'Monthly token limit reached. Resets on '.$this->getNextResetDate($quota)->format('d.m.Y')
            );
        }

        if ($this->isDailyRequestLimitReached($quota)) {
            throw new AIQuotaExceededException(
                'Daily request limit reached. Resets at midnight.'
            );
        }
    }

    /**
     * Log AI usage and update quotas
     */
    public function logUsage(
        User $user,
        AiServiceType $serviceType,
        int $tokensInput,
        int $tokensOutput,
        int $latencyMs,
        ?Model $contentable = null
    ): AiUsageLog {
        $totalTokens = $tokensInput + $tokensOutput;

        $log = AiUsageLog::create([
            'user_id' => $user->id,
            'service_type' => $serviceType,
            'tokens_input' => $tokensInput,
            'tokens_output' => $tokensOutput,
            'tokens_total' => $totalTokens,
            'latency_ms' => $latencyMs,
            'contentable_type' => $contentable ? get_class($contentable) : null,
            'contentable_id' => $contentable?->id,
        ]);

        $this->updateQuotaCounters($user, $totalTokens);

        return $log;
    }

    /**
     * Get today's usage statistics for a user
     */
    public function getTodayUsage(User $user): array
    {
        $logs = AiUsageLog::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->get();

        return [
            'requests' => $logs->count(),
            'tokens' => $logs->sum('tokens_total') ?? 0,
            'by_service' => $logs->groupBy('service_type')
                ->map(fn ($items) => [
                    'requests' => $items->count(),
                    'tokens' => $items->sum('tokens_total'),
                ])
                ->toArray(),
        ];
    }

    /**
     * Get monthly usage statistics for a user
     */
    public function getMonthlyUsage(User $user): array
    {
        $logs = AiUsageLog::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();

        return [
            'requests' => $logs->count(),
            'tokens' => $logs->sum('tokens_total') ?? 0,
            'by_service' => $logs->groupBy('service_type')
                ->map(fn ($items) => [
                    'requests' => $items->count(),
                    'tokens' => $items->sum('tokens_total'),
                ])
                ->toArray(),
            'daily_breakdown' => $logs->groupBy(fn ($log) => $log->created_at->format('Y-m-d'))
                ->map(fn ($items) => [
                    'requests' => $items->count(),
                    'tokens' => $items->sum('tokens_total'),
                ])
                ->toArray(),
        ];
    }

    /**
     * Get remaining quota for user
     */
    public function getRemainingQuota(User $user): array
    {
        $quota = $this->getOrCreateQuota($user);

        return [
            'tokens' => [
                'used' => $quota->tokens_used_this_month,
                'limit' => $quota->monthly_token_limit,
                'remaining' => max(0, $quota->monthly_token_limit - $quota->tokens_used_this_month),
                'percent_used' => $quota->monthly_token_limit > 0
                    ? round(($quota->tokens_used_this_month / $quota->monthly_token_limit) * 100, 1)
                    : 0,
            ],
            'requests' => [
                'used' => $quota->requests_today,
                'limit' => $quota->daily_request_limit,
                'remaining' => max(0, $quota->daily_request_limit - $quota->requests_today),
            ],
            'resets' => [
                'monthly' => $this->getNextResetDate($quota)->toISOString(),
                'daily' => now()->endOfDay()->toISOString(),
            ],
            'features' => [
                'explanations' => $quota->feature_explanations_enabled,
                'tutor' => $quota->feature_tutor_enabled,
                'practice' => $quota->feature_practice_enabled,
                'summaries' => $quota->feature_summaries_enabled,
            ],
        ];
    }

    /**
     * Get or create quota for user
     */
    public function getOrCreateQuota(User $user): AiUserQuota
    {
        return AiUserQuota::firstOrCreate(
            ['user_id' => $user->id],
            [
                'monthly_token_limit' => config('lernpfad.ai.default_monthly_tokens', 100000),
                'daily_request_limit' => config('lernpfad.ai.default_daily_requests', 100),
                'tokens_used_this_month' => 0,
                'requests_today' => 0,
                'last_request_at' => null,
                'month_reset_at' => now()->startOfMonth(),
                'feature_explanations_enabled' => true,
                'feature_tutor_enabled' => true,
                'feature_practice_enabled' => true,
                'feature_summaries_enabled' => true,
            ]
        );
    }

    protected function isFeatureEnabled(AiUserQuota $quota, AiServiceType $serviceType): bool
    {
        return match ($serviceType) {
            AiServiceType::Explanation => $quota->feature_explanations_enabled,
            AiServiceType::TutorChat => $quota->feature_tutor_enabled,
            AiServiceType::PracticeGen => $quota->feature_practice_enabled,
            AiServiceType::Summary => $quota->feature_summaries_enabled,
            default => true,
        };
    }

    protected function isMonthlyTokenLimitReached(AiUserQuota $quota): bool
    {
        $this->resetMonthlyCounterIfNeeded($quota);

        return $quota->tokens_used_this_month >= $quota->monthly_token_limit;
    }

    protected function isDailyRequestLimitReached(AiUserQuota $quota): bool
    {
        $this->resetDailyCounterIfNeeded($quota);

        return $quota->requests_today >= $quota->daily_request_limit;
    }

    protected function updateQuotaCounters(User $user, int $tokensUsed): void
    {
        $quota = $this->getOrCreateQuota($user);

        $this->resetMonthlyCounterIfNeeded($quota);
        $this->resetDailyCounterIfNeeded($quota);

        $quota->increment('tokens_used_this_month', $tokensUsed);
        $quota->increment('requests_today');
        $quota->update(['last_request_at' => now()]);
    }

    protected function resetMonthlyCounterIfNeeded(AiUserQuota $quota): void
    {
        if ($quota->month_reset_at->startOfMonth()->lt(now()->startOfMonth())) {
            $quota->update([
                'tokens_used_this_month' => 0,
                'month_reset_at' => now()->startOfMonth(),
            ]);
        }
    }

    protected function resetDailyCounterIfNeeded(AiUserQuota $quota): void
    {
        if ($quota->last_request_at && $quota->last_request_at->isYesterday()) {
            $quota->update(['requests_today' => 0]);
        }
    }

    protected function getNextResetDate(AiUserQuota $quota): Carbon
    {
        return $quota->month_reset_at->addMonth()->startOfMonth();
    }

    /**
     * Calculate estimated cost for token usage.
     *
     * @param  int  $inputTokens  Number of input tokens
     * @param  int  $outputTokens  Number of output tokens
     * @param  string|null  $model  Model identifier (defaults to haiku)
     * @return array{input_cost: float, output_cost: float, total_cost: float, currency: string}
     */
    public function calculateCost(int $inputTokens, int $outputTokens, ?string $model = null): array
    {
        // Pricing per 1M tokens (as of 2024, approximate values in USD)
        $pricing = [
            'claude-haiku-4-5-20251001' => ['input' => 0.25, 'output' => 1.25],
            'claude-sonnet-4-5-20250929' => ['input' => 3.00, 'output' => 15.00],
            'claude-opus-4-5-20250929' => ['input' => 15.00, 'output' => 75.00],
            'default' => ['input' => 0.25, 'output' => 1.25],
        ];

        $modelPricing = $pricing[$model] ?? $pricing['default'];

        $inputCost = ($inputTokens / 1_000_000) * $modelPricing['input'];
        $outputCost = ($outputTokens / 1_000_000) * $modelPricing['output'];

        return [
            'input_cost' => round($inputCost, 6),
            'output_cost' => round($outputCost, 6),
            'total_cost' => round($inputCost + $outputCost, 6),
            'currency' => 'USD',
        ];
    }

    /**
     * Get aggregated usage statistics for a user.
     */
    public function getUsageStats(User $user): array
    {
        $quota = $this->getOrCreateQuota($user);
        $today = $this->getTodayUsage($user);
        $monthly = $this->getMonthlyUsage($user);

        return [
            'quota' => [
                'tokens_used' => $quota->tokens_used_this_month,
                'tokens_limit' => $quota->monthly_token_limit,
                'tokens_remaining' => max(0, $quota->monthly_token_limit - $quota->tokens_used_this_month),
                'requests_today' => $quota->requests_today,
                'requests_limit' => $quota->daily_request_limit,
            ],
            'today' => $today,
            'monthly' => $monthly,
            'estimated_cost' => $this->calculateCost(
                $monthly['tokens'] ?? 0,
                0, // Output tokens tracked separately if needed
                config('lernpfad.ai.models.default')
            ),
        ];
    }
}
