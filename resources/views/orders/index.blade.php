@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="display-6 mb-1">My Orders</h1>
            <p class="text-muted mb-0">Track your purchases and payment status.</p>
        </div>
        <a href="{{ route('books.listing') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-book me-2"></i>Browse Books
        </a>
    </div>

    {{-- Responsive Status Filter Component --}}
    <div class="mb-4">
        <!-- 1. MOBILE SMART DROPDOWN FILTER (Visible strictly on mobile screens below 768px via d-block d-md-none) -->
        <div class="d-block d-md-none max-width-md-600">
            <div class="input-group">
                <label class="input-group-text bg-white text-muted border-end-0 rounded-start-pill ps-3" for="orderStatusFilter">
                    <i class="fas fa-filter text-primary"></i>
                </label>
                <select class="form-select bg-white border-start-0 rounded-end-pill fw-medium text-dark shadow-sm" id="orderStatusFilter" onchange="window.location.href=this.value;">
                    <option value="{{ route('orders.index') }}" {{ !request('status') ? 'selected' : '' }}>
                        📋 All Orders
                    </option>
                    <option value="{{ route('orders.index', ['status' => 'pending']) }}" {{ request('status') === 'pending' ? 'selected' : '' }}>
                        ⏳ Pending Payment
                    </option>
                    <option value="{{ route('orders.index', ['status' => 'paid']) }}" {{ request('status') === 'paid' ? 'selected' : '' }}>
                        ✅ Paid
                    </option>
                    <option value="{{ route('orders.index', ['status' => 'refunded']) }}" {{ request('status') === 'refunded' ? 'selected' : '' }}>
                        🔄 Refunded
                    </option>
                </select>
            </div>
        </div>

        <!-- 2. DESKTOP FILTER PILL BUTTON ROW (Visible strictly on desktop monitors via d-none d-md-inline-flex) -->
        <div class="d-none d-md-inline-flex bh-filter-scroll-wrapper">
            <div class="btn-group bh-custom-btn-group" role="group" aria-label="Filter by status">
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary {{ !request('status') ? 'active' : '' }} rounded-start-pill">
                    <i class="fas fa-list me-1"></i>All Orders
                </a>
                <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="btn btn-outline-warning {{ request('status') === 'pending' ? 'active bg-warning text-dark' : '' }}">
                    <i class="fas fa-clock me-1"></i>Pending
                </a>
                <a href="{{ route('orders.index', ['status' => 'paid']) }}" class="btn btn-outline-success {{ request('status') === 'paid' ? 'active bg-success text-white' : '' }}">
                    <i class="fas fa-check-circle me-1"></i>Paid
                </a>
                <a href="{{ route('orders.index', ['status' => 'refunded']) }}" class="btn btn-outline-danger {{ request('status') === 'refunded' ? 'active bg-danger text-white' : '' }} rounded-end-pill">
                    <i class="fas fa-undo me-1"></i>Refunded
                </a>
            </div>
        </div>
    </div>

    @if(session('success') === 'true' || request('success') === 'true')
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fas fa-check-circle fs-5"></i>
            <div><strong>Payment successful!</strong> Your order is now being processed.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
            <p class="text-muted fs-5">You have no orders yet.</p>
            <a href="{{ route('books.listing') }}" class="btn btn-primary rounded-pill px-4">Start Shopping</a>
        </div>
    @else
        <div class="row g-3">
            @foreach($orders as $order)
            @php
                $statusColor = match($order->status) {
                    'refunded' => 'danger',
                    'paid' => 'success',
                    'cancelled' => 'danger',
                    default => 'warning'
                };
                $statusLabel = match($order->status) {
                    'refunded' => 'Refunded',
                    'paid' => 'Paid',
                    'pending' => 'Pending Payment',
                    'cancelled' => 'Cancelled',
                    default => ucfirst($order->status),
                };
                $shippingStatusColor = match($order->shipping_status ?? 'pending') {
                    'delivered' => 'success',
                    'shipped' => 'info',
                    'processing' => 'primary',
                    'cancelled' => 'danger',
                    default => 'warning'
                };
                $shippingStatusLabel = match($order->shipping_status ?? 'pending') {
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                    default => ucfirst($order->shipping_status ?? 'pending'),
                };
                $shippingStatusIcon = match($order->shipping_status ?? 'pending') {
                    'pending' => 'fas fa-hourglass-start',
                    'processing' => 'fas fa-cogs',
                    'shipped' => 'fas fa-truck',
                    'delivered' => 'fas fa-check-circle',
                    'cancelled' => 'fas fa-times-circle',
                    default => 'fas fa-box'
                };
                $isPaid = in_array($order->status, ['paid', 'refunded']);
                $grandTotal = $order->total_price + ($order->shipping_cost ?? 0);
            @endphp
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-0">
                        {{-- Coloured left border accent --}}
                        <div class="d-flex" style="border-left: 4px solid var(--bs-{{ $statusColor }});">
                            <div class="p-3 p-md-4 flex-grow-1">
                                <div class="row align-items-center g-3 bh-mobile-row-lock">
                                    {{-- Invoice & Date --}}
                                    <div class="col-12 col-md-auto">
                                        <span class="badge bg-primary rounded-pill mb-1">{{ $order->invoice_number }}</span>
                                        <div class="text-muted small">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                    </div>

                                    {{-- Admin: Customer info --}}
                                    @if(in_array('admin', $userRoles))
                                    <div class="col-12 col-md">
                                        <div class="fw-semibold"><i class="fas fa-user me-1 text-muted small"></i>{{ $order->user->name }}</div>
                                        <div class="text-muted small">{{ $order->user->email }}</div>
                                    </div>
                                    @endif

                                    {{-- Totals --}}
                                    <div class="col-6 col-md">
                                        <div class="text-muted small">Books</div>
                                        <div class="small">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                        @if(($order->shipping_cost ?? 0) > 0)
                                        <div class="text-muted small" style="font-size: 0.7rem;">+Rp {{ number_format($order->shipping_cost, 0, ',', '.') }} shipping</div>
                                        @endif
                                    </div>
                                    <div class="col-6 col-md">
                                        <div class="text-muted small">Grand Total</div>
                                        <div class="fw-bold text-success small">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
                                    </div>

                                    {{-- Payment method --}}
                                    <div class="col-6 col-md-auto">
                                        @if($order->payment_method)
                                            <div class="text-muted small">Payment</div>
                                            <span class="badge bg-info text-dark" style="font-size: 0.75rem;">{{ formatPaymentMethod($order->payment_method) }}</span>
                                        @endif
                                    </div>

                                    {{-- Shipping Status --}}
                                    <div class="col-6 col-md-auto">
                                        <div class="text-muted small">Shipping</div>
                                        <span class="badge bg-{{ $shippingStatusColor }} {{ $shippingStatusColor === 'warning' ? 'text-dark' : '' }}" style="font-size: 0.75rem;">
                                            <i class="{{ $shippingStatusIcon }} me-1"></i>{{ $shippingStatusLabel }}
                                        </span>
                                        @if($order->tracking_number)
                                        <div class="text-muted small mt-1" style="font-size: 0.7rem;">
                                            <i class="fas fa-barcode me-1"></i>{{ $order->tracking_number }}
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Status + Action --}}
                                    <div class="col-12 col-md-auto text-md-end d-flex flex-row flex-md-column justify-content-between align-items-center align-items-md-end gap-2">
                                        <span class="badge bg-{{ $statusColor }} {{ $statusColor === 'warning' ? 'text-dark' : '' }} rounded-pill py-2 px-3 m-0" style="font-size: 0.75rem; min-width: max-content;">
                                            @if($order->status === 'refunded')
                                                <i class="fas fa-undo me-1"></i>
                                            @elseif($order->status === 'paid')
                                                <i class="fas fa-check-circle me-1"></i>
                                            @elseif($order->status === 'cancelled')
                                                <i class="fas fa-times-circle me-1"></i>
                                            @else
                                                <i class="fas fa-clock me-1"></i>
                                            @endif
                                            {{ $statusLabel }}
                                        </span>
                                        <div class="d-flex gap-1.5 justify-content-end">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm rounded-pill px-2.5" style="padding: 4px 10px !important; font-size: 0.75rem;">
                                                <i class="fas fa-eye me-1"></i>Details
                                            </a>
                                            @if($order->status === 'pending')
                                            <button type="button" class="btn btn-warning btn-sm rounded-pill px-2.5 pay-now-btn" data-order-id="{{ $order->id }}" style="padding: 4px 10px !important; font-size: 0.75rem;">
                                                <i class="fas fa-credit-card me-1"></i>Pay
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payButtons = document.querySelectorAll('.pay-now-btn');
    
    payButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            const originalText = this.innerHTML;
            
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...';
            
            axios.post('{{ route("checkout.generate-payment-token") }}', {
                order_id: orderId,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success && response.data.snapToken) {
                    snap.pay(response.data.snapToken, {
                        onSuccess: function(result) {
                            fetch('{{ route("checkout.mark-payment-complete") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ order_id: orderId, payment_type: result.payment_type || null })
                            }).then(() => {
                                setTimeout(() => {
                                    window.location.href = window.location.href + '?success=true';
                                }, 1500);
                            });
                        },
                        onPending: function() {
                            alert('Payment is being processed. Please check back soon.');
                            resetBtn();
                        },
                        onError: function() {
                            alert('Payment failed. Please try again.');
                            resetBtn();
                        },
                        onClose: function() {
                            alert('Payment cancelled. Order remains pending.');
                            resetBtn();
                        }
                    });
                } else {
                    alert('Error: ' + (response.data.error || 'Failed to generate payment token'));
                    resetBtn();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorMsg = error.response?.data?.message || error.message || 'Failed to process payment';
                alert('Error: ' + errorMsg);
                resetBtn();
            });
            
            function resetBtn() {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });
    });
});
</script>

