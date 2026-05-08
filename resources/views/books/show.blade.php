@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                @if($book->cover_image_url)
                <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}" style="height: 420px; object-fit: cover;" />
                @else
                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 420px;">No Cover Available</div>
                @endif
            </div>

            @if($book->images)
            <div class="row g-2 mt-3">
                @foreach($book->images as $image)
                <div class="col-3">
                    <img src="{{ $image }}" class="img-fluid rounded" alt="Gallery" style="height: 100px; object-fit: cover; width: 100%;" />
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <span class="badge bg-success mb-3">{{ ucfirst($book->status) }}</span>
                    <h1 class="h4">{{ $book->title }}</h1>
                    <p class="text-muted mb-3">by {{ $book->author ? $book->author->name : 'Unknown Author' }}</p>
                    <h2 class="h3 text-primary mb-3">${{ number_format($book->price, 2) }}</h2>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="fw-bold">{{ $book->pages ?: 'N/A' }}</div>
                                <small class="text-muted">Pages</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="fw-bold">{{ $book->language }}</div>
                                <small class="text-muted">Language</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="fw-bold">{{ $book->publication_year ?: 'N/A' }}</div>
                                <small class="text-muted">Published</small>
                            </div>
                        </div>
                        @if($book->weight_grams)
                        <div class="col-6">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="fw-bold">{{ number_format($book->weight_grams) }}g</div>
                                <small class="text-muted">Weight</small>
                            </div>
                        </div>
                        @endif
                    </div>

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item"><strong>ISBN:</strong> {{ $book->isbn ?: 'Not available' }}</li>
                        <li class="list-group-item"><strong>Category:</strong> {{ $book->category->name }}</li>
                        <li class="list-group-item"><strong>Publisher:</strong> {{ $book->publisher ?: 'Unknown' }}</li>
                    </ul>

                    @if($book->author)
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h5 class="card-title">About the Author</h5>
                            <p class="card-text mb-1"><strong>{{ $book->author->name }}</strong></p>
                            @if($book->author->bio)
                            <p class="text-muted mb-2">{{ Str::limit($book->author->bio, 100) }}</p>
                            @endif
                            <p class="text-muted mb-1">📧 <a href="mailto:{{ $book->author->email }}" class="text-decoration-none">{{ $book->author->email }}</a></p>
                            @if($book->author->phone)
                            <p class="text-muted mb-2">📱 <a href="tel:{{ $book->author->phone }}" class="text-decoration-none">{{ $book->author->phone }}</a></p>
                            @endif
                            <a href="{{ route('authors.show', $book->author->id) }}" class="btn btn-primary btn-sm">View Author</a>
                        </div>
                    </div>
                    @endif

                    <a href="mailto:{{ $book->author?->email ?? 'info@premiumbookstore.com' }}?subject=Inquiry%20about%20{{ urlencode($book->title) }}&body=I%20am%20interested%20in%20{{ urlencode($book->title) }}.%20Please%20provide%20more%20information." class="btn btn-primary w-100 mb-2">Contact Author</a>
                    @if($book->author && $book->author->phone)
                    <a href="tel:{{ $book->author->phone }}" class="btn btn-outline-primary w-100">Call Author</a>
                    @else
                    <a href="mailto:{{ $book->author?->email ?? 'info@premiumbookstore.com' }}" class="btn btn-outline-primary w-100">Contact Author</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5">Book Description</h2>
                    <p class="text-muted">{{ $book->description ?: 'No description available.' }}</p>
                    @if($book->genres)
                    <h3 class="h6 mt-4">Genres</h3>
                    <div class="row g-2">
                        @foreach($book->genres as $genre)
                        <div class="col-6">
                            <div class="border rounded-3 px-3 py-2">📚 {{ $genre }}</div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="h5">Publication Details</h3>
                    <div class="bg-light rounded-3 p-4">
                        <div class="mb-3">
                            <strong>Publisher:</strong><br>
                            <span class="text-muted">{{ $book->publisher ?: 'Unknown Publisher' }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Publication Year:</strong><br>
                            <span class="text-muted">{{ $book->publication_year ?: 'Unknown' }}</span>
                        </div>
                        @if($book->isbn)
                        <div class="mb-3">
                            <strong>ISBN:</strong><br>
                            <span class="text-muted">{{ $book->isbn }}</span>
                        </div>
                        @endif
                        @if($book->weight_grams)
                        <div class="mb-0">
                            <strong>Weight:</strong><br>
                            <span class="text-muted">{{ number_format($book->weight_grams) }} grams</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($similar_books->count() > 0)
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Similar Books</h2>
            <a href="{{ route('books.listing') }}" class="text-decoration-none">View all books</a>
        </div>
        <div class="row g-4">
            @foreach($similar_books as $similar)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    @if($similar->cover_image_url)
                    <img src="{{ $similar->cover_image_url }}" class="card-img-top" alt="{{ $similar->title }}" style="height: 180px; object-fit: cover;" />
                    @else
                    <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 180px;">No Cover</div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6">{{ $similar->title }}</h3>
                        <p class="text-muted small mb-2">by {{ $similar->author ? $similar->author->name : 'Unknown Author' }}</p>
                        <p class="fw-bold text-primary mb-3">${{ number_format($similar->price, 2) }}</p>
                        <a href="{{ route('books.show', $similar->id) }}" class="btn btn-outline-primary btn-sm mt-auto">View Details</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="text-center">
        <a href="{{ route('books.listing') }}" class="btn btn-outline-secondary">← Back to Books</a>
        @auth
            @if(auth()->user()->hasRole(['admin', 'owner']))
                <a href="{{ route('books.edit-form', $book->id) }}" class="btn btn-warning ms-2">
                    <i class="fas fa-edit me-1"></i>Edit Book
                </a>
                <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Are you sure you want to delete this book?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Book
                    </button>
                </form>
            @endif
        @endauth
    </div>
</div>
@endsection
