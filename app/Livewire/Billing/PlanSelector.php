<?php

namespace App\Livewire\Billing;

use App\Services\SubscriptionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Plan wählen - LearningPilot')]
class PlanSelector extends Component
{
    #[Url]
    public string $currency = 'chf';

    public ?string $selectedPlan = null;

    public function setCurrency(string $currency): void
    {
        if (in_array($currency, ['chf', 'eur', 'usd'])) {
            $this->currency = $currency;
        }
    }

    public function selectPlan(string $planId): void
    {
        $this->selectedPlan = $planId;
    }

    public function checkout(string $planId): mixed
    {
        $team = auth()->user()->currentTeam;

        if (!$team) {
            session()->flash('error', __('Bitte wählen Sie zuerst ein Team aus.'));
            return null;
        }

        $plan = config("lernpfad.plans.{$planId}");

        if (!$plan) {
            session()->flash('error', __('Ungültiger Plan.'));
            return null;
        }

        $priceId = $plan['stripe_prices'][$this->currency] ?? $plan['stripe_prices']['chf'];

        // If already subscribed, use Stripe billing portal for plan changes
        if ($team->subscribed()) {
            return redirect($team->billingPortalUrl(route('billing.index')));
        }

        // Create new checkout session
        return $team->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('billing.index') . '?checkout=success',
                'cancel_url' => route('billing.plans') . '?checkout=cancelled',
            ]);
    }

    public function render()
    {
        $plans = collect(config('lernpfad.plans', []))->map(function ($plan, $id) {
            $prices = $plan['prices'][$this->currency] ?? $plan['prices']['chf'];

            return array_merge($plan, [
                'id' => $id,
                'formatted_price' => $this->formatPrice($prices['monthly'], $this->currency),
                'interval' => __('/ Monat'),
            ]);
        })->values()->all();

        return view('livewire.billing.plan-selector', [
            'plans' => $plans,
            'currencies' => [
                'chf' => ['code' => 'CHF', 'symbol' => 'CHF'],
                'eur' => ['code' => 'EUR', 'symbol' => '€'],
                'usd' => ['code' => 'USD', 'symbol' => '$'],
            ],
            'currentPlanId' => $this->getCurrentPlanId(),
        ]);
    }

    protected function formatPrice(int $amount, string $currency): string
    {
        $symbols = ['chf' => 'CHF', 'eur' => '€', 'usd' => '$'];
        $symbol = $symbols[$currency] ?? $currency;

        return $symbol . ' ' . number_format($amount, 0, '.', "'");
    }

    protected function getCurrentPlanId(): ?string
    {
        $team = auth()->user()->currentTeam;

        if (!$team || !$team->subscribed()) {
            return null;
        }

        $priceId = $team->subscription()->stripe_price;
        $plans = config('lernpfad.plans', []);

        foreach ($plans as $id => $plan) {
            if (in_array($priceId, array_values($plan['stripe_prices'] ?? []))) {
                return $id;
            }
        }

        return null;
    }
}
