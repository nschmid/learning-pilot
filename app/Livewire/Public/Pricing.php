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

    #[Url]
    public string $billing = 'yearly';

    public int $studentCount = 100;

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function setBilling(string $billing): void
    {
        $this->billing = $billing;
    }

    public function setStudentCount(int $count): void
    {
        $this->studentCount = max(10, min(2000, $count));
    }

    public function render()
    {
        return view('livewire.public.pricing', [
            'plans' => $this->getPlans(),
            'currencies' => $this->getCurrencies(),
            'isPerStudent' => config('lernpfad.pricing_model') === 'per_student',
        ]);
    }

    protected function getPlans(): array
    {
        $billingConfig = config('lernpfad.plans', []);
        $plans = [];

        foreach ($billingConfig as $key => $plan) {
            $isContactSales = $plan['contact_sales'] ?? false;
            $isPerStudent = ($plan['pricing_type'] ?? 'flat') === 'per_student';
            $minStudents = $plan['min_students'] ?? 10;

            // Get per-student pricing
            $perStudentPrices = $plan['per_student_price'][$this->currency] ?? $plan['per_student_price']['chf'] ?? [];
            $perStudentMonthly = $perStudentPrices['monthly'] ?? null;
            $perStudentYearly = $perStudentPrices['yearly'] ?? null;

            // Calculate total based on student count
            $effectiveStudents = max($this->studentCount, $minStudents);
            $totalMonthly = $perStudentMonthly ? $perStudentMonthly * $effectiveStudents : null;
            $totalYearly = $perStudentYearly ? $perStudentYearly * $effectiveStudents : null;

            $plans[] = [
                'id' => $key,
                'name' => $plan['name'] ?? ucfirst($key),
                'description' => $plan['description'] ?? '',
                'is_per_student' => $isPerStudent,
                'min_students' => $minStudents,
                // Per-student prices
                'per_student_monthly' => $perStudentMonthly,
                'per_student_yearly' => $perStudentYearly,
                'formatted_per_student' => $perStudentMonthly ? $this->formatPrice($perStudentMonthly) : null,
                // Total prices for current student count
                'total_monthly' => $totalMonthly,
                'total_yearly' => $totalYearly,
                'formatted_total' => $this->billing === 'yearly'
                    ? ($totalYearly ? $this->formatPrice($totalYearly) : null)
                    : ($totalMonthly ? $this->formatPrice($totalMonthly) : null),
                'interval' => $this->billing === 'yearly' ? __('pro Jahr') : __('pro Monat'),
                'features' => $plan['features'] ?? [],
                'limits' => $plan['limits'] ?? [],
                'highlighted' => $plan['highlighted'] ?? ($key === 'professional'),
                'contact_sales' => $isContactSales,
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

        if ($price >= 1000) {
            return $symbol.' '.number_format($price, 0, '.', '\'');
        }

        return $symbol.' '.number_format($price, $price == (int)$price ? 0 : 2, '.', '\'');
    }
}
