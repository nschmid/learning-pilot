<?php

namespace App\Livewire\Billing;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Zahlungsmethoden - LearningPilot')]
class PaymentMethods extends Component
{
    public function redirectToPortal(): mixed
    {
        $team = auth()->user()->currentTeam;

        if (!$team || !$team->hasStripeId()) {
            session()->flash('error', __('Bitte wählen Sie zuerst einen Plan.'));
            return null;
        }

        return redirect($team->billingPortalUrl(route('billing.payment-methods')));
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;
        $paymentMethods = [];
        $defaultMethod = null;

        if ($team && $team->hasStripeId()) {
            $methods = $team->paymentMethods();
            $defaultMethod = $team->defaultPaymentMethod();

            $paymentMethods = $methods->map(function ($method) use ($defaultMethod) {
                return [
                    'id' => $method->id,
                    'brand' => $method->card->brand,
                    'last4' => $method->card->last4,
                    'exp_month' => str_pad($method->card->exp_month, 2, '0', STR_PAD_LEFT),
                    'exp_year' => $method->card->exp_year,
                    'is_default' => $defaultMethod && $method->id === $defaultMethod->id,
                ];
            })->all();
        }

        return view('livewire.billing.payment-methods', [
            'paymentMethods' => $paymentMethods,
        ]);
    }
}
