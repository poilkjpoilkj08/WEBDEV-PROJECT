<!-- Base Navigation Wrapper Structure -->
<nav class="navbar navbar-expand-lg navbar-dark py-3 px-md-4 shadow-sm {{ Route::is('home') ? 'navbar-home-transparent' : '' }}" 
     style="{{ Route::is('home') ? 'background: rgba(43, 24, 12, 0.15) !important;' : 'background: linear-gradient(135deg, #c25e25, #a64f1e) !important;' }}">
    <div class="container-fluid">
        <!-- Brand Identity with Smooth Animated Image Configuration -->
        <a class="navbar-brand fw-bold fs-4 tracking-tight text-white d-flex align-items-center brand-transition" 
           href="{{ route('home') }}" 
           style="text-decoration: none !important; box-shadow: none !important; outline: none !important;">
            <img src="{{ asset('images/logo.png') }}" 
                 alt="BookHive Logo" 
                 height="45"
                 class="me-2 d-inline-block align-top logo-smooth"
                 style="object-fit: contain; filter: none !important;">
            BookHive
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="box-shadow: none !important;">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-smooth nav-underline-lift {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('messages.home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-smooth nav-underline-lift {{ Route::is('books.listing') ? 'active' : '' }}" href="{{ route('books.listing') }}">{{ __('messages.books') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-smooth nav-underline-lift {{ Route::is('about') ? 'active' : '' }}" href="{{ route('about') }}">{{ __('messages.about') }}</a>
                </li>
                @auth
                    @if(auth()->user()->hasRole(['admin', 'owner']))
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-smooth dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('messages.admin_panel') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark border-0 shadow-lg animate slideIn" aria-labelledby="adminDropdown" style="background-color: #a64f1e;">
                            <li><h6 class="dropdown-header">Books</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.books.index') }}"><i class="fas fa-list fa-fw me-2 text-white-50"></i>Manage Books</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Stores</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.stores.index') }}"><i class="fas fa-store fa-fw me-2 text-white-50"></i>Manage Store Locations</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Orders</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i class="fas fa-receipt fa-fw me-2 text-white-50"></i>All Orders</a></li>
                        </ul>
                    </li>
                    @endif
                @endauth
            </ul>
        </div>

        <!-- REARRANGED ACTION HUB: Language -> Orders -> Cart -> Wishlist -> User Console -->
        <div class="d-flex text-white align-items-center flex-wrap gap-2 gap-md-3">
            
            <!-- [1] Language Switcher Module -->
            <div class="dropdown">
                <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center btn-smooth rounded-pill px-3" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-globe me-2"></i>
                    <span class="d-none d-sm-inline">{{ strtoupper(app()->getLocale()) }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="languageDropdown">
                    <li><a class="dropdown-item d-flex align-items-center {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                        <i class="fas fa-check me-2 {{ app()->getLocale() === 'en' ? 'text-success' : 'text-muted' }}"></i>English
                    </a></li>
                    <li><a class="dropdown-item d-flex align-items-center {{ app()->getLocale() === 'id' ? 'active' : '' }}" href="{{ route('language.switch', 'id') }}">
                        <i class="fas fa-check me-2 {{ app()->getLocale() === 'id' ? 'text-success' : 'text-muted' }}"></i>Bahasa Indonesia
                    </a></li>
                </ul>
            </div>
            
            @guest
                <a href="{{ route('login.show') }}" class="btn btn-light btn-sm px-4 fw-bold rounded-pill shadow-sm btn-smooth" style="color: #c25e25;">{{ __('messages.login') }}</a>
            @else
                @php $cartCount = count(session('cart', [])); @endphp
                
                <!-- [2] Dynamic Checkout History Receipts (Orders Button) -->
                <a href="{{ route('orders.index') }}" class="btn btn-outline-light btn-sm d-flex align-items-center rounded-pill shadow-sm btn-smooth px-3 px-sm-4">
                    <i class="fas fa-receipt me-2"></i>
                    <span class="d-none d-lg-inline">Orders</span>
                </a>
                
                <!-- [3] Active Cart Button Context (Borderless Style with Circular Hover Shadow effect) -->
                <a href="{{ route('cart.index') }}" class="btn btn-link btn-minimal-circle position-relative text-white text-decoration-none">
                    <i class="fas fa-shopping-cart"></i>
                    @if($cartCount > 0)
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" style="font-size: 0.6rem; padding: 0.25em 0.4em;">{{ $cartCount }}</span>
                    @endif
                </a>

                <!-- [4] Standalone Wishlist Button (Borderless Style with Circular Hover Shadow effect) -->
                <a href="{{ route('wishlist.index') }}" class="btn btn-link btn-minimal-circle text-white text-decoration-none" title="View Wishlist">
                    <i class="fas fa-bookmark"></i>
                </a>
                
                <!-- [5] Custom Context Profile Console Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light btn-sm px-3 px-sm-4 fw-bold rounded-pill d-flex align-items-center shadow-sm btn-smooth" type="button" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color: #c25e25;">
                        <i class="fas fa-user-circle me-2 fs-5"></i>
                        <span class="d-none d-lg-inline me-1">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down ms-1" style="font-size: 0.65em;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" aria-labelledby="userProfileDropdown">
                        <li>
                            <a href="#" class="dropdown-item text-danger d-flex align-items-center py-2" id="triggerLogoutModal">
                                <i class="fas fa-sign-out-alt me-2"></i> {{ __('messages.logout') }}
                            </a>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </div>
</nav>

<!-- Clean Intercept Confirmation Modal Logic -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="logoutModalLabel">
                    <i class="fas fa-sign-out-alt text-danger me-2"></i> Confirm Logout
                </h5>
                <button type="button" class="btn-close closeLogoutModal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-dark px-4 py-3">
                <p class="mb-0 text-muted fs-6">Are you sure you want to log out from your account? You will need to log in again to access your session.</p>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-medium rounded-pill px-4 closeLogoutModal">Cancel</button>
                <button type="button" class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm" id="confirmLogoutBtn">Logout</button>
            </div>
        </div>
    </div>
</div>

<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
    /* --- STRUCTURAL NAVBAR BASE POSITIONING --- */
    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    /* Apply transparent blurs ONLY when inside the isolated home class */
    nav.navbar.navbar-home-transparent {
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Active Scroll Blending class style */
    nav.navbar.scrolled {
        background: rgba(148, 67, 22, 0.95) !important; 
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        border-bottom: none !important;
    }

    /* --- EXPANDING UNTERLINE PLATFORM HOVER DESIGN --- */
    .nav-underline-lift {
        position: relative;
    }
    
    .nav-underline-lift::after {
        content: '';
        position: absolute;
        width: 100%;
        transform: scaleX(0);
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: #ffdcb3; /* Elegant matching soft cream platform baseline color */
        transform-origin: bottom center;
        transition: transform 0.25s cubic-bezier(0.1, 0.8, 0.3, 1);
    }

    .nav-underline-lift:hover::after,
    .nav-underline-lift.active::after {
        transform: scaleX(1);
    }

    /* --- BORDERLESS MINIMAL CIRCLE GLOW INTERACTION --- */
    .btn-minimal-circle {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50% !important;
        padding: 0 !important;
        transition: background-color 0.25s ease, box-shadow 0.25s ease, transform 0.2s ease !important;
    }

    .btn-minimal-circle:hover {
        background-color: rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    .btn-minimal-circle:active {
        transform: translateY(0);
    }

    .nav-smooth {
        transition: color 0.25s ease, opacity 0.25s ease !important;
    }
    .nav-smooth:hover {
        opacity: 1 !important;
    }
    .btn-smooth {
        transition: background-color 0.25s ease, color 0.25s ease, transform 0.2s ease, box-shadow 0.25s ease !important;
    }
    .btn-smooth:hover {
        transform: translateY(-1px);
    }
    .btn-smooth:active {
        transform: translateY(0);
    }
    .brand-transition {
        transition: transform 0.25s ease !important;
    }
    .brand-transition:hover {
        transform: scale(1.02);
    }
    .logo-smooth {
        transition: transform 0.3s ease;
    }
    .brand-transition:hover .logo-smooth {
        transform: rotate(3deg);
    }
    .dropdown-item.active, .dropdown-item:active {
        background-color: #c25e25 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Isolated Scroll Controller Engine ---
        const navbar = document.querySelector('nav.navbar');
        if (navbar && navbar.classList.contains('navbar-home-transparent')) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 80) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        }

        // --- Logout Handling Module ---
        const triggerLogout = document.getElementById('triggerLogoutModal');
        const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
        const logoutModalEl = document.getElementById('logoutModal');
        const closeButtons = document.querySelectorAll('.closeLogoutModal');
        
        let bsModal = null;
        if (logoutModalEl && typeof bootstrap !== 'undefined') {
            bsModal = new bootstrap.Modal(logoutModalEl);
        }

        if (triggerLogout) {
            triggerLogout.addEventListener('click', function(e) {
                e.preventDefault();
                if (bsModal) {
                    bsModal.show();
                } else if (logoutModalEl) {
                    logoutModalEl.style.display = 'block';
                    logoutModalEl.classList.add('show');
                    document.body.classList.add('modal-open');
                }
            });
        }
        
        function closeModal() {
            if (bsModal) {
                bsModal.hide();
            } else if (logoutModalEl) {
                logoutModalEl.style.display = 'none';
                logoutModalEl.classList.remove('show');
                document.body.classList.remove('modal-open');
                
                let backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        }
        
        closeButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                closeModal();
            });
        });
        
        if (confirmLogoutBtn) {
            confirmLogoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                confirmLogoutBtn.disabled = true;
                confirmLogoutBtn.textContent = 'Logging out...';
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                
                fetch('{{ route('logout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: '_token=' + encodeURIComponent(csrfToken)
                })
                .then(response => {
                    setTimeout(() => {
                        window.location.href = '{{ route('login.show') }}';
                    }, 300);
                })
                .catch(err => {
                    setTimeout(() => {
                        window.location.href = '{{ route('login.show') }}';
                    }, 300);
                });
            });
        }
    });
</script>
