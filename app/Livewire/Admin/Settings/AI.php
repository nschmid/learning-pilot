<?php

namespace App\Livewire\Admin\Settings;

use App\Models\AIUsageLog;
use App\Models\AIUserQuota;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AI extends Component
{
    public int $defaultMonthlyTokens;
    public int $defaultDailyRequests;

    public function mount(): void
    {
        $this->defaultMonthlyTokens = config('lernpfad.ai.default_monthly_tokens', 100000);
        $this->defaultDailyRequests = config('lernpfad.ai.default_daily_requests', 100);
    }

    #[Computed]
    public function aiConfigured(): bool
    {
        return ! empty(config('lernpfad.ai.api_key')) || ! empty(env('ANTHROPIC_API_KEY'));
    }

    #[Computed]
    public function aiConfig(): array
    {
        return [
            'provider' => config('lernpfad.ai.provider', 'anthropic'),
            'default_model' => config('lernpfad.ai.models.default', 'claude-haiku-4-5-20251001'),
            'tutor_model' => config('lernpfad.ai.models.tutor', 'claude-sonnet-4-5-20250929'),
            'practice_model' => config('lernpfad.ai.models.practice', 'claude-sonnet-4-5-20250929'),
        ];
    }

    #[Computed]
    public function usageStats(): array
    {
        $thisMonth = AIUsageLog::where('created_at', '>=', now()->startOfMonth());

        return [
            'total_requests' => $thisMonth->count(),
            'total_tokens' => $thisMonth->sum('tokens_used'),
            'unique_users' => $thisMonth->distinct('user_id')->count('user_id'),
            'avg_tokens_per_request' => $thisMonth->count() > 0
                ? round($thisMonth->sum('tokens_used') / $thisMonth->count())
                : 0,
        ];
    }

    #[Computed]
    public function quotaStats(): array
    {
        return [
            'users_with_quota' => AIUserQuota::count(),
            'users_at_limit' => AIUserQuota::whereRaw('tokens_used_this_month >= monthly_token_limit')->count(),
        ];
    }

    #[Computed]
    public function topFeatures(): array
    {
        return AIUsageLog::select('feature', DB::raw('COUNT(*) as count'), DB::raw('SUM(tokens_used) as tokens'))
            ->where('created_at', '>=', now()->startOfMonth())
            ->groupBy('feature')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'feature' => $row->feature,
                'count' => $row->count,
                'tokens' => $row->tokens,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.settings.ai')
            ->layout('layouts.admin', ['title' => __('KI-Einstellungen')]);
    }
}
