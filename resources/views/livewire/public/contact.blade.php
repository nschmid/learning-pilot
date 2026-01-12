<div>
    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-base font-semibold text-teal-600">{{ __('Kontakt') }}</h2>
                <p class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    {{ __('Sprechen Sie mit uns') }}
                </p>
                <p class="mt-6 text-lg text-gray-600">
                    {{ __('Haben Sie Fragen oder möchten Sie eine Demo? Wir freuen uns auf Ihre Nachricht.') }}
                </p>
            </div>

            <div class="mx-auto mt-16 max-w-xl">
                @if($submitted)
                    <div class="rounded-lg bg-green-50 p-6 text-center">
                        <svg class="mx-auto size-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('Nachricht gesendet!') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            {{ __('Vielen Dank für Ihre Nachricht. Wir werden uns in Kürze bei Ihnen melden.') }}
                        </p>
                        <button wire:click="$set('submitted', false)" class="mt-6 text-sm font-semibold text-teal-600 hover:text-teal-500">
                            {{ __('Weitere Nachricht senden') }}
                        </button>
                    </div>
                @else
                    <form wire:submit="submit" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-900">{{ __('Name') }} *</label>
                                <input
                                    type="text"
                                    id="name"
                                    wire:model="name"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                >
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-900">{{ __('E-Mail') }} *</label>
                                <input
                                    type="email"
                                    id="email"
                                    wire:model="email"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                >
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-900">{{ __('Schule / Organisation') }}</label>
                            <input
                                type="text"
                                id="company"
                                wire:model="company"
                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                            >
                            @error('company') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-900">{{ __('Betreff') }} *</label>
                            <select
                                id="subject"
                                wire:model="subject"
                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                            >
                                @foreach($subjects as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-900">{{ __('Nachricht') }} *</label>
                            <textarea
                                id="message"
                                wire:model="message"
                                rows="5"
                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                            ></textarea>
                            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <button
                                type="submit"
                                class="w-full rounded-lg bg-teal-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600 transition"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                            >
                                <span wire:loading.remove>{{ __('Nachricht senden') }}</span>
                                <span wire:loading>{{ __('Wird gesendet...') }}</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <!-- Contact Info -->
            <div class="mx-auto mt-16 max-w-2xl">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                    <div class="text-center">
                        <svg class="mx-auto size-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <h3 class="mt-4 text-base font-semibold text-gray-900">{{ __('E-Mail') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">hello@learningpilot.ch</p>
                    </div>
                    <div class="text-center">
                        <svg class="mx-auto size-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <h3 class="mt-4 text-base font-semibold text-gray-900">{{ __('Antwortzeit') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('Innerhalb von 24 Stunden') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
