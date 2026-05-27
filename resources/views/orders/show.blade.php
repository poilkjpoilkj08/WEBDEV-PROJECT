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
                    'paid'       => 'success',
                    'pending'    => 'warning text-dark',
                    'cancelled'  => 'danger',
                    default      => 'secondary',
                };
                $statusLabel = match($order->status) {
                    'paid'       => 'Paid',
                    'pending'    => 'Pending Payment',
                    'cancelled'  => 'Cancelled',
                    default      => ucfirst($order->status),
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
                    {{-- Subtotal --}}
                    <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                        <span class="text-muted fw-semibold">Subtotal</span>
                        <span class="fw-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>

                    {{-- Shipping Method --}}
                    <div class="mb-3">
                        <span class="text-muted fw-semibold d-block mb-2">Shipping: {{ $order->shipping_method ?? 'N/A' }}</span>
                        
                        {{-- Per-Book/Store Shipping Details --}}
                        @php
                            // Group order details by store
                            $detailsByStore = $order->order_details->groupBy('store_id');
                        @endphp

                        @foreach($detailsByStore as $storeId => $storeDetails)
                            @php
                                $store = $storeDetails->first()->store;
                                $storeBooks = $storeDetails->pluck('book_title')->join(', ');
                                
                                // Calculate total weight for this store's books
                                $totalWeight = $storeDetails->reduce(function($carry, $detail) {
                                    // Estimate weight as 200g per book by default
                                    $bookWeight = $detail->book?->weight_grams ?? 200;
                                    return $carry + ($bookWeight * $detail->quantity);
                                }, 0);
                                
                                $weightKg = $totalWeight / 1000;
                                
                                // Get breakdown info if available
                                $breakdown = $order->shipping_breakdown;
                                $zone = $breakdown['zone'] ?? 'C';
                                $basePrice = $breakdown['zone_base'] ?? 0;
                                $serviceFee = $breakdown['service_surcharge'] ?? 0;
                                $serviceLevel = $breakdown['service_level'] ?? 'regular';
                            @endphp
                            
                            <div class="small bg-light p-2 rounded mb-2">
                                <div class="mb-2">
                                    <strong>📦 {{ $store->city ?? 'Store' }}</strong>
                                </div>
                                
                                {{-- Books from this store --}}
                                @foreach($storeDetails as $detail)
                                <div class="ps-3 mb-1">
                                    <div>{{ $detail->book_title }}</div>
                                    @php
                                        $bookWeight = ($detail->book?->weight_grams ?? 200) * $detail->quantity / 1000;
                                    @endphp
                                    <span class="text-muted" style="font-size: 0.85rem;">
                                        Qty: {{ $detail->quantity }} • Weight: {{ number_format($bookWeight, 2, ',', '.') }} kg
                                    </span>
                                </div>
                                @endforeach
                                
                                {{-- Zone & Shipping Breakdown --}}
                                <div class="border-top pt-2 mt-2" style="font-size: 0.9rem;">
                                    <div>
                                        <span class="text-muted">Zone:</span>
                                        <strong>{{ $zone }}</strong>
                                    </div>
                                    <div>
                                        <span class="text-muted">Base Price:</span>
                                        <strong>Rp {{ number_format($basePrice, 0, ',', '.') }}</strong>
                                    </div>
                                    @if($serviceFee > 0)
                                    <div>
                                        <span class="text-muted">Service ({{ ucfirst($serviceLevel) }}):</span>
                                        <strong>Rp {{ number_format($serviceFee, 0, ',', '.') }}</strong>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Shipping Cost Total --}}
                    <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                        <span class="text-muted fw-semibold">Shipping Total</span>
                        <span class="fw-semibold text-primary">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</span>
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

    {{-- Delivery Confirmation Section --}}
    @if($order->shipping_status === 'shipped' && !$order->delivery_confirmed_by_user)
    <div class="row g-4 mt-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 border-warning">
                <div class="card-header bg-warning bg-opacity-10 fw-bold py-3">
                    <i class="fas fa-box-open me-2 text-warning"></i>Confirm Receipt of Delivery
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Your package has been shipped. Please confirm once you've received it safely to complete the transaction.
                    </p>
                    @if($order->delivery_confirmation_deadline)
                        @php
                            $daysRemaining = now()->diffInDays($order->delivery_confirmation_deadline, false);
                            $hoursRemaining = now()->diffInHours($order->delivery_confirmation_deadline, false);
                        @endphp
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-hourglass-end me-2"></i>
                            <strong>Confirmation Deadline:</strong> 
                            @if($daysRemaining >= 1)
                                {{ abs($daysRemaining) }} day(s) remaining
                            @else
                                {{ abs($hoursRemaining) }} hour(s) remaining
                            @endif
                        </div>
                    @endif
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmDeliveryModal">
                        <i class="fas fa-check me-2"></i>Confirm Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Delivery Modal -->
    <div class="modal fade" id="confirmDeliveryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Receipt of Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('orders.confirm-delivery', $order->id) }}">
                    @csrf
                    <div class="modal-body">
                        <p>I confirm that I have safely received this shipment and all items are in good condition.</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmCheckbox" required>
                            <label class="form-check-label" for="confirmCheckbox">
                                I confirm receipt of this order
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Confirm Delivery</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Refund Request Section --}}
    @if($order->canRequestRefund())
    <div class="row g-4 mt-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 border-danger">
                <div class="card-header bg-danger bg-opacity-10 fw-bold py-3">
                    <i class="fas fa-undo me-2 text-danger"></i>Request a Refund
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        You can request a refund for this order before delivery is confirmed. Once you confirm delivery, refunds will not be allowed.
                    </p>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#requestRefundModal">
                        <i class="fas fa-money-bill-wave me-2"></i>Request Refund
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Refund Modal -->
    <div class="modal fade" id="requestRefundModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('orders.request-refund', $order->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="refundReason" class="form-label">Reason for Refund</label>
                            <textarea class="form-control" id="refundReason" name="reason" rows="4" required placeholder="Please describe why you'd like to request a refund..."></textarea>
                            <small class="text-muted">Provide a clear reason so our team can process your request quickly</small>
                        </div>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Refund Amount:</strong> Rp {{ number_format($order->total_price + ($order->shipping_cost ?? 0), 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Submit Refund Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @elseif($order->refund_status !== 'none')
    <div class="row g-4 mt-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-bold py-3">
                    <i class="fas fa-receipt me-2"></i>Refund Status
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Status</p>
                        <span class="badge bg-{{ $order->refund_status === 'approved' ? 'success' : ($order->refund_status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($order->refund_status) }}
                        </span>
                    </div>
                    @if($order->refund_reason)
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Your Reason</p>
                        <p class="mb-0">{{ $order->refund_reason }}</p>
                    </div>
                    @endif
                    @if($order->refund_amount)
                    <div class="mb-0">
                        <p class="text-muted small mb-1">Refund Amount</p>
                        <p class="mb-0 fw-semibold text-success">Rp {{ number_format($order->refund_amount, 0, ',', '.') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

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
