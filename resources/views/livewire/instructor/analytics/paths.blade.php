<div>
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Lernpfad-Analytik') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Detaillierte Statistiken zu deinen Lernpfaden.') }}</p>
        </div>
        <a href="{{ route('instructor.analytics.index') }}" wire:navigate class="text-sm text-teal-600 hover:text-teal-800">
            &larr; {{ __('Zurück zur Übersicht') }}
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Lernpfade gesamt') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->totals['paths'] }}</p>
            <p class="text-xs text-gray-400">{{ $this->totals['published'] }} {{ __('veröffentlicht') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Einschreibungen') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->totals['total_enrollments'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Abschlüsse') }}</p>
            <p class="text-3xl font-bold text-green-600">{{ $this->totals['total_completed'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Abschlussrate') }}</p>
            <p class="text-3xl font-bold text-teal-600">
                {{ $this->totals['total_enrollments'] > 0 ? round(($this->totals['total_completed'] / $this->totals['total_enrollments']) * 100, 1) : 0 }}%
            </p>
        </div>
    </div>

    <!-- Paths Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Lernpfad') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Inhalt') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Teilnehmer') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Abschlüsse') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Ø Fortschritt') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Ø Zeit') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($this->paths as $path)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900">{{ $path['title'] }}</span>
                                        @if(!$path['is_published'])
                                            <span class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ __('Entwurf') }}</span>
                                        @endif
                                    </div>
                                    @if($path['category'])
                                        <p class="text-sm text-gray-500">{{ $path['category'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ $path['modules_count'] }} {{ __('Module') }}, {{ $path['steps_count'] }} {{ __('Schritte') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <span class="font-medium text-gray-900">{{ $path['enrollments_count'] }}</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900">{{ $path['completed_count'] }}</span>
                                <span class="rounded-full {{ $path['completion_rate'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }} px-2 py-0.5 text-xs font-medium">
                                    {{ $path['completion_rate'] }}%
                                </span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-20 overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full bg-teal-600" style="width: {{ $path['avg_progress'] }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $path['avg_progress'] }}%</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ $path['avg_time_spent'] }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <a href="{{ route('instructor.paths.show', $path['slug']) }}" wire:navigate class="text-teal-600 hover:text-teal-900">
                                {{ __('Details') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Lernpfade') }}</h3>
                            <p class="mt-2 text-gray-500">{{ __('Erstelle deinen ersten Lernpfad, um Statistiken zu sehen.') }}</p>
                            <a href="{{ route('instructor.paths.create') }}" wire:navigate class="mt-4 inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700">
                                {{ __('Lernpfad erstellen') }}
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
