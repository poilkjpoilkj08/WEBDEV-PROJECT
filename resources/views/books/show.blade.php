@extends('base.base')
@section('content')

<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-color: #ffffff; /* Sets everything below the hero fade region to pure white */
        min-height: 100vh;
        padding-top: 0 !important; /* Let hero breakout wrapper stretch seamlessly to the top */
        margin-top: 0 !important;
        overflow-x: hidden; /* Prevents any accidental horizontal scroll layout shifts */
    }

    /* Fixed Header Logic Compatibility */
    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }

    /* --- BREAKOUT HERO WRAPPER BLOCK --- */
    /* Forces alignment over the top and side constraints of parent containers safely */
    .hero-bg-wrapper {
        position: relative;
        margin-top: -100px !important; /* Pulls the block all the way up into structural gaps behind the navbar */
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
        width: 100vw;
        
        /* Direct asset reference ensuring it strictly maps to background2.jpg with a cache-buster timestamp */
        background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.4)), url("{{ asset('images/background2.jpg') }}?v={{ time() }}");
        background-repeat: no-repeat;
        background-position: center center; 
        background-size: cover; 
        padding-top: 220px; /* Enhanced top padding to offset the shift and pad text safely below the navbar */
        padding-bottom: 80px; /* Extra breathing space for elements inside the hero frame context */
        z-index: 1;
    }

    /* --- DIALED DOWN BOTTOM FADE EFFECT --- */
    /* Made shorter and pushed lower so it doesn't swallow up the content rows */
    .hero-bottom-fade {
        position: relative;
        width: 100vw;
        left: 50%;
        transform: translateX(-50%);
        height: 100px; /* Enhanced to smoothly fade back into the white footer layout modules */
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, #ffffff 100%);
        margin-top: -100px; /* Aligns it right over the baseline of the wrapper block */
        z-index: 3;
        pointer-events: none;
    }

    /* GLASS BOX FOR HEADERS */
    .glass-header-box {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 10px 28px;
        border-radius: 50px;
        display: inline-block;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    /* GLASS CARD FOR DETAILS BLOCK OVER THE HERO SPLIT */
    .glass-details-card {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
    }

    /* Remove core white background wrapper to let background image shine */
    .content-wrapper {
        background-color: transparent !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
    }

    /* Raw showcase image styling layout rules */
    .raw-book-image {
        height: 440px; 
        object-fit: contain; 
        transition: transform 0.3s ease, filter 0.3s ease;
    }
    
    .raw-book-image:hover {
        transform: scale(1.02);
    }

    /* Card lift hover animations */
    .hover-lift:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.1);
    }
    
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* Unified Muted Soft Orange Buttons global style helper */
    .btn-soft-orange {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
        transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }
    
    .btn-soft-orange:hover, .btn-soft-orange:focus {
        background-color: #a64f1e !important;
        border-color: #a64f1e !important;
        color: #ffffff !important;
    }

    .btn-outline-soft-orange {
        background-color: transparent !important;
        border: 2px solid #c25e25 !important;
        color: #c25e25 !important;
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .btn-outline-soft-orange:hover, .btn-outline-soft-orange.active {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
    }

    .max-width-fit {
        width: fit-content;
    }
    
    .max-width-md-600 {
        max-width: 600px;
    }
</style>

<!-- Top Hero Background Breakout Context Block -->
<div class="hero-bg-wrapper">
    <div class="container content-wrapper">
        <!-- Top Action Header -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-5 gap-3">
            <div>
                <div class="glass-header-box">
                    <h1 class="h4 mb-0 fw-bold text-white">Book Showcase</h1>
                </div>
            </div>
            <a href="{{ route('books.listing') }}" class="btn btn-light btn-sm fw-bold px-4 rounded-pill shadow-sm btn-smooth" style="color: #c25e25;">
                <i class="fas fa-arrow-left me-2"></i>Back to Catalog
            </a>
        </div>

        <!-- Main Showcase Split Layout - Shifted inside Hero to completely float over the background art -->
        <div class="row g-5 align-items-center position-relative" style="z-index: 5;">
            <!-- Visual Column Cover Artwork -->
            <div class="col-md-5 text-center text-md-start">
                @if($book->cover_image_url)
                    <img src="{{ $book->cover_image_src }}" class="raw-book-image img-fluid rounded-3" alt="{{ $book->title }}" />
                @else
                    <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded-3 mx-auto mx-md-0 shadow-sm" style="width: 280px; height: 420px; border: 1px solid rgba(255,255,255,0.2);">
                        <div class="text-center">
                            <i class="fas fa-book fa-3x opacity-20 mb-2" style="color: #c25e25;"></i><br>
                            <span class="fw-medium text-secondary small">No Cover Available</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Metric Details Selection Block -->
            <div class="col-md-7">
                <div class="card border-0 glass-details-card rounded-4 p-4">
                    <div class="card-body p-0">
                        
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1 text-uppercase font-monospace small shadow-sm" style="font-size: 0.7rem;">
                                {{ ucfirst($book->status) }}
                            </span>
                            <small class="text-muted fw-medium font-monospace small">{{ $book->isbn ?: 'ISBN Not available' }}</small>
                        </div>
                        
                        <h2 class="h3 fw-bold text-dark mb-2 lh-sm">{{ $book->title }}</h2>
                        <p class="lead text-muted mb-4" style="font-size: 1.05rem;">Written by <span class="fw-bold" style="color: #c25e25;">{{ $book->author ? $book->author->name : 'Unknown Author' }}</span></p>
                        
                        <div class="border-top border-bottom py-3 mb-4 d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted d-block text-uppercase small tracking-wider fw-bold mb-1" style="font-size: 0.7rem;">Price (IDR)</small>
                                <span class="h3 fw-bold text-dark mb-0">Rp {{ number_format($book->price, 0, ',', '.') }}</span>
                            </div>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small" style="font-size: 0.75rem;"><i class="fas fa-bookmark me-2" style="color: #c25e25;"></i>Shelf: {{ $book->category->name }}</span>
                        </div>

                        <!-- Main Core Stats Row Grid -->
                        <div class="row g-3 mb-3">
                            <div class="col-6 col-sm-3">
                                <div class="bg-light rounded-3 p-3 text-center border-0">
                                    <div class="fw-bold text-dark mb-1 small"><i class="fas fa-file-alt text-muted me-1 small"></i>{{ $book->pages ?: 'N/A' }}</div>
                                    <small class="text-muted small" style="font-size: 0.7rem;">Pages</small>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="bg-light rounded-3 p-3 text-center border-0">
                                    <div class="fw-bold text-dark mb-1 small"><i class="fas fa-globe text-muted me-1 small"></i>{{ $book->language }}</div>
                                    <small class="text-muted small" style="font-size: 0.7rem;">Language</small>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="bg-light rounded-3 p-3 text-center border-0">
                                    <div class="fw-bold text-dark mb-1 small"><i class="fas fa-calendar-check text-muted me-1 small"></i>{{ $book->publication_year ?: 'N/A' }}</div>
                                    <small class="text-muted small" style="font-size: 0.7rem;">Year</small>
                                </div>
                            </div>
                            @if($book->weight_grams)
                            <div class="col-6 col-sm-3">
                                <div class="bg-light rounded-3 p-3 text-center border-0">
                                    <div class="fw-bold text-dark mb-1 small"><i class="fas fa-weight text-muted me-1 small"></i>{{ number_format($book->weight_grams) }}g</div>
                                    <small class="text-muted small" style="font-size: 0.7rem;">Weight</small>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Refactored Soft-Edged Publisher Extra Info Bar -->
                        <div class="bg-light rounded-3 p-3 mb-4 border-0 text-secondary" style="font-size: 0.8rem;">
                            <div class="row g-2">
                                <div class="col-sm-6 d-flex align-items-center">
                                    <span class="fw-bold text-dark me-2" style="min-width: 80px;"><i class="fas fa-building text-muted me-2"></i>Publisher</span>
                                    <span class="text-truncate">: {{ $book->publisher ?: 'Unknown' }}</span>
                                </div>
                                @if($book->cover_type)
                                <div class="col-sm-6 d-flex align-items-center">
                                    <span class="fw-bold text-dark me-2" style="min-width: 80px;"><i class="fas fa-book-open text-muted me-2"></i>Edition</span>
                                    <span class="text-uppercase">: {{ $book->cover_type }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="row g-2 pt-2">
                            <div class="col-sm-6">
                                @auth
                                    <form id="addToCartForm" class="add-to-cart-form">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-soft-orange w-100 fw-bold rounded-3 py-2 text-uppercase" style="font-size: 0.85rem;">
                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login.show') }}" class="btn btn-soft-orange w-100 fw-bold rounded-3 py-2 text-uppercase d-block text-center text-decoration-none" style="font-size: 0.85rem;">
                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                    </a>
                                @endauth
                            </div>
                            <div class="col-sm-6">
                                @auth
                                    <form id="wishlistForm" class="wishlist-form">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <button type="submit" class="btn btn-outline-soft-orange w-100 fw-bold rounded-3 py-2 text-uppercase text-center" id="wishlistBtn" style="font-size: 0.85rem;">
                                            <i class="fas fa-bookmark me-2"></i><span id="wishlistText">Add to Wishlist</span>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login.show') }}" class="btn btn-outline-soft-orange w-100 fw-bold rounded-3 py-2 text-uppercase text-center text-decoration-none d-block" style="font-size: 0.85rem;">
                                        <i class="fas fa-bookmark me-2"></i>Add to Wishlist
                                    </a>
                                @endauth
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visible Fade Element Bridge -->
<div class="hero-bottom-fade"></div>

<!-- Main Content White Container Body -->
<div class="bg-white" style="position: relative; z-index: 4;">
    <div class="container py-5">

        <!-- Description & Author Grid Splits -->
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 bg-light p-4 h-100" style="border: 1px solid #eef0f2 !important;">
                    <div class="card-body p-1">
                        <h3 class="h6 fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-align-left me-2" style="color: #c25e25;"></i>Synopsis & Description</h3>
                        <p class="text-secondary lh-lg mb-4" style="font-size: 0.85rem;">{{ $book->description ?: 'No description available.' }}</p>
                        
                        @if($book->genres && is_array($book->genres) && count($book->genres) > 0)
                        <h4 class="h6 fw-bold text-dark mb-3" style="font-size: 0.85rem;">Associated Tags</h4>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($book->genres as $genre)
                            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill font-medium small" style="border-color: #eef0f2 !important;"><i class="fas fa-tag me-2" style="color: #c25e25;"></i>{{ $genre }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 rounded-4 bg-light p-4 h-100 d-flex flex-column justify-content-between" style="border: 1px solid #eef0f2 !important;">
                    <div class="card-body p-1">
                        <h3 class="h6 fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-feather-alt me-2" style="color: #c25e25;"></i>About the Author</h3>
                        @if($book->author)
                            <h4 class="h6 fw-bold text-dark mb-2" style="font-size: 0.9rem;">{{ $book->author->name }}</h4>
                            @if($book->author->bio)
                            <p class="text-secondary small lh-lg mb-0" style="font-size: 0.8rem;">{{ Str::limit($book->author->bio, 130) }}</p>
                            @endif
                        @else
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Unknown Author profile mapping catalog.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Injection Area -->
        <div class="mb-5 rounded-4 overflow-hidden bg-light p-2" style="border: 1px solid #eef0f2;">
            @include('books.partials.store-map', ['book' => $book])
        </div>

        <!-- Similar Books Section -->
        @if($similar_books->count() > 0)
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="h5 mb-0 fw-bold text-dark">You Might Also Like</h3>
                </div>
                <a href="{{ route('books.listing') }}" class="btn btn-sm px-3 rounded-pill text-white text-decoration-none btn-soft-orange" style="font-size: 0.75rem;">View All</a>
            </div>
            <div class="row g-4">
                @foreach($similar_books as $similar)
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 border-0 overflow-hidden book-ui-card">
                        @if($similar->cover_image_url)
                            <div style="padding: 12px 12px 0 12px;">
                                <img src="{{ $similar->cover_image_src }}" class="card-img-top rounded-3" alt="{{ $similar->title }}" style="height: 200px; object-fit: cover;" />
                            </div>
                        @else
                            <div class="padding: 12px 12px 0 12px;">
                                <div class="card-img-top bg-light text-muted d-flex align-items-center justify-content-center rounded-3" style="height: 200px;">
                                    <i class="fas fa-image opacity-20 fa-2x"></i>
                                </div>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column p-4 pt-3">
                            <h4 class="h6 fw-bold text-dark mb-1 text-truncate" style="font-size: 0.9rem;">{{ $similar->title }}</h4>
                            <p class="text-muted small mb-3">by {{ $similar->author ? $similar->author->name : 'Unknown Author' }}</p>
                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark small">Rp {{ number_format($similar->price, 0, ',', '.') }}</span>
                                <a href="{{ route('books.show', $similar->id) }}" class="btn btn-outline-soft-orange btn-sm rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Carousel Book Navigation Sliders -->
        <div class="d-flex justify-content-center gap-3 flex-wrap mb-5 bg-light py-2 px-4 rounded-pill d-inline-flex mx-auto max-width-fit" style="border: 1px solid #eef0f2;">
            @if($previous_book)
                <a href="{{ route('books.show', $previous_book->id) }}" class="btn btn-sm btn-link text-dark text-decoration-none fw-bold" style="font-size: 0.8rem;">
                    <i class="fas fa-chevron-left me-2" style="color: #c25e25;"></i>Previous Book
                </a>
            @else
                <button class="btn btn-sm btn-link text-muted text-decoration-none" disabled style="font-size: 0.8rem;">
                    <i class="fas fa-chevron-left me-2"></i>Previous Book
                </button>
            @endif

            @if($next_book)
                <a href="{{ route('books.show', $next_book->id) }}" class="btn btn-sm btn-link text-dark text-decoration-none fw-bold" style="font-size: 0.8rem;">
                    Next Book<i class="fas fa-chevron-right ms-2" style="color: #c25e25;"></i>
                </a>
            @else
                <button class="btn btn-sm btn-link text-muted text-decoration-none" disabled style="font-size: 0.8rem;">
                    Next Book<i class="fas fa-chevron-right ms-2"></i>
                </button>
            @endif
        </div>

        <!-- Management Console Dashboard Panels -->
        @auth
            @if(auth()->user()->hasRole(['admin', 'owner']))
                <div class="card border-0 bg-light rounded-4 p-4 text-center mt-4 max-width-md-600 mx-auto" style="border: 1px solid #eef0f2 !important;">
                    <h4 class="h6 text-muted text-uppercase tracking-wider fw-bold mb-3" style="font-size: 0.75rem;">Management Console</h4>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="{{ route('books.edit-form', $book->id) }}" class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm" style="font-size: 0.8rem;">
                            <i class="fas fa-edit me-2"></i>Edit Book
                        </a>
                        <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this catalog record?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm fw-bold px-4 rounded-pill shadow-sm" style="font-size: 0.8rem;">
                                <i class="fas fa-trash-alt me-2"></i>Delete Book
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @endauth
        
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Standard Navbar Scroll Observer Integration ---
        const globalNavbar = document.querySelector('nav.navbar');
        if (globalNavbar) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    globalNavbar.style.setProperty('background', 'rgba(148, 67, 22, 0.95)', 'important');
                } else {
                    globalNavbar.style.setProperty('background', 'rgba(43, 24, 12, 0.15)', 'important');
                }
            });
        }

        // --- Wishlist Handler Channel Engine ---
        @auth
        fetch('{{ route("wishlist.check", $book->id) }}')
            .then(res => res.json())
            .then(data => {
                if (data.inWishlist) {
                    updateWishlistButton(true);
                }
            })
            .catch(err => console.error('Error checking wishlist:', err));
        @endauth

        document.getElementById('wishlistForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const isInWishlist = document.getElementById('wishlistBtn').classList.contains('active');
                const route = isInWishlist ? '{{ route("wishlist.remove") }}' : '{{ route("wishlist.add") }}';
                
                const response = await fetch(route, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: { 'Accept': 'application/json' }
                });
                
                const data = await response.json();
                if (data.success) {
                    updateWishlistButton(!isInWishlist);
                    showToast(data.message, 'success', 3000);
                } else {
                    showToast(data.message || 'Error updating wishlist', 'error', 3000);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Error updating wishlist', 'error', 3000);
            }
        });

        function updateWishlistButton(inWishlist) {
            const btn = document.getElementById('wishlistBtn');
            const text = document.getElementById('wishlistText');
            if (!btn) return;
            if (inWishlist) {
                btn.classList.add('active');
                btn.classList.remove('btn-outline-soft-orange');
                btn.classList.add('btn-soft-orange');
                if(text) text.textContent = 'Remove from Wishlist';
            } else {
                btn.classList.remove('active');
                btn.classList.add('btn-outline-soft-orange');
                btn.classList.remove('btn-soft-orange');
                if(text) text.textContent = 'Add to Wishlist';
            }
        }

        // --- Add To Cart Handler Channel Engine ---
        document.getElementById('addToCartForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    body: new FormData(this),
                    headers: { 'Accept': 'application/json' }
                });
                
                const data = await response.json();
                if (data.success) {
                    showToast('✓ Added "{{ $book->title }}" to cart!', 'success', 5000);
                    this.reset();
                    updateCartBadge(data.cartCount);
                } else {
                    showToast('✗ ' + (data.message || 'Error adding to cart'), 'error', 5000);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('✓ Added "{{ $book->title }}" to cart!', 'success', 5000);
                this.reset();
                setTimeout(() => location.reload(), 1500);
            }
        });

        function updateCartBadge(count) {
            const cartLink = document.querySelector('a[href*="cart"]');
            if (!cartLink) return;
            
            let badge = cartLink.querySelector('.badge');
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill';
                badge.style.fontSize = '0.6rem';
                badge.style.padding = '0.25em 0.4em';
                cartLink.appendChild(badge);
            }
            badge.textContent = count;
        }
    });
</script>
@endsection