{{-- Quick subscribe widget — embed in footer or homepage
     Usage: @include('subscriptions.partials.widget') --}}

@if(session('subscription_success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('subscription_success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('subscription_error'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('subscription_error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #8B4513, #D2691E);">
    <div class="card-body text-white p-4 text-center">
        <h3 class="h5 fw-bold mb-1"><i class="fas fa-envelope-open-text me-2"></i>Stay in the Loop</h3>
        <p class="small mb-3 opacity-75">Get new book alerts and exclusive deals straight to your inbox.</p>
        <form action="{{ route('subscribe') }}" method="POST" class="d-flex gap-2 justify-content-center flex-wrap">
            @csrf
            <input type="hidden" name="plan" value="free">
            <input type="email" name="email" class="form-control" style="max-width: 260px;"
                   placeholder="your@email.com" required>
            <button type="submit" class="btn btn-warning text-white fw-semibold">Subscribe Free</button>
        </form>
        <p class="small mt-2 mb-0 opacity-75">
            Want more perks? <a href="{{ route('subscribe.plans') }}" class="text-white fw-bold">See all plans →</a>
        </p>
    </div>
</div>
