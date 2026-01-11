<x-layouts.admin :title="__('Abrechnungseinstellungen')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Einstellungen'), 'href' => route('admin.settings.index')],
            ['label' => __('Abrechnung')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Abrechnungseinstellungen') }}</h2>
        </x-slot:header>

        <x-ui.empty-state
            :title="__('Keine Abrechnungskonfiguration')"
            :description="__('Stripe-Integration ist noch nicht konfiguriert.')"
        >
            <p class="text-sm text-gray-500 mt-4">
                {{ __('Konfiguriere STRIPE_KEY und STRIPE_SECRET in der .env Datei.') }}
            </p>
        </x-ui.empty-state>
    </x-ui.card>
</x-layouts.admin>