<style>
    /* ==========================================================================
       SPECIFIC OVERRIDES FOR RESPONSIVE COMPACT ORDER VIEWS
       ========================================================================== */
    
    /* Dropdown wrapper control parameters */
    #orderStatusFilter {
        box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
        border-color: #dee2e6 !important;
        cursor: pointer;
    }
    #orderStatusFilter:focus {
        border-color: #c25e25 !important;
        box-shadow: 0 0 0 0.25rem rgba(194, 94, 37, 0.15) !important;
    }

    .bh-filter-scroll-wrapper {
        width: 100% !important;
        overflow-x: auto !important;
        white-space: nowrap !important;
        -webkit-overflow-scrolling: touch !important;
        padding-bottom: 4px;
    }

    .bh-custom-btn-group {
        display: inline-flex !important;
        flex-wrap: nowrap !important;
    }
    .bh-custom-btn-group .btn {
        white-space: nowrap !important;
        flex: 0 0 auto !important;
        font-size: 0.85rem !important;
        padding: 6px 14px !important;
    }

    @media (max-width: 767.98px) {
        .bh-mobile-row-lock {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .bh-mobile-row-lock > [class*='col-'] {
            flex: 0 0 auto !important;
            width: auto !important;
            margin-bottom: 0 !important;
        }

        .bh-mobile-row-lock .col-12 {
            width: 50% !important; 
        }
    }

    /* ===== RESPONSIVE STYLES FOR USER ORDERS INDEX PAGE ===== */
    @media (max-width: 768px) {
        .container { padding-left: 1rem; padding-right: 1rem; }
        .display-6 { font-size: 1.5rem; }
        .h1, h1 { font-size: 1.25rem; }
        .h5, h5 { font-size: 1rem; }
        .badge { font-size: 0.85rem; padding: 0.4rem 0.6rem; }
        .fs-5 { font-size: 0.95rem; }
        .card { border-radius: 12px; margin-bottom: 1rem; }
        .alert { font-size: 0.9rem; padding: 0.75rem; }
        .text-muted { font-size: 0.9rem; }
        .small { font-size: 0.85rem; }
        .fa-3x { font-size: 2rem; }
    }

    @media (max-width: 576px) {
        .container { padding-left: 0.75rem; padding-right: 0.75rem; }
        .display-6 { font-size: 1.25rem; }
        .h1, h1 { font-size: 1.1rem; }
        .h5, h5 { font-size: 0.95rem; }
        .badge { font-size: 0.7rem; padding: 0.3rem 0.5rem; }
        .card { border-radius: 10px; margin-bottom: 1rem; }
        .text-muted { font-size: 0.8rem; }
        .small { font-size: 0.75rem; }
        .fa-3x { font-size: 1.5rem; }
        .fa-2x { font-size: 1.2rem; }
        [style*="border-left: 4px"] { border-left-width: 3px !important; }
        body { overflow-x: hidden; }
        a { word-break: break-word; }
    }
</style>
@endsection