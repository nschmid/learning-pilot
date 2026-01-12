<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Einstellungen') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('Verwalte dein Profil und deine Einstellungen.') }}</p>
        </div>

        <div class="space-y-6">
            <!-- Profile Information -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Profilinformationen') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Aktualisiere dein Profil und deine E-Mail-Adresse.') }}</p>

                    @if (session('profile_status'))
                        <div class="mt-4 rounded-md bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800">{{ session('profile_status') }}</p>
                        </div>
                    @endif

                    <form wire:submit="updateProfile" class="mt-6 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                            <input wire:model="name" type="text" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" required>
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">{{ __('E-Mail') }}</label>
                            <input wire:model="email" type="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" required>
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700">{{ __('Bio') }}</label>
                            <textarea wire:model="bio" id="bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"></textarea>
                            @error('bio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-teal-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                {{ __('Speichern') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Passwort ändern') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Stelle sicher, dass dein Konto ein sicheres Passwort verwendet.') }}</p>

                    @if (session('password_status'))
                        <div class="mt-4 rounded-md bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800">{{ session('password_status') }}</p>
                        </div>
                    @endif

                    <form wire:submit="updatePassword" class="mt-6 space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">{{ __('Aktuelles Passwort') }}</label>
                            <input wire:model="current_password" type="password" id="current_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" autocomplete="current-password">
                            @error('current_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Neues Passwort') }}</label>
                            <input wire:model="password" type="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" autocomplete="new-password">
                            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Passwort bestätigen') }}</label>
                            <input wire:model="password_confirmation" type="password" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" autocomplete="new-password">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-teal-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                {{ __('Passwort ändern') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notification Preferences -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Benachrichtigungen') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Lege fest, welche Benachrichtigungen du erhalten möchtest.') }}</p>

                    @if (session('notification_status'))
                        <div class="mt-4 rounded-md bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800">{{ session('notification_status') }}</p>
                        </div>
                    @endif

                    <form wire:submit="updateNotifications" class="mt-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex h-5 items-center">
                                    <input wire:model="email_progress" id="email_progress" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="email_progress" class="font-medium text-gray-700">{{ __('Fortschrittsbenachrichtigungen') }}</label>
                                    <p class="text-gray-500">{{ __('Erhalte E-Mails über deinen Lernfortschritt.') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex h-5 items-center">
                                    <input wire:model="email_feedback" id="email_feedback" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="email_feedback" class="font-medium text-gray-700">{{ __('Feedback-Benachrichtigungen') }}</label>
                                    <p class="text-gray-500">{{ __('Erhalte E-Mails, wenn ein Dozent Feedback zu deinen Aufgaben gibt.') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex h-5 items-center">
                                    <input wire:model="email_certificates" id="email_certificates" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="email_certificates" class="font-medium text-gray-700">{{ __('Zertifikat-Benachrichtigungen') }}</label>
                                    <p class="text-gray-500">{{ __('Erhalte E-Mails, wenn du ein Zertifikat erhältst.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-teal-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                {{ __('Einstellungen speichern') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Learning Preferences -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Lernpräferenzen') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Passe deine Lernumgebung an.') }}</p>

                    @if (session('learning_status'))
                        <div class="mt-4 rounded-md bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800">{{ session('learning_status') }}</p>
                        </div>
                    @endif

                    <form wire:submit="updateLearningPreferences" class="mt-6">
                        <div class="space-y-4">
                            <div>
                                <label for="theme" class="block text-sm font-medium text-gray-700">{{ __('Erscheinungsbild') }}</label>
                                <select wire:model="theme" id="theme" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                    <option value="light">{{ __('Hell') }}</option>
                                    <option value="dark">{{ __('Dunkel') }}</option>
                                    <option value="system">{{ __('Systemeinstellung') }}</option>
                                </select>
                            </div>

                            <div class="flex items-start">
                                <div class="flex h-5 items-center">
                                    <input wire:model="autoplay" id="autoplay" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="autoplay" class="font-medium text-gray-700">{{ __('Video-Autoplay') }}</label>
                                    <p class="text-gray-500">{{ __('Videos automatisch abspielen, wenn du einen Schritt öffnest.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-teal-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                {{ __('Speichern') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
