@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-dark mb-3">{{ __('messages.about_us') }}</h1>
                <p class="lead text-muted">Discover the story behind BookHive</p>
            </div>

            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <i class="fas fa-book-open fa-3x text-warning mb-3"></i>
                                <h3 class="h4 fw-bold">Our Mission</h3>
                            </div>
                            <p class="text-muted">At BookHive, we believe in the power of stories to transform lives. Our mission is to connect readers with exceptional books from talented authors across all genres, making quality literature accessible to everyone.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                <h3 class="h4 fw-bold">Our Community</h3>
                            </div>
                            <p class="text-muted">We foster a vibrant community of book lovers, from casual readers to literary enthusiasts. Join thousands of members who share their passion for reading and discover new favorites every day.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <img src="{{ asset('images/kucing1.webp') }}" alt="BookHive Mascot" class="img-fluid rounded shadow-sm" style="max-width: 300px;">
            </div>
        </div>
    </div>
</div>
@endsection