@extends('layouts.admin')

@section('title', 'Payment Transactions')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Transactions</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $txn)
                        <tr>
                            <td><code>{{ $txn->transaction_id }}</code></td>
                            <td>
                                <div style="font-weight: 600;">{{ $txn->user->name ?? 'Unknown' }}</div>
                                <div style="color: var(--muted-text); font-size: 0.85rem;">{{ $txn->user->email ?? '' }}</div>
                                <div style="color: var(--muted-text); font-size: 0.85rem;">{{ $txn->user->phone ?? '' }}</div>
                            </td>
                            <td>{{ ucfirst($txn->user->subscription_plan ?? 'basic') }}</td>
                            <td>{{ ucfirst($txn->type ?? 'N/A') }}</td>
                            <td>{{ $txn->currency }} {{ number_format($txn->amount) }}</td>
                            <td>{{ ucfirst($txn->payment_method) }}</td>
                            <td>{{ $txn->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($txn->status == 'completed')
                                    <span class="status-badge status-active">Success</span>
                                @elseif($txn->status == 'pending')
                                    <span class="status-badge status-pending">Pending</span>
                                @else
                                    <span class="status-badge status-expired">Failed</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 1.5rem;">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
