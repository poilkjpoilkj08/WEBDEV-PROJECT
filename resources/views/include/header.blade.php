<nav class="navbar navbar-expand-lg navbar-dark py-3 px-md-4" style="background: linear-gradient(135deg,#00398e,#3578db);">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold fs-4 tracking-tight" href="#">ISB Commerce</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('store') ? 'active' : '' }}" href="{{ route('store') }}">Shop Here</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About Us</a>
                </li>
            </ul>
            <div class="d-flex text-white align-items-center">
                <a href="#" class="text-white me-3 fs-5"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white me-3 fs-5"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white me-3 fs-5"><i class="fab fa-instagram"></i></a>
                @auth
                <a href="{{ route('view_cart') }}" class="text-white me-3 fs-5"><i class="fas fa-shopping-cart"></i></a>
                @endauth
                @guest
                    <a href="{{ route('login.show') }}" class="btn btn-light btn-sm ms-2 px-4 fw-bold rounded-pill text-primary shadow-sm hover-shadow">Login</a>
                @else
                    <div class="position-relative ms-2">
                        <button class="btn btn-light btn-sm px-4 fw-bold rounded-pill text-primary d-flex align-items-center shadow-sm" type="button" id="customUserDropdown">
                            <i class="fas fa-user-circle me-2 fs-5"></i>
                            {{ auth()->user()->name }}
                            <i class="fas fa-chevron-down ms-2 mt-1" style="font-size: 0.65em;"></i>
                        </button>
                        <div class="dropdown-menu shadow-lg border-0 mt-2 position-absolute rounded-3" id="customUserMenu" style="right: 0; left: auto; min-width: 160px; display: none; z-index: 1050;">
                            <button class="w-100 text-start border-0 bg-transparent text-danger py-2 px-3 fw-medium d-flex align-items-center" type="button" id="logoutBtnDropdown" style="cursor: pointer; font-size: 0.95rem;">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('customUserDropdown');
        var menu = document.getElementById('customUserMenu');
        var logoutBtn = document.getElementById('logoutBtnDropdown');
        
        if (btn && menu) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (menu.style.display === 'none' || menu.style.display === '') {
                    menu.style.display = 'block';
                    menu.classList.add('show');
                } else {
                    menu.style.display = 'none';
                    menu.classList.remove('show');
                }
            });
            
            document.addEventListener('click', function(e) {
                // Only close menu if clicking outside the dropdown and logout button
                if (e.target !== btn && !btn.contains(e.target) && 
                    e.target !== menu && !menu.contains(e.target) &&
                    e.target !== logoutBtn && (logoutBtn && !logoutBtn.contains(e.target))) {
                    menu.style.display = 'none';
                    menu.classList.remove('show');
                }
            });
            
            menu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Logout button handler
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                menu.style.display = 'none';
                
                // Show modal using simple DOM manipulation
                const logoutModal = document.getElementById('logoutModal');
                if (logoutModal) {
                    // Add show class and set display
                    logoutModal.style.display = 'block';
                    logoutModal.classList.add('show');
                    // Add backdrop
                    let backdrop = document.querySelector('.modal-backdrop');
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                    }
                    document.body.classList.add('modal-open');
                }
            });
        }
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