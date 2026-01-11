<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('KI-Feedback-Übersicht') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Überprüfen und verwalten Sie das Feedback zu KI-generierten Inhalten') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">{{ __('Gesamt') }}</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $this->statistics['total'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">{{ __('Ungelöst') }}</p>
            <p class="mt-1 text-3xl font-bold text-red-600">{{ $this->statistics['unresolved'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">{{ __('Positiv') }}</p>
            <p class="mt-1 text-3xl font-bold text-green-600">{{ $this->statistics['positive'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">{{ __('Negativ') }}</p>
            <p class="mt-1 text-3xl font-bold text-yellow-600">{{ $this->statistics['negative'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">{{ __('Durchschn. Bewertung') }}</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $this->statistics['average_rating'] }}/5</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-wrap gap-4">
        <div class="flex gap-2">
            <button wire:click="setFilter('all')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
                {{ __('Alle') }}
            </button>
            <button wire:click="setFilter('unresolved')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $filter === 'unresolved' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
                {{ __('Ungelöst') }}
            </button>
            <button wire:click="setFilter('resolved')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $filter === 'resolved' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
                {{ __('Gelöst') }}
            </button>
            <button wire:click="setFilter('negative')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $filter === 'negative' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
                {{ __('Negativ') }}
            </button>
        </div>

        <select wire:model.live="typeFilter" class="rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('Alle Typen') }}</option>
            @foreach($this->feedbackTypes as $type)
                <option value="{{ $type['value'] }}">{{ $type['label'] }} ({{ $type['count'] }})</option>
            @endforeach
        </select>
    </div>

    <!-- Feedback Table -->
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Benutzer') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Typ') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Bewertung') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Kommentar') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Datum') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Aktionen') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($this->feedback as $item)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->user->name ?? __('Gelöscht') }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->user->email ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">
                                    {{ $item->feedback_type->label() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $item->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                            </td>
                            <td class="max-w-xs px-6 py-4">
                                <p class="truncate text-sm text-gray-700">{{ $item->comment ?? '-' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($item->is_resolved)
                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                        {{ __('Gelöst') }}
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700">
                                        {{ __('Offen') }}
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $item->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="viewDetails('{{ $item->id }}')" class="text-indigo-600 hover:text-indigo-900" title="{{ __('Details') }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    @if($item->is_resolved)
                                        <button wire:click="reopenFeedback('{{ $item->id }}')" class="text-yellow-600 hover:text-yellow-900" title="{{ __('Wieder öffnen') }}">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    <button wire:click="deleteFeedback('{{ $item->id }}')" wire:confirm="{{ __('Feedback wirklich löschen?') }}" class="text-red-600 hover:text-red-900" title="{{ __('Löschen') }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                {{ __('Kein Feedback gefunden.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-6 py-4">
            {{ $this->feedback->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal && $this->selectedFeedback)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="closeModal">
            <div class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">{{ __('Feedback-Details') }}</h2>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Benutzer') }}</label>
                            <p class="text-gray-900">{{ $this->selectedFeedback->user->name ?? __('Gelöscht') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Datum') }}</label>
                            <p class="text-gray-900">{{ $this->selectedFeedback->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Feedback-Typ') }}</label>
                            <p class="text-gray-900">{{ $this->selectedFeedback->feedback_type->label() }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Bewertung') }}</label>
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-5 w-5 {{ $i <= $this->selectedFeedback->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>

                    @if($this->selectedFeedback->comment)
                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Kommentar') }}</label>
                            <p class="mt-1 rounded-lg bg-gray-50 p-3 text-gray-900">{{ $this->selectedFeedback->comment }}</p>
                        </div>
                    @endif

                    @if($this->selectedFeedback->is_resolved)
                        <div class="rounded-lg bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800">{{ __('Gelöst am') }} {{ $this->selectedFeedback->resolved_at->format('d.m.Y H:i') }}</p>
                            @if($this->selectedFeedback->resolver_notes)
                                <p class="mt-2 text-sm text-green-700">{{ $this->selectedFeedback->resolver_notes }}</p>
                            @endif
                        </div>
                    @else
                        <form wire:submit="markAsResolved">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Notizen zur Lösung') }}</label>
                                <textarea wire:model="resolverNotes" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Optional: Notizen hinzufügen...') }}"></textarea>
                                @error('resolverNotes') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div class="mt-4 flex justify-end gap-3">
                                <button type="button" wire:click="closeModal" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                                    {{ __('Abbrechen') }}
                                </button>
                                <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                                    {{ __('Als gelöst markieren') }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
