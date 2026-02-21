@extends('layouts.dealer')

@section('title', 'Leads & Messages')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Inquiries</h3>
        </div>
        
        <div class="lead-list">
            @foreach($leads as $lead)
                <div class="lead-item" style="{{ $lead->read ? '' : 'background-color: #FEF9F0;' }}">
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <div class="lead-info">
                                <h4 style="color: var(--primary-color);">{{ $lead->name }}</h4>
                                <span style="font-size: 0.85rem; color: #666;">Interested in: <strong>{{ $lead->listing }}</strong></span>
                            </div>
                            <span class="lead-meta">{{ $lead->date }}</span>
                        </div>
                        
                        <div class="lead-message">
                            {{ $lead->message }}
                        </div>
                        
                        <div style="margin-top: 0.8rem; display: flex; gap: 1rem;">
                            <a href="mailto:{{ $lead->email }}" class="btn-action btn-view" style="text-decoration: none;">
                                Reply via Email
                            </a>
                            <a href="tel:{{ $lead->phone }}" class="btn-action btn-view" style="text-decoration: none;">
                                Call {{ $lead->phone }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection