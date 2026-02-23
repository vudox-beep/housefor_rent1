@extends('layouts.dealer')

@section('title', 'My Profile')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Profile Details -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Account Details</h3>
            </div>
            
            <form action="{{ route('dealer.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div style="text-align: center; margin-bottom: 2rem;">
                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" 
                         alt="{{ $user->name }}" 
                         style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem;">
                    
                    <div style="margin-top: 0.5rem;">
                        <label for="avatar" style="cursor: pointer; color: var(--primary-color); font-weight: 600; font-size: 0.9rem;">Change Photo</label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;" onchange="this.form.submit()">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    @error('name') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email Address</label>
                    <input type="email" value="{{ $user->email }}" disabled 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background-color: #f3f4f6; color: #6b7280;">
                    <p style="font-size: 0.8rem; color: var(--muted-text); margin-top: 0.25rem;">Email cannot be changed.</p>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required 
                           style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    @error('phone') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <button type="submit" style="width: 100%; background-color: var(--primary-color); color: white; padding: 0.8rem; border: none; border-radius: var(--radius-md); font-weight: 700; cursor: pointer;">
                    Update Profile
                </button>
            </form>
        </div>

        <!-- Right Column: Password & Subscription -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- Subscription Status -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Subscription Status</h3>
                    <a href="{{ route('dealer.subscription') }}" style="font-size: 0.9rem; color: var(--primary-color); text-decoration: none; font-weight: 600;">Upgrade / Renew</a>
                </div>
                
                <div style="padding: 1rem 0;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--muted-text);">Current Plan</span>
                        <span style="font-weight: 700; color: var(--dark-text);">{{ $subscription->plan }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--muted-text);">Status</span>
                        <span style="font-weight: 700; color: {{ $subscription->status === 'Active' ? '#166534' : ($subscription->status === 'Trial Active' ? '#166534' : '#92400E') }};">
                            {{ $subscription->status }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted-text);">Expires On</span>
                        <span style="font-weight: 700; color: var(--dark-text);">{{ $subscription->expires_on }}</span>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Change Password</h3>
                </div>
                
                <form action="{{ route('dealer.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Current Password</label>
                        <input type="password" name="current_password" required 
                               style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        @error('current_password') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">New Password</label>
                        <input type="password" name="password" required 
                               style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        @error('password') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required 
                               style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>

                    <button type="submit" style="width: 100%; background-color: var(--dark-text); color: white; padding: 0.8rem; border: none; border-radius: var(--radius-md); font-weight: 700; cursor: pointer;">
                        Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection