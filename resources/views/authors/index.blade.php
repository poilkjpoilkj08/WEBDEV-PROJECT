@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h1 class="display-6">Our Book Authors</h1>
        <p class="text-muted">Meet the talented authors who bring you the best books in our collection.</p>
    </div>

    <div class="row g-4">
        @forelse($authors as $author)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                @if($author->photo_url)
                <img src="{{ $author->photo_url }}" class="card-img-top" alt="{{ $author->name }}" style="height: 260px; object-fit: cover;" />
                @else
                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 260px;">👤</div>
                @endif
                <div class="card-body d-flex flex-column">
                    <h2 class="h5 mb-1">{{ $author->name }}</h2>
                    @if($author->publisher)
                    <p class="text-muted small mb-2">Publisher: {{ $author->publisher }}</p>
                    @endif
                    <div class="mb-3 p-3 bg-light rounded">
                        <p class="h4 mb-0 text-primary">{{ $author->books->count() }}</p>
                        <p class="small text-muted mb-0">Books Published</p>
                    </div>
                    <p class="text-muted small mb-3">{{ Str::limit($author->bio, 100) }}</p>
                    <div class="mb-3 small text-muted">
                        <p class="mb-1">📧 {{ $author->email }}</p>
                        @if($author->phone)
                        <p class="mb-0">📱 {{ $author->phone }}</p>
                        @endif
                    </div>
                    <a href="{{ route('authors.show', $author->id) }}" class="btn btn-primary mt-auto">View Profile</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning">No authors available.</div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $authors->links() }}
    </div>
</div>
@endsection
