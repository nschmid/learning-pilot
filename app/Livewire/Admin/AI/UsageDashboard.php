<?php

namespace App\Livewire\Admin\AI;

use App\Enums\AiServiceType;
use App\Models\AiFeedbackReport;
use App\Models\AiUsageLog;
use App\Models\AiUserQuota;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UsageDashboard extends Component
{
    public string $period = 'month';

    #[Computed]
    public function totalTokensUsed(): int
    {
        return $this->getUsageQuery()->sum('total_tokens');
    }

    #[Computed]
    public function totalRequests(): int
    {
        return $this->getUsageQuery()->count();
    }

    #[Computed]
    public function successRate(): float
    {
        $total = $this->totalRequests;
        if ($total === 0) {
            return 100;
        }

        $successful = $this->getUsageQuery()->successful()->count();

        return round(($successful / $total) * 100, 1);
    }

    #[Computed]
    public function averageResponseTime(): int
    {
        return (int) $this->getUsageQuery()->avg('response_time_ms');
    }

    #[Computed]
    public function usageByService(): array
    {
        return $this->getUsageQuery()
            ->select('service_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_tokens) as tokens'))
            ->groupBy('service_type')
            ->get()
            ->map(fn ($item) => [
                'service' => AiServiceType::from($item->service_type)->label(),
                'service_key' => $item->service_type,
                'count' => $item->count,
                'tokens' => $item->tokens,
            ])
            ->toArray();
    }

    #[Computed]
    public function dailyUsage(): array
    {
        $days = $this->period === 'week' ? 7 : 30;

        return AiUsageLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as requests'),
            DB::raw('SUM(total_tokens) as tokens')
        )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($item) => [
                'date' => $item->date,
                'requests' => $item->requests,
                'tokens' => $item->tokens,
            ])
            ->toArray();
    }

    #[Computed]
    public function topUsers(): \Illuminate\Support\Collection
    {
        return AiUsageLog::select('user_id', DB::raw('COUNT(*) as requests'), DB::raw('SUM(total_tokens) as tokens'))
            ->where('created_at', '>=', now()->subMonth())
            ->groupBy('user_id')
            ->orderByDesc('tokens')
            ->limit(10)
            ->with('user:id,name,email')
            ->get();
    }

    #[Computed]
    public function activeQuotas(): int
    {
        return AiUserQuota::where('last_request_at', '>=', now()->subDays(7))->count();
    }

    #[Computed]
    public function quotasNearLimit(): int
    {
        return AiUserQuota::where('monthly_token_limit', '>', 0)
            ->whereRaw('tokens_used_this_month >= monthly_token_limit * 0.9')
            ->count();
    }

    #[Computed]
    public function unresolvedFeedback(): int
    {
        return AiFeedbackReport::unresolved()->count();
    }

    #[Computed]
    public function recentErrors()
    {
        return AiUsageLog::failed()
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    protected function getUsageQuery()
    {
        $query = AiUsageLog::query();

        return match ($this->period) {
            'week' => $query->where('created_at', '>=', now()->subWeek()),
            'month' => $query->where('created_at', '>=', now()->subMonth()),
            'year' => $query->where('created_at', '>=', now()->subYear()),
            default => $query->where('created_at', '>=', now()->subMonth()),
        };
    }

    public function render()
    {
        return view('livewire.admin.ai.usage-dashboard')
            ->layout('layouts.admin', ['title' => __('KI-Nutzungsstatistiken')]);
    }
}
