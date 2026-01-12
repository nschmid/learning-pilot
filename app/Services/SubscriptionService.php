<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Subscription;

class SubscriptionService
{
    /**
     * Get the current plan configuration for a team.
     */
    public function getCurrentPlan(?Team $team = null): ?array
    {
        $team ??= auth()->user()?->currentTeam;

        if (!$team) {
            return null;
        }

        $subscription = $team->subscription();

        if (!$subscription || !$subscription->active()) {
            return $this->getTrialPlan($team);
        }

        $planId = $subscription->stripe_price;
        $plans = config('lernpfad.plans', []);

        foreach ($plans as $plan) {
            if (in_array($planId, array_values($plan['stripe_prices'] ?? []))) {
                return $plan;
            }
        }

        return null;
    }

    /**
     * Get trial plan info for teams still in trial.
     */
    protected function getTrialPlan(Team $team): ?array
    {
        if (!$team->onTrial()) {
            return null;
        }

        // Return starter plan as trial
        return config('lernpfad.plans.starter');
    }

    /**
     * Create a new subscription for a team.
     */
    public function createSubscription(Team $team, string $planId, string $currency = 'chf'): Subscription
    {
        $plan = config("lernpfad.plans.{$planId}");

        if (!$plan) {
            throw new \InvalidArgumentException("Invalid plan: {$planId}");
        }

        $priceId = $plan['stripe_prices'][$currency] ?? $plan['stripe_prices']['chf'];

        return $team->newSubscription('default', $priceId)->create();
    }

    /**
     * Change the team's subscription plan.
     */
    public function changePlan(Team $team, string $newPlanId, string $currency = 'chf'): void
    {
        $plan = config("lernpfad.plans.{$newPlanId}");

        if (!$plan) {
            throw new \InvalidArgumentException("Invalid plan: {$newPlanId}");
        }

        $priceId = $plan['stripe_prices'][$currency] ?? $plan['stripe_prices']['chf'];

        $team->subscription()->swap($priceId);
    }

    /**
     * Cancel the team's subscription.
     */
    public function cancelSubscription(Team $team): void
    {
        $team->subscription()?->cancel();
    }

    /**
     * Resume a cancelled subscription.
     */
    public function resumeSubscription(Team $team): void
    {
        $team->subscription()?->resume();
    }

    /**
     * Check if a team can use a specific feature.
     */
    public function canUseFeature(string $feature, ?Team $team = null): bool
    {
        $plan = $this->getCurrentPlan($team);

        if (!$plan) {
            return false;
        }

        return $plan['features'][$feature] ?? false;
    }

    /**
     * Check if the team has reached the student limit.
     */
    public function hasReachedStudentLimit(?Team $team = null): bool
    {
        $team ??= auth()->user()?->currentTeam;

        if (!$team) {
            return true;
        }

        $plan = $this->getCurrentPlan($team);
        $limit = $plan['limits']['students'] ?? 0;

        if ($limit === 0 || $limit === -1) {
            return false; // Unlimited
        }

        $currentCount = $team->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'learner'))
            ->count();

        return $currentCount >= $limit;
    }

    /**
     * Check if the team has reached the instructor limit.
     */
    public function hasReachedInstructorLimit(?Team $team = null): bool
    {
        $team ??= auth()->user()?->currentTeam;

        if (!$team) {
            return true;
        }

        $plan = $this->getCurrentPlan($team);
        $limit = $plan['limits']['instructors'] ?? 0;

        if ($limit === 0 || $limit === -1) {
            return false; // Unlimited
        }

        $currentCount = $team->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'instructor'))
            ->count();

        return $currentCount >= $limit;
    }

    /**
     * Check if the team has reached the storage limit.
     */
    public function hasReachedStorageLimit(?Team $team = null): bool
    {
        $team ??= auth()->user()?->currentTeam;

        if (!$team) {
            return true;
        }

        $plan = $this->getCurrentPlan($team);
        $limitGb = $plan['limits']['storage_gb'] ?? 0;

        if ($limitGb === 0 || $limitGb === -1) {
            return false; // Unlimited
        }

        $usedBytes = $this->getStorageUsed($team);
        $limitBytes = $limitGb * 1024 * 1024 * 1024;

        return $usedBytes >= $limitBytes;
    }

    /**
     * Get usage statistics for a team.
     */
    public function getUsageStats(?Team $team = null): array
    {
        $team ??= auth()->user()?->currentTeam;

        if (!$team) {
            return [];
        }

        $plan = $this->getCurrentPlan($team);

        $studentCount = $team->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'learner'))
            ->count();

        $instructorCount = $team->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'instructor'))
            ->count();

        $storageUsed = $this->getStorageUsed($team);
        $storageLimitGb = $plan['limits']['storage_gb'] ?? 5;
        $storageLimitBytes = $storageLimitGb * 1024 * 1024 * 1024;

        return [
            'students' => [
                'current' => $studentCount,
                'limit' => $plan['limits']['students'] ?? 50,
                'percent' => $this->calculatePercentage($studentCount, $plan['limits']['students'] ?? 50),
            ],
            'instructors' => [
                'current' => $instructorCount,
                'limit' => $plan['limits']['instructors'] ?? 3,
                'percent' => $this->calculatePercentage($instructorCount, $plan['limits']['instructors'] ?? 3),
            ],
            'storage' => [
                'used_bytes' => $storageUsed,
                'limit_bytes' => $storageLimitBytes,
                'used_formatted' => $this->formatBytes($storageUsed),
                'limit_formatted' => "{$storageLimitGb} GB",
                'percent' => $this->calculatePercentage($storageUsed, $storageLimitBytes),
            ],
            'ai_requests' => [
                'today' => $this->getAIRequestsToday($team),
                'daily_limit' => $plan['limits']['ai_daily_requests'] ?? 100,
                'percent' => $this->calculatePercentage(
                    $this->getAIRequestsToday($team),
                    $plan['limits']['ai_daily_requests'] ?? 100
                ),
            ],
        ];
    }

    /**
     * Get storage used by a team in bytes.
     */
    protected function getStorageUsed(Team $team): int
    {
        return Cache::remember(
            "team.{$team->id}.storage_used",
            now()->addMinutes(15),
            function () use ($team) {
                // Sum up file sizes from learning materials
                return $team->learningPaths()
                    ->with('modules.steps.materials')
                    ->get()
                    ->flatMap(fn ($path) => $path->modules)
                    ->flatMap(fn ($module) => $module->steps)
                    ->flatMap(fn ($step) => $step->materials)
                    ->sum('file_size');
            }
        );
    }

    /**
     * Get AI requests made today.
     */
    protected function getAIRequestsToday(Team $team): int
    {
        return Cache::remember(
            "team.{$team->id}.ai_requests_today",
            now()->addMinutes(5),
            fn () => \App\Models\AiUsageLog::where('team_id', $team->id)
                ->whereDate('created_at', today())
                ->count()
        );
    }

    /**
     * Calculate percentage, handling edge cases.
     */
    protected function calculatePercentage(int|float $current, int|float $limit): float
    {
        if ($limit <= 0 || $limit === -1) {
            return 0;
        }

        return min(100, round(($current / $limit) * 100, 1));
    }

    /**
     * Format bytes to human readable format.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Get the days remaining in trial.
     */
    public function getTrialDaysRemaining(?Team $team = null): int
    {
        $team ??= auth()->user()?->currentTeam;

        if (!$team || !$team->onTrial()) {
            return 0;
        }

        return now()->diffInDays($team->trial_ends_at, false);
    }

    /**
     * Check if the team's trial has ended.
     */
    public function hasTrialEnded(?Team $team = null): bool
    {
        $team ??= auth()->user()?->currentTeam;

        if (!$team) {
            return true;
        }

        if ($team->subscribed()) {
            return false;
        }

        return !$team->onTrial();
    }
}
