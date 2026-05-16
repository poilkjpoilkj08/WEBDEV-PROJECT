@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
        <div>
            <h1 class="display-6 mb-1">Browse Books</h1>
            <p class="text-muted">Search, filter, and explore our book collection.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('books.listing') }}" class="btn btn-outline-secondary">Reset Filters</a>
        </div>
    </div>

    <!-- Refine Search Section Above Books -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h2 class="h5 mb-4">Refine Search</h2>
            <form action="{{ route('books.search') }}" method="GET" class="row g-3">
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" value="{{ request('title') }}" class="form-control" placeholder="Enter book title" />
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label">Author</label>
                    <input type="text" name="author" value="{{ request('author') }}" class="form-control" placeholder="Enter author name" />
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label">Min Price</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control" placeholder="Min" step="0.01" />
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label">Max Price</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control" placeholder="Max" step="0.01" />
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label">Language</label>
                    <select name="language" class="form-select">
                        <option value="">All Languages</option>
                        <option value="English" {{ request('language') == 'English' ? 'selected' : '' }}>English</option>
                        <option value="Spanish" {{ request('language') == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                        <option value="French" {{ request('language') == 'French' ? 'selected' : '' }}>French</option>
                        <option value="German" {{ request('language') == 'German' ? 'selected' : '' }}>German</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($book_categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('books.listing') }}" class="btn btn-outline-secondary">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @forelse($books as $book)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        @if($book->cover_image_url)
                        <img src="{{ $book->cover_image_src }}" class="card-img-top" alt="{{ $book->title }}" style="height: 220px; object-fit: contain; background:#f8f9fa;" />
                        @else
                        <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 220px;">No Cover</div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-success mb-2">{{ ucfirst($book->status) }}</span>
                            <h3 class="h5 card-title">{{ $book->title }}</h3>
                            <p class="text-muted mb-2">by {{ $book->author ? $book->author->name : 'Unknown Author' }}</p>
                            <div class="mb-3 small text-muted">
                                <span class="me-2">📖 {{ $book->pages }} pages</span>
                                <span class="me-2">🌐 {{ $book->language }}</span>
                                <span>📅 {{ $book->publication_year }}</span>
                            </div>
                            <p class="mb-3"><strong>Category:</strong> {{ $book->category->name }}</p>
                            <p class="h5 text-primary mb-3">Rp {{ number_format($book->price, 0, ',', '.') }}</p>
                            <a href="{{ route('books.show', $book->id) }}" class="btn btn-primary mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-warning mb-0">No books found. Try adjusting your filters.</div>
                </div>
                @endforelse
    </div>

    <!-- Pagination Controls -->
    @if($books->hasPages())
    <div class="d-flex justify-content-center align-items-center gap-3 mt-5">
        @if($books->onFirstPage())
            <button class="btn btn-outline-secondary" disabled>
                <i class="fas fa-chevron-left me-2"></i>Previous
            </button>
        @else
            <a href="{{ $books->previousPageUrl() }}" class="btn btn-outline-primary">
                <i class="fas fa-chevron-left me-2"></i>Previous
            </a>
        @endif

        <div class="text-muted">
            Page {{ $books->currentPage() }} of {{ $books->lastPage() }}
        </div>

        @if($books->hasMorePages())
            <a href="{{ $books->nextPageUrl() }}" class="btn btn-outline-primary">
                Next<i class="fas fa-chevron-right ms-2"></i>
            </a>
        @else
            <button class="btn btn-outline-secondary" disabled>
                Next<i class="fas fa-chevron-right ms-2"></i>
            </button>
        @endif
    </div>
    @endif
</div>
@endsection
