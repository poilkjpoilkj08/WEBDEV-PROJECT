@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                @if($author->photo_url)
                <img src="{{ $author->photo_url }}" class="card-img-top" alt="{{ $author->name }}" style="height: 320px; object-fit: cover;" />
                @else
                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 320px; font-size: 4rem;">👤</div>
                @endif
                <div class="card-body">
                    <h1 class="h4 mb-2">{{ $author->name }}</h1>
                    <span class="badge bg-{{ $author->is_active ? 'success' : 'secondary' }} mb-3">{{ $author->is_active ? 'Active' : 'Inactive' }}</span>
                    @if($author->publisher)
                    <p class="text-muted mb-3"><strong>Publisher:</strong> {{ $author->publisher }}</p>
                    @endif
                    <div class="bg-light p-3 rounded mb-3 text-center">
                        <p class="h2 mb-1 text-primary">{{ $books->total() }}</p>
                        <p class="mb-0 text-muted">Books Published</p>
                    </div>
                    <a href="mailto:{{ $author->email }}" class="btn btn-primary w-100 mb-2">📧 Email Author</a>
                    @if($author->phone)
                    <a href="tel:{{ $author->phone }}" class="btn btn-outline-success w-100">📱 Call Author</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h4 mb-3">About {{ $author->name }}</h2>
                    <p class="text-muted mb-4">{{ $author->bio ?? 'No bio available.' }}</p>
                    <h3 class="h5 mb-3">Author Information</h3>
                    <ul class="list-unstyled mb-0">
                        <li>✓ Published {{ $books->total() }} books</li>
                        <li>✓ Professional writer and storyteller</li>
                        <li>✓ Available for collaborations and inquiries</li>
                        <li>✓ Contact via email or phone</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h4 mb-0">Books by {{ $author->name }}</h2>
                <p class="text-muted mb-0">Explore all books written by this author.</p>
            </div>
            <a href="{{ route('authors.index') }}" class="btn btn-outline-secondary">Back to Authors</a>
        </div>

        <div class="row g-4">
            @forelse($books as $book)
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    @if($book->cover_image_url)
                    <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}" style="height: 220px; object-fit: cover;" />
                    @else
                    <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 220px;">No Cover</div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-success mb-2">{{ ucfirst($book->status) }}</span>
                        <h3 class="h5 mb-2">{{ $book->title }}</h3>
                        <p class="text-muted mb-2">{{ $book->language }} · {{ $book->pages }} pages</p>
                        <p class="mb-3 small text-muted">Published {{ $book->publication_year }} by {{ $book->publisher ?: 'Unknown Publisher' }}</p>
                        <p class="h5 text-primary mb-3">${{ number_format($book->price, 2) }}</p>
                        <a href="{{ route('books.show', $book->id) }}" class="btn btn-primary mt-auto">View Details</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-warning">No books found for this author.</div>
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $books->links() }}
        </div>
    </section>

    <div class="text-center">
        <a href="{{ route('authors.index') }}" class="btn btn-outline-secondary">Back to Authors</a>
        @auth
            @if(auth()->user()->hasRole(['admin', 'owner']))
                <a href="{{ route('authors.edit-form', $author->id) }}" class="btn btn-warning ms-2">
                    <i class="fas fa-edit me-1"></i>Edit Author
                </a>
                <form action="{{ route('authors.destroy', $author->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Are you sure you want to delete this author?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Author
                    </button>
                </form>
            @endif
        @endauth
    </div>
@endsection
