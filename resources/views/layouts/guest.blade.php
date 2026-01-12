<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? __('Anmelden') }} - {{ config('app.name', 'LearningPilot') }}</title>
        <meta name="description" content="{{ __('Die moderne Lernplattform für Schulen und Bildungseinrichtungen') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <!-- Minimal Header -->
        <header class="fixed inset-x-0 top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100">
            <nav class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8" aria-label="Global">
                <div class="flex lg:flex-1">
                    <a href="{{ route('landing') }}" class="-m-1.5 p-1.5">
                        <span class="sr-only">{{ config('app.name') }}</span>
                        <x-application-logo class="h-8 w-auto" />
                    </a>
                </div>

                <div class="flex items-center gap-x-4">
                    <a href="{{ route('features') }}" class="hidden sm:block text-sm font-medium text-gray-700 hover:text-teal-600 transition">
                        {{ __('Funktionen') }}
                    </a>
                    <a href="{{ route('pricing') }}" class="hidden sm:block text-sm font-medium text-gray-700 hover:text-teal-600 transition">
                        {{ __('Preise') }}
                    </a>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="pt-16">
            {{ $slot }}
        </main>

        <!-- Simple Footer -->
        <footer class="bg-white border-t border-gray-100 mt-auto">
            <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('Alle Rechte vorbehalten.') }}
                    </p>
                    <div class="flex gap-x-6">
                        <a href="{{ route('legal.privacy') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Datenschutz') }}</a>
                        <a href="{{ route('legal.terms') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('AGB') }}</a>
                        <a href="{{ route('legal.imprint') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Impressum') }}</a>
                    </div>
                </div>
            </div>
        </footer>

        @livewireScripts

        @include('cookie-consent::index')
    </body>
</html>
