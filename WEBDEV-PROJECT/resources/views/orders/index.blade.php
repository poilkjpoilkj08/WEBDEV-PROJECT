@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 mb-1">My Orders</h1>
            <p class="text-muted">Track your purchases and payment status.</p>
        </div>
        <a href="{{ route('books.listing') }}" class="btn btn-outline-secondary">Browse Books</a>
    </div>

    @if($orders->isEmpty())
        <div class="alert alert-warning">You do not have any orders yet.</div>
    @else
        <div class="row g-4">
            @foreach($orders as $order)
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div>
                            <h5 class="mb-1">Order {{ $order->invoice_number }}</h5>
                            <p class="mb-1 text-muted">Placed on {{ $order->created_at->format('F j, Y') }}</p>
                            <p class="mb-0">Total: <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'pending' ? 'warning text-dark' : 'secondary') }} py-2 px-3">{{ ucfirst($order->status) }}</span>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm mt-3">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
