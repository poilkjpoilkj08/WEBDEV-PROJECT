@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h1 class="display-6">Our Book Authors</h1>
        <p class="text-muted">Meet the talented authors who bring you the best books in our collection.</p>
    </div>

    <div class="row g-4">
        @forelse($agents as $agent)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                @if($agent->photo_url)
                <img src="{{ $agent->photo_url }}" class="card-img-top" alt="{{ $agent->name }}" style="height: 260px; object-fit: cover;" />
                @else
                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 260px;">👤</div>
                @endif
                <div class="card-body d-flex flex-column">
                    <h2 class="h5 mb-1">{{ $agent->name }}</h2>
                    @if($agent->license_number)
                    <p class="text-muted small mb-2">License: {{ $agent->license_number }}</p>
                    @endif
                    <div class="mb-3 p-3 bg-light rounded">
                        <p class="h4 mb-0 text-primary">{{ $agent->properties->count() }}</p>
                        <p class="small text-muted mb-0">Active Listings</p>
                    </div>
                    <p class="text-muted small mb-3">{{ Str::limit($agent->bio, 100) }}</p>
                    <div class="mb-3 small text-muted">
                        <p class="mb-1">📧 {{ $agent->email }}</p>
                        @if($agent->phone)
                        <p class="mb-0">📱 {{ $agent->phone }}</p>
                        @endif
                    </div>
                    <a href="{{ route('authors.show', $agent->id) }}" class="btn btn-primary mt-auto">View Profile</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning">No agents available.</div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $agents->links() }}
    </div>
</div>
@endsection
