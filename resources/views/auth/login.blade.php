@extends('base.base')
@section('content')

<style>
    /* --- THEME BACKGROUND OVERHAUL --- */
    body {
        /* Direct asset reference ensuring it strictly maps to background2.jpg with a cache-buster timestamp */
        background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.25)), url("{{ asset('images/background2.jpg') }}?v={{ time() }}") !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important; 
        background-position: center center !important; 
        background-size: cover !important; 
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding-top: 80px; /* Offset for fixed global navbar spacing protection */
    }

    /* Fixed Header Logic Compatibility */
    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }

    /* Remove core white background wrapper to let background image shine */
    .content-wrapper {
        background-color: transparent !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
    }

    /* --- THE LOGIN BOX CONSOLE CARD --- */
    .login-box-card {
        background: #ffffff;
        border: 1px solid #eef0f2;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        border-radius: 24px;
    }

    /* --- RAW HOVER SHOWCASE SPACE (No background container box) --- */
    .scenic-artwork-column {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        perspective: 1200px; /* Essential for deep 3D card flipping realism */
    }

    /* Continuous Floating/Hovering Mechanism */
    .floating-stage {
        width: 260px;
        height: 400px;
        position: relative;
        transform-style: preserve-3d;
        animation: smoothFloat 4s ease-in-out infinite;
    }

    /* Card Flip Structure */
    .card-flipper {
        width: 100%;
        height: 100%;
        position: absolute;
        transform-style: preserve-3d;
        transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Flips the structure when active modifier class is appended via JS loop */
    .card-flipper.flipped {
        transform: rotateY(180deg);
    }

    .cover-face {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cover-face img {
        width: 100%;
        height: 100%;
        object-fit: contain; /* Shows entire raw pixel image without ugly crop alterations */
    }

    .cover-face.back {
        transform: rotateY(180deg);
    }

    /* --- ANIMATION KEYFRAMES --- */
    @keyframes smoothFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
    }

    /* --- BRAND THEMED FORM TOKENS --- */
    .btn-soft-orange {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
        transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }
    
    .btn-soft-orange:hover, .btn-soft-orange:focus {
        background-color: #a64f1e !important;
        border-color: #a64f1e !important;
    }

    .text-soft-orange {
        color: #c25e25 !important;
    }

    .text-soft-orange:hover {
        color: #a64f1e !important;
    }

    .form-control:focus {
        border-color: #c25e25 !important;
        box-shadow: 0 0 0 0.2rem rgba(194, 94, 37, 0.15) !important;
    }

    /* Google Sign-In Button Styling */
    .google-signin-btn {
        background-color: #ffffff !important;
        color: #3c4043 !important;
        border: 1px solid #dadce0 !important;
        transition: all 0.2s ease !important;
    }

    .google-signin-btn:hover {
        background-color: #f8f9fa !important;
        border-color: #d2d3d4 !important;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.08) !important;
    }

    .google-signin-btn:active {
        background-color: #f1f3f4 !important;
        border-color: #dadce0 !important;
    }
</style>

<div class="container my-auto content-wrapper">
    <div class="row justify-content-center align-items-center g-0">
        <div class="col-xl-10 col-lg-11">
            
            <!-- Grid Split: Separated completely into clean columns with distinct spacing gap metrics -->
            <div class="row align-items-center justify-content-between gap-5 gap-md-0">
                
                {{-- LEFT COLUMN: Raw floating 3D Book Cover Artwork --}}
                <div class="col-md-5 scenic-artwork-column d-none d-md-flex pe-lg-5">
                    <div class="floating-stage">
                        <div class="card-flipper" id="flipperInstance">
                            <!-- Front Face Card Slot -->
                            <div class="cover-face front">
                                <img id="frontImg" src="" alt="Book Cover Active View">
                            </div>
                            <!-- Back Face Card Slot (Preloads the upcoming cover variation) -->
                            <div class="cover-face back">
                                <img id="backImg" src="" alt="Book Cover Alternating View">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Differentiated Independent Login Console Card --}}
                <div class="col-md-6 col-xl-5">
                    <div class="card border-0 login-box-card p-4 p-md-5">
                        <div class="card-body p-0">
                            
                            <div class="mb-4">
                                <h3 class="fw-bold text-dark mb-1">Welcome Back</h3>
                                <p class="text-muted small mb-0">Please login to access your BookHive account</p>
                            </div>

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert" style="font-size: 0.85rem;">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('login.auth') }}" method="POST" novalidate>
                                @csrf

                                <!-- Email Input field -->
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold small text-secondary">Email address</label>
                                    <input
                                        type="email"
                                        class="form-control form-control-lg fs-6 @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="name@example.com"
                                        required
                                        autofocus
                                    >
                                    @error('email')
                                        <div class="invalid-feedback small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Password Input field -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                                    <div class="input-group input-group-lg has-validation">
                                        <input
                                            type="password"
                                            class="form-control fs-6 @error('password') is-invalid @enderror"
                                            id="password"
                                            name="password"
                                            placeholder="Enter your security credentials"
                                            required
                                        >
                                        <button class="btn btn-outline-secondary px-3" type="button" id="togglePassword" title="Toggle Password Visibility" style="border-color: #ced4da; background-color: #ffffff;">
                                            <i class="fas fa-eye" id="toggleIcon" style="color: #718096;"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback small">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Form Submit Action Button -->
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-soft-orange btn-lg fs-6 fw-bold py-2.5 text-uppercase rounded-3 shadow-sm">
                                        Sign In
                                    </button>
                                </div>

                                <!-- Divider -->
                                <div class="d-flex align-items-center my-4">
                                    <hr class="flex-grow-1">
                                    <span class="px-3 text-muted small">or</span>
                                    <hr class="flex-grow-1">
                                </div>

                                <!-- Google Sign-In Button -->
                                <div class="d-grid mb-3">
                                    <a href="{{ route('auth.google') }}" class="btn btn-lg fs-6 fw-bold py-2.5 rounded-3 shadow-sm google-signin-btn">
                                        <i class="fab fa-google me-2"></i>Sign in with Google
                                    </a>
                                </div>

                                <div class="text-center mt-4">
                                    <p class="text-muted mb-0 small">Don't have an account? <a href="{{ route('register.show') }}" class="text-soft-orange text-decoration-none fw-bold">Sign up here</a></p>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- Password Fields Input Masking Configuration ---
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");
        const toggleIcon = document.getElementById("toggleIcon");

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener("click", function () {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);
                
                // Updates icon visibility mapping smoothly
                if (toggleIcon) {
                    toggleIcon.classList.toggle('fa-eye');
                    toggleIcon.classList.toggle('fa-eye-slash');
                }
            });
        }

        // --- DYNAMIC CARD-FLIP VARIATION ROTATOR MODULE ---
        // Complete clean image array referenced strictly from asset token filenames inside Screenshot 2026-05-31 at 20.21.16.png
        const coverAssets = [
            "{{ asset('product_image/a-game-of-thrones-front.jpg') }}",
            "{{ asset('product_image/atomic-habits-front.jpg') }}",
            "{{ asset('product_image/becoming-front.jpg') }}",
            "{{ asset('product_image/dune-front.jpg') }}",
            "{{ asset('product_image/foundation-front.jpg') }}",
            "{{ asset('product_image/harry-potter-and-the-philosopher-s-stone-front.jpg') }}",
            "{{ asset('product_image/kafka-on-the-shore-front.jpg') }}",
            "{{ asset('product_image/pride-and-prejudice-front.jpg') }}",
            "{{ asset('product_image/sapiens-a-brief-history-of-humankind-front.jpg') }}",
            "{{ asset('product_image/the-great-gatsby-front.jpg') }}",
            "{{ asset('product_image/the-psychology-of-money-front.jpg') }}",
            "{{ asset('product_image/the-shining-front.jpg') }}"
        ];

        const flipper = document.getElementById('flipperInstance');
        const frontImg = document.getElementById('frontImg');
        const backImg = document.getElementById('backImg');

        let currentIndex = 0;
        let isFlipped = false;

        // Establish initial showcase cover values
        if (coverAssets.length > 0) {
            frontImg.src = coverAssets[0];
            backImg.src = coverAssets[(currentIndex + 1) % coverAssets.length];
        }

        /**
         * Selects a truly random distinct index variable to populate the alternate face card context
         */
        function getRandomIndex(excludeIndex) {
            let nextIdx;
            do {
                nextIdx = Math.floor(Math.random() * coverAssets.length);
            } while (nextIdx === excludeIndex && coverAssets.length > 1);
            return nextIdx;
        }

        /**
         * Executes card rotation steps dynamically updating raw targets sequentially
         */
        function rotateShowcaseCover() {
            if (!flipper || coverAssets.length < 2) return;

            const nextIndex = getRandomIndex(currentIndex);

            if (!isFlipped) {
                // Flipped to Back Face: Populate backend frame buffer, execute transition
                backImg.src = coverAssets[nextIndex];
                flipper.classList.add('flipped');
                isFlipped = true;
            } else {
                // Flipped back to Front Face: Populate frontend frame buffer, execute reverse transition
                frontImg.src = coverAssets[nextIndex];
                flipper.classList.remove('flipped');
                isFlipped = false;
            }

            currentIndex = nextIndex;
        }

        // Initialize carousel interval timers (Runs every 3.5 seconds)
        setInterval(rotateShowcaseCover, 3500);
    });
</script>
@endsection