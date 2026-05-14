@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="display-6 mb-1">Order Details</h1>
            <p class="text-muted">Invoice #{{ $order->invoice_number }}</p>
        </div>
        <div class="text-end">
            <span class="badge bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'pending' ? 'warning text-dark' : 'secondary') }} py-2 px-3">
                {{ ucfirst($order->status) }}
            </span>
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
                    <i class="fas fa-receipt me-2 text-primary"></i>Order Summary
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
                        <span class="fw-bold text-success">Rp {{ number_format(($order->total_price + ($order->shipping_cost ?? 0)), 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Payment Status</span>
                        <span class="badge bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'pending' ? 'warning text-dark' : 'secondary') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    @if($order->paid_at)
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Paid At</span>
                        <span>{{ $order->paid_at->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($order->status === 'pending' && $order->payment_url)
                        <a href="{{ $order->payment_url }}" target="_blank" class="btn btn-warning w-100 mb-2">Pay Now</a>
                    @endif
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">Back to Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
