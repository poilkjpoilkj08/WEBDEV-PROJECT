@extends('base.base')
@section('content')
<div class="container py-5">
    <!-- Hero Section with Warm Design -->
    <div class="bg-gradient text-white rounded-4 p-5 mb-5 shadow-lg position-relative overflow-hidden" style="background: linear-gradient(135deg, #8B4513, #D2691E);">
        <div class="position-absolute top-0 end-0 opacity-10">
            <i class="fas fa-book fa-6x"></i>
        </div>
        <div class="row align-items-center">
            <div class="col-md-7 mb-4 mb-md-0">
                <h1 class="display-5 fw-bold mb-3">{{ __('messages.featured_books') }}</h1>
                <p class="lead mb-4">{{ __('messages.search_books') }}. {{ __('messages.all_books') }}.</p>
                <div class="d-flex gap-2">
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
                        <h5 class="card-title text-dark mb-3">{{ __('messages.search_books') }}</h5>
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
                            <button type="submit" class="btn btn-warning w-100 fw-bold">
                                <i class="fas fa-search me-2"></i>{{ __('messages.search') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-gradient text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <div class="card-body text-center py-4">
                    <i class="fas fa-book fa-3x mb-3 opacity-75"></i>
                    <h3 class="h2 mb-1">{{ $total_books }}</h3>
                    <p class="mb-0">{{ __('messages.total_books') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-gradient text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #007bff, #6610f2);">
                <div class="card-body text-center py-4">
                    <i class="fas fa-user-tie fa-3x mb-3 opacity-75"></i>
                    <h3 class="h2 mb-1">{{ $authors_count ?? 4 }}</h3>
                    <p class="mb-0">Expert Authors</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-gradient text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #fd7e14, #dc3545);">
                <div class="card-body text-center py-4">
                    <i class="fas fa-tags fa-3x mb-3 opacity-75"></i>
                    <h3 class="h2 mb-1">{{ $categories_count ?? 8 }}</h3>
                    <p class="mb-0">Book Categories</p>
                </div>
            </div>
        </div>
    </div>

    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 fw-bold text-dark">🏆 Featured Books</h2>
            <a href="{{ route('books.listing') }}" class="btn btn-warning fw-bold">
                <i class="fas fa-arrow-right me-2"></i>View All Books
            </a>
        </div>
        <div class="row g-4">
            @forelse($featured_books as $book)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-lg h-100 border-0 overflow-hidden hover-lift" style="transition: transform 0.3s ease;">
                    @if($book->cover_image_url)
                    <div class="position-relative">
                        <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}" style="height: 220px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-warning text-dark fw-bold px-3 py-2">
                                <i class="fas fa-star me-1"></i>Featured
                            </span>
                        </div>
                    </div>
                    @else
                    <div class="card-img-top bg-light text-muted d-flex align-items-center justify-content-center" style="height: 220px;">
                        <i class="fas fa-image fa-3x opacity-50"></i>
                    </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h3 class="h5 card-title fw-bold text-dark mb-2">{{ Str::limit($book->title, 50) }}</h3>
                        <p class="text-muted mb-3">
                            <i class="fas fa-user me-1"></i>{{ $book->author ? $book->author->name : 'Unknown Author' }}
                        </p>
                        <div class="row g-2 mb-3 small">
                            <div class="col-4 text-center">
                                <i class="fas fa-file-alt text-primary"></i><br>
                                {{ $book->pages }} pages
                            </div>
                            <div class="col-4 text-center">
                                <i class="fas fa-language text-info"></i><br>
                                {{ $book->language }}
                            </div>
                            <div class="col-4 text-center">
                                <i class="fas fa-calendar text-success"></i><br>
                                {{ $book->publication_year }}
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h4 text-success fw-bold mb-0">${{ number_format($book->price, 2) }}</span>
                                <small class="text-muted">{{ $book->category->name }}</small>
                            </div>
                            <a href="{{ route('books.show', $book->id) }}" class="btn btn-primary w-100 fw-bold">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-warning border-0 shadow-sm">
                    <i class="fas fa-info-circle me-2"></i>No featured books available at the moment.
                </div>
            </div>
            @endforelse
        </div>
    </section>

    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">Browse by Category</h2>
            <a href="{{ route('books.listing') }}" class="text-decoration-none">See all categories</a>
        </div>
        <div class="row g-4">
            @forelse($book_categories as $category)
            <div class="col-12 col-md-6 col-lg-3">
                <a href="{{ route('books.listing', ['category_id' => $category->id]) }}" class="card h-100 text-decoration-none text-dark shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="h5">{{ $category->name }}</h3>
                        <p class="text-muted">{{ $category->description }}</p>
                        <p class="fw-bold text-primary">{{ $category->books->count() }} Books</p>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-secondary">No book categories available.</div>
            </div>
            @endforelse
        </div>
    </section>

    <section class="bg-primary text-white rounded-4 p-5 text-center">
        <h2 class="h3 mb-3">Looking for Your Next Great Read?</h2>
        <p class="mb-4">Browse our extensive book collection or discover new authors today.</p>
        <a href="{{ route('books.listing') }}" class="btn btn-light btn-lg me-2">View Books</a>
        <a href="{{ route('authors.index') }}" class="btn btn-outline-light btn-lg">Meet Authors</a>
    </section>
</div>
@endsection
