<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Billing extends Component
{
    #[Computed]
    public function stripeConfigured(): bool
    {
        return ! empty(config('cashier.key')) && ! empty(config('cashier.secret'));
    }

    #[Computed]
    public function billingStats(): array
    {
        $teamsWithSubscriptions = Team::whereNotNull('stripe_id')->count();
        $activeSubscriptions = Team::whereHas('subscriptions', function ($q) {
            $q->where('stripe_status', 'active');
        })->count();

        return [
            'teams_with_billing' => $teamsWithSubscriptions,
            'active_subscriptions' => $activeSubscriptions,
            'total_teams' => Team::count(),
        ];
    }

    #[Computed]
    public function recentTransactions(): array
    {
        // Get teams with recent invoice activity
        return Team::whereNotNull('stripe_id')
            ->with('owner')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(fn ($team) => [
                'id' => $team->id,
                'name' => $team->name,
                'owner' => $team->owner?->name ?? '-',
                'stripe_id' => $team->stripe_id,
                'has_subscription' => $team->subscribed(),
            ])
            ->toArray();
    }

    #[Computed]
    public function webhookStatus(): array
    {
        return [
            'url' => url('/stripe/webhook'),
            'configured' => ! empty(config('cashier.webhook.secret')),
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.billing')
            ->layout('layouts.admin', ['title' => __('Abrechnungseinstellungen')]);
    }
}
