<div>
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Teilnehmer') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Verwalte die Teilnehmer deiner Lernpfade.') }}</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="mb-6 flex flex-wrap gap-2">
        <button
            wire:click="$set('status', '')"
            class="rounded-full px-4 py-2 text-sm font-medium transition {{ $status === '' ? 'bg-teal-100 text-teal-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
        >
            {{ __('Alle') }} ({{ $this->statusCounts['all'] }})
        </button>
        <button
            wire:click="$set('status', 'active')"
            class="rounded-full px-4 py-2 text-sm font-medium transition {{ $status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
        >
            {{ __('Aktiv') }} ({{ $this->statusCounts['active'] }})
        </button>
        <button
            wire:click="$set('status', 'completed')"
            class="rounded-full px-4 py-2 text-sm font-medium transition {{ $status === 'completed' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
        >
            {{ __('Abgeschlossen') }} ({{ $this->statusCounts['completed'] }})
        </button>
        <button
            wire:click="$set('status', 'paused')"
            class="rounded-full px-4 py-2 text-sm font-medium transition {{ $status === 'paused' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
        >
            {{ __('Pausiert') }} ({{ $this->statusCounts['paused'] }})
        </button>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-wrap gap-4">
        <div class="flex-1">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Teilnehmer suchen...') }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
            >
        </div>
        <select
            wire:model.live="pathId"
            class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
        >
            <option value="">{{ __('Alle Lernpfade') }}</option>
            @foreach($this->paths as $path)
                <option value="{{ $path['id'] }}">{{ $path['title'] }}</option>
            @endforeach
        </select>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Teilnehmer') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Lernpfad') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Fortschritt') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Eingeschrieben') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($this->enrollments as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    @if($enrollment->user->profile_photo_url)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $enrollment->user->profile_photo_url }}" alt="">
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-teal-100 text-sm font-medium text-teal-600">
                                            {{ substr($enrollment->user->name, 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">{{ $enrollment->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $enrollment->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $enrollment->learningPath->title }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @switch($enrollment->status->value)
                                @case('active')
                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">{{ __('Aktiv') }}</span>
                                    @break
                                @case('completed')
                                    <span class="inline-flex rounded-full bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-800">{{ __('Abgeschlossen') }}</span>
                                    @break
                                @case('paused')
                                    <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">{{ __('Pausiert') }}</span>
                                    @break
                                @default
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800">{{ $enrollment->status->value }}</span>
                            @endswitch
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-24 overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full bg-teal-600" style="width: {{ $enrollment->progress_percent }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $enrollment->progress_percent }}%</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ $enrollment->created_at->format('d.m.Y') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <button
                                wire:click="viewStudent('{{ $enrollment->id }}')"
                                class="text-teal-600 hover:text-teal-900"
                            >
                                {{ __('Details') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Teilnehmer gefunden') }}</h3>
                            <p class="mt-2 text-gray-500">
                                @if($search || $status || $pathId)
                                    {{ __('Versuche es mit anderen Filterkriterien.') }}
                                @else
                                    {{ __('Sobald Lernende sich einschreiben, erscheinen sie hier.') }}
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($this->enrollments->hasPages())
        <div class="mt-6">
            {{ $this->enrollments->links() }}
        </div>
    @endif
</div>
