@extends('base.base')

@section('content')
<div class="container mt-5 mb-5 align-items-center justify-content-center">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold mb-0">Order Details</h2>
        <a href="{{ route('orders') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Back to Orders
        </a>
    </div>

    <div class="row gy-4">
        <!-- Order Summary Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light py-3 border-bottom">
                    <h5 class="mb-0 text-dark fw-bold"><i class="fas fa-file-invoice me-2"></i>Summary</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 pt-0 border-0">
                            <span class="text-muted">Invoice Number</span>
                            <span class="fw-bold">{{ $order->invoice_number }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                            <span class="text-muted">Order Date</span>
                            <span class="fw-medium">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                            <span class="text-muted">Status</span>
                            <span>
                                @if ($order->status == 'paid')
                                    <span class="badge bg-success rounded-pill px-3 py-2">Paid</span>
                                @elseif ($order->status == 'pending')
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending</span>
                                @elseif ($order->status == 'failed' || $order->status == 'cancelled')
                                    <span class="badge bg-danger rounded-pill px-3 py-2">Failed</span>
                                @elseif ($order->status == 'expired')
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Expired</span>
                                @else
                                    <span class="badge bg-info rounded-pill px-3 py-2">{{ ucfirst($order->status) }}</span>
                                @endif
                            </span>
                        </li>
                        @if($order->paid_at)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                            <span class="text-muted">Payment Date</span>
                            <span class="text-success small fw-medium">
                                <i class="bi bi-clock-history"></i> {{ \Carbon\Carbon::parse($order->paid_at)->format('d M Y, H:i') }}
                            </span>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                            <span class="text-muted">Customer Name</span>
                            <span class="fw-medium">{{ $order->user->name ?? $order->customer_name }}</span>
                        </li>
                        <hr class="my-2">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 pb-0 border-0">
                            <span class="text-muted fs-5">Total Amount</span>
                            <span class="fw-bold text-primary fs-5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </li>
                    </ul>
                </div>
                @if ($order->status == 'pending' && $order->payment_url && $order->user_id == auth()->id())
                <div class="card-footer border-top-0 bg-white pb-4 px-4 text-center">
                    <button type="button" class="btn btn-primary rounded-pill px-5 w-100 pay-now-btn" 
                            data-token="{{ $order->payment_url }}" 
                            data-status-url="{{ route('payment_status', $order->id) }}">
                        <i class="fas fa-credit-card me-2"></i> Pay Now
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Ordered Items Table Card -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 text-dark fw-bold"><i class="fas fa-box-open me-2"></i>Ordered Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->order_details as $detail)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                @if($detail->product && $detail->product->image_path)
                                                    <img src="{{ asset('product_image/' . $detail->product->image_path) }}" 
                                                         alt="{{ $detail->product_name }}" 
                                                         class="rounded-3 shadow-sm object-fit-cover" 
                                                         style="width: 60px; height: 60px;">
                                                @else
                                                    <img src="https://placehold.co/60x60?text=No+Image" 
                                                         alt="{{ $detail->product_name }}" 
                                                         class="rounded-3 shadow-sm object-fit-cover" 
                                                         style="width: 60px; height: 60px;">
                                                @endif
                                                <div class="ms-3">
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $detail->product_name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                        <td class="text-center fw-medium">{{ $detail->quantity }}</td>
                                        <td class="text-end pe-4 fw-bold text-primary">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No items found for this order.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap JS Modal trigger scripts required if button is present -->
@if ($order->status == 'pending' && $order->payment_url && $order->user_id == auth()->id())
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.pay-now-btn', function() {
                var token = $(this).data('token');
                var statusUrl = $(this).data('status-url');

                window.snap.pay(token, {
                    onSuccess: function(result){
                        window.location.href = statusUrl;
                    },
                    onPending: function(result){
                        window.location.href = statusUrl;
                    },
                    onError: function(result){
                        alert("Payment failed!");
                        window.location.href = statusUrl;
                    },
                    onClose: function(){
                        alert('You closed the popup without finishing the payment');
                        window.location.href = statusUrl;
                    }
                });
            });
        });
    </script>
@endif
@endsection