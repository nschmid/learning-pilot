<x-layouts.admin :title="__('Lernpfad-Bericht')">
    <div class="mb-6">
        <x-navigation.breadcrumb :items="[
            ['label' => __('Berichte'), 'href' => route('admin.reports.index')],
            ['label' => __('Lernpfade')],
        ]" />
    </div>

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('Lernpfad-Statistiken') }}</h2>
        </x-slot:header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-ui.stat-card :label="__('Gesamt')" value="{{ \App\Models\LearningPath::count() }}" />
            <x-ui.stat-card :label="__('Veröffentlicht')" value="{{ \App\Models\LearningPath::where('is_published', true)->count() }}" />
            <x-ui.stat-card :label="__('Entwurf')" value="{{ \App\Models\LearningPath::where('is_published', false)->count() }}" />
            <x-ui.stat-card :label="__('Module')" value="{{ \App\Models\Module::count() }}" />
        </div>
    </x-ui.card>
</x-layouts.admin>
