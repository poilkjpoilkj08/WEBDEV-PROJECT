@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 mb-1">{{ in_array('admin', $userRoles) ? 'All Orders' : 'My Orders' }}</h1>
            <p class="text-muted">{{ in_array('admin', $userRoles) ? 'View all customer orders' : 'Track your purchases and payment status.' }}</p>
        </div>
        <a href="{{ route('books.listing') }}" class="btn btn-outline-secondary">Browse Books</a>
    </div>

    @if($orders->isEmpty())
        <div class="alert alert-warning">{{ in_array('admin', $userRoles) ? 'No orders found.' : 'You do not have any orders yet.' }}</div>
    @else
        <div class="row g-4">
            @foreach($orders as $order)
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div>
                            <h5 class="mb-1">Order {{ $order->invoice_number }}</h5>
                            @if(in_array('admin', $userRoles))
                            <p class="mb-1 text-muted small">Customer: <strong>{{ $order->user->name }}</strong> ({{ $order->user->email }})</p>
                            @endif
                            <p class="mb-1 text-muted">Placed on {{ $order->created_at->format('F j, Y') }}</p>
                            <p class="mb-0">Total: <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></p>
                            @if($order->payment_method)
                            <p class="mb-0 small mt-2"><i class="fas fa-credit-card text-info me-1"></i>Payment: <span class="badge bg-info">{{ formatPaymentMethod($order->payment_method) }}</span></p>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $order->status === 'payment_paid' || $order->status === 'paid' ? 'success' : ($order->status === 'pending' ? 'warning text-dark' : 'secondary') }} py-2 px-3">{{ $order->status === 'payment_paid' ? 'Paid' : ucfirst($order->status) }}</span>
                            <div class="mt-3">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
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
