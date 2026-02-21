<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class SocialAuthController extends Controller
{
    private function googleRedirectUrl(): string
    {
        $configured = config('services.google.redirect');
        return $configured ?: url('/auth/google/callback');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl($this->googleRedirectUrl())
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($this->googleRedirectUrl())
                ->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['oauth' => 'Unable to login using Google. Please try again.']);
        }

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

        $user = User::create([
            'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? explode('@', $googleUser->getEmail())[0],
            'email' => $googleUser->getEmail(),
            'password' => bcrypt(Str::random(24)),
            'email_verified_at' => now(),
        ]);

        Auth::login($user, true);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
