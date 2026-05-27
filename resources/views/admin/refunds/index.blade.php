@extends('base.base')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-undo me-2 text-primary"></i>Refund Management</h1>
            <p class="text-muted mb-0">Review and process customer refund requests</p>
        </div>
        <span class="badge bg-primary rounded-pill fs-6">{{ $refunds->count() }} refunds</span>
    </div>

    {{-- Status Filter Menu --}}
    <div class="mb-4">
        <div class="btn-group" role="group">
            <a href="{{ route('admin.refunds.index') }}" class="btn btn-outline-primary {{ !request('status') ? 'active' : '' }}">
                <i class="fas fa-list me-1"></i>All
            </a>
            <a href="{{ route('admin.refunds.index', ['status' => 'pending']) }}" class="btn btn-outline-warning {{ request('status') === 'pending' ? 'active' : '' }}">
                <i class="fas fa-clock me-1"></i>Pending
            </a>
            <a href="{{ route('admin.refunds.index', ['status' => 'approved']) }}" class="btn btn-outline-success {{ request('status') === 'approved' ? 'active' : '' }}">
                <i class="fas fa-check-circle me-1"></i>Approved
            </a>
            <a href="{{ route('admin.refunds.index', ['status' => 'rejected']) }}" class="btn btn-outline-danger {{ request('status') === 'rejected' ? 'active' : '' }}">
                <i class="fas fa-times-circle me-1"></i>Rejected
            </a>
            <a href="{{ route('admin.refunds.index', ['status' => 'completed']) }}" class="btn btn-outline-secondary {{ request('status') === 'completed' ? 'active' : '' }}">
                <i class="fas fa-check me-1"></i>Completed
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mt-3">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($refunds->isEmpty())
        <div class="text-center py-5 mt-4">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">No refunds to display.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Refund ID</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($refunds as $refund)
                        <tr>
                            <td><strong>#{{ $refund->id }}</strong></td>
                            <td>#{{ $refund->order_id }}</td>
                            <td>
                                {{ $refund->user->name }}<br>
                                <small class="text-muted">{{ $refund->user->email }}</small>
                            </td>
                            <td class="fw-semibold text-danger">
                                Rp {{ number_format($refund->amount, 0, ',', '.') }}
                            </td>
                            <td>
                                <small>{{ \Str::limit($refund->reason, 50) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $refund->status === 'pending' ? 'warning' : ($refund->status === 'approved' ? 'success' : ($refund->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($refund->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $refund->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                @if($refund->status === 'pending')
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveRefundModal{{ $refund->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectRefundModal{{ $refund->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @elseif($refund->status === 'approved')
                                    <form method="POST" action="{{ route('admin.refunds.complete', $refund->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Mark refund as completed?')">
                                            <i class="fas fa-check me-1"></i>Complete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                                <a href="{{ route('orders.show', $refund->order_id) }}" class="btn btn-sm btn-outline-primary">View Order</a>
                            </td>
                        </tr>

                        <!-- Approve Refund Modal -->
                        <div class="modal fade" id="approveRefundModal{{ $refund->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Approve Refund #{{ $refund->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.refunds.approve', $refund->id) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <p class="text-muted small">Refund Amount</p>
                                                <p class="mb-0 fw-semibold text-success">Rp {{ number_format($refund->amount, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <p class="text-muted small">Customer Reason</p>
                                                <p class="mb-0">{{ $refund->reason }}</p>
                                            </div>
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Stock will be automatically restored to the fulfilling store.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Approve Refund</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Refund Modal -->
                        <div class="modal fade" id="rejectRefundModal{{ $refund->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Refund #{{ $refund->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.refunds.reject', $refund->id) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <p class="text-muted small">Admin Notes (Optional)</p>
                                                <textarea class="form-control" name="reason" rows="3" placeholder="Explain why this refund is being rejected..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Reject Refund</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
