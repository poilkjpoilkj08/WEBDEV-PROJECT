@extends('base.base')

@section('content')
<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-color: #ffffff; /* Overriding the master view background to pure clean white */
        min-height: 100vh;
    }

    /* Fixed Header Logic Compatibility */
    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }

    /* Modern scannable card layout adjustments */
    .wishlist-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #eef0f2 !important;
    }

    .wishlist-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.1) !important;
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
</style>

<div class="bg-white" style="position: relative; z-index: 4; padding-top: 40px;">
    <div class="container py-5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-5 gap-3">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">
                    <i class="fas fa-bookmark me-2" style="color: #c25e25;"></i>My Wishlist
                </h1>
                <p class="text-muted small mb-0">Books you have saved to your curated reading list</p>
            </div>
            <a href="{{ route('books.listing') }}" class="btn btn-outline-soft-orange btn-sm fw-bold px-4 rounded-pill shadow-sm">
                Continue Shopping
            </a>
        </div>

        @if($wishlists->isEmpty())
            <div class="alert bg-light border text-center p-5 rounded-4" style="border-color: #eef0f2 !important;">
                <i class="fas fa-bookmark text-muted fa-3x mb-3 opacity-40"></i>
                <h4 class="h6 fw-bold text-dark mb-2">Your wishlist is currently empty</h4>
                <p class="text-secondary small mb-4 mx-auto" style="max-width: 400px;">Explore our catalog dashboard to save titles you want to read or purchase later.</p>
                <a href="{{ route('books.listing') }}" class="btn btn-soft-orange btn-sm fw-bold px-4 rounded-pill shadow-sm">
                    Explore Books
                </a>
            </div>
        @else
            <div class="row g-4">
                @foreach($wishlists as $wishlist)
                    @if($wishlist->book && $wishlist->book->status === 'available')
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="card shadow-sm border-0 h-100 rounded-4 overflow-hidden wishlist-card">
                            
                            <div style="height: 250px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; padding: 16px;">
                                @if($wishlist->book->cover_image_url || $wishlist->book->cover_image_src)
                                    <img src="{{ $wishlist->book->cover_image_src }}" alt="{{ $wishlist->book->title }}" class="img-fluid rounded-2" style="max-height: 100%; object-fit: contain;">
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-book fa-3x mb-2 opacity-30" style="color: #c25e25;"></i>
                                        <p class="small text-secondary mb-0">No Cover Available</p>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column p-4">
                                <h5 class="h6 card-title text-dark fw-bold text-truncate mb-1" title="{{ $wishlist->book->title }}">{{ $wishlist->book->title }}</h5>
                                <p class="text-muted small mb-3">by <span class="fw-semibold" style="color: #c25e25;">{{ $wishlist->book->author?->name ?? 'Unknown Author' }}</span></p>
                                <p class="card-text text-secondary small text-overflow-clamp mb-4" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.8rem; height: 2.4rem; line-height: 1.2rem;">
                                    {{ $wishlist->book->description ?: 'No description catalog available.' }}
                                </p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fw-bold text-dark" style="font-size: 0.95rem;">Rp {{ number_format($wishlist->book->price, 0, ',', '.') }}</span>
                                        <span class="badge bg-light text-dark border px-2 py-1 small" style="font-size: 0.7rem;">Stock: {{ $wishlist->book->stock }}</span>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('books.show', $wishlist->book->id) }}" class="btn btn-outline-soft-orange btn-sm flex-grow-1 fw-bold rounded-3">
                                            <i class="fas fa-eye me-1"></i>Details
                                        </a>
                                        <form method="POST" action="{{ route('wishlist.remove') }}" class="d-inline" onsubmit="return confirm('Remove this book from your wishlist?')">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $wishlist->book->id }}">
                                            <button type="submit" class="btn btn-light btn-sm text-danger border rounded-3" title="Remove from wishlist">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
