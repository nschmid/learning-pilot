<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Schulverwaltung') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Welcome -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Willkommen, :name', ['name' => auth()->user()->name]) }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $team->name }}</p>
            </div>

            <!-- Quick Stats -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Students -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="size-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">{{ __('Lernende') }}</dt>
                                    <dd class="flex items-baseline">
                                        <span class="text-2xl font-semibold text-gray-900">{{ $stats['students']['current'] }}</span>
                                        <span class="ml-2 text-sm text-gray-500">/ {{ $stats['students']['limit'] }}</span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <a href="{{ route('school.students') }}" class="text-sm font-medium text-teal-600 hover:text-teal-500">{{ __('Verwalten') }}</a>
                    </div>
                </div>

                <!-- Instructors -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="size-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">{{ __('Dozenten') }}</dt>
                                    <dd class="flex items-baseline">
                                        <span class="text-2xl font-semibold text-gray-900">{{ $stats['instructors']['current'] }}</span>
                                        <span class="ml-2 text-sm text-gray-500">/ {{ $stats['instructors']['limit'] }}</span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <a href="{{ route('school.students') }}?role=instructor" class="text-sm font-medium text-teal-600 hover:text-teal-500">{{ __('Verwalten') }}</a>
                    </div>
                </div>

                <!-- Paths -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="size-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">{{ __('Lernpfade') }}</dt>
                                    <dd class="flex items-baseline">
                                        <span class="text-2xl font-semibold text-gray-900">{{ $stats['paths']['published'] }}</span>
                                        <span class="ml-2 text-sm text-gray-500">{{ __('veröffentlicht') }}</span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <a href="{{ route('instructor.paths.index') }}" class="text-sm font-medium text-teal-600 hover:text-teal-500">{{ __('Alle ansehen') }}</a>
                    </div>
                </div>

                <!-- Completion Rate -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="size-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">{{ __('Abschlussrate') }}</dt>
                                    <dd class="flex items-baseline">
                                        <span class="text-2xl font-semibold text-gray-900">{{ $stats['enrollments']['completion_rate'] }}%</span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <a href="{{ route('school.analytics') }}" class="text-sm font-medium text-teal-600 hover:text-teal-500">{{ __('Details') }}</a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900">{{ __('Schnellzugriff') }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <a href="{{ route('school.students.import') }}" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-4 hover:bg-gray-50">
                        <svg class="size-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-900">{{ __('Lernende importieren') }}</span>
                    </a>
                    <a href="{{ route('school.analytics') }}" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-4 hover:bg-gray-50">
                        <svg class="size-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-900">{{ __('Statistiken') }}</span>
                    </a>
                    <a href="{{ route('school.usage') }}" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-4 hover:bg-gray-50">
                        <svg class="size-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-900">{{ __('Nutzung') }}</span>
                    </a>
                    <a href="{{ route('billing.index') }}" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-4 hover:bg-gray-50">
                        <svg class="size-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-900">{{ __('Abonnement') }}</span>
                    </a>
                </div>
            </div>

            <!-- Storage Usage -->
            <div class="mt-8">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-base font-medium text-gray-900">{{ __('Speichernutzung') }}</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ $stats['storage']['used_formatted'] }} {{ __('von') }} {{ $stats['storage']['limit_formatted'] }}</span>
                            <span class="font-medium {{ $stats['storage']['percent'] > 90 ? 'text-red-600' : 'text-gray-900' }}">{{ $stats['storage']['percent'] }}%</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-gray-200">
                            <div class="h-3 rounded-full {{ $stats['storage']['percent'] > 90 ? 'bg-red-500' : ($stats['storage']['percent'] > 70 ? 'bg-yellow-500' : 'bg-teal-600') }}" style="width: {{ $stats['storage']['percent'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
