<div>
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('KI-Quota-Verwaltung') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Verwalten Sie die KI-Nutzungslimits der Benutzer') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">{{ __('Quotas gesamt') }}</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($this->statistics['total_quotas']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">{{ __('Aktive Nutzer (7 Tage)') }}</p>
            <p class="mt-1 text-3xl font-bold text-green-600">{{ number_format($this->statistics['active_users']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">{{ __('Nahe am Limit (>90%)') }}</p>
            <p class="mt-1 text-3xl font-bold text-yellow-600">{{ number_format($this->statistics['near_limit']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">{{ __('Über dem Limit') }}</p>
            <p class="mt-1 text-3xl font-bold text-red-600">{{ number_format($this->statistics['over_limit']) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-wrap gap-4">
        <div class="flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Benutzer suchen...') }}"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <select wire:model.live="filter" class="rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="all">{{ __('Alle') }}</option>
            <option value="active">{{ __('Aktiv') }}</option>
            <option value="near_limit">{{ __('Nahe am Limit') }}</option>
            <option value="over_limit">{{ __('Über dem Limit') }}</option>
            <option value="inactive">{{ __('Inaktiv') }}</option>
        </select>
    </div>

    <!-- Quotas Table -->
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Benutzer') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Monatliche Tokens') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Tägl. Anfragen') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Letzte Aktivität') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Features') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Aktionen') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($this->quotas as $quota)
                        @php
                            $tokenPercent = $quota->monthly_token_limit > 0 ? ($quota->tokens_used_this_month / $quota->monthly_token_limit) * 100 : 0;
                            $requestPercent = $quota->daily_request_limit > 0 ? ($quota->requests_today / $quota->daily_request_limit) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $quota->user->name ?? __('Gelöscht') }}</p>
                                    <p class="text-sm text-gray-500">{{ $quota->user->email ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-24">
                                        <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                                            <div class="h-full rounded-full {{ $tokenPercent >= 90 ? 'bg-red-500' : ($tokenPercent >= 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                                style="width: {{ min(100, $tokenPercent) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-700">
                                        {{ number_format($quota->tokens_used_this_month) }} / {{ number_format($quota->monthly_token_limit) }}
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="text-sm text-gray-700">
                                    {{ $quota->requests_today }} / {{ $quota->daily_request_limit }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $quota->last_request_at ? $quota->last_request_at->diffForHumans() : __('Nie') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex gap-1">
                                    @if($quota->feature_explanations_enabled ?? true)
                                        <span class="rounded bg-blue-100 px-1.5 py-0.5 text-xs text-blue-700" title="{{ __('Erklärungen') }}">E</span>
                                    @endif
                                    @if($quota->feature_tutor_enabled ?? true)
                                        <span class="rounded bg-purple-100 px-1.5 py-0.5 text-xs text-purple-700" title="{{ __('Tutor') }}">T</span>
                                    @endif
                                    @if($quota->feature_practice_enabled ?? true)
                                        <span class="rounded bg-green-100 px-1.5 py-0.5 text-xs text-green-700" title="{{ __('Übungen') }}">Ü</span>
                                    @endif
                                    @if($quota->feature_summaries_enabled ?? true)
                                        <span class="rounded bg-orange-100 px-1.5 py-0.5 text-xs text-orange-700" title="{{ __('Zusammenfassungen') }}">Z</span>
                                    @endif
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="editQuota('{{ $quota->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="resetMonthlyUsage('{{ $quota->id }}')" wire:confirm="{{ __('Monatliche Nutzung wirklich zurücksetzen?') }}" class="text-yellow-600 hover:text-yellow-900" title="{{ __('Monatlich zurücksetzen') }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                {{ __('Keine Quotas gefunden.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-6 py-4">
            {{ $this->quotas->links() }}
        </div>
    </div>

    <!-- Edit Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="closeModal">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                <h2 class="mb-6 text-xl font-bold text-gray-900">{{ __('Quota bearbeiten') }}</h2>

                <form wire:submit="updateQuota">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Monatliches Token-Limit') }}</label>
                            <input type="number" wire:model="monthlyTokenLimit" min="0" step="1000"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('monthlyTokenLimit') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Tägliches Anfrage-Limit') }}</label>
                            <input type="number" wire:model="dailyRequestLimit" min="0"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('dailyRequestLimit') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <label class="mb-3 block text-sm font-medium text-gray-700">{{ __('Aktivierte Features') }}</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" wire:model="featureExplanations" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700">{{ __('Erklärungen') }}</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" wire:model="featureTutor" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700">{{ __('KI-Tutor') }}</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" wire:model="featurePractice" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700">{{ __('Übungsgenerierung') }}</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" wire:model="featureSummaries" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700">{{ __('Zusammenfassungen') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                            {{ __('Abbrechen') }}
                        </button>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                            {{ __('Speichern') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
