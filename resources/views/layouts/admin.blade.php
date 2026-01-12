<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? __('Admin') }} - {{ config('app.name', 'LearningPilot') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <x-banner />

        <div class="min-h-screen" x-data="{ sidebarOpen: false }">
            <!-- Sidebar for mobile -->
            <div x-show="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" x-cloak>
                <div x-show="sidebarOpen"
                     x-transition:enter="transition-opacity ease-linear duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
                     @click="sidebarOpen = false"></div>

                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative flex w-full max-w-xs flex-1 flex-col bg-gray-900">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button type="button" class="ml-1 flex size-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebarOpen = false">
                            <span class="sr-only">{{ __('Sidebar schließen') }}</span>
                            <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @include('components.layouts.admin-sidebar-content')
                </div>
            </div>

            <!-- Static sidebar for desktop -->
            <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
                <div class="flex min-h-0 flex-1 flex-col bg-gray-900">
                    @include('components.layouts.admin-sidebar-content')
                </div>
            </div>

            <!-- Main content -->
            <div class="lg:pl-64 flex flex-col flex-1">
                <!-- Top bar -->
                <header class="sticky top-0 z-10 flex h-16 shrink-0 bg-white/95 backdrop-blur border-b border-gray-100">
                    <button type="button" class="px-4 border-r border-gray-100 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500 lg:hidden transition" @click="sidebarOpen = true">
                        <span class="sr-only">{{ __('Sidebar öffnen') }}</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <div class="flex flex-1 justify-between px-4 lg:px-6">
                        <div class="flex flex-1 items-center">
                            @if (isset($header))
                                <h1 class="text-lg font-semibold text-gray-900">{{ $header }}</h1>
                            @endif
                        </div>
                        <div class="ml-4 flex items-center gap-x-4">
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
                                            @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                                <a href="{{ route('teams.create') }}" class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">{{ __('Neues Team erstellen') }}</a>
                                            @endcan
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

                            <!-- Profile dropdown -->
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
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('modals')
        @livewireScripts

        @include('cookie-consent::index')
    </body>
</html>
