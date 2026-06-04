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

    {{-- Status Filter Menu --}}
    <div class="mb-4">
        <div class="btn-group" role="group" aria-label="Filter by status">
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
                $isPaid = in_array($order->status, ['paid', 'refunded']);
                $grandTotal = $order->total_price + ($order->shipping_cost ?? 0);
            @endphp
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-0">
                        {{-- Coloured left border accent --}}
                        <div class="d-flex" style="border-left: 4px solid var(--bs-{{ $statusColor }});">
                            <div class="p-3 p-md-4 flex-grow-1">
                                <div class="row align-items-center g-2">
                                    {{-- Invoice & Date --}}
                                    <div class="col-12 col-sm-auto">
                                        <span class="badge bg-primary rounded-pill mb-1">{{ $order->invoice_number }}</span>
                                        <div class="text-muted small">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                    </div>

                                    {{-- Admin: Customer info --}}
                                    @if(in_array('admin', $userRoles))
                                    <div class="col-12 col-sm">
                                        <div class="fw-semibold"><i class="fas fa-user me-1 text-muted small"></i>{{ $order->user->name }}</div>
                                        <div class="text-muted small">{{ $order->user->email }}</div>
                                    </div>
                                    @endif

                                    {{-- Totals --}}
                                    <div class="col-6 col-sm">
                                        <div class="text-muted small">Books</div>
                                        <div>Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                        @if(($order->shipping_cost ?? 0) > 0)
                                        <div class="text-muted small">+Rp {{ number_format($order->shipping_cost, 0, ',', '.') }} shipping</div>
                                        @endif
                                    </div>
                                    <div class="col-6 col-sm">
                                        <div class="text-muted small">Grand Total</div>
                                        <div class="fw-bold text-success">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
                                    </div>

                                    {{-- Payment method --}}
                                    <div class="col-12 col-sm-auto">
                                        @if($order->payment_method)
                                            <div class="text-muted small">Payment</div>
                                            <span class="badge bg-info text-dark">{{ formatPaymentMethod($order->payment_method) }}</span>
                                        @endif
                                    </div>

                                    {{-- Status + Action --}}
                                    <div class="col-12 col-sm-auto text-sm-end">
                                        <span class="badge bg-{{ $statusColor }} {{ $statusColor === 'warning' ? 'text-dark' : '' }} rounded-pill py-2 px-3 mb-2 d-block d-sm-inline-block">
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
                                        <div class="d-flex gap-2 justify-content-sm-end">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                                <i class="fas fa-eye me-1"></i>Details
                                            </a>
                                            @if($order->status === 'pending')
                                            <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 pay-now-btn" data-order-id="{{ $order->id }}">
                                                <i class="fas fa-credit-card me-1"></i>Pay Now
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
            
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...';
            
            // Request new payment token
            axios.post('{{ route("checkout.generate-payment-token") }}', {
                order_id: orderId,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success && response.data.snapToken) {
                    // Open Midtrans payment modal
                    snap.pay(response.data.snapToken, {
                        onSuccess: function(result) {
                            // Mark payment as complete
                            fetch('{{ route("checkout.mark-payment-complete") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ order_id: orderId, payment_type: result.payment_type || null })
                            }).then(() => {
                                // Reload with success message
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
@endsection
