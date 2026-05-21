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
            $countPaid      = $orders->whereIn('status', ['paid', 'payment_paid'])->count();
            $countPending   = $orders->where('status', 'pending')->count();
            $countCancelled = $orders->where('status', 'cancelled')->count();
            $totalRevenue   = $orders->whereIn('status', ['paid', 'payment_paid'])->sum(fn($o) => $o->total_price + ($o->shipping_cost ?? 0));
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
                    <div class="fw-bold fs-4 text-danger">{{ $countCancelled }}</div>
                    <div class="text-muted small">Cancelled</div>
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
                            $isPaid = in_array($order->status, ['payment_paid', 'paid']);
                            $statusColor = $isPaid ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning');
                            $statusLabel = match($order->status) {
                                'payment_paid', 'paid' => 'Paid',
                                'cancelled'            => 'Cancelled',
                                default                => 'Pending',
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
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill"
                                        title="Update Status"
                                        data-bs-toggle="modal"
                                        data-bs-target="#updateModal"
                                        data-order-id="{{ $order->id }}"
                                        data-invoice="{{ $order->invoice_number }}"
                                        data-status="{{ $order->status }}"
                                        data-shipping-status="{{ $order->shipping_status ?? 'pending' }}"
                                        data-tracking="{{ $order->tracking_number ?? '' }}">
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
                @method('PATCH')
                <div class="modal-body px-4 pt-3 pb-2">
                    <p class="text-muted mb-3">Invoice: <strong id="modalInvoice"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Status</label>
                        <select name="status" class="form-select" id="modalStatus">
                            <option value="pending">Pending Payment</option>
                            <option value="payment_paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Shipping Status</label>
                        <select name="shipping_status" class="form-select" id="modalShippingStatus">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="failed">Failed</option>
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
                '/admin/orders/' + orderId + '/status';
        });
    }
});
</script>
@endsection
