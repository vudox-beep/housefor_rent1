@extends('layouts.dealer')

@section('title', 'My Listings')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Your Listings</h3>
            <a href="{{ route('listings.create') }}" style="background-color: var(--primary-color); color: white; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-size: 0.85rem; font-weight: 600; text-decoration: none;">
                + New Listing
            </a>
        </div>
        
        @if($listings->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listings as $listing)
                            <tr>
                                <td style="width: 80px;">
                                    @if($listing->images && is_array($listing->images) && count($listing->images) > 0)
                                        <img src="{{ asset($listing->images[0]) }}" alt="{{ $listing->title }}" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <div style="width: 60px; height: 40px; background: var(--light-bg); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <svg width="20" height="20" fill="none" stroke="var(--muted-text)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 600;">{{ $listing->title }}</div>
                                    <div style="font-size: 0.8rem; color: var(--muted-text);">{{ ucfirst($listing->type) }} &bull; {{ ucfirst($listing->category) }}</div>
                                </td>
                                <td>{{ $listing->location }}</td>
                                <td>{{ $listing->currency }} {{ number_format($listing->price) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('dealer.listings.status', $listing) }}" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                        @csrf
                                        <span class="status-badge status-{{ $listing->status === 'active' ? 'active' : ($listing->status === 'rented' ? 'pending' : 'blocked') }}">
                                            {{ ucfirst($listing->status) }}
                                        </span>
                                        <select name="status" style="height: 34px; border: 1px solid var(--border-color); border-radius: 6px; padding: 0 0.6rem;">
                                            <option value="active" {{ $listing->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="rented" {{ $listing->status === 'rented' ? 'selected' : '' }}>Rented</option>
                                            <option value="sold" {{ $listing->status === 'sold' ? 'selected' : '' }}>Sold</option>
                                        </select>
                                        <button type="submit" class="btn-action btn-view" style="margin-right: 0;">Save</button>
                                    </form>
                                </td>
                                <td>{{ $listing->views }}</td>
                                <td>
                                    <div style="display: flex;">
                                        <a href="{{ route('listings.show', $listing->public_id) }}" class="btn-action btn-view" target="_blank">View</a>
                                        <a href="{{ route('listings.edit', $listing->public_id) }}" class="btn-action" style="background-color: #F3F4F6; color: #374151;">Edit</a>
                                        <form action="{{ route('listings.destroy', $listing->public_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this listing?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 1.5rem;">
                {{ $listings->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem 1rem;">
                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--muted-text); margin-bottom: 1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <p style="color: var(--muted-text); font-size: 1.1rem; margin-bottom: 1.5rem;">You haven't posted any listings yet.</p>
                <a href="{{ route('listings.create') }}" style="background-color: var(--primary-color); color: white; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; text-decoration: none;">
                    Create Your First Listing
                </a>
            </div>
        @endif
    </div>
@endsection
