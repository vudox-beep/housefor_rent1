<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="{{ route('home') }}" style="display: block; margin-bottom: 1rem; color: var(--primary-color); font-weight: 800; font-size: 1.5rem;">
                    HouseForRent
                </a>
                <h2 class="auth-title">Verify Your Email</h2>
                <p class="auth-subtitle">One more step before you can continue.</p>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="alert-success">
                    A new verification link has been sent to your email.
                </div>
            @endif

            <div style="color: var(--muted-text); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1rem;">
                We sent a verification link to <strong style="color: var(--dark-text);">{{ auth()->user()->email }}</strong>. Click the link in that email to verify your account.
                <div style="margin-top: 0.5rem;">If you don’t see it, check your spam/junk folder.</div>
            </div>

            <div class="auth-actions">
                <form method="POST" action="{{ route('verification.send') }}" style="flex: 1; min-width: 160px;">
                    @csrf
                    <button type="submit" class="btn-submit">Resend Email</button>
                </form>

                <form method="POST" action="{{ route('logout') }}" style="flex: 1; min-width: 160px;">
                    @csrf
                    <button type="submit" class="btn-secondary">Log Out</button>
                </form>
            </div>

            <div class="auth-footer">
                Already verified? <a href="{{ route('login') }}" class="auth-link">Log in</a>
            </div>
        </div>
    </div>
</body>
</html>
