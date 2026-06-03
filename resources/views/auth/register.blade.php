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

    .password-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 8px;
        background-color: #e9ecef;
    }

    .password-strength.weak {
        background-color: #dc3545;
    }

    .password-strength.fair {
        background-color: #ffc107;
    }

    .password-strength.good {
        background-color: #17a2b8;
    }

    .password-strength.strong {
        background-color: #28a745;
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
                            <div class="password-strength" id="passwordStrength"></div>
                            <small class="text-muted d-block mt-2" id="passwordHint">
                                Password strength: <span id="strengthText">None</span>
                            </small>
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
                            <small class="text-danger d-block mt-2" id="passwordMismatch" style="display: none;">
                                Passwords do not match
                            </small>
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

        // Password strength indicator
        const passwordStrengthBar = document.getElementById("passwordStrength");
        const strengthText = document.getElementById("strengthText");

        if (passwordInput) {
            passwordInput.addEventListener("input", function () {
                const password = this.value;
                let strength = 0;

                if (password.length >= 8) strength++;
                if (password.length >= 12) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^a-zA-Z0-9]/.test(password)) strength++;

                passwordStrengthBar.className = 'password-strength';
                
                if (strength === 0) {
                    passwordStrengthBar.classList.add('weak');
                    strengthText.textContent = 'Weak';
                } else if (strength <= 2) {
                    passwordStrengthBar.classList.add('weak');
                    strengthText.textContent = 'Weak';
                } else if (strength === 3) {
                    passwordStrengthBar.classList.add('fair');
                    strengthText.textContent = 'Fair';
                } else if (strength === 4) {
                    passwordStrengthBar.classList.add('good');
                    strengthText.textContent = 'Good';
                } else {
                    passwordStrengthBar.classList.add('strong');
                    strengthText.textContent = 'Strong';
                }

                // Check password match
                checkPasswordMatch();
            });
        }

        // Password confirmation match check
        function checkPasswordMatch() {
            const password = passwordInput.value;
            const passwordConfirm = passwordConfirmInput.value;
            const mismatchMsg = document.getElementById("passwordMismatch");

            if (passwordConfirm && password !== passwordConfirm) {
                mismatchMsg.style.display = 'block';
            } else {
                mismatchMsg.style.display = 'none';
            }
        }

        if (passwordConfirmInput) {
            passwordConfirmInput.addEventListener("input", checkPasswordMatch);
        }
    });
</script>

@endsection
