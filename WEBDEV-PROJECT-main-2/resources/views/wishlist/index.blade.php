@extends('base.base')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 mb-1"><i class="fas fa-heart text-danger me-2"></i>My Wishlist</h1>
            <p class="text-muted">Books you want to read</p>
        </div>
        <a href="{{ route('books.listing') }}" class="btn btn-outline-primary">Continue Shopping</a>
    </div>

    @if($wishlists->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-heart-broken me-2"></i>
            Your wishlist is empty. <a href="{{ route('books.listing') }}">Explore books</a> to add some!
        </div>
    @else
        <div class="row g-4">
            @foreach($wishlists as $wishlist)
                @if($wishlist->book && $wishlist->book->status === 'available')
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card shadow-sm border-0 h-100 position-relative wishlist-card" style="overflow: hidden;">
                        <!-- Book Cover -->
                        <div style="height: 250px; overflow: hidden; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                            @if($wishlist->book->cover_image)
                                <img src="{{ asset('storage/' . $wishlist->book->cover_image) }}" alt="{{ $wishlist->book->title }}" class="img-fluid" style="max-height: 100%; object-fit: cover;">
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-book fa-3x mb-2"></i>
                                    <p>No Cover</p>
                                </div>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate" title="{{ $wishlist->book->title }}">{{ $wishlist->book->title }}</h5>
                            <p class="text-muted small mb-2">by {{ $wishlist->book->author?->name ?? 'Unknown' }}</p>
                            <p class="card-text text-truncate text-secondary small">{{ $wishlist->book->description }}</p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h5 text-success fw-bold mb-0">Rp {{ number_format($wishlist->book->price, 0, ',', '.') }}</span>
                                    <span class="badge bg-light text-dark">{{ $wishlist->book->stock }} in stock</span>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('books.show', $wishlist->book->id) }}" class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <form method="POST" action="{{ route('wishlist.remove') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $wishlist->book->id }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Remove from wishlist">
                                            <i class="fas fa-trash"></i>
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

<style>
.wishlist-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.wishlist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}
</style>
@endsection
