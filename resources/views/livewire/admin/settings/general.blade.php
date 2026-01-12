<div>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-sm">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-gray-500 hover:text-teal-600 transition">{{ __('Dashboard') }}</a>
        <svg class="size-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('admin.settings.index') }}" wire:navigate class="text-gray-500 hover:text-teal-600 transition">{{ __('Einstellungen') }}</a>
        <svg class="size-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="font-medium text-gray-900">{{ __('Allgemein') }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Allgemeine Einstellungen') }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ __('Übersicht der Systemkonfiguration und Statistiken.') }}</p>
    </div>

    <!-- System Stats -->
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('Benutzer') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($this->systemStats['users']) }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('Lernpfade') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($this->systemStats['paths']) }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('Kategorien') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($this->systemStats['categories']) }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('PHP Version') }}</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ $this->systemStats['php_version'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('Laravel Version') }}</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ $this->systemStats['laravel_version'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Application Info -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="font-semibold text-gray-900">{{ __('Anwendung') }}</h2>
            </div>
            <div class="p-6">
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('App-Name') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->appInfo['name'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('URL') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->appInfo['url'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Umgebung') }}</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $this->appInfo['env'] === 'production' ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20' }}">
                                {{ $this->appInfo['env'] }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Debug-Modus') }}</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $this->appInfo['debug'] ? 'bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20' : 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' }}">
                                {{ $this->appInfo['debug'] ? __('Aktiv') : __('Inaktiv') }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Zeitzone') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->appInfo['timezone'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Sprache') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ strtoupper($this->appInfo['locale']) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- LernPfad Config -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="font-semibold text-gray-900">{{ __('Lernpfad-Einstellungen') }}</h2>
            </div>
            <div class="p-6">
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Bestehensgrenze') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->lernpfadConfig['passing_score'] }}%</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Max. Testversuche') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->lernpfadConfig['max_attempts'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Zertifikat-Gültigkeit') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->lernpfadConfig['certificate_validity'] }} {{ __('Jahre') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Max. Dateigröße') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->lernpfadConfig['max_file_size'] }} MB</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Gamification') }}</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $this->lernpfadConfig['gamification_enabled'] ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10' }}">
                                {{ $this->lernpfadConfig['gamification_enabled'] ? __('Aktiv') : __('Inaktiv') }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Mail Config -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="font-semibold text-gray-900">{{ __('E-Mail-Konfiguration') }}</h2>
            </div>
            <div class="p-6">
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Mailer') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->mailConfig['mailer'] ?? __('Nicht konfiguriert') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Absender-Adresse') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->mailConfig['from_address'] ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Absender-Name') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $this->mailConfig['from_name'] ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="font-semibold text-gray-900">{{ __('Weitere Einstellungen') }}</h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <a href="{{ route('admin.settings.billing') }}" wire:navigate class="group flex items-center justify-between rounded-lg p-3 hover:bg-gray-50 transition transition">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-green-50 ring-1 ring-green-600/10">
                                <svg class="size-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">{{ __('Abrechnung') }}</span>
                        </div>
                        <svg class="size-5 text-gray-400 group-hover:text-teal-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('admin.settings.ai') }}" wire:navigate class="group flex items-center justify-between rounded-lg p-3 hover:bg-gray-50 transition transition">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-purple-50 ring-1 ring-purple-600/10">
                                <svg class="size-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">{{ __('KI-Einstellungen') }}</span>
                        </div>
                        <svg class="size-5 text-gray-400 group-hover:text-teal-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" wire:navigate class="group flex items-center justify-between rounded-lg p-3 hover:bg-gray-50 transition transition">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-teal-50 ring-1 ring-teal-600/10">
                                <svg class="size-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">{{ __('Kategorien verwalten') }}</span>
                        </div>
                        <svg class="size-5 text-gray-400 group-hover:text-teal-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
