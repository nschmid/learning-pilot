<x-layouts.admin :title="__('Einschreibungs-Bericht')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Berichte'), 'href' => route('admin.reports.index')],
            ['label' => __('Einschreibungen')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Einschreibungs-Statistiken') }}</h2>
        </x-slot:header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-ui.stat-card :label="__('Gesamt')" value="{{ \App\Models\Enrollment::count() }}" />
            <x-ui.stat-card :label="__('Aktiv')" value="{{ \App\Models\Enrollment::where('status', 'active')->count() }}" />
            <x-ui.stat-card :label="__('Abgeschlossen')" value="{{ \App\Models\Enrollment::where('status', 'completed')->count() }}" />
            <x-ui.stat-card :label="__('Diese Woche')" value="{{ \App\Models\Enrollment::where('created_at', '>=', now()->subWeek())->count() }}" />
        </div>
    </x-ui.card>
</x-layouts.admin>
