<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in - {{ config('app.name', 'Laravel') }}</title>
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
                <h2 class="auth-title">Welcome Back</h2>
                <p class="auth-subtitle">Log in to manage your listings or favorites</p>
            </div>

            <div class="social-login">
                <a href="{{ route('auth.google') }}" class="btn-google">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23.52 12.29C23.52 11.43 23.44 10.61 23.29 9.82H12V14.44H18.47C18.18 15.98 17.31 17.29 16 18.16V21.24H19.87C22.13 19.16 23.52 16.03 23.52 12.29Z" fill="#4285F4"/><path d="M12 24C15.24 24 17.96 22.92 19.88 21.15L16 18.16C14.93 18.88 13.56 19.32 12 19.32C8.87 19.32 6.22 17.21 5.27 14.36H1.26V17.47C3.17 21.26 7.28 24 12 24Z" fill="#34A853"/><path d="M5.27 14.36C5.03 13.65 4.9 12.89 4.9 12.11C4.9 11.33 5.03 10.57 5.27 9.86V6.75H1.26C0.46 8.35 0 10.18 0 12.11C0 14.04 0.46 15.87 1.26 17.47L5.27 14.36Z" fill="#FBBC05"/><path d="M12 4.67C13.76 4.67 15.34 5.28 16.58 6.47L19.96 3.09C17.95 1.22 15.24 0 12 0C7.28 0 3.17 2.74 1.26 6.53L5.27 9.64C6.22 6.79 8.87 4.67 12 4.67Z" fill="#EA4335"/></svg>
                    Log in with Google
                </a>
            </div>

            <div class="divider">
                <span>Or log in with email</span>
            </div>

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="auth-input" placeholder="name@example.com" required autofocus>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="auth-input" required>
                </div>

                <div class="form-group">
                    <label>Login As</label>
                    <select name="role" class="auth-select">
                        <option value="user">User</option>
                        <option value="dealer">Dealer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="checkbox-group" style="margin: 1rem 0; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="remember" id="remember_me">
                        <label for="remember_me" class="checkbox-label" style="margin: 0;">Remember me</label>
                    </div>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: var(--primary-color);">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-submit">Log in</button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="{{ route('register') }}" class="auth-link">Sign up</a>
            </div>
        </div>
    </div>
</body>
</html>