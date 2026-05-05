@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h1 class="h3 mb-4">Add New Agent</h1>

                    <form action="{{ route('authors.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Agent Name *</label>
                            <input type="text" name="name" required class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" required class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">License Number</label>
                            <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror" value="{{ old('license_number') }}">
                            @error('license_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" rows="4" class="form-control @error('bio') is-invalid @enderror">{{ old('bio') }}</textarea>
                            @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Photo URL</label>
                            <input type="text" name="photo_url" class="form-control @error('photo_url') is-invalid @enderror" value="{{ old('photo_url') }}">
                            @error('photo_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Add Agent</button>
                            <a href="{{ route('authors.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
