<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Zertifikat Verifizierung') }} - LearningPilot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">
    <div class="flex min-h-screen flex-col items-center justify-center p-4">
        <div class="w-full max-w-lg">
            <!-- Logo -->
            <div class="mb-8 text-center">
                <a href="{{ url('/') }}" class="text-2xl font-bold text-teal-600">LearningPilot</a>
            </div>

            <!-- Verification Card -->
            <div class="overflow-hidden rounded-xl bg-white shadow-lg">
                @php
                    $certificate = $certificate->load(['enrollment.user', 'enrollment.learningPath']);
                    $isValid = $certificate->isValid();
                @endphp

                <!-- Status Header -->
                <div class="{{ $isValid ? 'bg-green-500' : 'bg-red-500' }} px-6 py-8 text-center text-white">
                    <div class="flex justify-center">
                        @if($isValid)
                            <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @else
                            <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>
                    <h1 class="mt-4 text-2xl font-bold">
                        @if($isValid)
                            {{ __('Zertifikat gültig') }}
                        @else
                            {{ __('Zertifikat abgelaufen') }}
                        @endif
                    </h1>
                    <p class="mt-2 text-white/90">
                        {{ __('Zertifikat-Nr.') }}: {{ $certificate->certificate_number }}
                    </p>
                </div>

                <!-- Certificate Details -->
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="rounded-lg bg-gray-50 p-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Inhaber') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $certificate->enrollment->user->name }}</dd>
                        </div>

                        <div class="rounded-lg bg-gray-50 p-4">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Lernpfad') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $certificate->enrollment->learningPath->title }}</dd>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-lg bg-gray-50 p-4">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Ausgestellt am') }}</dt>
                                <dd class="mt-1 font-semibold text-gray-900">{{ $certificate->issued_at->format('d.m.Y') }}</dd>
                            </div>

                            @if($certificate->expires_at)
                            <div class="rounded-lg bg-gray-50 p-4">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Gültig bis') }}</dt>
                                <dd class="mt-1 font-semibold {{ $certificate->isExpired() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $certificate->expires_at->format('d.m.Y') }}
                                </dd>
                            </div>
                            @endif
                        </div>
                    </dl>

                    @if(!$isValid)
                    <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-center">
                        <p class="text-sm text-red-700">
                            {{ __('Dieses Zertifikat ist am :date abgelaufen.', ['date' => $certificate->expires_at->format('d.m.Y')]) }}
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 text-center">
                    <p class="text-xs text-gray-500">
                        {{ __('Verifiziert am') }} {{ now()->format('d.m.Y H:i') }} {{ __('Uhr') }}
                    </p>
                </div>
            </div>

            <!-- Back Link -->
            <div class="mt-6 text-center">
                <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    &larr; {{ __('Zur Startseite') }}
                </a>
            </div>
        </div>
    </div>
</body>
</html>
