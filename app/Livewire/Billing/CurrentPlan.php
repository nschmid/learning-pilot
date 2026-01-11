<?php

namespace App\Livewire\Billing;

use App\Services\SubscriptionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Abonnement - LearningPilot')]
class CurrentPlan extends Component
{
    public function cancelSubscription(): void
    {
        $team = auth()->user()->currentTeam;

        if (!$team?->subscribed()) {
            session()->flash('error', __('Kein aktives Abonnement gefunden.'));
            return;
        }

        app(SubscriptionService::class)->cancelSubscription($team);

        session()->flash('success', __('Ihr Abonnement wurde gekündigt. Sie haben bis zum Ende der Abrechnungsperiode Zugriff.'));
    }

    public function resumeSubscription(): void
    {
        $team = auth()->user()->currentTeam;

        if (!$team?->subscription()?->cancelled()) {
            session()->flash('error', __('Kein gekündigtes Abonnement gefunden.'));
            return;
        }

        app(SubscriptionService::class)->resumeSubscription($team);

        session()->flash('success', __('Ihr Abonnement wurde reaktiviert.'));
    }

    public function render()
    {
        $subscriptionService = app(SubscriptionService::class);
        $team = auth()->user()->currentTeam;

        return view('livewire.billing.current-plan', [
            'currentPlan' => $subscriptionService->getCurrentPlan($team),
            'usageStats' => $subscriptionService->getUsageStats($team),
            'trialDaysRemaining' => $subscriptionService->getTrialDaysRemaining($team),
            'hasTrialEnded' => $subscriptionService->hasTrialEnded($team),
            'isSubscribed' => $team?->subscribed() ?? false,
            'isCancelled' => $team?->subscription()?->cancelled() ?? false,
            'onGracePeriod' => $team?->subscription()?->onGracePeriod() ?? false,
        ]);
    }
}
