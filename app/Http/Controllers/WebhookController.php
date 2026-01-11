<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Stripe\Event;

class WebhookController extends CashierController
{
    /**
     * Handle customer subscription created.
     */
    protected function handleCustomerSubscriptionCreated(array $payload): void
    {
        $data = $payload['data']['object'];

        $team = $this->findTeamByStripeId($data['customer']);

        if ($team) {
            Log::info('Subscription created for team', [
                'team_id' => $team->id,
                'subscription_id' => $data['id'],
            ]);

            // Could dispatch event or notification here
        }
    }

    /**
     * Handle customer subscription updated.
     */
    protected function handleCustomerSubscriptionUpdated(array $payload): void
    {
        $data = $payload['data']['object'];

        $team = $this->findTeamByStripeId($data['customer']);

        if ($team) {
            Log::info('Subscription updated for team', [
                'team_id' => $team->id,
                'subscription_id' => $data['id'],
                'status' => $data['status'],
            ]);

            // Handle plan changes
            if ($data['status'] === 'active') {
                $team->update(['is_active' => true]);
            }
        }
    }

    /**
     * Handle customer subscription deleted.
     */
    protected function handleCustomerSubscriptionDeleted(array $payload): void
    {
        $data = $payload['data']['object'];

        $team = $this->findTeamByStripeId($data['customer']);

        if ($team) {
            Log::info('Subscription cancelled for team', [
                'team_id' => $team->id,
                'subscription_id' => $data['id'],
            ]);

            // Grace period handling - don't deactivate immediately
        }
    }

    /**
     * Handle invoice payment succeeded.
     */
    protected function handleInvoicePaymentSucceeded(array $payload): void
    {
        $data = $payload['data']['object'];

        $team = $this->findTeamByStripeId($data['customer']);

        if ($team) {
            Log::info('Payment succeeded for team', [
                'team_id' => $team->id,
                'invoice_id' => $data['id'],
                'amount' => $data['amount_paid'],
            ]);

            // Send payment confirmation notification
            // $team->owner->notify(new PaymentSucceededNotification($data));
        }
    }

    /**
     * Handle invoice payment failed.
     */
    protected function handleInvoicePaymentFailed(array $payload): void
    {
        $data = $payload['data']['object'];

        $team = $this->findTeamByStripeId($data['customer']);

        if ($team) {
            Log::warning('Payment failed for team', [
                'team_id' => $team->id,
                'invoice_id' => $data['id'],
            ]);

            // Send payment failed notification
            // $team->owner->notify(new PaymentFailedNotification($data));
        }
    }

    /**
     * Handle checkout session completed.
     */
    protected function handleCheckoutSessionCompleted(array $payload): void
    {
        $data = $payload['data']['object'];

        $team = $this->findTeamByStripeId($data['customer']);

        if ($team) {
            Log::info('Checkout completed for team', [
                'team_id' => $team->id,
                'session_id' => $data['id'],
            ]);
        }
    }

    /**
     * Find team by Stripe customer ID.
     */
    protected function findTeamByStripeId(string $stripeId): ?Team
    {
        return Team::where('stripe_id', $stripeId)->first();
    }
}
