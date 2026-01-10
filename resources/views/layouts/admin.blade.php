<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? __('Admin') }} - {{ config('app.name', 'LearningPilot') }}</title>

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

        <div class="min-h-screen bg-gray-100" x-data="{ sidebarOpen: false }">
            <!-- Sidebar for mobile -->
            <div x-show="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" x-cloak>
                <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>

                <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex w-full max-w-xs flex-1 flex-col bg-indigo-700">
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
                <div class="flex min-h-0 flex-1 flex-col bg-indigo-700">
                    @include('components.layouts.admin-sidebar-content')
                </div>
            </div>

            <!-- Main content -->
            <div class="lg:pl-64 flex flex-col flex-1">
                <!-- Top bar -->
                <div class="sticky top-0 z-10 flex h-16 shrink-0 bg-white shadow">
                    <button type="button" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 lg:hidden" @click="sidebarOpen = true">
                        <span class="sr-only">{{ __('Sidebar öffnen') }}</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <div class="flex flex-1 justify-between px-4">
                        <div class="flex flex-1 items-center">
                            @if (isset($header))
                                <h1 class="text-xl font-semibold text-gray-900">{{ $header }}</h1>
                            @endif
                        </div>
                        <div class="ml-4 flex items-center gap-x-4">
                            <!-- Teams Dropdown -->
                            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                                <x-dropdown align="right" width="60">
                                    <x-slot name="trigger">
                                        <button type="button" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                                            {{ Auth::user()->currentTeam->name }}
                                            <svg class="ml-2 size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <div class="w-60">
                                            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Team verwalten') }}</div>
                                            <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">{{ __('Team-Einstellungen') }}</x-dropdown-link>
                                            @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                                <x-dropdown-link href="{{ route('teams.create') }}">{{ __('Neues Team erstellen') }}</x-dropdown-link>
                                            @endcan
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
                            @endif

                            <!-- Profile dropdown -->
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
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
                </div>

                <!-- Page Content -->
                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('modals')
        @livewireScripts
    </body>
</html>
