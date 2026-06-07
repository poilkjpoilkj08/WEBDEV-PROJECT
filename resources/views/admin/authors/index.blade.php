@extends('base.base')
@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-user-pen me-2 text-primary"></i>Manage Authors</h1>
            <p class="text-muted mb-0">{{ $authors->total() }} authors in the system</p>
        </div>
        <a href="{{ route('authors.create-form') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Author
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
            @if($authors->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-user-pen fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No authors yet. <a href="{{ route('authors.create-form') }}">Add the first one.</a></p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Books Published</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($authors as $index => $author)
                            <tr>
                                <td class="text-muted">{{ $author->id }}</td>
                                <td class="fw-semibold">{{ $author->name }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $author->books_count ?? $author->books->count() }} books
                                    </span>
                                </td>
                                <td>
                                    @if($author->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('authors.show', $author->id) }}" class="btn btn-sm btn-outline-secondary" title="View Profile">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('authors.edit-form', $author->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('authors.destroy', $author->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ addslashes($author->name) }}?')">
                                           @csrf @method('DELETE')
                                             <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
                <!-- Pagination Controls -->
                @if($authors->hasPages())
                <div class="d-flex justify-content-center align-items-center gap-3 mt-5 bg-light py-2 px-4 rounded-pill shadow-sm d-inline-flex mx-auto" style="border: 1px solid #eef0f2;">
                    @if($authors->onFirstPage())
                        <button class="btn btn-sm btn-link text-muted text-decoration-none" disabled style="font-size: 0.8rem;">
                            <i class="fas fa-chevron-left me-2"></i>Previous
                        </button>
                    @else
                        <a href="{{ $authors->appends(request()->query())->previousPageUrl() }}" class="btn btn-sm btn-link text-dark text-decoration-none fw-bold" style="font-size: 0.8rem;">
                            <i class="fas fa-chevron-left me-2" style="color: #c25e25;"></i>Previous
                        </a>
                    @endif

                    <div class="text-dark fw-medium small mx-2" style="font-size: 0.8rem;">
                        Page {{ $authors->currentPage() }} of {{ $authors->lastPage() }}
                    </div>

                    @if($authors->hasMorePages())
                        <a href="{{ $authors->appends(request()->query())->nextPageUrl() }}" class="btn btn-sm btn-link text-dark text-decoration-none fw-bold" style="font-size: 0.8rem;">
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
/* ===== RESPONSIVE STYLES FOR ADMIN AUTHORS INDEX PAGE ===== */
@media (max-width: 768px) {
    /* Container and spacing */
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }

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

    /* Gap utilities */
    .gap-1 {
        gap: 0.5rem !important;
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
        font-size: 0.65rem;
        min-height: 36px;
        white-space: nowrap;
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
        font-size: 0.9rem;
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
    }

    .d-flex.justify-content-end {
        flex-direction: column !important;
        gap: 0.5rem !important;
    }

    /* Gap utilities */
    .gap-1 {
        gap: 0.25rem !important;
    }

    /* Row and column adjustments */
    .row {
        gap: 0.5rem !important;
    }
}
</style>
@endsection