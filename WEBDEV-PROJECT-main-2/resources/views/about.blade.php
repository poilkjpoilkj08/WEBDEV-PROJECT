@extends('base.base')
@section('content')

<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-image: url("{{ asset('images/bg1.jpg') }}") !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important; 
        background-position: center center !important; 
        background-size: cover !important; 
        min-height: 100vh;
        padding-top: 100px;
    }

    /* Fixed Header Logic Compatibility */
    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }

    /* GLASS BOX FOR HEADERS */
    .glass-header-box {
        background: rgba(255, 255, 255, 0.45); 
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 10px 28px;
        border-radius: 50px;
        display: inline-block;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    /* Remove core white background wrapper to let background image shine */
    .content-wrapper {
        background-color: transparent !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
    }

    /* Card lift hover animations */
    .hover-lift:hover {
        transform: translateY(-8px);
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .hover-lift {
        transition: transform 0.3s ease;
    }
</style>

<div class="container py-5 content-wrapper">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Top Page Header -->
            <div class="text-center mb-5">
                <div class="glass-header-box mb-3">
                    <h1 class="h2 mb-0 fw-bold text-dark">{{ __('messages.about_us') }}</h1>
                </div>
                <div class="mt-2">
                    <p class="text-white bg-dark bg-opacity-25 d-inline-block px-4 py-2 rounded-pill shadow-sm backdrop-blur lead mb-0">
                        Discover the story behind BookHive
                    </p>
                </div>
            </div>

            <!-- Content Split Grid Blocks -->
            <div class="row g-5 mb-5">
                <!-- Our Mission Card -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg h-100 hover-lift bg-white rounded-4 overflow-hidden">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="d-inline-flex p-3 bg-warning bg-opacity-10 rounded-circle mb-3">
                                    <i class="fas fa-book-open fa-2x text-warning"></i>
                                </div>
                                <h3 class="h4 fw-bold text-dark">Our Mission</h3>
                            </div>
                            <p class="text-secondary text-center lh-lg mb-0" style="font-size: 0.95rem;">
                                At BookHive, we believe in the power of stories to transform lives. Our mission is to connect readers with exceptional books from talented authors across all genres, making quality literature accessible to everyone.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Our Community Card -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg h-100 hover-lift bg-white rounded-4 overflow-hidden">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="d-inline-flex p-3 bg-primary bg-opacity-10 rounded-circle mb-3">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                                <h3 class="h4 fw-bold text-dark">Our Community</h3>
                            </div>
                            <p class="text-secondary text-center lh-lg mb-0" style="font-size: 0.95rem;">
                                We foster a vibrant community of book lovers, from casual readers to literary enthusiasts. Join thousands of members who share their passion for reading and discover new favorites every day.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Showcase Mascot Section -->
            <div class="text-center mt-5 pt-3">
                <div class="position-relative d-inline-block p-4 rounded-4 bg-white bg-opacity-10 backdrop-blur shadow-lg border border-white border-opacity-20">
                    <img src="{{ asset('images/kucing1.webp') }}" 
                         alt="BookHive Mascot" 
                         class="img-fluid rounded-3 shadow-md hover-lift" 
                         style="max-width: 260px; object-fit: contain;">
                    <div class="mt-3">
                        <span class="badge bg-dark text-warning border border-warning fw-bold px-3 py-2 rounded-pill shadow-sm small">
                            <i class="fas fa-paw me-2"></i>Meet Our Library Mascot
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection