<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirm Password - {{ config('app.name', 'Laravel') }}</title>
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
                <h2 class="auth-title">Confirm Password</h2>
                <p class="auth-subtitle">Please confirm your password to continue.</p>
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    Please check the form for errors.
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label>Password</label>
                    <input id="password" type="password" name="password" class="auth-input" required autocomplete="current-password">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Confirm</button>
            </form>
        </div>
    </div>
</body>
</html>
