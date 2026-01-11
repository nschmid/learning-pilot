<div>
    <!-- Header -->
    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-base font-semibold text-indigo-600">{{ __('Funktionen') }}</h2>
                <p class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    {{ __('Alles für erfolgreiches E-Learning') }}
                </p>
                <p class="mt-6 text-lg text-gray-600">
                    {{ __('LearningPilot bietet alle Werkzeuge, die Sie für modernes, effektives Lernen benötigen.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="bg-gray-50 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-2">
                @foreach($categories as $category)
                    <div class="relative bg-white rounded-2xl p-8 shadow-sm ring-1 ring-gray-200">
                        <h3 class="text-xl font-bold text-gray-900">{{ $category['title'] }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ $category['description'] }}</p>
                        <ul class="mt-6 space-y-3">
                            @foreach($category['features'] as $feature)
                                <li class="flex items-start gap-x-3">
                                    <svg class="h-5 w-5 flex-none text-indigo-600 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="bg-indigo-600">
        <div class="mx-auto max-w-7xl px-6 py-16 sm:py-24 lg:flex lg:items-center lg:justify-between lg:px-8">
            <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                {{ __('Überzeugt?') }}
                <br>
                <span class="text-indigo-200">{{ __('Testen Sie LearningPilot 30 Tage kostenlos.') }}</span>
            </h2>
            <div class="mt-8 flex gap-x-4 lg:mt-0 lg:shrink-0">
                <a href="{{ route('register') }}" class="rounded-lg bg-white px-5 py-3 text-sm font-semibold text-indigo-600 shadow hover:bg-indigo-50 transition">
                    {{ __('Kostenlos starten') }}
                </a>
                <a href="{{ route('pricing') }}" class="rounded-lg border border-white px-5 py-3 text-sm font-semibold text-white hover:bg-white/10 transition">
                    {{ __('Preise ansehen') }}
                </a>
            </div>
        </div>
    </div>
</div>
