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

<style>
    /* ===== RESPONSIVE STYLES FOR AUTHORS INDEX PAGE ===== */
    @media (max-width: 768px) {
        /* Container padding */
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Heading sizing */
        .display-6 {
            font-size: 1.5rem;
        }

        /* Card sizing */
        .card {
            box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
        }

        .card-img-top {
            height: 200px !important;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Text sizing */
        .h5 {
            font-size: 1rem;
        }

        .text-muted {
            font-size: 0.9rem;
        }

        .small {
            font-size: 0.85rem;
        }

        /* Row spacing */
        .row {
            gap: 1rem;
        }

        .g-4 {
            gap: 1rem;
        }

        /* Button sizing */
        .btn {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        /* Box styling */
        .bg-light {
            padding: 0.75rem !important;
        }

        .h4 {
            font-size: 1rem;
        }

        /* Margin utilities */
        .mb-4 {
            margin-bottom: 1rem !important;
        }

        .mb-3 {
            margin-bottom: 0.75rem !important;
        }

        .mb-2 {
            margin-bottom: 0.5rem !important;
        }
    }

    @media (max-width: 576px) {
        /* Extra small screens */
        body {
            padding-left: 0;
            padding-right: 0;
        }

        /* Container padding */
        .container {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .py-5 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        /* Heading sizing */
        .display-6 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem !important;
        }

        /* Column sizing - make cards full width */
        .col-12, .col-md-6, .col-lg-4 {
            max-width: 100%;
            flex-basis: 100%;
        }

        /* Card styling */
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
            height: auto !important;
        }

        .card-img-top {
            height: 180px !important;
            object-fit: cover !important;
        }

        .card-body {
            padding: 1rem;
        }

        /* Text sizing */
        .h5 {
            font-size: 0.95rem;
            margin-bottom: 0.25rem !important;
        }

        .h4 {
            font-size: 0.9rem;
            margin-bottom: 0 !important;
        }

        .text-muted {
            font-size: 0.8rem;
        }

        .small {
            font-size: 0.75rem;
        }

        .display-6 {
            font-size: 1.25rem;
        }

        /* Row and column spacing */
        .row {
            gap: 0.75rem;
            flex-direction: column;
        }

        .g-4 {
            gap: 0.75rem;
        }

        /* Button sizing */
        .btn {
            padding: 0.65rem 1rem;
            font-size: 0.8rem;
            min-height: 44px;
            width: 100%;
        }

        /* Box styling */
        .bg-light {
            padding: 0.5rem !important;
        }

        /* Margin utilities */
        .mb-4 {
            margin-bottom: 0.75rem !important;
        }

        .mb-3 {
            margin-bottom: 0.5rem !important;
        }

        .mb-2 {
            margin-bottom: 0.25rem !important;
        }

        .mb-1 {
            margin-bottom: 0.125rem !important;
        }

        .mt-auto {
            margin-top: auto !important;
        }

        .mt-4 {
            margin-top: 1rem !important;
        }

        .p-3 {
            padding: 0.5rem !important;
        }

        /* Alert styling */
        .alert {
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
        }

        /* Pagination */
        .pagination {
            font-size: 0.8rem;
        }

        /* Prevent horizontal overflow */
        body {
            overflow-x: hidden;
        }
    }
</style>
@endsection
