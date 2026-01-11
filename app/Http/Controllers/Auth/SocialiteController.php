<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class SocialiteController extends Controller
{
    /**
     * Supported OAuth providers.
     */
    protected array $providers = ['google', 'microsoft'];

    /**
     * Redirect to the OAuth provider.
     */
    public function redirect(string $provider): RedirectResponse
    {
        if (! $this->isValidProvider($provider)) {
            return redirect()->route('login')
                ->with('error', __('Ungültiger Anbieter.'));
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth callback.
     */
    public function callback(string $provider): RedirectResponse
    {
        if (! $this->isValidProvider($provider)) {
            return redirect()->route('login')
                ->with('error', __('Ungültiger Anbieter.'));
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (InvalidStateException $e) {
            return redirect()->route('login')
                ->with('error', __('Anmeldung abgebrochen. Bitte versuchen Sie es erneut.'));
        } catch (\Exception $e) {
            report($e);

            return redirect()->route('login')
                ->with('error', __('Anmeldung fehlgeschlagen. Bitte versuchen Sie es erneut.'));
        }

        // Find or create the user
        $user = $this->findOrCreateUser($socialUser, $provider);

        if (! $user) {
            return redirect()->route('login')
                ->with('error', __('Benutzer konnte nicht erstellt werden.'));
        }

        // Update the OAuth token
        $user->update([
            "oauth_{$provider}_id" => $socialUser->getId(),
            "oauth_{$provider}_token" => $socialUser->token,
            "oauth_{$provider}_refresh_token" => $socialUser->refreshToken,
        ]);

        // Log the user in
        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Find an existing user or create a new one.
     */
    protected function findOrCreateUser($socialUser, string $provider): ?User
    {
        // First, try to find by OAuth provider ID
        $user = User::where("oauth_{$provider}_id", $socialUser->getId())->first();

        if ($user) {
            return $user;
        }

        // Then, try to find by email
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Link the existing account to the OAuth provider
            $user->update([
                "oauth_{$provider}_id" => $socialUser->getId(),
            ]);

            return $user;
        }

        // Create a new user
        return User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
            'email' => $socialUser->getEmail(),
            'email_verified_at' => now(), // OAuth emails are verified
            'password' => Hash::make(Str::random(32)),
            'role' => UserRole::Learner,
            "oauth_{$provider}_id" => $socialUser->getId(),
            "oauth_{$provider}_token" => $socialUser->token,
            "oauth_{$provider}_refresh_token" => $socialUser->refreshToken,
            'profile_photo_path' => $socialUser->getAvatar(),
        ]);
    }

    /**
     * Check if the provider is valid.
     */
    protected function isValidProvider(string $provider): bool
    {
        return in_array($provider, $this->providers, true);
    }
}
