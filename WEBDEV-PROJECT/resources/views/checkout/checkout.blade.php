@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 mb-1">Checkout</h1>
            <p class="text-muted">Review your order and complete payment securely.</p>
        </div>
        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">Back to Cart</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-4">Order Summary</h5>
                    @foreach($items as $item)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $item['book']->title }}</h6>
                            <small class="text-muted">Qty: {{ $item['quantity'] }}</small>
                        </div>
                        <span class="fw-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="d-flex justify-content-between align-items-center pt-4">
                        <span class="fw-bold">Total</span>
                        <span class="h4 text-success">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-4">Payment Method</h5>
                    <div class="alert alert-info mb-4">
                        <strong>Available Payment Methods:</strong>
                        <ul class="mb-0 mt-2">
                            <li>💳 Credit/Debit Card (Visa, MasterCard, Amex)</li>
                            <li>📱 QRIS (QR Code Payment)</li>
                            <li>🏦 Bank Transfer (BCA, BRI, BNI, Mandiri)</li>
                            <li>📱 Mobile Banking (BCA mBanking, BRI Mobile)</li>
                            <li>🔐 E-Wallet (GoPay, OVO, Dana, LinkAja)</li>
                            <li>💰 Buy Now Pay Later</li>
                        </ul>
                    </div>

                    <form id="paymentForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled />
                        </div>
                        <div class="alert alert-warning">
                            <small>A secure payment popup will appear where you can select your preferred payment method.</small>
                        </div>
                        <button type="button" id="payButton" class="btn btn-warning w-100 fw-bold btn-lg">
                            <span id="buttonText">Proceed to Payment</span>
                            <span id="loadingSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Processing...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Midtrans Snap JavaScript -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>

<script>
document.getElementById('payButton').addEventListener('click', function(e) {
    e.preventDefault();
    
    const buttonText = document.getElementById('buttonText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const payButton = document.getElementById('payButton');
    
    payButton.disabled = true;
    buttonText.classList.add('d-none');
    loadingSpinner.classList.remove('d-none');
    
    const formData = new FormData(document.getElementById('paymentForm'));
    
    fetch('{{ route("checkout.process") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.snapToken) {
            // Open Snap popup
            snap.pay(data.snapToken, {
                onSuccess: function(result) {
                    // Payment successful - clear cart before redirect
                    fetch('{{ route("cart.clear") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    }).finally(() => {
                        window.location.href = '{{ route("orders.index") }}?success=true';
                    });
                },
                onPending: function(result) {
                    // Payment pending - keep cart
                    console.log('Payment pending:', result);
                    alert('Payment is pending. Please complete your payment.');
                    resetPaymentButton();
                },
                onError: function(result) {
                    // Payment error - keep cart
                    console.error('Payment error:', result);
                    alert('Payment failed. Please try again.');
                    resetPaymentButton();
                },
                onClose: function() {
                    // User closed the popup without completing payment - keep cart
                    console.log('Snap popup closed');
                    resetPaymentButton();
                }
            });
        } else {
            alert('Error: ' + (data.error || 'Failed to initiate payment'));
            resetPaymentButton();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        resetPaymentButton();
    });
    
    function resetPaymentButton() {
        payButton.disabled = false;
        buttonText.classList.remove('d-none');
        loadingSpinner.classList.add('d-none');
    }
});
</script>
@endsection
