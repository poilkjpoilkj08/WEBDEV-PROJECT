@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="display-6 mb-1">{{ in_array('admin', $userRoles) ? 'All Orders' : 'My Orders' }}</h1>
            <p class="text-muted mb-0">{{ in_array('admin', $userRoles) ? 'View all customer orders' : 'Track your purchases and payment status.' }}</p>
        </div>
        <a href="{{ route('books.listing') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-book me-2"></i>Browse Books
        </a>
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
            <p class="text-muted fs-5">{{ in_array('admin', $userRoles) ? 'No orders found.' : 'You have no orders yet.' }}</p>
            <a href="{{ route('books.listing') }}" class="btn btn-primary rounded-pill px-4">Start Shopping</a>
        </div>
    @else
        <div class="row g-3">
            @foreach($orders as $order)
            @php
                $isPaid = in_array($order->status, ['payment_paid', 'paid']);
                $statusColor = $isPaid ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning');
                $statusLabel = match($order->status) {
                    'payment_paid', 'paid' => 'Paid',
                    'pending'              => 'Pending Payment',
                    'cancelled'            => 'Cancelled',
                    default                => ucfirst($order->status),
                };
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
                                            <i class="fas fa-{{ $isPaid ? 'check-circle' : ($order->status === 'cancelled' ? 'times-circle' : 'clock') }} me-1"></i>
                                            {{ $statusLabel }}
                                        </span>
                                        <div class="d-flex gap-2 justify-content-sm-end">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                                <i class="fas fa-eye me-1"></i>Details
                                            </a>
                                            @if($order->status === 'pending' && $order->payment_url)
                                            <a href="{{ $order->payment_url }}" target="_blank" class="btn btn-warning btn-sm rounded-pill px-3">
                                                <i class="fas fa-credit-card me-1"></i>Pay
                                            </a>
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
@endsection
