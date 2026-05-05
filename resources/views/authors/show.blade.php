@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                @if($agent->photo_url)
                <img src="{{ $agent->photo_url }}" class="card-img-top" alt="{{ $agent->name }}" style="height: 320px; object-fit: cover;" />
                @else
                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 320px; font-size: 4rem;">👤</div>
                @endif
                <div class="card-body">
                    <h1 class="h4 mb-2">{{ $agent->name }}</h1>
                    <span class="badge bg-{{ $agent->is_active ? 'success' : 'secondary' }} mb-3">{{ $agent->is_active ? 'Active' : 'Inactive' }}</span>
                    @if($agent->license_number)
                    <p class="text-muted mb-3"><strong>License:</strong> {{ $agent->license_number }}</p>
                    @endif
                    <div class="bg-light p-3 rounded mb-3 text-center">
                        <p class="h2 mb-1 text-primary">{{ $properties->total() }}</p>
                        <p class="mb-0 text-muted">Active Listings</p>
                    </div>
                    <a href="mailto:{{ $agent->email }}" class="btn btn-primary w-100 mb-2">📧 Email Agent</a>
                    @if($agent->phone)
                    <a href="tel:{{ $agent->phone }}" class="btn btn-outline-success w-100">📱 Call Agent</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h4 mb-3">About {{ $agent->name }}</h2>
                    <p class="text-muted mb-4">{{ $agent->bio ?? 'No bio available.' }}</p>
                    <h3 class="h5 mb-3">Specialties</h3>
                    <ul class="list-unstyled mb-0">
                        <li>✓ Residential Properties</li>
                        <li>✓ Investment Properties</li>
                        <li>✓ Commercial Real Estate</li>
                        <li>✓ Market Analysis & Consulting</li>
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
</div>
@endsection
