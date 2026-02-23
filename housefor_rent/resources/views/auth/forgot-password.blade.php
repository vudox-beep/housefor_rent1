<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - {{ config('app.name', 'Laravel') }}</title>
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
                <h2 class="auth-title">Forgot Password</h2>
                <p class="auth-subtitle">We’ll send a reset link to your email.</p>
            </div>

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-error">
                    Please check the form for errors.
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label>Email Address</label>
                    <input id="email" type="email" name="email" class="auth-input" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Send Reset Link</button>
            </form>

            <div class="auth-footer">
                <a href="{{ route('login') }}" class="auth-link">Back to login</a>
            </div>
        </div>
    </div>
</body>
</html>
