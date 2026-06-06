@extends('base.base')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-receipt me-2 text-primary"></i>Order Management</h1>
            <p class="text-muted mb-0">Manage and update all customer orders</p>
        </div>
        <span class="badge bg-primary rounded-pill fs-6">{{ $orders->count() }} orders</span>
    </div>

    {{-- Status Filter Menu --}}
    <div class="mb-4">
        <div class="btn-group" role="group" aria-label="Filter by status">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary {{ !request('status') ? 'active' : '' }} rounded-start-pill">
                <i class="fas fa-list me-1"></i>All Orders
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-outline-warning {{ request('status') === 'pending' ? 'active bg-warning text-dark' : '' }}">
                <i class="fas fa-clock me-1"></i>Pending
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}" class="btn btn-outline-success {{ request('status') === 'paid' ? 'active bg-success text-white' : '' }} rounded-end-pill">
                <i class="fas fa-check-circle me-1"></i>Paid
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mt-3" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($orders->isEmpty())
        <div class="text-center py-5 mt-4">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">No orders in the system yet.</p>
        </div>
    @else
        {{-- Stats summary row --}}
        @php
            $countPaid      = $orders->where('status', 'paid')->count();
            $countPending   = $orders->where('status', 'pending')->count();
            $countRefunded  = $orders->where('status', 'refunded')->count();
            $countCancelled = $orders->where('status', 'cancelled')->count();
            // Revenue only counts when user confirms delivery AND revenue_recorded is true
            $totalRevenue   = $orders->where('revenue_recorded', true)->sum(fn($o) => $o->total_price + ($o->shipping_cost ?? 0));
        @endphp
        <div class="row g-3 mb-4 mt-1">
            <div class="col-6 col-md-3">
                <div class="card border-0 bg-success bg-opacity-10 text-center py-3">
                    <div class="fw-bold fs-4 text-success">{{ $countPaid }}</div>
                    <div class="text-muted small">Paid</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 bg-warning bg-opacity-10 text-center py-3">
                    <div class="fw-bold fs-4 text-warning">{{ $countPending }}</div>
                    <div class="text-muted small">Pending</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 bg-danger bg-opacity-10 text-center py-3">
                    <div class="fw-bold fs-4 text-danger">{{ $countRefunded }}</div>
                    <div class="text-muted small">Refunded</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 bg-primary bg-opacity-10 text-center py-3">
                    <div class="fw-bold text-primary" style="font-size:1.1rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                    <div class="text-muted small">Total Revenue</div>
                </div>
            </div>
        </div>

        {{-- Orders table --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background: #f8f4f0;">
                        <tr>
                            <th class="ps-4">Invoice</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Subtotal</th>
                            <th>Shipping</th>
                            <th>Grand Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Shipping Status</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        @php
                            $statusColor = match($order->status) {
                                'paid'       => 'success',
                                'pending'    => 'warning',
                                'cancelled'  => 'danger',
                                'refunded'   => 'danger',
                                default      => 'secondary',
                            };
                            $statusLabel = match($order->status) {
                                'paid'       => 'Paid',
                                'pending'    => 'Pending',
                                'cancelled'  => 'Cancelled',
                                'refunded'   => 'Refunded',
                                default      => ucfirst($order->status),
                            };
                            $shippingColors = [
                                'pending'    => 'secondary',
                                'processing' => 'info',
                                'shipped'    => 'primary',
                                'delivered'  => 'success',
                                'failed'     => 'danger',
                            ];
                            $sc = $shippingColors[$order->shipping_status ?? 'pending'] ?? 'secondary';
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-primary rounded-pill">{{ $order->invoice_number }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->user->name }}</div>
                                <div class="text-muted small">{{ $order->user->email }}</div>
                            </td>
                            <td>
                                <div>{{ $order->created_at->format('d M Y') }}</div>
                                <div class="text-muted small">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td>
                                @if($order->shipping_cost)
                                    Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                                    @if($order->shipping_distance_km)
                                        <div class="text-muted small">{{ $order->shipping_distance_km }} km</div>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="fw-bold text-success">
                                Rp {{ number_format($order->total_price + ($order->shipping_cost ?? 0), 0, ',', '.') }}
                            </td>
                            <td>
                                @if($order->payment_method)
                                    <span class="badge bg-info text-dark">{{ formatPaymentMethod($order->payment_method) }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusColor }} {{ $statusColor === 'warning' ? 'text-dark' : '' }} rounded-pill">
                                    {{ $statusLabel }}
                                </span>
                                @if($order->paid_at)
                                    <div class="text-muted small">{{ $order->paid_at->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $sc }} rounded-pill">
                                    {{ ucfirst($order->shipping_status ?? 'pending') }}
                                </span>
                                @if($order->tracking_number)
                                    <div class="text-muted small font-monospace">{{ $order->tracking_number }}</div>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-2 justify-content-end align-items-center">
                                    {{-- Show pending/approved refund indicator --}}
                                    @php
                                        $pendingRefund = $order->refunds()->where('status', 'pending')->first();
                                        $approvedRefund = $order->refunds()->where('status', 'approved')->first();
                                        $isLocked = $order->delivery_confirmed_by_user || $pendingRefund || $approvedRefund;
                                    @endphp
                                    @if($pendingRefund)
                                    <a href="{{ route('admin.refunds.index', ['status' => 'pending']) }}" class="badge bg-danger rounded-pill" title="Click to manage pending refunds" data-bs-toggle="tooltip" style="text-decoration: none;">
                                        <i class="fas fa-exclamation-circle me-1"></i>Refund Pending
                                    </a>
                                    @endif
                                    @if($approvedRefund)
                                    <a href="{{ route('admin.refunds.index', ['status' => 'approved']) }}" class="badge bg-info rounded-pill" title="Click to manage approved refunds" data-bs-toggle="tooltip" style="text-decoration: none;">
                                        <i class="fas fa-check-circle me-1"></i>Refund Approved
                                    </a>
                                    @endif
                                    @if($order->delivery_confirmed_by_user)
                                    <span class="badge bg-success rounded-pill" title="Order locked - delivery confirmed by user" data-bs-toggle="tooltip">
                                        <i class="fas fa-lock me-1"></i>Confirmed
                                    </span>
                                    @endif
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill"
                                        title="{{ $isLocked ? 'Cannot modify - order is locked' : 'Update Status' }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#updateModal"
                                        data-order-id="{{ $order->id }}"
                                        data-invoice="{{ $order->invoice_number }}"
                                        data-status="{{ $order->status }}"
                                        data-shipping-status="{{ $order->shipping_status ?? 'pending' }}"
                                        data-tracking="{{ $order->tracking_number ?? '' }}"
                                        {{ $isLocked ? 'disabled' : '' }}>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- Update Status Modal --}}
