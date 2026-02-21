@extends('layouts.dealer')

@section('title', 'Manage Agents')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Agents</h3>
            <button onclick="document.getElementById('addAgentModal').style.display='block'" 
                    style="background-color: var(--primary-color); color: white; padding: 0.5rem 1rem; border: none; border-radius: var(--radius-md); font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                + Add Agent
            </button>
        </div>
        
        @if($agents->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agents as $agent)
                            <tr>
                                <td style="width: 60px;">
                                    @if($agent->photo_path)
                                        <img src="{{ asset($agent->photo_path) }}" alt="{{ $agent->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                    @else
                                        <div style="width: 40px; height: 40px; background-color: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #888; font-weight: bold;">
                                            {{ substr($agent->name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td style="font-weight: 600;">{{ $agent->name }}</td>
                                <td>{{ $agent->phone }}</td>
                                <td>{{ $agent->email ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('dealer.agents.destroy', $agent->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this agent?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 1.5rem;">
                {{ $agents->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem 1rem;">
                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--muted-text); margin-bottom: 1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <p style="color: var(--muted-text); font-size: 1.1rem; margin-bottom: 1.5rem;">You haven't added any agents yet.</p>
                <button onclick="document.getElementById('addAgentModal').style.display='block'" 
                        style="background-color: var(--primary-color); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                    Add Your First Agent
                </button>
            </div>
        @endif
    </div>

    <!-- Add Agent Modal -->
    <div id="addAgentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100;">
        <div style="background: white; width: 90%; max-width: 500px; margin: 10vh auto; padding: 2rem; border-radius: var(--radius-lg); position: relative;">
            <button onclick="document.getElementById('addAgentModal').style.display='none'" 
                    style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            
            <h3 style="margin-bottom: 1.5rem;">Add New Agent</h3>
            
            <form action="{{ route('dealer.agents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Full Name</label>
                    <input type="text" name="name" required style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Phone Number</label>
                    <input type="text" name="phone" required style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email (Optional)</label>
                    <input type="email" name="email" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Photo (Optional)</label>
                    <input type="file" name="photo" accept="image/*" style="width: 100%;">
                </div>
                
                <button type="submit" style="width: 100%; background-color: var(--primary-color); color: white; padding: 0.8rem; border: none; border-radius: var(--radius-md); font-weight: 700; cursor: pointer;">
                    Add Agent
                </button>
            </form>
        </div>
    </div>
@endsection