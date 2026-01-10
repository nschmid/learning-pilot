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
    <body class="font-sans antialiased bg-gray-900">
        <div class="min-h-screen flex flex-col" x-data="{ sidebarOpen: true }">
            <!-- Top Bar -->
            <header class="bg-gray-800 border-b border-gray-700 h-14 flex items-center justify-between px-4 shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Toggle Sidebar -->
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <!-- Back to Overview -->
                    @if (isset($path))
                        <a href="{{ route('learner.path.overview', $path) }}" class="flex items-center text-gray-400 hover:text-white">
                            <svg class="size-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            <span class="text-sm">{{ __('Zur Übersicht') }}</span>
                        </a>
                    @endif
                </div>

                <!-- Path Title -->
                <div class="flex-1 text-center">
                    @if (isset($pathTitle))
                        <h1 class="text-white font-medium truncate">{{ $pathTitle }}</h1>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <!-- Progress -->
                    @if (isset($progress))
                        <div class="hidden sm:flex items-center gap-2">
                            <div class="w-32 bg-gray-700 rounded-full h-2">
                                <div class="bg-sky-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                            <span class="text-sm text-gray-400">{{ $progress }}%</span>
                        </div>
                    @endif

                    <!-- Bookmark -->
                    <button type="button" class="text-gray-400 hover:text-yellow-400" title="{{ __('Lesezeichen') }}">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                        </svg>
                    </button>

                    <!-- Notes -->
                    <button type="button" class="text-gray-400 hover:text-white" title="{{ __('Notizen') }}">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </button>

                    <!-- AI Tutor -->
                    <button type="button" class="text-gray-400 hover:text-sky-400" title="{{ __('KI-Tutor') }}">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                        </svg>
                    </button>

                    <!-- Exit -->
                    <a href="{{ route('learner.dashboard') }}" class="text-gray-400 hover:text-white" title="{{ __('Beenden') }}">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>
            </header>

            <div class="flex-1 flex overflow-hidden">
                <!-- Sidebar (Module/Step Navigation) -->
                <aside x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="w-80 bg-gray-800 border-r border-gray-700 overflow-y-auto shrink-0">
                    {{ $sidebar ?? '' }}
                </aside>

                <!-- Main Content Area -->
                <main class="flex-1 overflow-y-auto bg-gray-900">
                    {{ $slot }}
                </main>
            </div>

            <!-- Bottom Navigation Bar -->
            @if (isset($navigation))
                <footer class="bg-gray-800 border-t border-gray-700 h-16 flex items-center justify-between px-6 shrink-0">
                    {{ $navigation }}
                </footer>
            @endif
        </div>

        @stack('modals')
        @livewireScripts
    </body>
</html>
