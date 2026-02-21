@extends('layouts.dealer')

@section('title', 'Dealer Dashboard')

@section('content')
    <div style="display: flex; justify-content: flex-end; margin-bottom: 2rem;">
        <a href="{{ route('listings.create') }}" style="background-color: var(--primary-color); color: white; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; box-shadow: var(--hover-shadow);">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Post New Listing
        </a>
    </div>

    <!-- Subscription Status Card -->
    <div class="card" style="background: linear-gradient(135deg, #3E2723 0%, #5D4037 100%); color: white;">
        <div class="card-header" style="margin-bottom: 1rem;">
            <h3 class="card-title" style="color: white;">Current Subscription</h3>
            @if($subscription->status === 'Active')
                <span class="status-badge status-active" style="background-color: rgba(255,255,255,0.2); color: white;">{{ $subscription->status }}</span>
            @elseif($subscription->status === 'Expired')
                <span class="status-badge status-expired" style="background-color: rgba(255,255,255,0.2); color: white;">{{ $subscription->status }}</span>
            @else
                <span class="status-badge status-pending" style="background-color: rgba(255,255,255,0.2); color: white;">{{ $subscription->status }}</span>
            @endif
        </div>
        <div style="display: flex; gap: 4rem; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <span style="display: block; font-size: 0.9rem; opacity: 0.8; margin-bottom: 0.5rem;">Plan</span>
                <span style="font-size: 1.5rem; font-weight: 800;">{{ $subscription->plan }}</span>
            </div>
            @if(in_array($subscription->status, ['Active', 'Trial']))
                <div>
                    <span style="display: block; font-size: 0.9rem; opacity: 0.8; margin-bottom: 0.5rem;">Days Remaining</span>
                    <span style="font-size: 2.5rem; font-weight: 800; color: #FCD34D;">{{ $subscription->days_remaining }} Days</span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.9rem; opacity: 0.8; margin-bottom: 0.5rem;">Expires On</span>
                    <span style="font-size: 1.2rem;">{{ $subscription->expires_on }}</span>
                </div>
            @else
                <div>
                    <span style="display: block; font-size: 0.9rem; opacity: 0.8; margin-bottom: 0.5rem;">Upload Limits</span>
                    <span style="font-size: 1.2rem;">1 photo, no video</span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.9rem; opacity: 0.8; margin-bottom: 0.5rem;">Upgrade</span>
                    <a href="{{ route('dealer.subscription') }}" style="font-size: 1.2rem; color: #FCD34D; text-decoration: underline;">Go Gold</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Active Listings</span>
            <span class="stat-value">{{ $stats->active_listings }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Views</span>
            <span class="stat-value">{{ number_format($stats->total_views) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Leads</span>
            <span class="stat-value">{{ number_format($stats->total_leads) }}</span>
        </div>
    </div>

    <!-- Analytics / Top Listings -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Top Performing Listings</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Listing Name</th>
                        <th>Views</th>
                        <th>Leads Generated</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analytics as $item)
                        <tr>
                            <td>{{ $item->listing }}</td>
                            <td>{{ $item->views }}</td>
                            <td>{{ $item->leads }}</td>
                            <td>
                                <div style="width: 100%; background-color: #eee; height: 6px; border-radius: 3px; overflow: hidden;">
                                    <div style="width: {{ min(($item->views / 1500) * 100, 100) }}%; background-color: var(--primary-color); height: 100%;"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
