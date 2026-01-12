<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? __('Lernen') }} - {{ config('app.name', 'LearningPilot') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-white">
        <x-banner />

        <div class="min-h-screen">
            <!-- Navigation -->
            <header class="fixed inset-x-0 top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100" x-data="{ open: false, profileOpen: false }">
                <nav class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8">
                    <div class="flex items-center gap-x-8">
                        <!-- Logo -->
                        <a href="{{ route('learner.dashboard') }}" class="-m-1.5 p-1.5">
                            <x-application-logo class="h-8 w-auto" />
                        </a>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden lg:flex lg:gap-x-6">
                            <a href="{{ route('learner.dashboard') }}" class="text-sm font-medium transition {{ request()->routeIs('learner.dashboard') ? 'text-teal-600' : 'text-gray-700 hover:text-teal-600' }}">
                                {{ __('Mein Lernen') }}
                            </a>
                            <a href="{{ route('learner.catalog') }}" class="text-sm font-medium transition {{ request()->routeIs('learner.catalog*') ? 'text-teal-600' : 'text-gray-700 hover:text-teal-600' }}">
                                {{ __('Katalog') }}
                            </a>
                            <a href="{{ route('learner.bookmarks') }}" class="text-sm font-medium transition {{ request()->routeIs('learner.bookmarks') ? 'text-teal-600' : 'text-gray-700 hover:text-teal-600' }}">
                                {{ __('Lesezeichen') }}
                            </a>
                            <a href="{{ route('learner.certificates') }}" class="text-sm font-medium transition {{ request()->routeIs('learner.certificates') ? 'text-teal-600' : 'text-gray-700 hover:text-teal-600' }}">
                                {{ __('Zertifikate') }}
                            </a>
                        </div>
                    </div>

                    <div class="hidden lg:flex lg:items-center lg:gap-x-4">
                        <!-- Search -->
                        <div class="relative">
                            <input type="text" placeholder="{{ __('Suchen...') }}" class="block w-56 rounded-lg border-0 bg-gray-50 py-2 ps-10 pe-4 text-sm text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-teal-500">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <button type="button" class="p-2 text-gray-400 hover:text-gray-600 transition">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </button>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-x-2 rounded-lg p-1.5 hover:bg-gray-50 transition">
                                <img class="size-8 rounded-full object-cover ring-2 ring-gray-100" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                                <div class="p-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="p-1">
                                    <a href="{{ route('profile.show') }}" class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">{{ __('Profil') }}</a>
                                    <a href="{{ route('learner.settings') }}" class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">{{ __('Einstellungen') }}</a>
                                </div>
                                <div class="border-t border-gray-100 p-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">{{ __('Abmelden') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex lg:hidden">
                        <button @click="open = !open" type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </nav>

                <!-- Mobile menu -->
                <div class="lg:hidden" x-show="open" x-cloak>
                    <div class="border-t border-gray-100 px-4 py-4 space-y-1">
                        <a href="{{ route('learner.dashboard') }}" class="block rounded-lg px-3 py-2 text-base font-medium {{ request()->routeIs('learner.dashboard') ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50' }}">{{ __('Mein Lernen') }}</a>
                        <a href="{{ route('learner.catalog') }}" class="block rounded-lg px-3 py-2 text-base font-medium {{ request()->routeIs('learner.catalog*') ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50' }}">{{ __('Katalog') }}</a>
                        <a href="{{ route('learner.bookmarks') }}" class="block rounded-lg px-3 py-2 text-base font-medium {{ request()->routeIs('learner.bookmarks') ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50' }}">{{ __('Lesezeichen') }}</a>
                        <a href="{{ route('learner.certificates') }}" class="block rounded-lg px-3 py-2 text-base font-medium {{ request()->routeIs('learner.certificates') ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50' }}">{{ __('Zertifikate') }}</a>
                    </div>
                    <div class="border-t border-gray-100 px-4 py-4">
                        <div class="flex items-center gap-x-3 mb-3">
                            <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <a href="{{ route('profile.show') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">{{ __('Profil') }}</a>
                            <a href="{{ route('learner.settings') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">{{ __('Einstellungen') }}</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">{{ __('Abmelden') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="pt-20">
                @if (isset($header))
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
                        {{ $header }}
                    </div>
                @endif

                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-12">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @stack('modals')
        @livewireScripts

        @include('cookie-consent::index')
    </body>
</html>
