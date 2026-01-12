<div class="mx-auto max-w-3xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('learner.dashboard') }}" wire:navigate class="hover:text-gray-700">{{ __('Dashboard') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('learner.certificates') }}" wire:navigate class="hover:text-gray-700">{{ __('Zertifikate') }}</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-700">{{ $certificate->certificate_number }}</span>
    </nav>

    <!-- Certificate Card -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <!-- Header with Gradient -->
        <div class="bg-gradient-to-r from-teal-500 to-purple-600 p-8 text-center text-white">
            <div class="flex justify-center">
                <svg class="h-16 w-16 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <h1 class="mt-4 text-2xl font-bold">{{ __('Abschlusszertifikat') }}</h1>
            <p class="mt-2 text-lg text-white/90">{{ $certificate->enrollment->learningPath->title }}</p>

            <!-- Status Badge -->
            <div class="mt-4">
                @if($certificate->isValid())
                    <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-sm font-medium">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Gültig') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-red-400/50 px-3 py-1 text-sm font-medium">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Abgelaufen') }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Certificate Details -->
        <div class="p-8">
            <!-- Recipient Info -->
            <div class="mb-8 rounded-lg bg-gray-50 p-6 text-center">
                <p class="text-sm text-gray-500">{{ __('Ausgestellt für') }}</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ $certificate->enrollment->user->name }}</p>
                <p class="text-gray-500">{{ $certificate->enrollment->user->email }}</p>
            </div>

            <!-- Details Grid -->
            <dl class="grid gap-6 sm:grid-cols-2">
                <div class="rounded-lg border border-gray-200 p-4">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Zertifikat-Nummer') }}</dt>
                    <dd class="mt-1 font-mono text-lg font-semibold text-gray-900">{{ $certificate->certificate_number }}</dd>
                </div>

                <div class="rounded-lg border border-gray-200 p-4">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Ausstellungsdatum') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $certificate->issued_at->format('d.m.Y') }}</dd>
                </div>

                @if($certificate->expires_at)
                <div class="rounded-lg border border-gray-200 p-4">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Gültig bis') }}</dt>
                    <dd class="mt-1 text-lg font-semibold {{ $certificate->isExpired() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $certificate->expires_at->format('d.m.Y') }}
                    </dd>
                </div>
                @endif

                @if(isset($certificate->metadata['total_time_spent']))
                <div class="rounded-lg border border-gray-200 p-4">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Gesamte Lernzeit') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $certificate->metadata['total_time_spent'] }}</dd>
                </div>
                @endif

                @if(isset($certificate->metadata['points_earned']) && $certificate->metadata['points_earned'] > 0)
                <div class="rounded-lg border border-gray-200 p-4">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Erreichte Punkte') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($certificate->metadata['points_earned']) }}</dd>
                </div>
                @endif

                @if(isset($certificate->metadata['completed_at']))
                <div class="rounded-lg border border-gray-200 p-4">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Abgeschlossen am') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $certificate->metadata['completed_at'] }}</dd>
                </div>
                @endif
            </dl>

            <!-- Verification URL -->
            @if(isset($certificate->metadata['verification_url']))
            <div class="mt-8 rounded-lg border border-blue-200 bg-blue-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-800">{{ __('Verifizierungs-URL') }}</p>
                        <a
                            href="{{ $certificate->metadata['verification_url'] }}"
                            target="_blank"
                            class="mt-1 block text-sm text-blue-600 hover:text-blue-800 break-all"
                        >
                            {{ $certificate->metadata['verification_url'] }}
                        </a>
                        <p class="mt-2 text-xs text-blue-700">
                            {{ __('Teile diese URL, damit andere die Echtheit deines Zertifikats überprüfen können.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="border-t border-gray-200 bg-gray-50 px-8 py-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <a
                    href="{{ route('learner.certificates') }}"
                    wire:navigate
                    class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Alle Zertifikate') }}
                </a>

                <div class="flex gap-3">
                    @if(isset($certificate->metadata['verification_url']))
                    <button
                        onclick="navigator.clipboard.writeText('{{ $certificate->metadata['verification_url'] }}'); alert('{{ __('Link kopiert!') }}')"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        {{ __('Link kopieren') }}
                    </button>
                    @endif

                    <button
                        wire:click="download"
                        class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        {{ __('PDF herunterladen') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
