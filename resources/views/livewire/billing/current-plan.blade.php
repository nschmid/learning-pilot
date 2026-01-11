<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Abonnement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Flash messages -->
            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-50 p-4">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg bg-red-50 p-4">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Trial Banner -->
            @if($trialDaysRemaining > 0)
                <div class="mb-6 rounded-lg bg-indigo-50 p-4">
                    <div class="flex">
                        <svg class="size-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-indigo-800">
                                {{ __('Sie befinden sich in der Testphase.') }}
                                {{ __(':days Tage verbleibend.', ['days' => $trialDaysRemaining]) }}
                            </p>
                            <p class="mt-1 text-sm text-indigo-700">
                                <a href="{{ route('billing.plans') }}" class="font-medium underline">{{ __('Jetzt einen Plan wählen') }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($hasTrialEnded && !$isSubscribed)
                <div class="mb-6 rounded-lg bg-red-50 p-4">
                    <div class="flex">
                        <svg class="size-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                {{ __('Ihre Testphase ist abgelaufen.') }}
                            </p>
                            <p class="mt-1 text-sm text-red-700">
                                <a href="{{ route('billing.plans') }}" class="font-medium underline">{{ __('Wählen Sie einen Plan um fortzufahren') }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Current Plan Card -->
                <div class="lg:col-span-2">
                    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Aktueller Plan') }}</h3>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                            @if($currentPlan)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-2xl font-bold text-gray-900">{{ $currentPlan['name'] }}</h4>
                                        <p class="mt-1 text-sm text-gray-500">{{ $currentPlan['description'] ?? '' }}</p>
                                    </div>
                                    @if($isSubscribed)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
                                            {{ __('Aktiv') }}
                                        </span>
                                    @elseif($trialDaysRemaining > 0)
                                        <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-800">
                                            {{ __('Testphase') }}
                                        </span>
                                    @endif
                                </div>

                                @if($isCancelled && $onGracePeriod)
                                    <div class="mt-4 rounded-lg bg-yellow-50 p-4">
                                        <p class="text-sm text-yellow-700">
                                            {{ __('Ihr Abonnement wurde gekündigt und endet am Ende der aktuellen Abrechnungsperiode.') }}
                                        </p>
                                        <button wire:click="resumeSubscription" class="mt-2 text-sm font-medium text-yellow-800 underline">
                                            {{ __('Abonnement reaktivieren') }}
                                        </button>
                                    </div>
                                @endif

                                <div class="mt-6 flex gap-4">
                                    <a href="{{ route('billing.plans') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                                        {{ $isSubscribed ? __('Plan ändern') : __('Plan wählen') }}
                                    </a>
                                    @if($isSubscribed && !$isCancelled)
                                        <button
                                            wire:click="cancelSubscription"
                                            wire:confirm="{{ __('Möchten Sie Ihr Abonnement wirklich kündigen?') }}"
                                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                        >
                                            {{ __('Abonnement kündigen') }}
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto size-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Kein aktiver Plan') }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ __('Wählen Sie einen Plan um alle Funktionen freizuschalten.') }}</p>
                                    <a href="{{ route('billing.plans') }}" class="mt-4 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                                        {{ __('Plan wählen') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <a href="{{ route('billing.invoices') }}" class="flex items-center rounded-lg bg-white p-4 shadow hover:bg-gray-50">
                            <svg class="size-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span class="ml-3 text-sm font-medium text-gray-900">{{ __('Rechnungen') }}</span>
                        </a>
                        <a href="{{ route('billing.payment-methods') }}" class="flex items-center rounded-lg bg-white p-4 shadow hover:bg-gray-50">
                            <svg class="size-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                            <span class="ml-3 text-sm font-medium text-gray-900">{{ __('Zahlungsmethoden') }}</span>
                        </a>
                        @if($isSubscribed)
                            <a href="{{ route('billing.portal') }}" class="flex items-center rounded-lg bg-white p-4 shadow hover:bg-gray-50">
                                <svg class="size-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="ml-3 text-sm font-medium text-gray-900">{{ __('Stripe Portal') }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Usage Stats -->
                <div class="space-y-6">
                    @if($usageStats)
                        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Nutzung') }}</h3>
                            </div>
                            <div class="border-t border-gray-200 px-4 py-5 sm:px-6 space-y-6">
                                <!-- Students -->
                                <div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">{{ __('Lernende') }}</span>
                                        <span class="font-medium text-gray-900">{{ $usageStats['students']['current'] }} / {{ $usageStats['students']['limit'] }}</span>
                                    </div>
                                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-2 rounded-full {{ $usageStats['students']['percent'] > 90 ? 'bg-red-500' : ($usageStats['students']['percent'] > 70 ? 'bg-yellow-500' : 'bg-indigo-600') }}" style="width: {{ $usageStats['students']['percent'] }}%"></div>
                                    </div>
                                </div>

                                <!-- Instructors -->
                                <div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">{{ __('Dozenten') }}</span>
                                        <span class="font-medium text-gray-900">{{ $usageStats['instructors']['current'] }} / {{ $usageStats['instructors']['limit'] }}</span>
                                    </div>
                                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-2 rounded-full {{ $usageStats['instructors']['percent'] > 90 ? 'bg-red-500' : ($usageStats['instructors']['percent'] > 70 ? 'bg-yellow-500' : 'bg-indigo-600') }}" style="width: {{ $usageStats['instructors']['percent'] }}%"></div>
                                    </div>
                                </div>

                                <!-- Storage -->
                                <div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">{{ __('Speicher') }}</span>
                                        <span class="font-medium text-gray-900">{{ $usageStats['storage']['used_formatted'] }} / {{ $usageStats['storage']['limit_formatted'] }}</span>
                                    </div>
                                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-2 rounded-full {{ $usageStats['storage']['percent'] > 90 ? 'bg-red-500' : ($usageStats['storage']['percent'] > 70 ? 'bg-yellow-500' : 'bg-indigo-600') }}" style="width: {{ $usageStats['storage']['percent'] }}%"></div>
                                    </div>
                                </div>

                                <!-- AI Requests -->
                                <div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">{{ __('KI-Anfragen heute') }}</span>
                                        <span class="font-medium text-gray-900">{{ $usageStats['ai_requests']['today'] }} / {{ $usageStats['ai_requests']['daily_limit'] }}</span>
                                    </div>
                                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-2 rounded-full {{ $usageStats['ai_requests']['percent'] > 90 ? 'bg-red-500' : ($usageStats['ai_requests']['percent'] > 70 ? 'bg-yellow-500' : 'bg-indigo-600') }}" style="width: {{ $usageStats['ai_requests']['percent'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($currentPlan && isset($currentPlan['features']))
                        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Funktionen') }}</h3>
                            </div>
                            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                                <ul class="space-y-3">
                                    @foreach($currentPlan['features'] as $feature => $enabled)
                                        <li class="flex items-center gap-x-3">
                                            @if($enabled)
                                                <svg class="size-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="size-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H4.75A.75.75 0 014 10z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                            <span class="text-sm {{ $enabled ? 'text-gray-900' : 'text-gray-400' }}">
                                                @switch($feature)
                                                    @case('learning_paths') {{ __('Lernpfade') }} @break
                                                    @case('assessments') {{ __('Assessments') }} @break
                                                    @case('certificates') {{ __('Zertifikate') }} @break
                                                    @case('ai_tutor') {{ __('KI-Tutor') }} @break
                                                    @case('ai_practice') {{ __('KI-Übungen') }} @break
                                                    @case('analytics') {{ __('Erweiterte Statistiken') }} @break
                                                    @case('custom_branding') {{ __('Eigenes Branding') }} @break
                                                    @case('api_access') {{ __('API-Zugang') }} @break
                                                    @case('priority_support') {{ __('Prioritäts-Support') }} @break
                                                    @default {{ ucfirst(str_replace('_', ' ', $feature)) }}
                                                @endswitch
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
