@extends('base.base')
@section('content')

<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-color: #ffffff; /* Replaced the image with a pure white background */
        min-height: 100vh;
        padding-top: 100px;
    }

    /* Fixed Header Logic Compatibility */
    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }

    /* GLASS BOX FOR HEADERS - Styled to complement white background theme */
    .glass-header-box {
        background: rgba(248, 249, 250, 0.85); 
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 10px 28px;
        border-radius: 50px;
        display: inline-block;
        border: 1px solid #eef0f2;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }

    /* Remove core white background wrapper to let background image shine */
    .content-wrapper {
        background-color: transparent !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
    }

    /* UI Recreation: White Rounded Book Container */
    .book-ui-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #eef0f2;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .book-ui-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    /* Unified Muted Soft Orange Action Buttons */
    .btn-soft-orange {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    
    .btn-soft-orange:hover, .btn-soft-orange:focus {
        background-color: #a64f1e !important;
        border-color: #a64f1e !important;
        color: #ffffff !important;
    }

    .max-width-fit {
        width: fit-content;
    }
</style>

<div class="container py-5 content-wrapper">
    <!-- Header Area -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-3">
        <div class="glass-header-box">
            <h1 class="h3 mb-0 fw-bold text-dark">Browse Books</h1>
        </div>
    </div>

    <!-- Refine Search Filter Card -->
    <div class="card shadow-sm border-0 mb-5 bg-light rounded-4" style="border: 1px solid #eef0f2 !important;">
        <div class="card-body p-4">
            <h2 class="h5 mb-4 small fw-bold text-uppercase text-muted">
                <i class="fas fa-sliders-h me-2" style="color: #c25e25;"></i>Refine Search
            </h2>
            <form action="{{ route('books.search') }}" method="GET" class="row g-3">
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label fw-medium text-dark small">Title</label>
                    <input type="text" name="title" value="{{ request('title') }}" class="form-control" placeholder="Enter book title" />
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label fw-medium text-dark small">Author</label>
                    <input type="text" name="author" value="{{ request('author') }}" class="form-control" placeholder="Enter author name" />
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label fw-medium text-dark small">Min Price</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control" placeholder="Min" step="0.01" />
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label fw-medium text-dark small">Max Price</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control" placeholder="Max" step="0.01" />
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label fw-medium text-dark small">Language</label>
                    <select name="language" class="form-select">
                        <option value="">All Languages</option>
                        <option value="English" {{ request('language') == 'English' ? 'selected' : '' }}>English</option>
                        <option value="Spanish" {{ request('language') == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                        <option value="French" {{ request('language') == 'French' ? 'selected' : '' }}>French</option>
                        <option value="German" {{ request('language') == 'German' ? 'selected' : '' }}>German</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label fw-medium text-dark small">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($book_categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-soft-orange fw-bold px-4 rounded-pill">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('books.listing') }}" class="btn btn-outline-dark fw-bold px-4 rounded-pill">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
        @forelse($books as $book)
        <div class="col">
            <div class="card h-100 border-0 overflow-hidden book-ui-card">
                @if($book->cover_image_url)
                <div class="position-relative" style="padding: 12px 12px 0 12px;">
                    <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}" style="height: 240px; object-fit: cover; border-radius: 12px;" />
                    <div class="position-absolute top-0 end-0 m-4">
                        <span class="badge bg-light text-dark fw-bold px-2 py-1 shadow-sm" style="font-size: 0.7rem; border: 1px solid #eef0f2;">
                            {{ ucfirst($book->status) }}
                        </span>
                    </div>
                </div>
                @else
                <div class="m-2">
                    <div class="card-img-top bg-light text-muted d-flex align-items-center justify-content-center" style="height: 240px; border-radius: 12px;">
                        <i class="fas fa-image fa-3x opacity-30"></i>
                    </div>
                </div>
                @endif
                
                <div class="card-body d-flex flex-column p-4 pt-3">
                    <h3 class="h6 card-title fw-bold text-dark mb-1 text-truncate" style="font-size: 0.95rem;">{{ $book->title }}</h3>
                    <p class="text-muted mb-3 small">By {{ $book->author ? $book->author->name : 'Unknown Author' }}</p>
                    
                    <div class="mb-3 small text-muted bg-light p-2 rounded-3 d-flex justify-content-around" style="font-size: 0.75rem;">
                        <span>📖 {{ $book->pages }} pages</span>
                        <span>🌐 {{ $book->language }}</span>
                        <span>📅 {{ $book->publication_year }}</span>
                    </div>
                    
                    <p class="mb-3 text-secondary small" style="font-size: 0.8rem;"><strong>Shelf:</strong> {{ $book->category->name }}</p>
                    
                    <div class="mt-auto pt-2">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-dark" style="font-size: 1rem;">Rp {{ number_format($book->price, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('books.show', $book->id) }}" class="btn btn-soft-orange w-100 fw-bold py-2" style="font-size: 0.85rem; border-radius: 8px;">
                            <i class="fas fa-eye me-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm bg-light text-dark">
                <i class="fas fa-info-circle me-2" style="color: #c25e25;"></i>No books found. Try adjusting your filters.
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination Controls -->
    @if($books->hasPages())
    <div class="d-flex justify-content-center align-items-center gap-3 mt-5 bg-light py-2 px-4 rounded-pill shadow-sm d-inline-flex mx-auto max-width-fit" style="border: 1px solid #eef0f2;">
        @if($books->onFirstPage())
            <button class="btn btn-sm btn-link text-muted text-decoration-none" disabled style="font-size: 0.8rem;">
                <i class="fas fa-chevron-left me-2"></i>Previous
            </button>
        @else
            <a href="{{ $books->previousPageUrl() }}" class="btn btn-sm btn-link text-dark text-decoration-none fw-bold" style="font-size: 0.8rem;">
                <i class="fas fa-chevron-left me-2" style="color: #c25e25;"></i>Previous
            </a>
        @endif

        <div class="text-dark fw-medium small mx-2" style="font-size: 0.8rem;">
            Page {{ $books->currentPage() }} of {{ $books->lastPage() }}
        </div>

        @if($books->hasMorePages())
            <a href="{{ $books->nextPageUrl() }}" class="btn btn-sm btn-link text-dark text-decoration-none fw-bold" style="font-size: 0.8rem;">
                Next<i class="fas fa-chevron-right ms-2" style="color: #c25e25;"></i>
            </a>
        @else
            <button class="btn btn-sm btn-link text-muted text-decoration-none" disabled style="font-size: 0.8rem;">
                Next<i class="fas fa-chevron-right ms-2"></i>
            </button>
        @endif
    </div>
    @endif
</div>
@endsection
