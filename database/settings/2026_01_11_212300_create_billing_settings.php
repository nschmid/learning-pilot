<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('billing.default_currency', 'chf');
        $this->migrator->add('billing.supported_currencies', ['chf', 'eur', 'usd']);
        $this->migrator->add('billing.trial_days', 14);
        $this->migrator->add('billing.require_payment_method', false);
    }
};
