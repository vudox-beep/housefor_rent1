@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Registered Users</h3>
            <!-- Filter or Search could go here -->
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Listings</th>
                        <th>Joined Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" style="width: 32px; height: 32px; border-radius: 50%;">
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->listings_count }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($user->status == 'active')
                                    <span class="status-badge status-active">Active</span>
                                @else
                                    <span class="status-badge status-blocked">Suspended</span>
                                @endif
                            </td>
                            <td>
                                @if($user->status == 'active')
                                    <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-danger" onclick="return confirm('Are you sure you want to suspend this user?')">Suspend</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-view" onclick="return confirm('Are you sure you want to activate this user?')">Activate</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection