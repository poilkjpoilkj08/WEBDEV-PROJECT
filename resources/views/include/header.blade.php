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
                            <li><h6 class="dropdown-header">Books</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.books.index') }}"><i class="fas fa-list fa-fw me-2 text-muted"></i>Manage Books</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Authors</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.authors.index') }}"><i class="fas fa-list fa-fw me-2 text-muted"></i>Manage Authors</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Stores</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.stores.index') }}"><i class="fas fa-store fa-fw me-2 text-muted"></i>Manage Store Locations</a></li>
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
        // Language functionality handled by base.blade.php
    });
</script>


