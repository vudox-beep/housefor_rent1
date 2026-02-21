<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - {{ config('app.name', 'Laravel') }}</title>
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
                <h2 class="auth-title">Reset Password</h2>
                <p class="auth-subtitle">Create a new password for your account.</p>
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    Please check the form for errors.
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="auth-form">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label>Email Address</label>
                    <input id="email" type="email" name="email" class="auth-input" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input id="password" type="password" name="password" class="auth-input" required autocomplete="new-password">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="auth-input" required autocomplete="new-password">
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Reset Password</button>
            </form>

            <div class="auth-footer">
                <a href="{{ route('login') }}" class="auth-link">Back to login</a>
            </div>
        </div>
    </div>
</body>
</html>
