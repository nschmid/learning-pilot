<?php

use App\Livewire\Billing\CurrentPlan;
use App\Livewire\Billing\InvoiceHistory;
use App\Livewire\Billing\PaymentMethods;
use App\Livewire\Billing\PlanSelector;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Billing Routes
|--------------------------------------------------------------------------
|
| These routes handle subscription management, plan selection, and payment
| processing using Laravel Cashier with Stripe.
|
*/

Route::middleware(['auth', 'verified'])->prefix('billing')->name('billing.')->group(function () {
    // Current subscription status
    Route::get('/', CurrentPlan::class)->name('index');

    // Plan selection and checkout
    Route::get('/plans', PlanSelector::class)->name('plans');

    // Invoice history
    Route::get('/invoices', InvoiceHistory::class)->name('invoices');

    // Payment methods management
    Route::get('/payment-methods', PaymentMethods::class)->name('payment-methods');

    // Redirect to Stripe billing portal
    Route::get('/portal', function () {
        $team = auth()->user()->currentTeam;

        if (!$team || !$team->hasStripeId()) {
            return redirect()->route('billing.plans')
                ->with('error', __('Bitte wählen Sie zuerst einen Plan.'));
        }

        return $team->redirectToBillingPortal(route('billing.index'));
    })->name('portal');

    // Download invoice PDF
    Route::get('/invoices/{invoice}', function (string $invoice) {
        $team = auth()->user()->currentTeam;

        return $team->downloadInvoice($invoice, [
            'vendor' => config('app.name'),
            'product' => __('LearningPilot Abonnement'),
        ]);
    })->name('invoices.download');
});
