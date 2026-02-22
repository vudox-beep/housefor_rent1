<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card auth-card-wide">
            <div class="auth-header">
                <a href="{{ route('home') }}" style="display: block; margin-bottom: 1rem; color: var(--primary-color); font-weight: 800; font-size: 1.5rem;">
                    HouseForRent
                </a>
                <h2 class="auth-title">Create an Account</h2>
                <p class="auth-subtitle">Join us to find or list properties today</p>

                @if(\App\Models\Setting::getBool('free_trial_enabled', true))
                    <div style="margin-top: 1rem; background: rgba(217, 119, 6, 0.12); border: 1px solid rgba(217, 119, 6, 0.25); padding: 0.9rem 1rem; border-radius: 0.75rem; font-weight: 600;">
                        ⏰ <strong>Free Trial Available:</strong> Dealers get 20 photos + 1 video per listing for 1 month free! After trial, basic accounts are limited to 1 image per listing.
                    </div>
                @endif
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    Please check the form for errors.
                </div>
            @endif

            <div class="social-login">
                <a href="{{ route('auth.google') }}" class="btn-google">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23.52 12.29C23.52 11.43 23.44 10.61 23.29 9.82H12V14.44H18.47C18.18 15.98 17.31 17.29 16 18.16V21.24H19.87C22.13 19.16 23.52 16.03 23.52 12.29Z" fill="#4285F4"/><path d="M12 24C15.24 24 17.96 22.92 19.88 21.15L16 18.16C14.93 18.88 13.56 19.32 12 19.32C8.87 19.32 6.22 17.21 5.27 14.36H1.26V17.47C3.17 21.26 7.28 24 12 24Z" fill="#34A853"/><path d="M5.27 14.36C5.03 13.65 4.9 12.89 4.9 12.11C4.9 11.33 5.03 10.57 5.27 9.86V6.75H1.26C0.46 8.35 0 10.18 0 12.11C0 14.04 0.46 15.87 1.26 17.47L5.27 14.36Z" fill="#FBBC05"/><path d="M12 4.67C13.76 4.67 15.34 5.28 16.58 6.47L19.96 3.09C17.95 1.22 15.24 0 12 0C7.28 0 3.17 2.74 1.26 6.53L5.27 9.64C6.22 6.79 8.87 4.67 12 4.67Z" fill="#EA4335"/></svg>
                    Sign up with Google
                </a>
            </div>

            <div class="divider">
                <span>Or sign up with email</span>
            </div>

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="auth-input" placeholder="e.g. John Doe" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="auth-input" placeholder="name@example.com" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Country</label>
                        <select name="country" class="auth-select" required>
                            <option value="">Select Country</option>
                            <option value="ZM" {{ old('country') == 'ZM' ? 'selected' : '' }}>Zambia</option>
                            <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>United States</option>
                            <option value="ZA" {{ old('country') == 'ZA' ? 'selected' : '' }}>South Africa</option>
                            <!-- Add more as needed -->
                        </select>
                        @error('country')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <div class="phone-group">
                            <select name="phone_code" class="auth-select phone-code">
                                <option value="+260" {{ old('phone_code') == '+260' ? 'selected' : '' }}>+260</option>
                                <option value="+44" {{ old('phone_code') == '+44' ? 'selected' : '' }}>+44</option>
                                <option value="+1" {{ old('phone_code') == '+1' ? 'selected' : '' }}>+1</option>
                                <option value="+27" {{ old('phone_code') == '+27' ? 'selected' : '' }}>+27</option>
                            </select>
                            <input type="tel" name="phone" class="auth-input" placeholder="977 123 456" value="{{ old('phone') }}" required>
                        </div>
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" class="auth-input" value="{{ old('dob') }}" required>
                        @error('dob')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Register As</label>
                        <select name="role" class="auth-select" required onchange="updateRoleInfo()">
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User - Browse & Contact</option>
                            <option value="dealer" {{ old('role') == 'dealer' ? 'selected' : '' }}>Dealer - Post Listings</option>
                        </select>
                        @error('role')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <div id="role-info" style="margin-top: 1rem; padding: 1rem; background: rgba(217, 119, 6, 0.08); border-left: 4px solid rgba(217, 119, 6, 0.5); border-radius: 0.5rem; font-size: 0.9rem; color: var(--dark-text);">
                            <p id="role-description"></p>
                        </div>
                    </div>

                    <script>
                    function updateRoleInfo() {
                        const select = document.querySelector('select[name="role"]');
                        const roleInfo = document.getElementById('role-description');
                        
                        if (select.value === 'dealer') {
                            roleInfo.innerHTML = `<strong>🏠 Dealer Account</strong><br/>
                                ✓ Free Trial: 20 images + 1 video for 1 month<br/>
                                ✓ After Trial: 1 image per listing (upgrade to Gold for unlimited)<br/>
                                ✓ Gold Plan: 5 images + videos per listing<br/>
                                ✓ Manage agents & track leads`;
                        } else {
                            roleInfo.innerHTML = `<strong>👤 User Account</strong><br/>
                                ✓ Browse all property listings<br/>
                                ✓ Save favorite properties<br/>
                                ✓ Contact dealers & agents<br/>
                                ✓ No listing restrictions`;
                        }
                    }
                    document.addEventListener('DOMContentLoaded', updateRoleInfo);
                    </script>

                    <div class="form-group">
                        <label>Favorite Color</label>
                        <select name="color" class="auth-select">
                            <option value="">Select Color</option>
                            <option value="Red" style="color: red;">Red</option>
                            <option value="Blue" style="color: blue;">Blue</option>
                            <option value="Green" style="color: green;">Green</option>
                            <option value="Yellow" style="color: #D97706;">Yellow</option>
                            <option value="Purple" style="color: purple;">Purple</option>
                            <option value="Orange" style="color: orange;">Orange</option>
                            <option value="Black" style="color: black;">Black</option>
                            <option value="White" style="color: #999;">White</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="auth-input" placeholder="Min. 8 characters" required>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="auth-input" placeholder="Repeat password" required>
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="terms" id="terms" required>
                    <label for="terms" class="checkbox-label">
                        I agree to the Terms of Service. I understand that if I am found posting fake listings, <strong>legal action will be taken against me</strong>.
                    </label>
                </div>
                @error('terms')
                    <span class="error-message">{{ $message }}</span>
                @enderror

                <button type="submit" class="btn-submit">Create Account</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="{{ route('login') }}" class="auth-link">Log in</a>
            </div>
        </div>
    </div>
</body>
</html>
