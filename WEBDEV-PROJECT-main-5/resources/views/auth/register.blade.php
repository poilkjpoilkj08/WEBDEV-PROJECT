@extends('base.base')
@section('content')

<style>
    /* --- THEME BACKGROUND OVERHAUL --- */
    body {
        background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.25)), url("{{ asset('images/background2.jpg') }}?v={{ time() }}") !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important; 
        background-position: center center !important; 
        background-size: cover !important; 
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding-top: 80px;
    }

    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }

    .content-wrapper {
        background-color: transparent !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
    }

    .register-box-card {
        background: #ffffff;
        border: 1px solid #eef0f2;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        border-radius: 24px;
    }

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



    /* ===== RESPONSIVE STYLES FOR REGISTER PAGE ===== */
    @media (max-width: 768px) {
        /* Register card padding */
        .register-box-card {
            padding: 1.5rem !important;
        }

        /* Heading sizing */
        .h3, h3 {
            font-size: 1.25rem;
        }

        /* Form label sizing */
        .form-label {
            font-size: 0.9rem;
        }

        /* Form control sizing */
        .form-control {
            font-size: 0.95rem;
            padding: 0.75rem 0.75rem;
        }

        /* Button sizing */
        .btn {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        /* Small text sizing */
        .small {
            font-size: 0.85rem;
        }

        /* Text utilities */
        .text-muted {
            font-size: 0.9rem;
        }

        /* Password strength indicator */
        .password-strength {
            height: 3px;
        }

        /* Icon sizing */
        .fa-lg {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 576px) {
        /* Extra small screens */
        /* Background padding adjustment */
        body {
            padding-top: 70px;
            padding-left: 0;
            padding-right: 0;
        }

        /* Container adjustments */
        .container {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        /* Register card sizing */
        .register-box-card {
            padding: 1rem !important;
            border-radius: 16px;
        }

        .card-body {
            padding: 0 !important;
        }

        /* Heading sizing */
        .h3, h3 {
            font-size: 1.1rem;
        }

        .mb-4 {
            margin-bottom: 1rem !important;
        }

        .mb-3 {
            margin-bottom: 0.75rem !important;
        }

        .mb-1 {
            margin-bottom: 0.5rem !important;
        }

        /* Form styling */
        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .form-control {
            font-size: 16px; /* Prevent iOS zoom */
            padding: 0.75rem;
            border-radius: 0.375rem;
        }

        .form-control:focus {
            border-color: #c25e25 !important;
            box-shadow: 0 0 0 0.15rem rgba(194, 94, 37, 0.15) !important;
        }

        /* Button sizing */
        .btn {
            padding: 0.65rem 1rem;
            font-size: 0.9rem;
            min-height: 44px;
            border-radius: 0.5rem;
            width: 100%;
        }

        .btn-soft-orange {
            width: 100%;
        }

        /* Alert sizing */
        .alert {
            font-size: 0.8rem;
            padding: 0.75rem;
            border-radius: 0.75rem;
        }

        .alert-dismissible .btn-close {
            padding: 0.3rem;
        }

        /* Text utilities */
        .text-muted {
            font-size: 0.8rem;
        }

        .small {
            font-size: 0.75rem;
        }

        /* Icon sizing */
        .fa-2x {
            font-size: 1.2rem;
        }

        .fa-lg {
            font-size: 0.9rem;
        }

        /* Row and column adjustments */
        .row {
            gap: 0.75rem !important;
        }

        .col-md-6, .col-xl-4 {
            flex-basis: 100%;
        }

        /* Password strength indicator */
        .password-strength {
            height: 3px;
            margin-top: 6px;
        }

        /* Link styling */
        a {
            word-break: break-word;
            font-size: 0.85rem;
        }

        /* Prevent horizontal overflow */
        body {
            overflow-x: hidden;
        }
    }
</style>

<div class="container my-auto content-wrapper">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 register-box-card p-4 p-md-5">
                <div class="card-body p-0">
                    
                    <div class="mb-4">
                        <h3 class="fw-bold text-dark mb-1">Create Account</h3>
                        <p class="text-muted small mb-0">Join BookHive and discover your next favorite book</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert" style="font-size: 0.85rem;">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('register.store') }}" method="POST" novalidate>
                        @csrf

                        <!-- Full Name Input field -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold small text-secondary">Full Name</label>
                            <input
                                type="text"
                                class="form-control form-control-lg fs-6 @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="e.g. John Doe"
                                required
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

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
                            >
                            @error('email')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Input field -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                            <div class="input-group input-group-lg has-validation">
                                <input
                                    type="password"
                                    class="form-control fs-6 @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    placeholder="Min. 8 characters with mixed case"
                                    required
                                >
                                <button class="btn btn-outline-secondary px-3" type="button" id="togglePassword" title="Toggle Password Visibility" style="border-color: #ced4da; background-color: #ffffff;">
                                    <i class="fas fa-eye" id="toggleIcon" style="color: #718096;"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Confirmation field -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Confirm Password</label>
                            <div class="input-group input-group-lg has-validation">
                                <input
                                    type="password"
                                    class="form-control fs-6 @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="Re-enter your password"
                                    required
                                >
                                <button class="btn btn-outline-secondary px-3" type="button" id="togglePasswordConfirm" title="Toggle Password Visibility" style="border-color: #ced4da; background-color: #ffffff;">
                                    <i class="fas fa-eye" id="toggleIconConfirm" style="color: #718096;"></i>
                                </button>
                                @error('password_confirmation')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Submit Action Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-soft-orange btn-lg fs-6 fw-bold py-2.5 text-uppercase rounded-3 shadow-sm">
                                Create Account
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-0 small">Already have an account? <a href="{{ route('login.show') }}" class="text-soft-orange text-decoration-none fw-bold">Sign in here</a></p>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Password visibility toggle
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");
        const toggleIcon = document.getElementById("toggleIcon");

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener("click", function () {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);
                
                if (toggleIcon) {
                    toggleIcon.classList.toggle('fa-eye');
                    toggleIcon.classList.toggle('fa-eye-slash');
                }
            });
        }

        // Confirm password visibility toggle
        const togglePasswordConfirm = document.getElementById("togglePasswordConfirm");
        const passwordConfirmInput = document.getElementById("password_confirmation");
        const toggleIconConfirm = document.getElementById("toggleIconConfirm");

        if (togglePasswordConfirm && passwordConfirmInput) {
            togglePasswordConfirm.addEventListener("click", function () {
                const type = passwordConfirmInput.getAttribute("type") === "password" ? "text" : "password";
                passwordConfirmInput.setAttribute("type", type);
                
                if (toggleIconConfirm) {
                    toggleIconConfirm.classList.toggle('fa-eye');
                    toggleIconConfirm.classList.toggle('fa-eye-slash');
                }
            });
        }
    });
</script>

@endsection
