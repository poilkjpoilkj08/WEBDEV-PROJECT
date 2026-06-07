@extends('base.base')
@section('content')
<div class="container py-5 text-center">
    <div class="py-5">
        <i class="fas fa-heart-broken fa-4x text-muted mb-4"></i>
        <h1 class="h3 fw-bold">You've been unsubscribed</h1>
        <p class="text-muted mb-4">We're sorry to see you go! You won't receive any more emails from BookHive.</p>
        <a href="{{ route('subscribe.plans') }}" class="btn btn-outline-primary me-2">Resubscribe</a>
        <a href="{{ route('home') }}" class="btn btn-primary">Back to Books</a>
    </div>
</div>

<style>
/* ===== RESPONSIVE STYLES FOR UNSUBSCRIBED PAGE ===== */
@media (max-width: 768px) {
    /* Heading sizing */
    .h3 {
        font-size: 1.25rem;
    }

    /* Button sizing */
    .btn {
        padding: 0.6rem 0.9rem;
        font-size: 0.9rem;
    }

    /* Text utilities */
    .text-muted {
        font-size: 0.95rem;
    }

    .mb-4 {
        margin-bottom: 1rem !important;
    }

    /* Icon sizing */
    .fa-4x {
        font-size: 2rem;
    }
}

@media (max-width: 576px) {
    /* Extra small screens */
    .container {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    /* Padding adjustment */
    .py-5 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }

    /* Heading sizing */
    .h3 {
        font-size: 1.1rem;
    }

    /* Button sizing and stacking */
    .btn {
        padding: 0.65rem 1rem;
        font-size: 0.85rem;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin-bottom: 0.5rem !important;
    }

    .me-2 {
        margin-right: 0 !important;
    }

    /* Text utilities */
    .text-muted {
        font-size: 0.9rem;
    }

    .mb-4 {
        margin-bottom: 0.75rem !important;
    }

    /* Icon sizing */
    .fa-4x {
        font-size: 1.75rem;
    }

    /* Prevent horizontal overflow */
    body {
        overflow-x: hidden;
    }
}
</style>
@endsection
