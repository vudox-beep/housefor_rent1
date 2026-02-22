<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    private function googleRedirectUrl(): string
    {
        $configured = config('services.google.redirect');
        return $configured ?: url('/auth/google/callback');
    }

    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')
                ->redirectUrl($this->googleRedirectUrl())
                ->scopes(['openid', 'profile', 'email'])
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Google redirect failed: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['oauth' => 'Unable to redirect to Google. Please try again.']);
        }
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($this->googleRedirectUrl())
                ->user();
        } catch (\Exception $e) {
            Log::error('Google callback error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['oauth' => 'Unable to login using Google. Please try again.']);
        }

        if (!$googleUser || !$googleUser->getEmail()) {
            Log::error('Google user data missing');
            return redirect()->route('login')->withErrors(['oauth' => 'Unable to retrieve your Google information.']);
        }

        try {
            // Check if user exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                Auth::login($user, true);
                
                if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }

                if ($user->isAdmin()) {
                    return redirect()->intended(route('admin.dashboard', absolute: false));
                }
                if ($user->isDealer()) {
                    return redirect()->intended(route('dealer.dashboard', absolute: false));
                }

                return redirect()->intended(route('dashboard', absolute: false));
            }

            // Create new user with all required fields
            $newUser = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? explode('@', $googleUser->getEmail())[0],
                'email' => $googleUser->getEmail(),
                'password' => bcrypt(Str::random(24)),
                'email_verified_at' => now(),
                'role' => 'user',  // Default role
                'status' => 'active',  // Default status
                'subscription_plan' => 'basic',  // Default plan
                'trial_expires_at' => Setting::getBool('free_trial_enabled', true) ? now()->addMonth() : null,
            ]);

            Auth::login($newUser, true);

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error during Google signup: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['oauth' => 'Unable to create account. Please try again or contact support.']);
        } catch (\Exception $e) {
            Log::error('Unexpected error during Google signup: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['oauth' => 'An unexpected error occurred. Please try again.']);
        }
    }
}
