@extends('layouts.admin')

@section('title', 'Reports & Issues')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">User Reports</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reporter</th>
                        <th>Reported Entity</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->created_at->format('M d, Y') }}</td>
                            <td>{{ $report->reporter->name ?? 'Unknown' }}</td>
                            <td>
                                @if($report->reportable_type == 'App\Models\Listing')
                                    Listing: #{{ $report->reportable_id }}
                                @else
                                    {{ class_basename($report->reportable_type) }}: #{{ $report->reportable_id }}
                                @endif
                            </td>
                            <td><span style="font-weight: 600; color: #C81E1E;">{{ $report->type }}</span></td>
                            <td>{{ Str::limit($report->reason, 50) }}</td>
                            <td>
                                @if($report->status == 'pending')
                                    <span class="status-badge status-pending">Pending</span>
                                @else
                                    <span class="status-badge status-resolved">Resolved</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn-action btn-view">View Details</button>
                                <button class="btn-action btn-view">Mark Resolved</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection