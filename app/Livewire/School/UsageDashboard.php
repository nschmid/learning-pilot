<?php

namespace App\Livewire\School;

use App\Services\SchoolUsageService;
use App\Services\SubscriptionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Nutzung - LearningPilot')]
class UsageDashboard extends Component
{
    public function render()
    {
        $team = auth()->user()->currentTeam;
        $usageService = app(SchoolUsageService::class);
        $subscriptionService = app(SubscriptionService::class);

        return view('livewire.school.usage-dashboard', [
            'stats' => $usageService->getUsageStats($team),
            'currentPlan' => $subscriptionService->getCurrentPlan($team),
            'trialDaysRemaining' => $subscriptionService->getTrialDaysRemaining($team),
        ]);
    }
}
