<div class="mx-auto max-w-4xl">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Meine Zertifikate') }}</h1>
            <p class="mt-1 text-gray-500">{{ __('Deine erworbenen Abschlusszertifikate') }}</p>
        </div>

        @if($this->eligibleEnrollments->isNotEmpty())
            <button
                wire:click="openRequestModal"
                class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('Zertifikat anfordern') }}
            </button>
        @endif
    </div>

    <!-- Error Message -->
    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <!-- Certificates Grid -->
    @if($this->certificates->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Noch keine Zertifikate') }}</h3>
            <p class="mt-2 text-gray-500">
                {{ __('Schliesse einen Lernpfad ab, um dein erstes Zertifikat zu erhalten.') }}
            </p>
            <a
                href="{{ route('learner.catalog') }}"
                wire:navigate
                class="mt-6 inline-flex items-center gap-2 text-orange-600 hover:text-orange-800"
            >
                {{ __('Katalog erkunden') }}
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2">
            @foreach($this->certificates as $certificate)
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md">
                    <!-- Certificate Header with Gradient -->
                    <div class="bg-gradient-to-r from-teal-500 to-purple-600 p-6 text-white">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-sm font-medium opacity-90">{{ __('Zertifikat') }}</div>
                                <h3 class="mt-1 text-lg font-bold">
                                    {{ $certificate->enrollment->learningPath->title }}
                                </h3>
                            </div>
                            @if($certificate->isValid())
                                <span class="inline-flex items-center rounded-full bg-white/20 px-2 py-1 text-xs font-medium">
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('Gültig') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-400/50 px-2 py-1 text-xs font-medium">
                                    {{ __('Abgelaufen') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Certificate Details -->
                    <div class="p-6">
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('Zertifikat-Nr.') }}</dt>
                                <dd class="font-mono font-medium text-gray-900">{{ $certificate->certificate_number }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('Ausgestellt am') }}</dt>
                                <dd class="text-gray-900">{{ $certificate->issued_at->format('d.m.Y') }}</dd>
                            </div>
                            @if($certificate->expires_at)
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">{{ __('Gültig bis') }}</dt>
                                    <dd class="{{ $certificate->isExpired() ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $certificate->expires_at->format('d.m.Y') }}
                                    </dd>
                                </div>
                            @endif
                        </dl>

                        <div class="mt-6 flex gap-3">
                            <a
                                href="{{ route('learner.certificates.show', $certificate) }}"
                                wire:navigate
                                class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                {{ __('Details') }}
                            </a>
                            <a
                                href="{{ route('learner.certificates.show', $certificate) }}"
                                wire:navigate
                                class="flex-1 rounded-lg bg-orange-500 px-4 py-2 text-center text-sm font-medium text-white hover:bg-orange-600"
                            >
                                {{ __('Herunterladen') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Request Certificate Modal -->
    @if($showRequestModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <div class="fixed inset-0 bg-black/50" wire:click="closeRequestModal"></div>

            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Zertifikat anfordern') }}</h3>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('Wähle einen abgeschlossenen Lernpfad aus, für den du ein Zertifikat erhalten möchtest.') }}
                </p>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Lernpfad') }}</label>
                    <select
                        wire:model="selectedEnrollmentId"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                    >
                        <option value="">{{ __('Bitte wählen...') }}</option>
                        @foreach($this->eligibleEnrollments as $enrollment)
                            <option value="{{ $enrollment->id }}">
                                {{ $enrollment->learningPath->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="closeRequestModal"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        {{ __('Abbrechen') }}
                    </button>
                    <button
                        wire:click="requestCertificate"
                        wire:loading.attr="disabled"
                        class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600 disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="requestCertificate">
                            {{ __('Zertifikat erstellen') }}
                        </span>
                        <span wire:loading wire:target="requestCertificate">
                            {{ __('Wird erstellt...') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
