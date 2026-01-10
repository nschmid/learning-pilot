<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? __('Lernen') }} - {{ config('app.name', 'LearningPilot') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-50">
            <!-- Navigation -->
            <nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('learner.dashboard') }}">
                                    <x-application-mark class="block h-9 w-auto" />
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <x-nav-link href="{{ route('learner.dashboard') }}" :active="request()->routeIs('learner.dashboard')">
                                    {{ __('Mein Lernen') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('learner.catalog') }}" :active="request()->routeIs('learner.catalog*')">
                                    {{ __('Katalog') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('learner.bookmarks') }}" :active="request()->routeIs('learner.bookmarks')">
                                    {{ __('Lesezeichen') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('learner.certificates') }}" :active="request()->routeIs('learner.certificates')">
                                    {{ __('Zertifikate') }}
                                </x-nav-link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Search -->
                            <div class="relative">
                                <input type="text" placeholder="{{ __('Suchen...') }}" class="block w-64 rounded-full border-gray-300 bg-gray-100 py-2 ps-10 pe-4 text-sm focus:border-sky-500 focus:bg-white focus:ring-sky-500">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Notifications -->
                            <button type="button" class="ms-4 p-2 text-gray-400 hover:text-gray-500">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </button>

                            <!-- Profile Dropdown -->
                            <div class="ms-3 relative">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                            <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <div class="px-4 py-3">
                                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                        </div>
                                        <div class="border-t border-gray-200"></div>
                                        <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profil') }}</x-dropdown-link>
                                        <x-dropdown-link href="{{ route('learner.settings') }}">{{ __('Einstellungen') }}</x-dropdown-link>
                                        <div class="border-t border-gray-200"></div>
                                        <form method="POST" action="{{ route('logout') }}" x-data>
                                            @csrf
                                            <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Abmelden') }}</x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <x-responsive-nav-link href="{{ route('learner.dashboard') }}" :active="request()->routeIs('learner.dashboard')">{{ __('Mein Lernen') }}</x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('learner.catalog') }}" :active="request()->routeIs('learner.catalog*')">{{ __('Katalog') }}</x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('learner.bookmarks') }}" :active="request()->routeIs('learner.bookmarks')">{{ __('Lesezeichen') }}</x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('learner.certificates') }}" :active="request()->routeIs('learner.certificates')">{{ __('Zertifikate') }}</x-responsive-nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div class="shrink-0 me-3">
                                <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </div>
                            <div>
                                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">{{ __('Profil') }}</x-responsive-nav-link>
                            <x-responsive-nav-link href="{{ route('learner.settings') }}" :active="request()->routeIs('learner.settings')">{{ __('Einstellungen') }}</x-responsive-nav-link>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Abmelden') }}</x-responsive-nav-link>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @stack('modals')
        @livewireScripts
    </body>
</html>
