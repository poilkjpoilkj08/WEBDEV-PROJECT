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

<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold"><i class="fas fa-shopping-cart text-primary me-2"></i>My Cart</h2>
        <a href="{{ route('store') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Continue Shopping
        </a>
    </div>

    @if(empty($cart) || count($cart) == 0)
        <!-- Empty State -->
        <div class="card shadow-sm border-0 text-center py-5">
            <div class="card-body py-5">
                <i class="fas fa-cart-arrow-down text-muted mb-4" style="font-size: 5rem; opacity: 0.5;"></i>
                <h3 class="text-muted fw-bold mb-2">Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any products to your cart yet.</p>
                <a href="{{ route('store') }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                    Start Shopping
                </a>
            </div>
        </div>
    @else
        <!-- Cart Content -->
        <div class="row g-4">
            <!-- Cart Items List (Left Column) -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-4 border-0 py-3 text-muted text-uppercase small" style="width: 40%;">Product</th>
                                        <th scope="col" class="border-0 py-3 text-muted text-uppercase small text-center">Price</th>
                                        <th scope="col" class="border-0 py-3 text-muted text-uppercase small text-center">Qty</th>
                                        <th scope="col" class="border-0 py-3 text-muted text-uppercase small text-end">Subtotal</th>
                                        <th scope="col" class="border-0 py-3 text-muted text-uppercase small text-center pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach ($cart as $id => $item)
                                        @php $total += $item['price'] * $item['quantity']; @endphp
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-box text-secondary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $item['name'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center py-3 text-muted">
                                                Rp {{ number_format($item['price'], 2, ',', '.') }}
                                            </td>
                                            <td class="text-center py-3" style="width: 140px;">
                                                <form action="{{ route('update_cart', $id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="input-group input-group-sm mx-auto shadow-sm rounded border overflow-hidden" style="max-width: 110px;">
                                                        <button class="btn btn-light border-0 border-end fw-bold px-2 text-muted" type="button" onclick="const input = this.nextElementSibling; if(input.value > 1) { input.value--; this.form.submit(); }">ー</button>
                                                        <input type="number" name="quantity" class="form-control text-center border-0 bg-white fw-bold px-1" value="{{ $item['quantity'] }}" min="1" max="10" readonly>
                                                        <button class="btn btn-light border-0 border-start fw-bold px-2 text-muted" type="button" onclick="const input = this.previousElementSibling; if(input.value < 10) { input.value++; this.form.submit(); }">＋</button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-end py-3 fw-bold text-dark">
                                                Rp {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}
                                            </td>
                                            <td class="text-center py-3 pe-4">
                                                <form action="{{ route('remove_from_cart', $id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0;" title="Remove Item">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Board (Right Column) -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-lg-top" style="top: 2rem;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4 border-bottom pb-3">Order Summary</h4>
                        
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Subtotal ({{ count($cart) }} Items)</span>
                            <span class="fw-medium text-dark">Rp {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                        
                        <hr class="text-muted opacity-25">
                        
                        <div class="d-flex justify-content-between mb-4 align-items-center">
                            <span class="fw-bold fs-5">Estimated Total</span>
                            <span class="fw-bold fs-4 text-primary">Rp {{ number_format($total, 2, ',', '.') }}</span>
                        </div>

                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center">
                                Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>
                        
                        <div class="text-center mt-3 small text-muted">
                            <i class="fas fa-lock me-1"></i> Secure Checkout Process
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection