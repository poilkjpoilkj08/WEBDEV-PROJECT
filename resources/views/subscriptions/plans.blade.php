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

<style>
/* ===== RESPONSIVE STYLES FOR SUBSCRIPTION PLANS ===== */
@media (max-width: 768px) {
    /* Heading adjustments */
    .display-5 {
        font-size: 2rem;
    }

    .lead {
        font-size: 1rem;
    }

    /* Plan cards better spacing */
    .row.g-4 {
        gap: 1.5rem !important;
    }

    /* Card sizing */
    .card {
        border-radius: 12px;
    }

    /* Button sizing */
    .btn {
        padding: 0.65rem 1rem;
        font-size: 0.95rem;
    }

    /* Price display */
    .display-6 {
        font-size: 2rem;
    }

    /* List items */
    .list-unstyled li {
        font-size: 0.95rem;
        margin-bottom: 0.75rem !important;
    }

    /* Icon sizing */
    .fa-2x {
        font-size: 1.5rem;
    }
}

@media (max-width: 576px) {
    /* Extra small screens */
    .container {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

    /* Heading sizing */
    .display-5 {
        font-size: 1.5rem;
    }

    h1, .h1 {
        font-size: 1.5rem;
    }

    .lead {
        font-size: 0.95rem;
    }

    /* Cards full width with margins */
    .row.g-4 > [class*='col-'] {
        margin-bottom: 0.5rem;
    }

    .card {
        margin-bottom: 1.5rem;
        border-radius: 10px;
    }

    .card-body {
        padding: 1.5rem 1rem !important;
    }

    /* Price display */
    .display-6 {
        font-size: 1.75rem;
    }

    .fs-6 {
        font-size: 0.8rem !important;
    }

    /* List items mobile friendly */
    .list-unstyled li {
        font-size: 0.85rem;
        margin-bottom: 0.5rem !important;
    }

    .list-unstyled .fa-check,
    .list-unstyled .fa-times {
        font-size: 0.85rem;
    }

    /* Button sizing */
    .btn {
        padding: 0.6rem 0.8rem;
        font-size: 0.9rem;
        width: 100%;
    }

    /* Badge sizing */
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.6rem;
    }

    /* Card header */
    .card-header {
        padding: 0.75rem !important;
        font-size: 0.9rem;
    }

    /* Icon sizing */
    .fa-2x {
        font-size: 1.25rem;
    }

    /* Alert sizing */
    .alert {
        padding: 0.75rem;
        font-size: 0.9rem;
    }

    /* Modal sizing */
    .modal-dialog {
        max-width: 95% !important;
    }

    .modal-body {
        font-size: 0.95rem;
    }

    /* Prevent horizontal overflow */
    body {
        overflow-x: hidden;
    }

    /* Text truncation for long content */
    .text-truncate {
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Form controls */
    .form-control,
    .form-select {
        font-size: 16px; /* Prevent iOS zoom */
        padding: 0.75rem;
    }
}
</style>
@endsection
