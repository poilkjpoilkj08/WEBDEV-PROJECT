@extends('base.base')
@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    /* Outer layout styling */
    body {
        background-color: #ffffff; /* Sets everything below the hero to pure white */
        min-height: 100vh;
        padding-top: 0 !important; /* Let hero stretch to the top */
        margin-top: 0 !important;
        overflow-x: hidden; /* Prevents any accidental horizontal scroll layout shifts */
    }

    /* --- HERO WRAPPER BLOCK --- */
    /* Forces alignment over the top and side constraints of parent containers safely */
    .hero-bg-wrapper {
        position: relative;
        margin-top: -100px; /* Increased negative margin to aggressively pull the background asset to the absolute top of the viewport */
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
        width: 100vw;
        
        /* Direct asset reference ensuring it strictly maps to background1.jpg */
        background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url("{{ asset('images/background1.jpg') }}");
        background-repeat: no-repeat;
        background-position: center center; 
        background-size: cover; 
        padding-top: 200px; /* Increased top padding to cleanly offset the deeper negative margin and keep hero elements perfectly balanced below the navigation block */
        padding-bottom: 50px; 
    }

    /* --- DIALED DOWN BOTTOM FADE EFFECT --- */
    /* Made shorter and pushed lower so it doesn't swallow up the counter rows */
    .hero-bottom-fade {
        position: relative;
        width: 100vw;
        left: 50%;
        transform: translateX(-50%);
        height: 60px; /* Reduced from 100px to keep it short and localized */
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, #ffffff 100%);
        margin-top: -60px; /* Perfectly aligns it right over the baseline of the wrapper */
        z-index: 3;
        pointer-events: none;
    }

    /* --- IMAGE-MATCHED MAIN SEARCH CONTAINER --- */
    .custom-hero-box {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
    }

    /* --- IMAGE-MATCHED SLIDER ELEMENT DESIGN --- */
    .book-slider-container {
        position: relative;
        overflow: hidden;
    }

    .book-slider-track {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        -ms-overflow-style: none;
        padding: 15px 5px;
    }

    .book-slider-track::-webkit-scrollbar { 
        display: none; 
    }

    .book-card-item { 
        flex: 0 0 200px; 
        max-width: 200px; 
    }

    /* UI Recreation: White Rounded Book Container */
    .book-ui-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #eef0f2;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .book-ui-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    .book-ui-img-frame {
        width: 100%;
        height: 220px;
        border-radius: 12px;
        overflow: hidden;
        background-color: #f8f9fa;
        position: relative;
    }

    .book-ui-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .slider-nav-btn {
        width: 36px; 
        height: 36px;
        display: flex; 
        align-items: center; 
        justify-content: center;
        background: #ffffff; 
        border: 1px solid #e2e8f0;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .slider-nav-btn:hover {
        background: #f8f9fa;
        color: #c25e25; /* Match hover states to soft orange */
    }

    /* Unified Muted Soft Orange Buttons global style helper */
    .btn-soft-orange {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    
    .btn-soft-orange:hover, .btn-soft-orange:focus {
        background-color: #a64f1e !important;
        border-color: #a64f1e !important;
    }

    /* ===== RESPONSIVE STYLES FOR HOME PAGE ===== */
    @media (max-width: 768px) {
        /* Hero section adjustments */
        .hero-bg-wrapper {
            padding-top: 120px;
            padding-bottom: 40px;
            margin-top: -80px;
        }

        /* Hero content better sizing */
        .hero-content h1 {
            font-size: 2rem;
        }

        .hero-content p {
            font-size: 1rem;
        }

        /* Search bar adjustments */
        .search-bar {
            max-width: 100%;
            padding: 0.75rem;
        }

        .search-bar input {
            font-size: 16px; /* Prevent iOS zoom */
        }

        /* Button sizing */
        .btn {
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
        }

        /* Counter/stats cards better on tablet */
        .counter-card {
            padding: 1.5rem 1rem;
        }

        .counter-card h3 {
            font-size: 1.75rem;
        }

        .counter-card p {
            font-size: 0.9rem;
        }

        /* Feature grid adjustments */
        .feature-grid {
            gap: 1.5rem;
        }

        /* Featured books carousel adjustments */
        .carousel-item img {
            max-height: 300px;
            object-fit: cover;
        }

        /* Testimonials better on tablet */
        .testimonial-card {
            padding: 1.5rem;
        }

        .testimonial-card p {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 576px) {
        /* Extra small screens */
        .hero-bg-wrapper {
            padding-top: 100px;
            padding-bottom: 30px;
            margin-top: -70px;
        }

        /* Hero heading sizing */
        .hero-content h1 {
            font-size: 1.5rem;
        }

        .hero-content p {
            font-size: 0.95rem;
        }

        /* Container better spacing */
        .container {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }

        /* Search bar full width */
        .search-bar {
            width: 100%;
            padding: 0.5rem;
        }

        .search-bar input {
            font-size: 16px;
            padding: 0.75rem;
        }

        .search-bar button {
            padding: 0.75rem 1rem;
        }

        /* Button sizing */
        .btn-lg {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        /* Counter cards stack and resize */
        .row.g-3 > [class*='col-'] {
            margin-bottom: 0.5rem;
        }

        .counter-card {
            padding: 1rem;
            text-align: center;
        }

        .counter-card h3 {
            font-size: 1.5rem;
        }

        .counter-card p {
            font-size: 0.85rem;
        }

        /* Section headings */
        h2, .h2 {
            font-size: 1.5rem !important;
        }

        h3, .h3 {
            font-size: 1.25rem !important;
        }

        /* Carousel sizing */
        .carousel-inner {
            max-height: 250px;
        }

        .carousel-item img {
            max-height: 250px;
            object-fit: cover;
        }

        /* Featured books fit better */
        .book-preview {
            max-width: 100%;
        }

        .book-preview img {
            max-height: 200px;
        }

        /* Testimonials mobile friendly */
        .testimonial-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .testimonial-card p {
            font-size: 0.9rem;
        }

        .testimonial-card .rating {
            font-size: 1rem;
        }

        /* CTA buttons stack vertically */
        .cta-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .cta-buttons .btn {
            width: 100%;
        }

        /* Icon sizing */
        .fa-3x {
            font-size: 2rem !important;
        }

        .fa-2x {
            font-size: 1.5rem !important;
        }

        /* Text alignment and wrapping */
        .text-center {
            word-break: break-word;
        }

        /* Prevent horizontal overflow */
        body {
            overflow-x: hidden;
        }

        /* Alert and info boxes */
        .alert {
            padding: 0.75rem;
            font-size: 0.9rem;
        }

        /* Badge sizing */
        .badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.6rem;
        }

        /* List items better spacing */
        .list-unstyled li {
            margin-bottom: 0.5rem;
        }
    }
</style>

<div class="hero-bg-wrapper">
    <div class="container">
        <div class="custom-hero-box text-white p-5 mb-5 shadow-lg">
            <div class="row align-items-center">
                <div class="col-md-7 mb-4 mb-md-0">
                    <h1 class="display-6 fw-bold mb-3">
                        @auth Welcome back, {{ Auth::user()->name }}! @else Welcome to BookHive @endauth
                    </h1>
                    <p class="lead mb-4 opacity-90" id="soothing-quote" style="min-height: 1.5em; transition: opacity 0.5s ease-in-out; font-size: 1.1rem;">
                        "Welcome soothing quote. rotating soothing quote, living more over the world."
                    </p>
                    <div class="d-flex gap-2 opacity-75">
                        <i class="fas fa-check-circle text-warning"></i>
                        <span class="small">{{ __('messages.books') }}</span>
                        <i class="fas fa-check-circle text-warning ms-3"></i>
                        <span class="small">{{ __('messages.authors') }}</span>
                        <i class="fas fa-check-circle text-warning ms-3"></i>
                        <span class="small">{{ __('messages.price') }}</span>
                    </div>
                </div>
                
                <div class="col-md-5">
                    <div class="card bg-white bg-opacity-95 shadow-lg border-0">
                        <div class="card-body p-4">
                            <h5 class="card-title text-dark mb-3 small fw-bold text-uppercase">{{ __('messages.search_books') }}</h5>
                            <form action="{{ route('books.search') }}" method="GET">
                                <div class="mb-3">
                                    <input type="text" name="title" value="{{ request('title') }}" placeholder="{{ __('messages.book_title') }}..." class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="author" value="{{ request('author') }}" placeholder="{{ __('messages.author') }}..." class="form-control" />
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ __('messages.min_price') }}" class="form-control" />
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ __('messages.max_price') }}" class="form-control" />
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-soft-orange w-100 fw-bold">
                                    <i class="fas fa-search me-2"></i>{{ __('messages.search') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm text-white" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);">
                    <div class="card-body text-center py-3">
                        <h3 class="h4 mb-0 fw-bold">{{ $total_books }}</h3>
                        <p class="small mb-0 opacity-75">{{ __('messages.total_books') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm text-white" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);">
                    <div class="card-body text-center py-3">
                        <h3 class="h4 mb-0 fw-bold">{{ $authors_count ?? 4 }}</h3>
                        <p class="small mb-0 opacity-75">Expert Authors</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm text-white" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);">
                    <div class="card-body text-center py-3">
                        <h3 class="h4 mb-0 fw-bold">{{ $categories_count ?? 8 }}</h3>
                        <p class="small mb-0 opacity-75">Book Categories</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visible Fade Element Bridge -->
<div class="hero-bottom-fade"></div>

<div class="bg-white py-5">
    <div class="container">
        
        <section class="mb-5 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1 fw-bold text-dark">Featured Books</h2>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button class="slider-nav-btn" id="prevBtn">
                        <i class="fas fa-chevron-left small"></i>
                    </button>
                    <button class="slider-nav-btn" id="nextBtn">
                        <i class="fas fa-chevron-right small"></i>
                    </button>
                </div>
            </div>

            <div class="book-slider-container">
                <div class="book-slider-track" id="bookSlider">
                    @forelse($featured_books as $book)
                    <div class="book-card-item">
                        <div class="book-ui-card">
                            <div class="book-ui-img-frame">
                                @if($book->cover_image_url)
                                    <img src="{{ $book->cover_image_src }}" class="book-ui-img" alt="{{ $book->title }}">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted">
                                        <i class="fas fa-book fa-2x opacity-20"></i>
                                    </div>
                                @endif
                                <div class="position-absolute top-0 start-0 m-2">
                                    @if($book->status === 'out_of_stock')
                                        <span class="badge bg-danger text-white px-2 py-1 small shadow-sm" style="font-size: 0.65rem;">Out of Stock</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-2 py-1 small shadow-sm" style="font-size: 0.65rem;">Featured</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="pt-3">
                                <h4 class="h6 fw-bold text-dark mb-1 text-truncate" style="font-size: 0.9rem;">{{ $book->title }}</h4>
                                <p class="text-muted mb-2 text-truncate" style="font-size: 0.75rem;">By {{ $book->author ? $book->author->name : 'Unknown Author' }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center pt-1">
                                    @if($book->status === 'out_of_stock')
                                        <span class="fw-bold text-danger" style="font-size: 0.85rem;">Out of Stock</span>
                                    @else
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">Rp {{ number_format($book->price, 0, ',', '.') }}</span>
                                    @endif
                                    <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm px-2 py-1 btn-soft-orange fw-bold" style="font-size: 0.7rem; border-radius: 6px;">
                                        Review
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="w-100 py-3 text-center">
                        <p class="text-muted small">No featured books available at the moment.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>

        @foreach($book_categories as $index => $category)
        <section class="mb-5 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="h5 mb-0 fw-bold text-dark">{{ $category->name }}</h3>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button class="slider-nav-btn dynamic-prev" data-target="shelf-{{ $index }}">
                        <i class="fas fa-chevron-left small"></i>
                    </button>
                    <button class="slider-nav-btn dynamic-next" data-target="shelf-{{ $index }}">
                        <i class="fas fa-chevron-right small"></i>
                    </button>
                </div>
            </div>

            <div class="book-slider-container">
                <div class="book-slider-track" id="shelf-{{ $index }}">
                    @forelse($category->books as $cBook)
                    <div class="book-card-item">
                        <div class="book-ui-card">
                            <div class="book-ui-img-frame">
                                @if($cBook->cover_image_url)
                                    <img src="{{ $cBook->cover_image_src }}" class="book-ui-img" alt="{{ $cBook->title }}">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted">
                                        <i class="fas fa-book fa-2x opacity-20"></i>
                                    </div>
                                @endif
                                @if($cBook->status === 'out_of_stock')
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-danger text-white px-2 py-1 small shadow-sm" style="font-size: 0.65rem;">Out of Stock</span>
                                </div>
                                @endif
                            </div>
                            
                            <div class="pt-3">
                                <h4 class="h6 fw-bold text-dark mb-1 text-truncate" style="font-size: 0.9rem;">{{ $cBook->title }}</h4>
                                <p class="text-muted mb-2 text-truncate" style="font-size: 0.75rem;">By {{ $cBook->author ? $cBook->author->name : 'Unknown Author' }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center pt-1">
                                    @if($cBook->status === 'out_of_stock')
                                        <span class="fw-bold text-danger" style="font-size: 0.85rem;">Out of Stock</span>
                                    @else
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">Rp {{ number_format($cBook->price, 0, ',', '.') }}</span>
                                    @endif
                                    <a href="{{ route('books.show', $cBook->id) }}" class="btn btn-sm px-2 py-1 btn-soft-orange fw-bold" style="font-size: 0.7rem; border-radius: 6px;">
                                        Review
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-2 ps-1">
                        <span class="text-muted small">No books listed on this shelf yet.</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>
        @endforeach

        <div class="text-center pt-2">
            <a href="{{ route('books.listing') }}" class="btn px-4 py-2 fw-bold btn-soft-orange shadow-sm" style="border-radius: 25px; font-size: 0.9rem;">
                View All Books
            </a>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Standard Home Page Navbar Scroll Observer ---
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

        // --- Featured Slider Navigation Engine ---
        const bookSlider = document.getElementById('bookSlider');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');

        if(bookSlider && nextBtn && prevBtn) {
            nextBtn.onclick = () => bookSlider.scrollBy({ left: 220, behavior: 'smooth' });
            prevBtn.onclick = () => bookSlider.scrollBy({ left: -220, behavior: 'smooth' });
        }

        // --- Category Shelves Navigation Engine ---
        document.querySelectorAll('.dynamic-next').forEach(btn => {
            btn.onclick = function() {
                const shelfId = this.getAttribute('data-target');
                const targetShelf = document.getElementById(shelfId);
                if(targetShelf) targetShelf.scrollBy({ left: 220, behavior: 'smooth' });
            };
        });

        document.querySelectorAll('.dynamic-prev').forEach(btn => {
            btn.onclick = function() {
                const shelfId = this.getAttribute('data-target');
                const targetShelf = document.getElementById(shelfId);
                if(targetShelf) targetShelf.scrollBy({ left: -220, behavior: 'smooth' });
            };
        });

        // --- Rotating Soothing Quotes Setup ---
        const quotes = [
            "\"Welcome soothing quote. rotating soothing quote, living more over the world.\"",
            "\"Find a cozy spot and start a beautiful new chapter today.\"",
            "\"Every book is a grand journey waiting for your eyes to open it.\"",
            "\"The world is a magnificent book, open up and discover more.\""
        ];
        
        let index = 0;
        const quoteElement = document.getElementById('soothing-quote');

        if(quoteElement) {
            setInterval(() => {
                quoteElement.style.opacity = 0;
                setTimeout(() => {
                    index = (index + 1) % quotes.length;
                    quoteElement.textContent = quotes[index];
                    quoteElement.style.opacity = 1;
                }, 500);
            }, 5000);
        }
    });
</script>
@endsection
