@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 mb-1">Checkout</h1>
            <p class="text-muted">Review your order, enter shipping details, and complete payment.</p>
        </div>
        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">← Back to Cart</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">

        {{-- LEFT: Order Summary + Shipping Form --}}
        <div class="col-lg-7">

            {{-- Order summary --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-shopping-bag me-2 text-primary"></i>Order Summary
                </div>
                <div class="card-body">
                    @foreach($items as $item)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $item['book']->cover_image_src }}" alt="{{ $item['book']->title }}"
                                 style="width:40px;height:55px;object-fit:cover;border-radius:4px;">
                            <div>
                                <div class="fw-semibold">{{ $item['book']->title }}</div>
                                <small class="text-muted">Qty: {{ $item['quantity'] }} × Rp {{ number_format($item['book']->price, 0, ',', '.') }}</small>
                            </div>
                        </div>
                        <span class="fw-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="d-flex justify-content-between pt-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-semibold" id="subtotalDisplay">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between pt-1">
                        <span class="text-muted">Shipping</span>
                        <span id="shippingDisplay" class="text-muted">— select method</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-success" id="grandTotalDisplay">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Shipping address form --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>Shipping Address
                </div>
                <div class="card-body">
                    <form id="paymentForm">
                        @csrf
                        <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name *</label>
                                <input type="text" name="shipping_name" value="{{ auth()->user()->name }}"
                                       required class="form-control" placeholder="Recipient name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number *</label>
                                <input type="text" name="shipping_phone" required
                                       class="form-control" placeholder="+62 81234567890">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Street Address *</label>
                                <textarea name="shipping_address" required rows="2"
                                          class="form-control" placeholder="Jl. Example No. 123, RT 01/RW 02"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">City *</label>
                                <input type="text" name="shipping_city" required
                                       class="form-control" placeholder="Surabaya">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Province *</label>
                                <input type="text" name="shipping_province" required
                                       class="form-control" placeholder="Jawa Timur">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Postal Code *</label>
                                <input type="text" name="shipping_postal_code" required
                                       class="form-control" placeholder="60111">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text" name="shipping_country" value="Indonesia" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT: Shipping method + Payment --}}
        <div class="col-lg-5">

            {{-- Shipping method --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-truck me-2 text-primary"></i>Shipping Method
                </div>
                <div class="card-body p-0">
                    @php
                        $shippingMethods = \App\Http\Controllers\CheckoutController::shippingMethods();
                    @endphp
                    @foreach($shippingMethods as $key => $method)
                    <label class="d-flex align-items-center justify-content-between p-3 border-bottom shipping-option"
                           style="cursor:pointer;" for="ship_{{ $key }}">
                        <div class="d-flex align-items-center gap-3">
                            <input type="radio" name="shipping_method" id="ship_{{ $key }}"
                                   value="{{ $key }}"
                                   data-cost="{{ $method['cost'] }}"
                                   form="paymentForm"
                                   class="form-check-input shipping-radio mt-0"
                                   {{ $loop->first ? 'required checked' : '' }}>
                            <div>
                                <div class="fw-semibold">{{ $method['name'] }}</div>
                            </div>
                        </div>
                        <span class="fw-bold text-primary">
                            @if($method['cost'] === 0)
                                <span class="text-success">FREE</span>
                            @else
                                Rp {{ number_format($method['cost'], 0, ',', '.') }}
                            @endif
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Payment --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-credit-card me-2 text-primary"></i>Payment
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3 small">
                        <strong>Accepted methods:</strong>
                        💳 Credit/Debit Card &nbsp;|&nbsp; 📱 QRIS &nbsp;|&nbsp;
                        🏦 Bank Transfer &nbsp;|&nbsp; 🔐 E-Wallet (GoPay, OVO, Dana)
                        &nbsp;|&nbsp; 💰 Buy Now Pay Later
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                    </div>
                    <div class="alert alert-warning small">
                        A secure payment popup will appear to select your payment method.
                    </div>
                    <button type="button" id="payButton" class="btn btn-warning w-100 fw-bold btn-lg">
                        <span id="buttonText"><i class="fas fa-lock me-2"></i>Pay Now</span>
                        <span id="loadingSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
<script>
const subtotal = {{ $total }};

// Update totals when shipping method changes
document.querySelectorAll('.shipping-radio').forEach(function(radio) {
    radio.addEventListener('change', updateTotals);
});

function updateTotals() {
    const selected = document.querySelector('.shipping-radio:checked');
    if (!selected) return;
    const cost = parseInt(selected.dataset.cost) || 0;
    const grand = subtotal + cost;
    document.getElementById('shippingDisplay').textContent = cost === 0
        ? 'FREE'
        : 'Rp ' + cost.toLocaleString('id-ID');
    document.getElementById('grandTotalDisplay').textContent = 'Rp ' + grand.toLocaleString('id-ID');
}
updateTotals(); // init on load

document.getElementById('payButton').addEventListener('click', function(e) {
    e.preventDefault();
    const payButton = this;
    payButton.disabled = true;
    document.getElementById('buttonText').classList.add('d-none');
    document.getElementById('loadingSpinner').classList.remove('d-none');

    const formData = new FormData(document.getElementById('paymentForm'));

    fetch('{{ route("checkout.process") }}', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.snapToken) {
            snap.pay(data.snapToken, {
                onSuccess: function() {
                    fetch('{{ route("cart.clear") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    }).finally(() => {
                        window.location.href = '{{ route("orders.index") }}?success=true';
                    });
                },
                onPending: function() {
                    alert('Payment is pending. Please complete your payment.');
                    resetBtn();
                },
                onError: function() {
                    alert('Payment failed. Please try again.');
                    resetBtn();
                },
                onClose: function() { resetBtn(); }
            });
        } else {
            alert('Error: ' + (data.error || 'Failed to initiate payment'));
            resetBtn();
        }
    })
    .catch(() => { alert('An error occurred. Please try again.'); resetBtn(); });

    function resetBtn() {
        payButton.disabled = false;
        document.getElementById('buttonText').classList.remove('d-none');
        document.getElementById('loadingSpinner').classList.add('d-none');
    }
});
</script>
@endsection
