<?php

namespace App\Livewire\Learner\AI;

use App\Models\AiUserQuota;
use App\Services\AI\AIUsageService;
use Livewire\Component;

class UsageStats extends Component
{
    public function render()
    {
        $user = auth()->user();
        $usageService = app(AIUsageService::class);

        $quota = AiUserQuota::where('user_id', $user->id)->first();
        $todayData = $usageService->getTodayUsage($user);
        $monthlyData = $usageService->getMonthlyUsage($user);

        return view('livewire.learner.ai.usage-stats', [
            'quota' => $quota,
            'todayUsage' => $todayData['requests'] ?? 0,
            'monthlyUsage' => $monthlyData['tokens'] ?? 0,
            'dailyLimit' => $quota?->daily_request_limit ?? config('lernpfad.ai.default_daily_requests', 100),
            'monthlyTokenLimit' => $quota?->monthly_token_limit ?? config('lernpfad.ai.default_monthly_tokens', 100000),
        ]);
    }
}
