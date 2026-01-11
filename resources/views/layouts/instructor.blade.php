<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? __('Dozent') }} - {{ config('app.name', 'LearningPilot') }}</title>

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
                                <a href="{{ route('instructor.dashboard') }}">
                                    <x-application-mark class="block h-9 w-auto" />
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <x-nav-link href="{{ route('instructor.dashboard') }}" :active="request()->routeIs('instructor.dashboard')">
                                    {{ __('Dashboard') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('instructor.paths.index') }}" :active="request()->routeIs('instructor.paths.*')">
                                    {{ __('Meine Lernpfade') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('instructor.submissions.index') }}" :active="request()->routeIs('instructor.submissions.*')">
                                    {{ __('Einreichungen') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('instructor.students.index') }}" :active="request()->routeIs('instructor.students.*')">
                                    {{ __('Teilnehmer') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('instructor.analytics.index') }}" :active="request()->routeIs('instructor.analytics.*')">
                                    {{ __('Statistiken') }}
                                </x-nav-link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Teams Dropdown -->
                            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                                <div class="ms-3 relative">
                                    <x-dropdown align="right" width="60">
                                        <x-slot name="trigger">
                                            <button type="button" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition">
                                                {{ Auth::user()->currentTeam->name }}
                                                <svg class="ms-2 size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            <div class="w-60">
                                                <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Team verwalten') }}</div>
                                                <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">{{ __('Team-Einstellungen') }}</x-dropdown-link>
                                                @if (Auth::user()->allTeams()->count() > 1)
                                                    <div class="border-t border-gray-200"></div>
                                                    <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Team wechseln') }}</div>
                                                    @foreach (Auth::user()->allTeams() as $team)
                                                        <x-switchable-team :team="$team" />
                                                    @endforeach
                                                @endif
                                            </div>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            @endif

                            <!-- Profile Dropdown -->
                            <div class="ms-3 relative">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                            <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Konto verwalten') }}</div>
                                        <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profil') }}</x-dropdown-link>
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
                        <x-responsive-nav-link href="{{ route('instructor.dashboard') }}" :active="request()->routeIs('instructor.dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('instructor.paths.index') }}" :active="request()->routeIs('instructor.paths.*')">{{ __('Meine Lernpfade') }}</x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('instructor.submissions.index') }}" :active="request()->routeIs('instructor.submissions.*')">{{ __('Einreichungen') }}</x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('instructor.students.index') }}" :active="request()->routeIs('instructor.students.*')">{{ __('Teilnehmer') }}</x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('instructor.analytics.index') }}" :active="request()->routeIs('instructor.analytics.*')">{{ __('Statistiken') }}</x-responsive-nav-link>
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

        @include('cookie-consent::index')
    </body>
</html>
