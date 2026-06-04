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

    {{-- Status Filter --}}
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
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Refund ID</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th style="max-width:180px;">Reason</th>
                        <th>Evidence</th>
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

                            {{-- Reason: truncated with tooltip --}}
                            <td style="max-width:180px;">
                                <div style="max-width:180px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; font-size:13px;"
                                     title="{{ $refund->reason }}">
                                    {{ $refund->reason }}
                                </div>
                            </td>

                            {{-- Evidence thumbnail --}}
                            <td>
                                @if($refund->image_path)
                                    <img src="{{ Storage::url($refund->image_path) }}"
                                         alt="Evidence"
                                         class="refund-thumb"
                                         data-img="{{ Storage::url($refund->image_path) }}"
                                         style="width:56px; height:56px; object-fit:cover; border-radius:6px; border:1px solid #dee2e6; cursor:zoom-in;"
                                         title="Click to enlarge">
                                @else
                                    <span class="text-muted small">None</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-{{ $refund->status === 'pending' ? 'warning text-dark' : ($refund->status === 'approved' ? 'success' : ($refund->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($refund->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $refund->created_at->format('M d, Y') }}</small>
                            </td>

                            {{-- Actions --}}
                            <td>
                                @if($refund->status === 'pending')
                                    <div class="d-flex gap-1 flex-wrap">
                                        {{-- View button --}}
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewRefundModal{{ $refund->id }}"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#approveRefundModal{{ $refund->id }}">
                                            <i class="fas fa-check me-1"></i>Approve
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectRefundModal{{ $refund->id }}">
                                            <i class="fas fa-times me-1"></i>Reject
                                        </button>
                                    </div>
                                @else
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewRefundModal{{ $refund->id }}"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('orders.show', $refund->order_id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Order
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>

                        {{-- ── VIEW modal (read-only) ─────────────────────────────── --}}
                        <div class="modal fade" id="viewRefundModal{{ $refund->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-file-alt me-2 text-secondary"></i>
                                            Refund #{{ $refund->id }} — Details
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <p class="text-muted small mb-1">Customer</p>
                                                <p class="mb-0 fw-semibold">{{ $refund->user->name }}</p>
                                                <p class="mb-0 small text-muted">{{ $refund->user->email }}</p>
                                            </div>
                                            <div class="col-sm-3">
                                                <p class="text-muted small mb-1">Refund Amount</p>
                                                <p class="mb-0 fw-bold text-danger fs-5">Rp {{ number_format($refund->amount, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="col-sm-3">
                                                <p class="text-muted small mb-1">Status</p>
                                                <span class="badge bg-{{ $refund->status === 'pending' ? 'warning text-dark' : ($refund->status === 'approved' ? 'success' : ($refund->status === 'rejected' ? 'danger' : 'secondary')) }} fs-6">
                                                    {{ ucfirst($refund->status) }}
                                                </span>
                                            </div>
                                            <div class="col-12">
                                                <p class="text-muted small mb-1">Reason for Refund</p>
                                                <div class="p-3 bg-light rounded border" style="white-space:pre-wrap; word-break:break-word; max-height:200px; overflow-y:auto;">{{ $refund->reason }}</div>
                                            </div>
                                            @if($refund->image_path)
                                            <div class="col-12">
                                                <p class="text-muted small mb-2">Evidence Image</p>
                                                <div class="text-center border rounded p-2 bg-light">
                                                    <img src="{{ Storage::url($refund->image_path) }}"
                                                         alt="Evidence"
                                                         class="img-fluid rounded"
                                                         style="max-height:400px; object-fit:contain; cursor:zoom-in;"
                                                         onclick="window.open(this.src,'_blank')">
                                                    <p class="text-muted small mt-2 mb-0">Click image to open full size</p>
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-12">
                                                <p class="text-muted small mb-1">Evidence Image</p>
                                                <p class="text-muted fst-italic">No image provided</p>
                                            </div>
                                            @endif
                                            @if($refund->admin_notes)
                                            <div class="col-12">
                                                <p class="text-muted small mb-1">Admin Notes</p>
                                                <div class="alert alert-secondary mb-0">{{ $refund->admin_notes }}</div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ── APPROVE modal ─────────────────────────────────────── --}}
                        <div class="modal fade" id="approveRefundModal{{ $refund->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="fas fa-check-circle me-2 text-success"></i>Approve Refund #{{ $refund->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.refunds.approve', $refund->id) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <p class="text-muted small mb-1">Refund Amount</p>
                                                <p class="mb-0 fw-bold text-success fs-5">Rp {{ number_format($refund->amount, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <p class="text-muted small mb-1">Customer Reason</p>
                                                <div class="p-2 bg-light rounded border" style="white-space:pre-wrap; word-break:break-word; max-height:120px; overflow-y:auto; font-size:14px;">{{ $refund->reason }}</div>
                                            </div>
                                            @if($refund->image_path)
                                            <div class="mb-3">
                                                <p class="text-muted small mb-1">Evidence Image</p>
                                                <img src="{{ Storage::url($refund->image_path) }}" alt="Evidence" class="img-fluid rounded border" style="max-height:250px; object-fit:contain; cursor:zoom-in;" onclick="window.open(this.src,'_blank')">
                                            </div>
                                            @endif
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Stock will be automatically restored to the fulfilling store.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>Approve Refund</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- ── REJECT modal ──────────────────────────────────────── --}}
                        <div class="modal fade" id="rejectRefundModal{{ $refund->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="fas fa-times-circle me-2 text-danger"></i>Reject Refund #{{ $refund->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.refunds.reject', $refund->id) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <p class="text-muted small mb-1">Customer Reason</p>
                                                <div class="p-2 bg-light rounded border" style="white-space:pre-wrap; word-break:break-word; max-height:120px; overflow-y:auto; font-size:14px;">{{ $refund->reason }}</div>
                                            </div>
                                            @if($refund->image_path)
                                            <div class="mb-3">
                                                <p class="text-muted small mb-1">Evidence Image</p>
                                                <img src="{{ Storage::url($refund->image_path) }}" alt="Evidence" class="img-fluid rounded border" style="max-height:200px; object-fit:contain; cursor:zoom-in;" onclick="window.open(this.src,'_blank')">
                                            </div>
                                            @endif
                                            <div class="mb-3">
                                                <p class="text-muted small mb-1">Admin Notes (Optional)</p>
                                                <textarea class="form-control" name="reason" rows="3" placeholder="Explain why this refund is being rejected..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger"><i class="fas fa-times me-1"></i>Reject Refund</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── Lightbox overlay for thumbnail clicks ─────────────────────── --}}
        <div id="imgLightbox" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; cursor:zoom-out;"
             onclick="this.style.display='none'">
            <img id="imgLightboxSrc" src="" alt="Evidence full size"
                 style="max-width:90vw; max-height:90vh; object-fit:contain; border-radius:8px; box-shadow:0 0 40px rgba(0,0,0,0.6);">
            <span style="position:absolute; top:20px; right:28px; color:#fff; font-size:32px; cursor:pointer; line-height:1;"
                  onclick="document.getElementById('imgLightbox').style.display='none'">&times;</span>
        </div>
    @endif
</div>

<script>
// Thumbnail lightbox
document.querySelectorAll('.refund-thumb').forEach(img => {
    img.addEventListener('click', function () {
        document.getElementById('imgLightboxSrc').src = this.dataset.img;
        const lb = document.getElementById('imgLightbox');
        lb.style.display = 'flex';
    });
});

// Close lightbox on Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.getElementById('imgLightbox').style.display = 'none';
    }
});
</script>
@endsection
