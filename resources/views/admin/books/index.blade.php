@extends('base.base')
@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-book me-2 text-primary"></i>Manage Books</h1>
            <p class="text-muted mb-0">{{ $books->total() }} books in the system</p>
        </div>
        <a href="{{ route('books.create-form') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Book
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($books->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No books yet. <a href="{{ route('books.create-form') }}">Add the first one.</a></p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $book)
                            <tr>
                                <td class="text-muted">{{ $book->id }}</td>
                                <td>
                                    <img src="{{ $book->cover_image_src }}" alt="{{ $book->title }}"
                                         style="width:40px;height:55px;object-fit:cover;border-radius:4px;">
                                </td>
                                <td class="fw-semibold">{{ Str::limit($book->title, 35) }}</td>
                                <td class="text-muted">{{ $book->author?->name ?? '—' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $book->category?->name ?? '—' }}</span></td>
                                <td>Rp {{ number_format($book->price, 0, ',', '.') }}</td>
                                <td>
                                    @php $totalStock = $book->total_stock; @endphp
                                    @if($totalStock > 10)
                                        <span class="text-success fw-semibold">{{ $totalStock }}</span>
                                    @elseif($totalStock > 0)
                                        <span class="text-warning fw-semibold">{{ $totalStock }}</span>
                                    @else
                                        <span class="text-danger fw-semibold">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($book->status === 'available')
                                        <span class="badge bg-success">Available</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Out of Stock</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('books.edit-form', $book->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete {{ addslashes($book->title) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Controls -->
                @if($books->hasPages())
                <div class="d-flex justify-content-center align-items-center gap-3 mt-5 bg-light py-2 px-4 rounded-pill shadow-sm d-inline-flex mx-auto" style="border: 1px solid #eef0f2;">
                    @if($books->onFirstPage())
                        <button class="btn btn-sm btn-link text-muted text-decoration-none" disabled style="font-size: 0.8rem;">
                            <i class="fas fa-chevron-left me-2"></i>Previous
                        </button>
                    @else
                        <a href="{{ $books->appends(request()->query())->previousPageUrl() }}" class="btn btn-sm btn-link text-dark text-decoration-none fw-bold" style="font-size: 0.8rem;">
                            <i class="fas fa-chevron-left me-2" style="color: #c25e25;"></i>Previous
                        </a>
                    @endif
                    </div>

                    @if($books->hasMorePages())
                        <a href="{{ $books->appends(request()->query())->nextPageUrl() }}" class="btn btn-sm btn-link text-dark text-decoration-none fw-bold" style="font-size: 0.8rem;">
                            Next<i class="fas fa-chevron-right ms-2" style="color: #c25e25;"></i>
                        </a>
                    @else
                        <button class="btn btn-sm btn-link text-muted text-decoration-none" disabled style="font-size: 0.8rem;">
                            Next<i class="fas fa-chevron-right ms-2"></i>
                        </button>
                    @endif
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
/* ===== RESPONSIVE STYLES FOR ADMIN BOOKS INDEX PAGE ===== */
@media (max-width: 768px) {
    /* Container and spacing */
    .container {
        padding-left: 1rem;
        padding-right: 1rem;

    /* Heading sizing */
    .h3 {
        font-size: 1.25rem;
    }

    .h4 {
        font-size: 1.1rem;
    }

    /* Page header layout */
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }

    /* Button sizing */
    .btn {
        padding: 0.6rem 0.9rem;
        font-size: 0.9rem;
    }

    .btn-sm {
        padding: 0.35rem 0.65rem;
        font-size: 0.8rem;
    }

    /* Table responsiveness */
    .table {
        font-size: 0.9rem;
    }

    .table thead {
        font-size: 0.85rem;
    }

    .table td {
        padding: 0.75rem 0.5rem;
    }

    /* Text utilities */
    .text-muted {
        font-size: 0.9rem;
    }

    .small {
        font-size: 0.85rem;
    }

    /* Badge sizing */
    .badge {
        font-size: 0.8rem;
        padding: 0.35rem 0.6rem;
    }

    /* Alert sizing */
    .alert {
        font-size: 0.9rem;
        padding: 0.75rem;
    }

    /* Card padding */
    .card-body {
        padding: 1rem !important;
    }
}

@media (max-width: 576px) {
    /* Extra small screens */
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    /* Page header */
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 0.75rem;
    }

    /* Heading sizing */
    .h3 {
        font-size: 1rem;
    }

    .h4 {
        font-size: 0.95rem;
    }

    .mb-4 {
        margin-bottom: 1rem !important;
    }

    .mb-3 {
        margin-bottom: 0.75rem !important;
    }

    /* Button sizing */
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-sm {
        padding: 0.3rem 0.5rem;
        font-size: 0.7rem;
        min-height: 36px;
    }

    .btn-primary {
        width: 100%;
    }

    /* Table responsiveness - horizontal scroll */
    .table-responsive {
        -webkit-overflow-scrolling: touch;
    }

    .table {
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .table thead {
        font-size: 0.7rem;
    }

    .table td,
    .table th {
        padding: 0.4rem 0.25rem;
    }

    /* Table images */
    img {
        width: 35px !important;
        height: 50px !important;
    }

    /* Text utilities */
    .text-muted {
        font-size: 0.8rem;
    }

    .small {
        font-size: 0.7rem;
    }

    /* Badge sizing */
    .badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
        display: inline-block;
        word-wrap: break-word;
    }

    /* Alert sizing */
    .alert {
        font-size: 0.8rem;
        padding: 0.5rem;
        margin-bottom: 1rem;
    }

    .alert-dismissible .btn-close {
        padding: 0.3rem;
    }

    /* Icon sizing */
    .fa-3x {
        font-size: 1.5rem;
    }

    .fa-2x {
        font-size: 1.2rem;
    }

    .fa-lg {
        font-size: 0.95rem;
    }

    /* Text truncation */
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    /* Card padding */
    .card-body {
        padding: 0.75rem !important;
    }

    .p-3 {
        padding: 0.75rem !important;
    }

    /* Prevent horizontal overflow */
    body {
        overflow-x: hidden;
    }

    /* Action buttons wrapping */
    .text-end {
        text-align: left !important;
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    /* Row and column adjustments */
    .row {
        gap: 0.5rem !important;
    }
}
</style>
@endsection
