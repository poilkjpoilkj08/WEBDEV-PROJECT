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

    @include('admin.partials.nav', ['active' => 'books'])

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
                                <th>#</th>
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
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ $book->cover_image_src }}" alt="{{ $book->title }}"
                                         style="width:40px;height:55px;object-fit:cover;border-radius:4px;">
                                </td>
                                <td class="fw-semibold">{{ Str::limit($book->title, 35) }}</td>
                                <td class="text-muted">{{ $book->author?->name ?? '—' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $book->category?->name ?? '—' }}</span></td>
                                <td>Rp {{ number_format($book->price, 0, ',', '.') }}</td>
                                <td>
                                    @if($book->stock > 10)
                                        <span class="text-success fw-semibold">{{ $book->stock }}</span>
                                    @elseif($book->stock > 0)
                                        <span class="text-warning fw-semibold">{{ $book->stock }}</span>
                                    @else
                                        <span class="text-danger fw-semibold">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($book->status === 'available')
                                        <span class="badge bg-success">Available</span>
                                    @elseif($book->status === 'out_of_stock')
                                        <span class="badge bg-warning text-dark">Out of Stock</span>
                                    @else
                                        <span class="badge bg-secondary">Discontinued</span>
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
                <div class="p-3">{{ $books->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
