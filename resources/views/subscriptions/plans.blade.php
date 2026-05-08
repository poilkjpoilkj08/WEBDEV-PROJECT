@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">BookHive Subscription Plans</h1>
        <p class="lead text-muted">Stay updated with new arrivals, exclusive deals, and reading recommendations.</p>
    </div>

    @if(session('subscription_success'))
    <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('subscription_success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('subscription_error'))
    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('subscription_error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4 justify-content-center mb-5">

        {{-- FREE --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 text-center">
                <div class="card-body p-4">
                    <div class="mb-3"><i class="fas fa-envelope fa-2x text-secondary"></i></div>
                    <h3 class="h4 fw-bold">Free</h3>
                    <div class="display-6 fw-bold my-3">$0<small class="fs-6 text-muted">/mo</small></div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Monthly newsletter</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>New arrivals digest</li>
                        <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i>Early access deals</li>
                        <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i>Exclusive discounts</li>
                    </ul>
                    <button class="btn btn-outline-secondary w-100" onclick="openSubscribeModal('free')">Get Started</button>
                </div>
            </div>
        </div>

        {{-- BASIC --}}
        <div class="col-md-4">
            <div class="card shadow border-primary h-100 text-center" style="border-width: 2px !important;">
                <div class="card-header bg-primary text-white fw-bold py-2">Most Popular</div>
                <div class="card-body p-4">
                    <div class="mb-3"><i class="fas fa-book-open fa-2x text-primary"></i></div>
                    <h3 class="h4 fw-bold">Basic</h3>
                    <div class="display-6 fw-bold my-3">$4.99<small class="fs-6 text-muted">/mo</small></div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Weekly newsletter</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>New arrivals digest</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Early access deals</li>
                        <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i>Exclusive discounts</li>
                    </ul>
                    <button class="btn btn-primary w-100" onclick="openSubscribeModal('basic')">Subscribe Now</button>
                </div>
            </div>
        </div>

        {{-- PREMIUM --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 text-center" style="background: linear-gradient(135deg, #fff8e1, #fff);">
                <div class="card-body p-4">
                    <div class="mb-3"><i class="fas fa-crown fa-2x text-warning"></i></div>
                    <h3 class="h4 fw-bold">Premium</h3>
                    <div class="display-6 fw-bold my-3">$9.99<small class="fs-6 text-muted">/mo</small></div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Daily newsletter</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>New arrivals digest</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Early access deals</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Exclusive 15% discount</li>
                    </ul>
                    <button class="btn btn-warning w-100 text-white" onclick="openSubscribeModal('premium')">Go Premium</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Subscribe Modal --}}
<div class="modal fade" id="subscribeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-bookmark text-primary me-2"></i>Subscribe to BookHive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('subscribe') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" id="modal-plan" value="free">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Your Name (optional)</label>
                        <input type="text" name="name" class="form-control" placeholder="Jane Doe">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                    </div>
                    <div class="mb-3 p-3 bg-light rounded-3">
                        Selected plan: <strong id="modal-plan-label" class="text-primary">Free</strong>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane me-2"></i>Confirm Subscription
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openSubscribeModal(plan) {
    const labels = { free: 'Free', basic: 'Basic – $4.99/mo', premium: 'Premium – $9.99/mo' };
    document.getElementById('modal-plan').value = plan;
    document.getElementById('modal-plan-label').textContent = labels[plan] || plan;
    new bootstrap.Modal(document.getElementById('subscribeModal')).show();
}
</script>
@endsection
