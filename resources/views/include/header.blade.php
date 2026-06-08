<!-- Base Navigation Wrapper Structure -->
<nav class="navbar navbar-expand-lg navbar-dark py-3 px-3 px-md-4 shadow-sm {{ Route::is('home') ? 'navbar-home-transparent' : '' }}" 
     style="{{ Route::is('home') ? 'background: rgba(43, 24, 12, 0.15) !important;' : 'background: linear-gradient(135deg, #c25e25, #a64f1e) !important;' }} z-index: 1050 !important;">
    <div class="container-fluid bh-nav-flex-container">
        
        <!-- ================= [LEFT HAND SEGMENT: TOGGLER + BRAND] ================= -->
        <div class="bh-nav-left-cluster">
            <!-- Mobile Hamburger Trigger (RESIZED & RESTRICTED: Hidden on desktop 'd-lg-none', visible on mobile 'd-block') -->
            <button class="bh-hamburger-trigger d-lg-none d-block" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Brand Logo Identity -->
            <a class="bh-brand-anchor brand-transition" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="BookHive Logo" class="logo-smooth">
                <span class="bh-brand-label">BookHive</span>
            </a>
        </div>
        
        <!-- ================= [MIDDLE SEGMENT: EXPANDABLE NAVIGATION DRAWER] ================= -->
        <div class="collapse navbar-collapse bh-navigation-drawer" id="navbarNav">
            <!-- Tight vertical spacing utility applied to completely eliminate item gaps -->
            <ul class="navbar-nav mt-3 mt-lg-0 py-0 vertical-gap-tight">
                <li class="nav-item">
                    <a class="nav-link nav-smooth nav-underline-lift {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('messages.home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-smooth nav-underline-lift {{ Route::is('books.listing') ? 'active' : '' }}" href="{{ route('books.listing') }}">{{ __('messages.books') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-smooth nav-underline-lift {{ Route::is('about') ? 'active' : '' }}" href="{{ route('about') }}">{{ __('messages.about') }}</a>
                </li>
                
                {{-- Roulette Link --}}
                @auth
                    @if(!auth()->user()->hasRole(['admin', 'owner']))
                    <li class="nav-item">
                        <a class="nav-link nav-smooth nav-underline-lift {{ Route::is('books.roulette') ? 'active' : '' }}" href="{{ route('books.roulette') }}">Roulette</a>
                    </li>
                    
                    <!-- UNIFIED MOBILE IN-LINE TEXT LIST: Spaced exactly like desktop links, hidden on standard viewports -->
                    <li class="nav-item bh-mobile-drawer-only-link">
                        <a class="nav-link {{ Route::is('orders.index') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                            My Orders
                        </a>
                    </li>
                    <li class="nav-item bh-mobile-drawer-only-link">
                        <a class="nav-link {{ Route::is('wishlist.index') ? 'active' : '' }}" href="{{ route('wishlist.index') }}">
                            My Wishlist
                        </a>
                    </li>
                    <li class="nav-item bh-mobile-drawer-only-link">
                        @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
                        <a class="nav-link {{ Route::is('cart.index') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                            My Cart {!! $cartCount > 0 ? '<span class="badge bg-danger ms-1 px-1.5 rounded-pill" style="font-size:0.7em;">'.$cartCount.'</span>' : '' !!}
                        </a>
                    </li>
                    <li class="nav-item bh-mobile-drawer-only-link">
                        <a class="nav-link text-danger-hover fw-bold" href="#" id="triggerLogoutModalMobile">
                            {{ __('messages.logout') }}
                        </a>
                    </li>
                    @endif
                @endauth

                {{-- Admin Menu Dropdowns --}}
                @auth
                    @if(auth()->user()->hasRole(['admin', 'owner']))
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-smooth dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('messages.admin_panel') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark border-0 shadow-lg animate slideIn" aria-labelledby="adminDropdown" style="background-color: #a64f1e;">
                            <li><h6 class="dropdown-header text-white-50">Books</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.books.index') }}"><i class="fas fa-list fa-fw me-2 text-white-50"></i>Manage Books</a></li>
                            <li><hr class="dropdown-divider border-light opacity-10"></li>
                            <li><h6 class="dropdown-header text-white-50">Authors</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.authors.index') }}"><i class="fas fa-list fa-fw me-2 text-white-50"></i>Manage Authors</a></li>
                            <li><hr class="dropdown-divider border-light opacity-10"></li>
                            <li><h6 class="dropdown-header text-white-50">Publishers</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.publishers.index') }}"><i class="fas fa-building fa-fw me-2 text-white-50"></i>Manage Publishers</a></li>
                            <li><hr class="dropdown-divider border-light opacity-10"></li>
                            <li><h6 class="dropdown-header text-white-50">Stores</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.stores.index') }}"><i class="fas fa-store fa-fw me-2 text-white-50"></i>Manage Store Locations</a></li>
                            <li><hr class="dropdown-divider border-light opacity-10"></li>
                            <li><h6 class="dropdown-header text-white-50">Orders</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i class="fas fa-receipt fa-fw me-2 text-white-50"></i>All Orders</a></li>
                        </ul>
                    </li>
                    <!-- Admin Logout option added directly inside mobile background menu drawer -->
                    <li class="nav-item bh-mobile-drawer-only-link">
                        <a class="nav-link text-danger-hover fw-bold" href="#" id="triggerLogoutModalAdminMobile">
                            {{ __('messages.logout') }}
                        </a>
                    </li>
                    @endif
                @endauth

                {{-- Mobile Login Button (shown only when not authenticated) --}}
                @guest
                <li class="nav-item bh-mobile-drawer-only-link d-lg-none mt-3 pt-2 border-top">
                    <a href="{{ route('login.show') }}" class="btn btn-light btn-sm px-4 fw-bold rounded-pill shadow-sm w-100" style="color: #c25e25;">{{ __('messages.login') }}</a>
                </li>
                @endguest
            </ul>
        </div>

        <!-- ================= [RIGHT HAND SEGMENT: DESKTOP ONLY CONSOLE HUB] ================= -->
        <!-- Locked layout container: Disappears on mobile viewports, stays aligned on normal web views -->
        <div class="bh-nav-right-cluster d-none d-lg-flex">
            
            @auth
                @if(!auth()->user()->hasRole(['admin', 'owner']))
                    @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
                    <a href="{{ route('orders.index') }}" class="bh-desktop-link-node text-white text-decoration-none" title="View Orders"><i class="fas fa-receipt"></i></a>
                    <a href="{{ route('wishlist.index') }}" class="bh-desktop-link-node text-white text-decoration-none" title="View Wishlist"><i class="fas fa-bookmark"></i></a>
                    <a href="{{ route('cart.index') }}" class="bh-cart-trigger text-white text-decoration-none" title="View Cart">
                        <i class="fas fa-shopping-cart"></i>
                        @if($cartCount > 0)
                            <span class="cart-badge-dot-indicator animate-badge">{{ $cartCount }}</span>
                        @endif
                    </a>
                @endif
            @endauth

            @auth
                <div class="dropdown">
                    <button class="btn btn-light bh-profile-trigger-pill fw-bold rounded-pill d-flex align-items-center" type="button" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color: #c25e25 !important; background-color: #ffffff !important;">
                        <i class="fas fa-user-circle fs-5"></i>
                        <span class="ms-1.5 me-0.5 bh-profile-text-limit">{{ explode(' ', trim(auth()->user()->name))[0] }}</span>
                        <i class="fas fa-chevron-down ms-1" style="font-size: 0.6em;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 bh-profile-popup-card" aria-labelledby="userProfileDropdown">
                        <li>
                            <a href="#" class="dropdown-item bh-logout-action-row text-danger d-flex align-items-center" id="triggerLogoutModalDesktop">
                                <i class="fas fa-sign-out-alt me-2 fs-6"></i> 
                                <span class="fw-bold">{{ __('messages.logout') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login.show') }}" class="btn btn-light btn-sm px-4 fw-bold rounded-pill shadow-sm" style="color: #c25e25;">{{ __('messages.login') }}</a>
            @endauth

        </div>

    </div>
</nav>

<!-- Intercept Confirmation Modal Layout -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true" style="z-index: 1150 !important;">
    <div class="modal-dialog modal-sm modal-dialog-centered mx-auto" style="max-width: 380px;">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="logoutModalLabel" style="font-size: 1.15rem;">
                    <i class="fas fa-sign-out-alt text-danger me-2"></i> Confirm Logout
                </h5>
                <button type="button" class="btn-close closeLogoutModal" aria-label="Close" style="box-shadow: none !important;"></button>
            </div>
            <div class="modal-body text-dark px-4 py-3">
                <p class="mb-0 text-muted small" style="line-height: 1.5;">Are you sure you want to log out from your account? You will need to log in again to access your session.</p>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4 d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-light fw-medium rounded-pill px-3.5 btn-sm closeLogoutModal">Cancel</button>
                <button type="button" class="btn btn-danger fw-bold rounded-pill px-3.5 btn-sm shadow-sm" id="confirmLogoutBtn">Logout</button>
            </div>
        </div>
    </div>
</div>

<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
    /* ==========================================================================
       RIGID FIXED STRUCTURAL FLEX LAYOUTS
       ========================================================================== */
    .bh-nav-flex-container {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: nowrap !important;
    }

    nav.navbar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        z-index: 1000 !important;
        transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    nav.navbar.navbar-home-transparent {
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    nav.navbar.scrolled {
        background: rgba(148, 67, 22, 0.95) !important; 
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
    }

    .vertical-gap-tight {
        gap: 2px !important; /* Uniform tight metric grid list alignment */
    }

    .bh-nav-left-cluster {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        flex-shrink: 0 !important;
    }

    .bh-hamburger-trigger {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        padding: 6px !important;
        align-items: center;
        justify-content: center;
    }
    .bh-hamburger-trigger .navbar-toggler-icon {
        width: 1.4rem !important;
        height: 1.4rem !important;
    }

    .bh-brand-anchor {
        display: flex !important;
        align-items: center !important;
        text-decoration: none !important;
    }
    .bh-brand-anchor img {
        height: 38px !important;
        width: auto !important;
        margin-right: 8px !important;
    }
    .bh-brand-label {
        font-size: 1.2rem !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        line-height: 1 !important;
    }

    .bh-nav-right-cluster {
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 10px !important;
        flex-shrink: 0 !important;
        margin-left: auto !important;
    }

    .bh-desktop-link-node {
        width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;
        transition: background-color 0.2s ease;
    }
    .bh-desktop-link-node:hover { background-color: rgba(255,255,255,0.15) !important; }

    .bh-cart-trigger {
        width: 38px !important;
        height: 38px !important;
        background: transparent !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 50% !important;
        font-size: 1.05rem !important;
        position: relative !important;
        transition: background-color 0.2s ease !important;
    }
    .bh-cart-trigger:hover { background-color: rgba(255, 255, 255, 0.15) !important; }

    .cart-badge-dot-indicator {
        position: absolute !important;
        top: 2px !important;
        right: 2px !important;
        background-color: #dc3545 !important;
        color: #ffffff !important;
        font-size: 0.55rem !important;
        font-weight: 700 !important;
        min-width: 14px !important;
        height: 14px !important;
        padding: 0 2px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid #c25e25 !important;
    }

    .bh-profile-trigger-pill {
        padding: 0.35rem 0.85rem !important;
        font-size: 0.9rem !important;
        border: none !important;
        box-shadow: none !important;
    }

    .bh-profile-text-limit {
        max-width: 100px !important;
        display: inline-block !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .bh-profile-popup-card {
        min-width: 150px !important;
        padding: 0 !important;
        background-color: #ffffff !important;
        border: 1px solid #eef0f2 !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
    }

    .bh-logout-action-row {
        padding: 12px 18px !important; 
        color: #dc3545 !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
    }

    /* ==========================================================================
       NAVIGATION DRAWER PORTALS 
       ========================================================================== */
    .bh-navigation-drawer {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        width: 100% !important;
        z-index: 999 !important;
    }

    .bh-navigation-drawer .navbar-nav {
        background: rgba(166, 79, 30, 0.99) !important;
        border-radius: 16px !important;
        padding: 1rem 1.25rem !important;
        margin: 0.75rem 12px 0 12px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    @media (max-width: 991.98px) {
        .bh-mobile-drawer-only-link {
            display: block !important; 
        }
        .navbar-collapse .navbar-nav .nav-link {
            padding: 0.5rem 1rem !important; 
        }
        .text-danger-hover:hover, .text-danger-hover {
            color: #ff9999 !important; 
        }
    }

    @media (min-width: 992px) {
        .bh-mobile-drawer-only-link {
            display: none !important; 
        }
        .bh-navigation-drawer {
            position: static !important;
            width: auto !important;
            display: flex !important;
            flex-basis: auto !important;
            margin-left: 24px !important;
        }
        .bh-navigation-drawer .navbar-nav {
            background: transparent !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: none !important;
            flex-direction: row !important;
            column-gap: 8px !important;
        }
        .navbar-collapse .navbar-nav .nav-link {
            padding: 0.65rem 1rem !important;
        }
    }

    .navbar-collapse .navbar-nav .nav-link {
        border-radius: 8px !important;
        color: rgba(255, 255, 255, 0.9) !important;
    }
    .navbar-collapse .navbar-nav .nav-link:hover,
    .navbar-collapse .navbar-nav .nav-link.active {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
    }

    .navbar-collapse .dropdown-menu {
        background-color: #a64f1e !important;
    }

    @media (min-width: 992px) {
        .nav-underline-lift { position: relative; }
        .nav-underline-lift::after {
            content: ''; position: absolute; width: 100%; transform: scaleX(0); height: 2px;
            bottom: 0; left: 0; background-color: #ffdcb3; transform-origin: bottom center;
            transition: transform 0.25s cubic-bezier(0.1, 0.8, 0.3, 1);
        }
        .nav-underline-lift:hover::after, .nav-underline-lift.active::after { transform: scaleX(1); }
    }

    .nav-smooth { transition: color 0.25s ease, opacity 0.25s ease !important; }
    .brand-transition { transition: transform 0.25s ease !important; }
    .brand-transition:hover { transform: scale(1.01); }
    .logo-smooth { transition: transform 0.3s ease; }
    .brand-transition:hover .logo-smooth { transform: rotate(3deg); }
    .dropdown-item.active, .dropdown-item:active { background-color: #c25e25 !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        // --- Logout Modal Multi-Trigger Routing Bridge ---
        const logoutModalEl = document.getElementById('logoutModal');
        const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
        const closeButtons = document.querySelectorAll('.closeLogoutModal');
        
        let bsModal = null;
        if (logoutModalEl && typeof bootstrap !== 'undefined') {
            bsModal = new bootstrap.Modal(logoutModalEl);
        }

        function displayLogoutPrompt(e) {
            e.preventDefault();
            if (bsModal) {
                bsModal.show();
            } else if (logoutModalEl) {
                logoutModalEl.style.display = 'block';
                logoutModalEl.classList.add('show');
                document.body.classList.add('modal-open');
            }
        }

        ['triggerLogoutModalMobile', 'triggerLogoutModalAdminMobile', 'triggerLogoutModalDesktop'].forEach(id => {
            const node = document.getElementById(id);
            if (node) node.addEventListener('click', displayLogoutPrompt);
        });
        
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