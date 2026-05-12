@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="display-6 mb-1">Order Details</h1>
            <p class="text-muted">Invoice #{{ $order->invoice_number }}</p>
        </div>
        <div class="text-end">
            <span class="badge bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'pending' ? 'warning text-dark' : 'secondary') }} py-2 px-3">{{ ucfirst($order->status) }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Items</h5>
                    @foreach($order->order_details as $detail)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $detail->book_title }}</h6>
                            <p class="mb-0 text-muted">Quantity: {{ $detail->quantity }}</p>
                        </div>
                        <div class="text-end">
                            <p class="mb-1">Rp {{ number_format($detail->price, 0, ',', '.') }}</p>
                            <p class="mb-0 fw-semibold">Subtotal: Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-4">Shipping</h5>
                    <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $order->user->email }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 p-4">
                <h5 class="mb-4">Order Summary</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span>Payment Status</span>
                    <span>{{ ucfirst($order->status) }}</span>
                </div>
                @if($order->status === 'pending' && $order->payment_url)
                    <a href="{{ $order->payment_url }}" target="_blank" class="btn btn-warning w-100">Pay Now</a>
                @else
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">Back to Orders</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
