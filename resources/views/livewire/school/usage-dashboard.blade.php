<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Nutzungsübersicht') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Trial Banner -->
            @if($trialDaysRemaining > 0)
                <div class="mb-6 rounded-lg bg-teal-50 p-4">
                    <div class="flex">
                        <svg class="size-5 text-teal-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-teal-800">
                                {{ __('Testphase: Noch :days Tage verbleibend', ['days' => $trialDaysRemaining]) }}
                            </p>
                            <p class="mt-1 text-sm text-teal-700">
                                <a href="{{ route('billing.plans') }}" class="font-medium underline">{{ __('Jetzt einen Plan wählen') }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Current Plan -->
            @if($currentPlan)
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $currentPlan['name'] }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $currentPlan['description'] ?? '' }}</p>
                            </div>
                            <a href="{{ route('billing.plans') }}" class="text-sm font-medium text-teal-600 hover:text-teal-500">
                                {{ __('Plan ändern') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Usage Stats Grid -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Students -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-medium text-gray-900">{{ __('Lernende') }}</h3>
                            <span class="text-sm text-gray-500">{{ $stats['students']['current'] }} / {{ $stats['students']['limit'] }}</span>
                        </div>
                        <div class="mt-4">
                            <div class="h-4 overflow-hidden rounded-full bg-gray-200">
                                <div
                                    class="h-4 rounded-full {{ $stats['students']['percent'] > 90 ? 'bg-red-500' : ($stats['students']['percent'] > 70 ? 'bg-yellow-500' : 'bg-teal-600') }}"
                                    style="width: {{ $stats['students']['percent'] }}%"
                                ></div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ __(':count Plätze verbleibend', ['count' => $stats['students']['remaining']]) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Instructors -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-medium text-gray-900">{{ __('Dozenten') }}</h3>
                            <span class="text-sm text-gray-500">{{ $stats['instructors']['current'] }} / {{ $stats['instructors']['limit'] }}</span>
                        </div>
                        <div class="mt-4">
                            <div class="h-4 overflow-hidden rounded-full bg-gray-200">
                                <div
                                    class="h-4 rounded-full {{ $stats['instructors']['percent'] > 90 ? 'bg-red-500' : ($stats['instructors']['percent'] > 70 ? 'bg-yellow-500' : 'bg-teal-600') }}"
                                    style="width: {{ $stats['instructors']['percent'] }}%"
                                ></div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ __(':count Plätze verbleibend', ['count' => $stats['instructors']['remaining']]) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Storage -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-medium text-gray-900">{{ __('Speicher') }}</h3>
                            <span class="text-sm text-gray-500">{{ $stats['storage']['used_formatted'] }} / {{ $stats['storage']['limit_formatted'] }}</span>
                        </div>
                        <div class="mt-4">
                            <div class="h-4 overflow-hidden rounded-full bg-gray-200">
                                <div
                                    class="h-4 rounded-full {{ $stats['storage']['percent'] > 90 ? 'bg-red-500' : ($stats['storage']['percent'] > 70 ? 'bg-yellow-500' : 'bg-teal-600') }}"
                                    style="width: {{ $stats['storage']['percent'] }}%"
                                ></div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ $stats['storage']['percent'] }}% {{ __('belegt') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- AI Requests -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-medium text-gray-900">{{ __('KI-Anfragen heute') }}</h3>
                            <span class="text-sm text-gray-500">{{ $stats['ai_requests']['today'] }} / {{ $stats['ai_requests']['daily_limit'] }}</span>
                        </div>
                        <div class="mt-4">
                            <div class="h-4 overflow-hidden rounded-full bg-gray-200">
                                <div
                                    class="h-4 rounded-full {{ $stats['ai_requests']['daily_percent'] > 90 ? 'bg-red-500' : ($stats['ai_requests']['daily_percent'] > 70 ? 'bg-yellow-500' : 'bg-teal-600') }}"
                                    style="width: {{ $stats['ai_requests']['daily_percent'] }}%"
                                ></div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ __(':count Anfragen diesen Monat', ['count' => $stats['ai_requests']['month']]) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollments Stats -->
            <div class="mt-8 overflow-hidden rounded-lg bg-white shadow">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-base font-medium text-gray-900">{{ __('Einschreibungen') }}</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="grid grid-cols-1 divide-y divide-gray-200 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                        <div class="px-4 py-5 text-center sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Gesamt') }}</dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['enrollments']['total'] }}</dd>
                        </div>
                        <div class="px-4 py-5 text-center sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Aktiv') }}</dt>
                            <dd class="mt-1 text-3xl font-semibold text-teal-600">{{ $stats['enrollments']['active'] }}</dd>
                        </div>
                        <div class="px-4 py-5 text-center sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Abgeschlossen') }}</dt>
                            <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $stats['enrollments']['completed'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Paths Stats -->
            <div class="mt-8 overflow-hidden rounded-lg bg-white shadow">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-base font-medium text-gray-900">{{ __('Lernpfade') }}</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="grid grid-cols-1 divide-y divide-gray-200 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                        <div class="px-4 py-5 text-center sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Gesamt') }}</dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['paths']['total'] }}</dd>
                        </div>
                        <div class="px-4 py-5 text-center sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Veröffentlicht') }}</dt>
                            <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $stats['paths']['published'] }}</dd>
                        </div>
                        <div class="px-4 py-5 text-center sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Entwurf') }}</dt>
                            <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $stats['paths']['draft'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Upgrade CTA -->
            @if($stats['students']['percent'] > 80 || $stats['storage']['percent'] > 80)
                <div class="mt-8 rounded-lg bg-teal-600 px-4 py-8 text-center sm:px-6">
                    <h3 class="text-lg font-medium text-white">{{ __('Mehr Kapazität benötigt?') }}</h3>
                    <p class="mt-2 text-sm text-teal-200">{{ __('Upgraden Sie auf einen höheren Plan für mehr Lernende, Speicher und Funktionen.') }}</p>
                    <a href="{{ route('billing.plans') }}" class="mt-4 inline-flex items-center rounded-lg bg-white px-4 py-2 text-sm font-medium text-teal-600 hover:bg-teal-50">
                        {{ __('Pläne ansehen') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
