<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('Preise - LearningPilot')]
class Pricing extends Component
{
    #[Url]
    public string $currency = 'chf';

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function render()
    {
        return view('livewire.public.pricing', [
            'plans' => $this->getPlans(),
            'currencies' => $this->getCurrencies(),
        ]);
    }

    protected function getPlans(): array
    {
        $billingConfig = config('lernpfad.billing.plans', []);
        $plans = [];

        foreach ($billingConfig as $key => $plan) {
            $price = $plan['prices'][$this->currency] ?? $plan['prices']['chf'] ?? 0;

            $plans[] = [
                'id' => $key,
                'name' => $plan['name'] ?? ucfirst($key),
                'description' => $plan['description'] ?? '',
                'price' => $price,
                'formatted_price' => $this->formatPrice($price),
                'interval' => __('pro Monat'),
                'features' => $plan['features'] ?? [],
                'limits' => $plan['limits'] ?? [],
                'highlighted' => $key === 'professional',
            ];
        }

        return $plans;
    }

    protected function getCurrencies(): array
    {
        return [
            'chf' => ['code' => 'CHF', 'symbol' => 'CHF'],
            'eur' => ['code' => 'EUR', 'symbol' => '€'],
            'usd' => ['code' => 'USD', 'symbol' => '$'],
        ];
    }

    protected function formatPrice(int|float $price): string
    {
        $symbol = $this->getCurrencies()[$this->currency]['symbol'] ?? 'CHF';

        return $symbol.' '.number_format($price, 0, '.', '\'');
    }
}
