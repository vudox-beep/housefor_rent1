@extends('layouts.admin')

@section('title', 'Dealer Management')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Registered Dealers</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Dealer ID</th>
                        <th>Business Name</th>
                        <th>Listings</th>
                        <th>Plan</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dealers as $dealer)
                        <tr>
                            <td>#{{ $dealer->id }}</td>
                            <td>
                                <strong>{{ $dealer->name }}</strong><br>
                                <span style="font-size: 0.85rem; color: #666;">{{ $dealer->email }}</span>
                            </td>
                            <td>{{ $dealer->listings_count }}</td>
                            <td>
                                @if($dealer->subscription_plan == 'gold')
                                    <span class="status-badge" style="background-color: #FEF3C7; color: #92400E;">Gold</span>
                                @else
                                    <span class="status-badge" style="background-color: #E5E7EB; color: #374151;">Basic</span>
                                @endif
                            </td>
                            <td>{{ $dealer->subscription_expires_at ? \Carbon\Carbon::parse($dealer->subscription_expires_at)->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                @if($dealer->status == 'active')
                                    <span class="status-badge status-active">Active</span>
                                @else
                                    <span class="status-badge status-expired">{{ ucfirst($dealer->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($dealer->status == 'active')
                                    <form action="{{ route('admin.users.suspend', $dealer->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-danger" onclick="return confirm('Are you sure you want to suspend this dealer?')">Suspend</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.activate', $dealer->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-view" onclick="return confirm('Are you sure you want to activate this dealer?')">Activate</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 1.5rem;">
            {{ $dealers->links() }}
        </div>
    </div>
@endsection