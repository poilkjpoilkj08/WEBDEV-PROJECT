@extends('base.base')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-building me-2 text-primary"></i>Publisher Management</h1>
            <p class="text-muted mb-0">Manage book publishers</p>
        </div>
        <a href="{{ route('admin.publishers.create') }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus me-2"></i>Add Publisher
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($publishers->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">No publishers yet. <a href="{{ route('admin.publishers.create') }}">Create one</a>.</p>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background: #f8f4f0;">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Publisher Name</th>
                            <th>Books</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($publishers as $publisher)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $publisher->id }}</td>
                            <td class="fw-semibold">{{ $publisher->name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $publisher->books()->count() }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $publisher->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $publisher->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-outline-info btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#viewBooksModal{{ $publisher->id }}" title="View Books">
                                        <i class="fas fa-book me-1"></i>Books
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editPublisherModal{{ $publisher->id }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.publishers.destroy', $publisher) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this publisher?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Controls -->
        @if($publishers->hasPages())
        <div class="d-flex justify-content-center align-items-center gap-3 mt-5 bg-light py-2 px-4 rounded-pill shadow-sm d-inline-flex mx-auto" style="border: 1px solid #eef0f2;">
            @if($publishers->onFirstPage())
                <button class="btn btn-sm btn-link text-muted text-decoration-none" disabled style="font-size: 0.8rem;">
                    <i class="fas fa-chevron-left me-2"></i>Previous
                </button>
            @else
                <a href="{{ $publishers->appends(request()->query())->previousPageUrl() }}" class="btn btn-sm btn-link text-dark text-decoration-none fw-bold" style="font-size: 0.8rem;">
                    <i class="fas fa-chevron-left me-2" style="color: #c25e25;"></i>Previous
                </a>
            @endif

            <div class="text-dark fw-medium small mx-2" style="font-size: 0.8rem;">
                Page {{ $publishers->currentPage() }} of {{ $publishers->lastPage() }}
            </div>

            @if($publishers->hasMorePages())
                <a href="{{ $publishers->appends(request()->query())->nextPageUrl() }}" class="btn btn-sm btn-link text-dark text-decoration-none fw-bold" style="font-size: 0.8rem;">
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

    <!-- All Modals (Outside table structure) -->
    @foreach($publishers as $publisher)
        <!-- Edit Publisher Modal -->
        <div class="modal fade" id="editPublisherModal{{ $publisher->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Publisher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.publishers.update', $publisher->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Publisher Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ $publisher->name }}" required class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Books Modal -->
        <div class="modal fade" id="viewBooksModal{{ $publisher->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Books by {{ $publisher->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($publisher->books->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Book Title</th>
                                            <th>Author</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($publisher->books as $book)
                                        <tr>
                                            <td class="fw-semibold">{{ $book->title }}</td>
                                            <td>{{ $book->author?->name ?? 'N/A' }}</td>
                                            <td>Rp {{ number_format($book->price, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge {{ $book->status === 'available' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $book->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">No books published yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
/* ===== RESPONSIVE STYLES FOR ADMIN PUBLISHERS INDEX PAGE ===== */
@media (max-width: 768px) {
    /* Container and spacing */
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* Heading sizing */
    .h3 {
        font-size: 1.25rem;
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

    /* Card */
    .card {
        border-radius: 12px;
    }

    /* Gap utilities */
    .gap-2 {
        gap: 0.5rem !important;
    }
}

@media (max-width: 576px) {
    /* Extra small screens */
    .container-fluid {
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

    .h1, h1 {
        font-size: 1.1rem;
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
        font-size: 0.65rem;
        min-height: 36px;
        border-radius: 50px !important;
    }

    .btn-primary {
        width: 100%;
    }

    /* Table responsiveness - horizontal scroll */
    .table-responsive {
        -webkit-overflow-scrolling: touch;
    }

    .table {
        font-size: 0.7rem;
        white-space: nowrap;
    }

    .table thead {
        font-size: 0.65rem;
    }

    .table td,
    .table th {
        padding: 0.4rem 0.25rem;
    }

    .table th.ps-4,
    .table td.ps-4 {
        padding-left: 0.5rem !important;
    }

    .table th.pe-4,
    .table td.pe-4 {
        padding-right: 0.5rem !important;
    }

    /* Text utilities */
    .text-muted {
        font-size: 0.8rem;
    }

    .small {
        font-size: 0.65rem;
    }

    /* Badge sizing */
    .badge {
        font-size: 0.6rem;
        padding: 0.25rem 0.4rem;
        display: inline-block;
    }

    /* Alert sizing */
    .alert {
        font-size: 0.8rem;
        padding: 0.5rem;
        margin-bottom: 1rem;
    }

    .alert-dismissible .btn-close {
        padding: 0.2rem;
    }

    /* Icon sizing */
    .fa-3x {
        font-size: 1.5rem;
    }

    .fa-2x {
        font-size: 1.2rem;
    }

    .fa-lg {
        font-size: 0.9rem;
    }

    /* Text truncation */
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    /* Card */
    .card {
        border-radius: 10px;
        overflow: hidden;
    }

    /* Prevent horizontal overflow */
    body {
        overflow-x: hidden;
    }

    /* Action buttons wrapping */
    .text-end {
        text-align: left !important;
    }

    .d-flex.justify-content-end {
        flex-direction: column !important;
        gap: 0.25rem !important;
    }

    /* Gap utilities */
    .gap-2 {
        gap: 0.25rem !important;
    }

    /* Row and column adjustments */
    .row {
        gap: 0.5rem !important;
    }

    /* Links */
    a {
        word-break: break-word;
        font-size: 0.8rem;
    }

    /* Pagination */
    .mt-4 {
        margin-top: 1.5rem !important;
    }

    /* Links in table */
    .text-decoration-none {
        font-size: 0.75rem;
    }
}
</style>
@endsection
