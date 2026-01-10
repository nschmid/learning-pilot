<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Einreichungen') }}</h1>
        <p class="mt-1 text-gray-500">{{ __('Prüfe und bewerte Aufgaben-Einreichungen deiner Teilnehmer') }}</p>
    </div>

    <!-- Status Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex gap-6">
            <button
                wire:click="$set('status', 'pending')"
                class="{{ $status === 'pending' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} flex items-center gap-2 border-b-2 pb-4 text-sm font-medium"
            >
                {{ __('Ausstehend') }}
                @if($this->statusCounts['pending'] > 0)
                    <span class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-800">
                        {{ $this->statusCounts['pending'] }}
                    </span>
                @endif
            </button>
            <button
                wire:click="$set('status', 'reviewed')"
                class="{{ $status === 'reviewed' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} flex items-center gap-2 border-b-2 pb-4 text-sm font-medium"
            >
                {{ __('Bewertet') }}
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">
                    {{ $this->statusCounts['reviewed'] }}
                </span>
            </button>
            <button
                wire:click="$set('status', 'revision_requested')"
                class="{{ $status === 'revision_requested' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} flex items-center gap-2 border-b-2 pb-4 text-sm font-medium"
            >
                {{ __('Überarbeitung') }}
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">
                    {{ $this->statusCounts['revision_requested'] }}
                </span>
            </button>
        </nav>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row">
        <!-- Search -->
        <div class="flex-1">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="{{ __('Nach Teilnehmer oder Aufgabe suchen...') }}"
                    class="w-full rounded-lg border-gray-300 pl-10 focus:border-orange-500 focus:ring-orange-500"
                >
            </div>
        </div>

        <!-- Path Filter -->
        <div class="sm:w-64">
            <select
                wire:model.live="pathId"
                class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
            >
                <option value="">{{ __('Alle Lernpfade') }}</option>
                @foreach($this->paths as $path)
                    <option value="{{ $path->id }}">{{ $path->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Submissions List -->
    @if($this->submissions->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Keine Einreichungen') }}</h3>
            <p class="mt-2 text-gray-500">
                @if($status === 'pending')
                    {{ __('Es gibt keine ausstehenden Einreichungen zum Bewerten.') }}
                @else
                    {{ __('Keine Einreichungen mit diesem Status gefunden.') }}
                @endif
            </p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Teilnehmer') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Aufgabe') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Lernpfad') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Eingereicht') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Status') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Aktion') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($this->submissions as $submission)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ $submission->enrollment->user->profile_photo_url }}"
                                        alt="{{ $submission->enrollment->user->name }}"
                                        class="h-8 w-8 rounded-full object-cover"
                                    >
                                    <span class="font-medium text-gray-900">
                                        {{ $submission->enrollment->user->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-900">{{ $submission->task->title }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500">
                                    {{ $submission->task->step->module->learningPath->title }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="text-sm text-gray-500">
                                    {{ $submission->submitted_at->format('d.m.Y H:i') }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @switch($submission->status->value)
                                    @case('pending')
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800">
                                            {{ __('Ausstehend') }}
                                        </span>
                                        @break
                                    @case('reviewed')
                                        <span class="inline-flex items-center rounded-full {{ $submission->scorePercent() >= 60 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-2 py-1 text-xs font-medium">
                                            {{ $submission->score }}/{{ $submission->task->max_points }}
                                        </span>
                                        @break
                                    @case('revision_requested')
                                        <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-1 text-xs font-medium text-orange-800">
                                            {{ __('Überarbeitung') }}
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <a
                                    href="{{ route('instructor.submissions.review', $submission) }}"
                                    class="inline-flex items-center gap-1 text-sm font-medium text-orange-600 hover:text-orange-800"
                                >
                                    @if($submission->status === \App\Enums\SubmissionStatus::Pending)
                                        {{ __('Bewerten') }}
                                    @else
                                        {{ __('Ansehen') }}
                                    @endif
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $this->submissions->links() }}
        </div>
    @endif
</div>
