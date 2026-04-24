@extends('base.base')

@section('content')
<!-- Toast Notifications -->
@if(session('error'))
   <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-4 shadow" style="z-index: 1050;" role="alert">
      <strong>Error!</strong> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
   </div>
@endif

@if(session('success'))
   <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-4 shadow" style="z-index: 1050;" role="alert">
      <strong>Success!</strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
   </div>
@endif

<!-- DataTables CSS for modern styling and filtering -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container mt-5 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h3 class="mb-0 text-primary">{{ in_array('admin', $userRoles) ? 'All Orders' : 'My Orders' }}</h3>
        </div>
        
        <div class="card-body p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($orders->isEmpty())
                <div class="alert alert-info text-center py-4 rounded-3 border-0 bg-light text-muted">
                    <h5 class="mb-0">You don't have any orders yet.</h5>
                </div>
            @else
                <div class="table-responsive">
                    <table id="ordersTable" class="table table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice Number</th>
                                @if (in_array('admin', $userRoles))
                                    <th>User</th>
                                @endif
                                <th>Total Price</th>
                                <th>Status (Click to Check)</th>
                                <th>Payment</th>
                                <th>Order Date</th>
                                <th>View Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td><span class="fw-bold">{{ $order->invoice_number }}</span></td>
                                    @if (in_array('admin', $userRoles))
                                        <td>{{ $order->user->name ?? 'Unknown User' }}</td>
                                    @endif
                                    <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('payment_status', $order->id) }}" class="text-decoration-none" title="Click to refresh status">
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
                                        </a>
                                    </td>
                                    <td>
                                        @if ($order->status == 'pending' && $order->payment_url && $order->user_id == auth()->id())
                                            <!-- Trigger Snap.js Modal using the stored Token -->
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 pay-now-btn" 
                                                    data-token="{{ $order->payment_url }}" 
                                                    data-status-url="{{ route('payment_status', $order->id) }}">
                                                Pay Now
                                            </button>
                                        @elseif ($order->status == 'paid' && $order->paid_at)
                                            <span class="text-success small fw-medium">
                                                <i class="bi bi-clock-history"></i> {{ \Carbon\Carbon::parse($order->paid_at)->format('d M Y H:i') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <!-- Use data-sort attribute to ensure DataTables sorts by timestamp rather than text -->
                                    <td data-sort="{{ $order->created_at->timestamp }}">{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('order_details', $order->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">View Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- jQuery (Required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS & Bootstrap 5 integration -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            // Order by the 'Order Date' column descending by default
            // If admin, it's column index 5, otherwise 4
            "order": [[ {{ in_array('admin', $userRoles) ? 5 : 4 }}, "desc" ]],
            "language": {
                "search": "Filter records:",
                "lengthMenu": "Display _MENU_ orders per page",
            },
            "pageLength": 10,
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": [ {{ in_array('admin', $userRoles) ? 6 : 5 }} ] } // Disable sorting on the View Details button column
            ]
        });

        // Initialize Midtrans Snap Modal Trigger
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

<!-- Midtrans Snap JS setup -->
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

@endsection