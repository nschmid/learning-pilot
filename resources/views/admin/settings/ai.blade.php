<x-layouts.admin :title="__('KI-Einstellungen')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Einstellungen'), 'href' => route('admin.settings.index')],
            ['label' => __('KI')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('KI-Einstellungen') }}</h2>
        </x-slot:header>

        <div class="space-y-6">
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('Provider') }}</h3>
                <p class="text-gray-900">{{ config('lernpfad.ai.provider', 'Nicht konfiguriert') }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('Standard-Modell') }}</h3>
                <p class="text-gray-900">{{ config('lernpfad.ai.models.default', 'Nicht konfiguriert') }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('Standard Token-Limit (monatlich)') }}</h3>
                <p class="text-gray-900">{{ number_format(config('lernpfad.ai.default_monthly_tokens', 0)) }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('Standard Anfragen-Limit (täglich)') }}</h3>
                <p class="text-gray-900">{{ number_format(config('lernpfad.ai.default_daily_requests', 0)) }}</p>
            </div>
        </div>
    </x-ui.card>
</x-layouts.admin>
