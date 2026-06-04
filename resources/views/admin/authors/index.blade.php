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
                <div class="p-3">{{ $authors->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection