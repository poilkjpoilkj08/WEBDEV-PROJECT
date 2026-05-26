@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-4 flex-wrap">
        <div>
            <h1 class="display-6 mb-1">Order Details</h1>
            <p class="text-muted">Invoice #{{ $order->invoice_number }}</p>
        </div>
        <div class="text-end">
            @php
                $statusColor = match($order->status) {
                    'payment_paid', 'paid' => 'success',
                    'pending'              => 'warning text-dark',
                    'cancelled'            => 'danger',
                    default                => 'secondary',
                };
                $statusLabel = match($order->status) {
                    'payment_paid' => 'Paid',
                    'paid'         => 'Paid',
                    'pending'      => 'Pending Payment',
                    'cancelled'    => 'Cancelled',
                    default        => ucfirst($order->status),
                };
            @endphp
            <span class="badge bg-{{ $statusColor }} py-2 px-3">{{ $statusLabel }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">

            {{-- Items --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-shopping-bag me-2 text-primary"></i>Items Ordered
                </div>
                <div class="card-body">
                    @foreach($order->order_details as $detail)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $detail->book_title }}</h6>
                            <small class="text-muted">Qty: {{ $detail->quantity }} × Rp {{ number_format($detail->price, 0, ',', '.') }}</small>
                        </div>
                        <span class="fw-semibold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Shipping details --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-truck me-2 text-primary"></i>Shipping Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Recipient</p>
                            <p class="mb-0 fw-semibold">{{ $order->shipping_name ?? $order->customer_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Phone</p>
                            <p class="mb-0">{{ $order->shipping_phone ?? '—' }}</p>
                        </div>
                        <div class="col-12">
                            <p class="mb-1 text-muted small">Address</p>
                            <p class="mb-0">
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}<br>
                                {{ $order->shipping_country }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Shipping Method</p>
                            <p class="mb-0">{{ $order->shipping_method ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Shipping Status</p>
                            @php
                                $shippingColors = ['pending' => 'secondary', 'processing' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'failed' => 'danger'];
                                $sc = $shippingColors[$order->shipping_status ?? 'pending'] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $sc }}">{{ ucfirst($order->shipping_status ?? 'pending') }}</span>
                        </div>
                        @if($order->tracking_number)
                        <div class="col-12">
                            <p class="mb-1 text-muted small">Tracking Number</p>
                            <p class="mb-0 fw-semibold font-monospace">{{ $order->tracking_number }}</p>
                        </div>
                        @endif
                        @if($order->shipped_at)
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Shipped At</p>
                            <p class="mb-0">{{ $order->shipped_at->format('d M Y, H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- Summary sidebar --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-receipt me-2 text-primary"></i>Transaction Summary
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Shipping</span>
                        <span>Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Grand Total</span>
                        <span class="fw-bold text-success">Rp {{ number_format($order->total_price + ($order->shipping_cost ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Payment Status</span>
                        <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Payment Method</span>
                        @if($order->payment_method)
                            <span class="badge bg-info text-dark">{{ formatPaymentMethod($order->payment_method) }}</span>
                        @else
                            <span class="text-muted fst-italic small">—</span>
                        @endif
                    </div>
                    @if($order->paid_at)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Paid At</span>
                        <span>{{ $order->paid_at->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($order->status === 'pending')
                        <button type="button" id="payNowBtn" class="btn btn-warning w-100 mb-2 mt-2" data-order-id="{{ $order->id }}">
                            <i class="fas fa-lock me-2"></i>Pay Now
                        </button>
                    @endif
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 mt-2">Back to Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payNowBtn = document.getElementById('payNowBtn');
    
    if (payNowBtn) {
        payNowBtn.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            const originalText = this.innerHTML;
            
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading payment...';
            
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
                                body: JSON.stringify({ payment_type: result.payment_type || null })
                            }).then(() => {
                                // Reload page to show updated status
                                setTimeout(() => {
                                    window.location.href = window.location.href + '?paid=true';
                                }, 1500);
                            });
                        },
                        onPending: function() {
                            alert('Payment is being processed. Please check back soon.');
                            resetPayBtn();
                        },
                        onError: function() {
                            alert('Payment failed. Please try again.');
                            resetPayBtn();
                        },
                        onClose: function() {
                            alert('Payment cancelled. Order remains pending.');
                            resetPayBtn();
                        }
                    });
                } else {
                    alert('Error: ' + (response.data.error || 'Failed to generate payment token'));
                    resetPayBtn();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorMsg = error.response?.data?.message || error.message || 'Failed to process payment';
                alert('Error: ' + errorMsg);
                resetPayBtn();
            });
            
            function resetPayBtn() {
                payNowBtn.disabled = false;
                payNowBtn.innerHTML = originalText;
            }
        });
    }
});
</script>
@endsection
