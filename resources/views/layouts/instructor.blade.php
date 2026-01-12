<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? __('Dozent') }} - {{ config('app.name', 'LearningPilot') }}</title>

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
            <header class="fixed inset-x-0 top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100" x-data="{ open: false }">
                <nav class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8">
                    <div class="flex items-center gap-x-8">
                        <!-- Logo -->
                        <a href="{{ route('instructor.dashboard') }}" class="-m-1.5 p-1.5">
                            <x-application-logo class="h-8 w-auto" />
                        </a>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden lg:flex lg:gap-x-6">
                            <a href="{{ route('instructor.dashboard') }}" class="text-sm font-medium transition {{ request()->routeIs('instructor.dashboard') ? 'text-teal-600' : 'text-gray-700 hover:text-teal-600' }}">
                                {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('instructor.paths.index') }}" class="text-sm font-medium transition {{ request()->routeIs('instructor.paths.*') ? 'text-teal-600' : 'text-gray-700 hover:text-teal-600' }}">
                                {{ __('Meine Lernpfade') }}
                            </a>
                            <a href="{{ route('instructor.submissions.index') }}" class="text-sm font-medium transition {{ request()->routeIs('instructor.submissions.*') ? 'text-teal-600' : 'text-gray-700 hover:text-teal-600' }}">
                                {{ __('Einreichungen') }}
                            </a>
                            <a href="{{ route('instructor.students.index') }}" class="text-sm font-medium transition {{ request()->routeIs('instructor.students.*') ? 'text-teal-600' : 'text-gray-700 hover:text-teal-600' }}">
                                {{ __('Teilnehmer') }}
                            </a>
                            <a href="{{ route('instructor.analytics.index') }}" class="text-sm font-medium transition {{ request()->routeIs('instructor.analytics.*') ? 'text-teal-600' : 'text-gray-700 hover:text-teal-600' }}">
                                {{ __('Statistiken') }}
                            </a>
                        </div>
                    </div>

                    <div class="hidden lg:flex lg:items-center lg:gap-x-4">
                        <!-- Teams Dropdown -->
                        @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" type="button" class="flex items-center gap-x-1 text-sm font-medium text-gray-700 hover:text-teal-600 transition">
                                    {{ Auth::user()->currentTeam->name }}
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
                                    <div class="p-1">
                                        <p class="px-3 py-2 text-xs font-medium text-gray-400">{{ __('Team verwalten') }}</p>
                                        <a href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">{{ __('Team-Einstellungen') }}</a>
                                    </div>
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-100 p-1">
                                            <p class="px-3 py-2 text-xs font-medium text-gray-400">{{ __('Team wechseln') }}</p>
                                            @foreach (Auth::user()->allTeams() as $team)
                                                <x-switchable-team :team="$team" />
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

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
                        <a href="{{ route('instructor.dashboard') }}" class="block rounded-lg px-3 py-2 text-base font-medium {{ request()->routeIs('instructor.dashboard') ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50' }}">{{ __('Dashboard') }}</a>
                        <a href="{{ route('instructor.paths.index') }}" class="block rounded-lg px-3 py-2 text-base font-medium {{ request()->routeIs('instructor.paths.*') ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50' }}">{{ __('Meine Lernpfade') }}</a>
                        <a href="{{ route('instructor.submissions.index') }}" class="block rounded-lg px-3 py-2 text-base font-medium {{ request()->routeIs('instructor.submissions.*') ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50' }}">{{ __('Einreichungen') }}</a>
                        <a href="{{ route('instructor.students.index') }}" class="block rounded-lg px-3 py-2 text-base font-medium {{ request()->routeIs('instructor.students.*') ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50' }}">{{ __('Teilnehmer') }}</a>
                        <a href="{{ route('instructor.analytics.index') }}" class="block rounded-lg px-3 py-2 text-base font-medium {{ request()->routeIs('instructor.analytics.*') ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50' }}">{{ __('Statistiken') }}</a>
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
