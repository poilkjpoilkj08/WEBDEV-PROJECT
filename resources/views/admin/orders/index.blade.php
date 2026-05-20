@extends('base.base')

@section('content')
<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 mb-1"><i class="fas fa-receipt me-2"></i>All Orders Management</h1>
            <p class="text-muted">View all customer orders</p>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No orders found in the system.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Shipping Method</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>
                            <span class="badge bg-primary">{{ $order->invoice_number }}</span>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $order->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $order->user->email }}</small>
                            </div>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <span class="fw-bold text-success">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            @if($order->payment_method)
                            <span class="badge bg-info">{{ formatPaymentMethod($order->payment_method) }}</span>
                            @else
                            <span class="badge bg-secondary">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'pending' ? 'warning text-dark' : 'secondary') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            @if($order->shipping_method)
                            <small class="text-muted">{{ ucfirst($order->shipping_method) }}</small><br>
                            <small class="text-primary">{{ $order->shipping_distance_km ?? 'N/A' }} km</small>
                            @else
                            <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
