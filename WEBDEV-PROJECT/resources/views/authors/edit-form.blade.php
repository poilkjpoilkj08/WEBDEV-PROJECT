@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h1 class="h3 mb-4">Edit Agent</h1>

                    <form action="{{ route('authors.update', $author->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Agent Name *</label>
                            <input type="text" name="name" value="{{ old('name', $agent->name) }}" required class="form-control @error('name') is-invalid @enderror">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" value="{{ old('email', $agent->email) }}" required class="form-control @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" value="{{ old('phone', $agent->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">License Number</label>
                            <input type="text" name="license_number" value="{{ old('license_number', $agent->license_number) }}" class="form-control @error('license_number') is-invalid @enderror">
                            @error('license_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" rows="4" class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $agent->bio) }}</textarea>
                            @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Photo URL</label>
                            <input type="text" name="photo_url" value="{{ old('photo_url', $agent->photo_url) }}" class="form-control @error('photo_url') is-invalid @enderror">
                            @error('photo_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $agent->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Agent</button>
                            <a href="{{ route('authors.show', $author->id) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
