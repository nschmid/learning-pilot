<?php

namespace App\Livewire\Billing;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Rechnungen - LearningPilot')]
class InvoiceHistory extends Component
{
    public function render()
    {
        $team = auth()->user()->currentTeam;
        $invoices = [];

        if ($team && $team->hasStripeId()) {
            $invoices = $team->invoices()->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'date' => $invoice->date()->format('d.m.Y'),
                    'total' => $invoice->total(),
                    'status' => $invoice->paid ? 'paid' : 'unpaid',
                ];
            })->all();
        }

        return view('livewire.billing.invoice-history', [
            'invoices' => $invoices,
        ]);
    }
}
