<div>
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Teilnehmer-Analytik') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Leistungsübersicht deiner Teilnehmer.') }}</p>
        </div>
        <a href="{{ route('instructor.analytics.index') }}" wire:navigate class="text-sm text-teal-600 hover:text-teal-800">
            &larr; {{ __('Zurück zur Übersicht') }}
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Teilnehmer gesamt') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->summary['unique_students'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Aktiv diese Woche') }}</p>
            <p class="text-3xl font-bold text-green-600">{{ $this->summary['active_this_week'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Ø Abschlussrate') }}</p>
            <p class="text-3xl font-bold text-teal-600">{{ $this->summary['avg_completion_rate'] }}%</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Gesamte Lernzeit') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $this->summary['total_learning_time'] }}</p>
        </div>
    </div>

    <!-- Leaderboard -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Teilnehmer-Rangliste') }}</h2>
            <p class="text-sm text-gray-500">{{ __('Sortiert nach Gesamtpunkten') }}</p>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Rang') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Teilnehmer') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Einschreibungen') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Abgeschlossen') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Ø Fortschritt') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Lernzeit') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Punkte') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Zuletzt aktiv') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($this->studentStats as $index => $student)
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($index < 3)
                                <span class="flex h-8 w-8 items-center justify-center rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-800') }} text-sm font-bold">
                                    {{ $index + 1 }}
                                </span>
                            @else
                                <span class="pl-2 text-sm text-gray-500">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-teal-100 text-sm font-medium text-teal-600">
                                    {{ substr($student['name'], 0, 2) }}
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">{{ $student['name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $student['email'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                            {{ $student['enrollments_count'] }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="text-sm font-medium {{ $student['completed_count'] > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                {{ $student['completed_count'] }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-16 overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full bg-teal-600" style="width: {{ $student['avg_progress'] }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $student['avg_progress'] }}%</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ $student['total_time'] }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="rounded-full bg-teal-100 px-2.5 py-1 text-sm font-semibold text-teal-800">
                                {{ number_format($student['total_points']) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ $student['last_active'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Teilnehmer') }}</h3>
                            <p class="mt-2 text-gray-500">{{ __('Sobald Lernende sich einschreiben, erscheinen sie hier.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
