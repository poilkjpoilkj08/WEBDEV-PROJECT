<nav class="navbar navbar-expand-lg navbar-dark py-3 px-md-4 shadow-sm" style="background: linear-gradient(135deg,#2c3e50,#34495e);">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold fs-4 tracking-tight text-white" href="{{ route('home') }}">
            <i class="fas fa-book me-2"></i>BookHive
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('messages.home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('books.listing') ? 'active' : '' }}" href="{{ route('books.listing') }}">{{ __('messages.books') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('authors.index') ? 'active' : '' }}" href="{{ route('authors.index') }}">{{ __('messages.authors') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('about') ? 'active' : '' }}" href="{{ route('about') }}">{{ __('messages.about') }}</a>
                </li>
                @auth
                    @if(auth()->user()->hasRole(['admin', 'owner']))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('messages.admin_panel') }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="{{ route('books.create-form') }}">{{ __('messages.add_new_book') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('authors.create-form') }}">{{ __('messages.add_new_author') }}</a></li>
                        </ul>
                    </li>
                    @endif
                @endauth
            </ul>
        </div>

        <!-- User Controls (Outside collapsible nav) -->
        <div class="d-flex text-white align-items-center gap-2">
            <!-- Language Selector -->
            <div class="dropdown">
                <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-globe me-1"></i>
                    <span class="d-none d-sm-inline">{{ strtoupper(app()->getLocale()) }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                    <li><a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                        <i class="fas fa-check me-2 {{ app()->getLocale() === 'en' ? 'text-success' : 'text-muted' }}"></i>English
                    </a></li>
                    <li><a class="dropdown-item {{ app()->getLocale() === 'id' ? 'active' : '' }}" href="{{ route('language.switch', 'id') }}">
                        <i class="fas fa-check me-2 {{ app()->getLocale() === 'id' ? 'text-success' : 'text-muted' }}"></i>Bahasa Indonesia
                    </a></li>
                </ul>
            </div>

            <a href="#" class="text-white fs-5" title="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-white fs-5" title="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white fs-5" title="Instagram"><i class="fab fa-instagram"></i></a>
            
            @guest
                <a href="{{ route('login.show') }}" class="btn btn-light btn-sm px-4 fw-bold rounded-pill text-primary shadow-sm">{{ __('messages.login') }}</a>
            @else
                @php $cartCount = count(session('cart', [])); @endphp
                <a href="{{ route('cart.index') }}" class="btn btn-outline-light btn-sm d-flex align-items-center rounded-pill shadow-sm">
                    <i class="fas fa-shopping-cart me-2"></i>
                    <span class="d-none d-lg-inline">Cart</span>
                    @if($cartCount > 0)
                        <span class="badge bg-danger ms-2">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-light btn-sm d-flex align-items-center rounded-pill shadow-sm">
                    <i class="fas fa-receipt me-2"></i>
                    <span class="d-none d-lg-inline">Orders</span>
                </a>
                
                <!-- User Profile Dropdown using Bootstrap's native dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light btn-sm px-3 fw-bold rounded-pill text-primary d-flex align-items-center shadow-sm" type="button" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-2 fs-5"></i>
                        <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down ms-1" style="font-size: 0.65em;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3" aria-labelledby="userProfileDropdown">
                        <li>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center">
                                    <i class="fas fa-sign-out-alt me-2"></i> {{ __('messages.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </div>
</nav>

<script>
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('customUserDropdown');
        const menu = document.getElementById('customUserMenu');
        const logoutBtn = document.getElementById('logoutBtnDropdown');
        
        console.log('Button found:', btn ? 'Yes' : 'No');
        console.log('Menu found:', menu ? 'Yes' : 'No');
        
        if (!btn || !menu) {
            console.error('Could not find button or menu elements');
            return;
        }
        
        // Toggle dropdown on button click
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Button clicked, menu display was:', menu.style.display);
            
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        });
        
        // Handle logout button
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Logout clicked');
                document.getElementById('logout-form').submit();
            });
        }
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (btn && menu && e.target !== btn && !btn.contains(e.target) && e.target !== menu && !menu.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
    });
</script>

<!-- Logout Modal -->
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

<!-- Hidden Logout Form -->
<form id="logoutForm" action="{{ route('logout') }}" method="POST">
    @csrf
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
        const logoutForm = document.getElementById('logoutForm');
        const logoutModal = document.getElementById('logoutModal');
        const closeButtons = document.querySelectorAll('.closeLogoutModal');
        
        // Function to close modal
        function closeModal() {
            if (logoutModal) {
                logoutModal.style.display = 'none';
                logoutModal.classList.remove('show');
                document.body.classList.remove('modal-open');
                
                // Remove backdrop
                let backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            }
        }
        
        // Attach close functionality to all close buttons
        closeButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeModal();
            });
        });
        
        if (confirmLogoutBtn && logoutForm) {
            confirmLogoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Disable the button
                confirmLogoutBtn.disabled = true;
                confirmLogoutBtn.textContent = 'Logging out...';
                
                // Get csrf token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                
                // Send logout via fetch
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
                    console.log('Logout response status:', response.status);
                    // Redirect regardless of response
                    setTimeout(() => {
                        window.location.href = '{{ route('login.show') }}';
                    }, 300);
                })
                .catch(err => {
                    console.error('Logout error:', err);
                    // Still redirect on error
                    setTimeout(() => {
                        window.location.href = '{{ route('login.show') }}';
                    }, 300);
                });
            });
        }
    });
</script>