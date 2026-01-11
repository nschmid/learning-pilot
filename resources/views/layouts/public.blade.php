<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'LearningPilot') }}</title>
        <meta name="description" content="{{ $description ?? __('Die moderne Lernplattform für Schulen und Bildungseinrichtungen') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-white">
        <!-- Header -->
        <header class="fixed inset-x-0 top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100" x-data="{ mobileMenuOpen: false }">
            <nav class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8" aria-label="Global">
                <div class="flex lg:flex-1">
                    <a href="{{ route('landing') }}" class="-m-1.5 p-1.5">
                        <span class="sr-only">{{ config('app.name') }}</span>
                        <x-application-logo class="h-8 w-auto" />
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="flex lg:hidden">
                    <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700" @click="mobileMenuOpen = true">
                        <span class="sr-only">{{ __('Menu') }}</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>

                <!-- Desktop navigation -->
                <div class="hidden lg:flex lg:gap-x-8">
                    <a href="{{ route('features') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition {{ request()->routeIs('features') ? 'text-indigo-600' : '' }}">
                        {{ __('Funktionen') }}
                    </a>
                    <a href="{{ route('pricing') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition {{ request()->routeIs('pricing') ? 'text-indigo-600' : '' }}">
                        {{ __('Preise') }}
                    </a>
                    <a href="{{ route('contact') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition {{ request()->routeIs('contact') ? 'text-indigo-600' : '' }}">
                        {{ __('Kontakt') }}
                    </a>
                </div>

                <div class="hidden lg:flex lg:flex-1 lg:justify-end lg:gap-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">
                            {{ __('Anmelden') }}
                        </a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition">
                            {{ __('Kostenlos testen') }}
                        </a>
                    @endauth
                </div>
            </nav>

            <!-- Mobile menu -->
            <div class="lg:hidden" x-show="mobileMenuOpen" x-cloak>
                <div class="fixed inset-0 z-50" @click="mobileMenuOpen = false"></div>
                <div class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-full">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('landing') }}" class="-m-1.5 p-1.5">
                            <x-application-logo class="h-8 w-auto" />
                        </a>
                        <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" @click="mobileMenuOpen = false">
                            <span class="sr-only">{{ __('Schliessen') }}</span>
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-gray-500/10">
                            <div class="space-y-2 py-6">
                                <a href="{{ route('features') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">{{ __('Funktionen') }}</a>
                                <a href="{{ route('pricing') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">{{ __('Preise') }}</a>
                                <a href="{{ route('contact') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">{{ __('Kontakt') }}</a>
                            </div>
                            <div class="py-6">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">{{ __('Dashboard') }}</a>
                                @else
                                    <a href="{{ route('login') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">{{ __('Anmelden') }}</a>
                                    <a href="{{ route('register') }}" class="-mx-3 mt-2 block rounded-lg bg-indigo-600 px-3 py-2.5 text-base font-medium text-white hover:bg-indigo-500">{{ __('Kostenlos testen') }}</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="pt-16">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900" aria-labelledby="footer-heading">
            <h2 id="footer-heading" class="sr-only">Footer</h2>
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                    <div class="space-y-4">
                        <x-application-logo class="h-8 w-auto" />
                        <p class="text-sm text-gray-400">
                            {{ __('Die moderne Lernplattform für Schulen und Bildungseinrichtungen.') }}
                        </p>
                    </div>
                    <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
                        <div class="md:grid md:grid-cols-2 md:gap-8">
                            <div>
                                <h3 class="text-sm font-semibold text-white">{{ __('Produkt') }}</h3>
                                <ul role="list" class="mt-4 space-y-3">
                                    <li><a href="{{ route('features') }}" class="text-sm text-gray-400 hover:text-white">{{ __('Funktionen') }}</a></li>
                                    <li><a href="{{ route('pricing') }}" class="text-sm text-gray-400 hover:text-white">{{ __('Preise') }}</a></li>
                                </ul>
                            </div>
                            <div class="mt-10 md:mt-0">
                                <h3 class="text-sm font-semibold text-white">{{ __('Unternehmen') }}</h3>
                                <ul role="list" class="mt-4 space-y-3">
                                    <li><a href="{{ route('contact') }}" class="text-sm text-gray-400 hover:text-white">{{ __('Kontakt') }}</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="md:grid md:grid-cols-2 md:gap-8">
                            <div>
                                <h3 class="text-sm font-semibold text-white">{{ __('Rechtliches') }}</h3>
                                <ul role="list" class="mt-4 space-y-3">
                                    <li><a href="{{ route('legal.privacy') }}" class="text-sm text-gray-400 hover:text-white">{{ __('Datenschutz') }}</a></li>
                                    <li><a href="{{ route('legal.terms') }}" class="text-sm text-gray-400 hover:text-white">{{ __('AGB') }}</a></li>
                                    <li><a href="{{ route('legal.imprint') }}" class="text-sm text-gray-400 hover:text-white">{{ __('Impressum') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-12 border-t border-gray-800 pt-8">
                    <p class="text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('Alle Rechte vorbehalten.') }}</p>
                </div>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
