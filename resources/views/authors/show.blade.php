@extends('base.base')
@section('content')

<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-color: #ffffff; /* Pure white canvas background */
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

    /* GLASS BOX FOR HEADERS */
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

    .content-wrapper {
        background-color: transparent !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
    }

    /* Author Profile Layout Panel */
    .author-profile-card {
        background: #ffffff;
        border: 1px solid #eef0f2;
        border-radius: 24px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.02);
    }

    /* UI Recreation: White Rounded Book Container Cards */
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

    /* Unified Muted Soft Orange Action Elements */
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
        transform: translateY(-1px);
    }

    .stat-badge-box {
        background-color: #fff4eb;
        border: 1px solid rgba(194, 94, 37, 0.1);
        border-radius: 14px;
        padding: 16px 24px;
        display: inline-block;
    }
</style>

<div class="container py-5">
    
    <div class="mb-4">
        <a href="{{ route('admin.authors.index') }}" class="text-decoration-none text-muted small fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Back to Author Management
        </a>
    </div>

    <div class="card author-profile-card border-0 p-4 p-md-5 mb-5">
        <div class="row align-items-center">
            <div class="col-12">
                
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h1 class="h2 fw-bold text-dark mb-0">{{ $author->name }}</h1>
                    <span class="badge bg-{{ $author->is_active ? 'success' : 'secondary' }} rounded-pill px-3 py-1.5 small">
                        {{ $author->is_active ? 'Active Status' : 'Inactive Status' }}
                    </span>
                </div>

                <p class="text-secondary mb-4 lh-base" style="font-size: 0.98rem; max-width: 850px;">
                    {{ $author->bio ?? 'No biographical description has been configured for this writer record inside the system catalog logs yet.' }}
                </p>

                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <div class="stat-badge-box text-center text-md-start">
                            <span class="d-block h3 fw-bold mb-0 text-dark" style="color: #c25e25 !important;">{{ $books->total() }}</span>
                            <span class="text-muted extra-small font-monospace text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Books Logged</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <section class="mb-5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3 border-bottom pb-3">
            <div class="glass-header-box">
                <h2 class="h4 mb-0 fw-bold text-dark">Books by {{ $author->name }}</h2>
            </div>
            <p class="text-muted small mb-0 fw-medium">Explore all verified physical stock items linked to this writer profile</p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
            @forelse($books as $book)
            <div class="col">
                <div class="card h-100 border-0 overflow-hidden book-ui-card">
                    
                    @if($book->cover_image_url ?? $book->cover_image_src)
                    <div class="position-relative" style="padding: 12px 12px 0 12px;">
                        <img src="{{ asset($book->cover_image_url ?? $book->cover_image_src) }}" class="card-img-top" alt="{{ $book->title }}" style="height: 240px; object-fit: cover; border-radius: 12px;" onerror="this.src='https://via.placeholder.com/300x240?text=No+Cover+Image'; this.onerror=null;" />
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
                        <p class="text-muted mb-3 small">By {{ $author->name }}</p>
                        
                        <div class="mb-3 small text-muted bg-light p-2 rounded-3 d-flex justify-content-around" style="font-size: 0.75rem;">
                            <span>📖 {{ $book->pages }} pages</span>
                            <span>🌐 {{ $book->language }}</span>
                            <span>📅 {{ $book->publication_year }}</span>
                        </div>
                        
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
                    <i class="fas fa-info-circle me-2" style="color: #c25e25;"></i>No books registered under this specific author account inside our records yet.
                </div>
            </div>
            @endforelse
        </div>

        @if($books->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $books->links() }}
        </div>
        @endif
    </section>

    @auth
        @if(auth()->user()->hasRole(['admin', 'owner']))
        <div class="mt-5 pt-4 border-top text-center">
            <span class="d-block text-muted small font-monospace text-uppercase mb-3">Administrative Terminal Commands</span>
            <div class="d-inline-flex gap-2">
                <a href="{{ route('authors.edit-form', $author->id) }}" class="btn btn-warning fw-bold px-4 rounded-pill shadow-sm">
                    <i class="fas fa-edit me-1.5"></i>Edit Author Entry
                </a>
                <form action="{{ route('authors.destroy', $author->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this author? This action cannot be revoked.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm">
                        <i class="fas fa-trash me-1.5"></i>Remove Author
                    </button>
                </form>
            </div>
        </div>
        @endif
    @endauth

</div>
@endsection