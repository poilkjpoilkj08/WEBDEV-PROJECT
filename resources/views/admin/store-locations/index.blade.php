@extends('base.base')
@section('content')
<div class="container py-5">

    {{-- Page header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-store me-2 text-primary"></i>Manage Store Locations</h1>
            <p class="text-muted mb-0">Add, edit, and remove physical store locations.</p>
        </div>
        <a href="{{ route('admin.stores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Store
        </a>
    </div>

    {{-- Admin nav --}}

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($stores->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No store locations yet. <a href="{{ route('admin.stores.create') }}">Add the first one.</a></p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>City</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Hours</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stores as $store)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $store->name }}</td>
                                <td>{{ $store->city }}, {{ $store->country }}</td>
                                <td class="text-muted small">{{ Str::limit($store->address, 40) }}</td>
                                <td>{{ $store->phone ?? '—' }}</td>
                                <td class="small">{{ $store->opening_hours ?? '—' }}</td>
                                <td>
                                    @if($store->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.stores.edit', $store->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.stores.destroy', $store->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete {{ addslashes($store->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $stores->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
