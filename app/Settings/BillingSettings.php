<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BillingSettings extends Settings
{
    public string $default_currency;

    public array $supported_currencies;

    public int $trial_days;

    public bool $require_payment_method;

    public static function group(): string
    {
        return 'billing';
    }
}