<div class="modal fade" id="updateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Update Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateOrderForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 pt-3 pb-2">
                    <p class="text-muted mb-3">Invoice: <strong id="modalInvoice"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Status</label>
                        <select name="status" class="form-select" id="modalStatus">
                            <option value="pending">Pending Payment</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Shipping Status</label>
                        <select name="shipping_status" class="form-select" id="modalShippingStatus">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="delivered">Delivered</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tracking Number <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="tracking_number" id="modalTracking" class="form-control font-monospace" placeholder="e.g. JNE1234567890">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const updateModal = document.getElementById('updateModal');
    if (updateModal) {
        updateModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const orderId        = btn.dataset.orderId;
            const invoice        = btn.dataset.invoice;
            const status         = btn.dataset.status;
            const shippingStatus = btn.dataset.shippingStatus;
            const tracking       = btn.dataset.tracking;

            document.getElementById('modalInvoice').textContent = invoice;
            document.getElementById('modalStatus').value = status;
            document.getElementById('modalShippingStatus').value = shippingStatus;
            document.getElementById('modalTracking').value = tracking;

            document.getElementById('updateOrderForm').action =
                '/admin/orders/' + orderId;
        });
    }

    // Initialize tooltips for refund indicators
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
/* ===== RESPONSIVE STYLES FOR ADMIN ORDERS INDEX PAGE ===== */
@media (max-width: 768px) {
    /* Container and spacing */
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* Heading sizing */
    .h3 {
        font-size: 1.25rem;
    }

    .h4 {
        font-size: 1.1rem;
    }

    .h5 {
        font-size: 1rem;
    }

    /* Badge and stat sizing */
    .badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.6rem;
    }

    .fs-6 {
        font-size: 0.95rem;
    }

    /* Button group wrapping */
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
    }

    .btn-group .btn {
        flex: 1;
        min-width: 100px;
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }

    /* Table responsiveness */
    .table {
        font-size: 0.9rem;
    }

    .table thead {
        font-size: 0.85rem;
    }

    .table td {
        padding: 0.75rem 0.5rem;
    }

    /* Status badge sizing */
    .badge {
        font-size: 0.75rem;
    }

    /* Modal sizing */
    .modal-dialog {
        max-width: 95%;
    }

    .modal-header {
        padding: 1rem;
    }

    .modal-body {
        padding: 1rem;
    }

    .modal-footer {
        padding: 0.75rem;
    }

    /* Form controls */
    .form-control,
    .form-select {
        font-size: 0.95rem;
        padding: 0.65rem;
    }

    /* Text utilities */
    .text-muted {
        font-size: 0.9rem;
    }

    /* Alert sizing */
    .alert {
        font-size: 0.9rem;
        padding: 0.75rem;
    }

    /* Icon sizing */
    .fa-2x {
        font-size: 1.5rem;
    }

    .fa-3x {
        font-size: 2rem;
    }
}

