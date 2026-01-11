<x-layouts.admin :title="__('Benutzer-Bericht')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Berichte'), 'href' => route('admin.reports.index')],
            ['label' => __('Benutzer')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Benutzer-Statistiken') }}</h2>
        </x-slot:header>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.stat-card :label="__('Gesamt')" value="{{ \App\Models\User::count() }}" />
                <x-ui.stat-card :label="__('Aktiv')" value="{{ \App\Models\User::where('is_active', true)->count() }}" />
                <x-ui.stat-card :label="__('Diese Woche')" value="{{ \App\Models\User::where('created_at', '>=', now()->subWeek())->count() }}" />
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('Nach Rolle') }}</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="p-3 bg-gray-50 rounded-lg text-center">
                        <p class="text-2xl font-semibold">{{ \App\Models\User::where('role', 'learner')->count() }}</p>
                        <p class="text-sm text-gray-500">{{ __('Lernende') }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg text-center">
                        <p class="text-2xl font-semibold">{{ \App\Models\User::where('role', 'instructor')->count() }}</p>
                        <p class="text-sm text-gray-500">{{ __('Instruktoren') }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg text-center">
                        <p class="text-2xl font-semibold">{{ \App\Models\User::where('role', 'admin')->count() }}</p>
                        <p class="text-sm text-gray-500">{{ __('Admins') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>
</x-layouts.admin>
