@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total Users</span>
            <span class="stat-value">{{ number_format($total_users) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Registered Dealers</span>
            <span class="stat-value">{{ number_format($total_dealers) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Listings</span>
            <span class="stat-value">{{ number_format(\App\Models\Listing::count()) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Collected (ZMW)</span>
            <span class="stat-value">{{ number_format($total_collected_zwm, 2) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Pending Reports</span>
            <span class="stat-value">{{ $pending_reports }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Pending Payments (ZMW)</span>
            <span class="stat-value">{{ number_format($pending_collected_zwm, 2) }}</span>
        </div>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3 class="card-title">Marketing Offer</h3>
        </div>
        <form method="POST" action="{{ route('admin.settings.free-trial') }}">
            @csrf
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                <div>
                    <div style="font-weight: 700;">Free Trial (20 photos + video for 1 month)</div>
                    <div style="color: var(--muted-text); font-size: 0.9rem;">Applies to new registrations only.</div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                        <input type="checkbox" name="free_trial_enabled" value="1" {{ $free_trial_enabled ? 'checked' : '' }}>
                        Enabled
                    </label>
                    <button type="submit" class="btn-action btn-view">Save</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3 class="card-title">Listings Status</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Listing</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_listings as $listing)
                        <tr>
                            <td style="font-weight: 600;">{{ $listing->title }}</td>
                            <td>{{ $listing->user?->name ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $listing->status === 'active' ? 'active' : ($listing->status === 'rented' ? 'pending' : 'blocked') }}">
                                    {{ ucfirst($listing->status) }}
                                </span>
                            </td>
                            <td>{{ $listing->updated_at?->diffForHumans() }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.listings.status', $listing) }}" style="display: flex; align-items: center; gap: 0.5rem;">
                                    @csrf
                                    <select name="status" style="height: 36px; border: 1px solid var(--border-color); border-radius: 6px; padding: 0 0.6rem;">
                                        <option value="active" {{ $listing->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="rented" {{ $listing->status === 'rented' ? 'selected' : '' }}>Rented</option>
                                        <option value="sold" {{ $listing->status === 'sold' ? 'selected' : '' }}>Sold</option>
                                    </select>
                                    <button type="submit" class="btn-action btn-view" style="margin-right: 0;">Save</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Activity</h3>
            <button class="btn-action btn-view">View All</button>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date Joined</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_users as $user)
                        <tr>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                @if($user->status == 'active')
                                    <span class="status-badge status-active">Active</span>
                                @elseif($user->status == 'suspended')
                                    <span class="status-badge status-blocked">Suspended</span>
                                @else
                                    <span class="status-badge status-pending">{{ ucfirst($user->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