@media (max-width: 576px) {
    /* Extra small screens */
    .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    /* Page header */
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem !important;
    }

    /* Heading sizing */
    .h3 {
        font-size: 1rem;
    }

    .h4 {
        font-size: 0.95rem;
    }

    .h5 {
        font-size: 0.9rem;
    }

    .h1, h1 {
        font-size: 1.1rem;
    }

    /* Badge and buttons */
    .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.5rem;
    }

    .fs-6 {
        font-size: 0.85rem;
    }

    /* Button group single column */
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-group .btn {
        width: 100%;
        font-size: 0.85rem;
        padding: 0.5rem;
        border-radius: 0.25rem !important;
    }

    /* Button sizing */
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Table responsiveness - horizontal scroll */
    .table-responsive {
        -webkit-overflow-scrolling: touch;
    }

    .table {
        font-size: 0.8rem;
        white-space: nowrap;
    }

    .table thead {
        font-size: 0.75rem;
    }

    .table td,
    .table th {
        padding: 0.5rem 0.25rem;
    }

    /* Status badge in table */
    .badge {
        display: inline-block;
        word-wrap: break-word;
    }

    /* Text truncation */
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Links in table */
    a {
        word-break: break-word;
    }

    /* Modal sizing */
    .modal-dialog {
        max-width: 95%;
        margin: 0.5rem auto;
    }

    .modal-header {
        padding: 0.75rem;
    }

    .modal-body {
        padding: 0.75rem;
    }

    .modal-footer {
        padding: 0.5rem;
        flex-wrap: wrap;
        gap: 0.25rem;
    }

    .modal-header .btn-close {
        padding: 0.3rem;
    }

    /* Form controls */
    .form-control,
    .form-select {
        font-size: 16px; /* Prevent iOS zoom */
        padding: 0.75rem;
    }

    .form-label {
        font-size: 0.9rem;
    }

    /* Text utilities */
    .text-muted {
        font-size: 0.8rem;
    }

    .small {
        font-size: 0.75rem;
    }

    /* Alert sizing */
    .alert {
        font-size: 0.85rem;
        padding: 0.5rem;
        margin-bottom: 0.75rem;
    }

    /* Alert icon sizing */
    .alert i {
        font-size: 1rem;
        margin-right: 0.5rem;
    }

    /* Icon sizing */
    .fa-2x {
        font-size: 1.2rem;
    }

    .fa-3x {
        font-size: 1.5rem;
    }

    /* Margin/padding reductions */
    .mb-4 {
        margin-bottom: 1rem !important;
    }

    .mb-3 {
        margin-bottom: 0.75rem !important;
    }

    .gap-3 {
        gap: 0.75rem !important;
    }

    .gap-2 {
        gap: 0.5rem !important;
    }

    /* Row and column spacing */
    .row.g-3 {
        gap: 0.75rem !important;
    }

    .row.g-4 {
        gap: 1rem !important;
    }

    /* Prevent horizontal overflow */
    body {
        overflow-x: hidden;
    }

    /* Stat cards */
    .card {
        border-radius: 8px;
    }

    .card-body {
        padding: 0.75rem;
    }

    /* Card header sizing */
    .card-header {
        padding: 0.75rem;
        font-size: 0.95rem;
    }
}
</style>
@endsection
