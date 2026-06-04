@extends('base.base')
@section('content')

<style>
    body {
        background-color: #ffffff; /* Unified pure clean white background theme */
        min-height: 100vh;
        padding-top: 100px;
    }

    /* Fixed Header Logic Compatibility */
    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }

    /* Author Form Panel Card Layout styling */
    .author-form-card {
        background: #ffffff;
        border: 1px solid #eef0f2;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .form-control:focus {
        border-color: #c25e25 !important;
        box-shadow: 0 0 0 0.25rem rgba(194, 94, 37, 0.15) !important;
    }

    /* Unified Orange Action Button Elements */
    .btn-soft-orange {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    
    .btn-soft-orange:hover, .btn-soft-orange:focus {
        background-color: #a64f1e !important;
        border-color: #a64f1e !important;
        color: #ffffff !important;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card author-form-card border-0 p-4">
                <div class="card-body">
                    <h1 class="h3 mb-1 fw-bold text-dark"><i class="fas fa-plus me-2 text-secondary"></i>Add New Author</h1>
                    <p class="text-muted small mb-4">Register a fresh verified catalog writer into the system database</p>

                    <form action="{{ route('authors.store') }}" method="POST">
                        @csrf

                        <!-- HIDDEN DATA BUFFERS: Bypasses strict database restrictions safely without fields in UI -->
                        <input type="hidden" name="email" value="author_{{ time() }}@bookhive.test">
                        <input type="hidden" name="phone" value="">
                        <input type="hidden" name="photo_url" value="">

                        <!-- Author Name Input -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Author Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="form-control rounded-3 @error('name') is-invalid @enderror" placeholder="e.g. George R.R. Martin">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Publisher Input -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Publisher</label>
                            <input type="text" name="publisher" value="{{ old('publisher') }}" class="form-control rounded-3 @error('publisher') is-invalid @enderror" placeholder="e.g. Bantam Spectra">
                            @error('publisher') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Biographical Description Textarea -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Bio</label>
                            <textarea name="bio" rows="5" class="form-control rounded-3 @error('bio') is-invalid @enderror" placeholder="Write a brief overview about the author's background, achievements, or history...">{{ old('bio') }}</textarea>
                            @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Control Actions Submission Deck -->
                        <div class="d-flex gap-2 border-top pt-3">
                            <button type="submit" class="btn btn-soft-orange px-4 rounded-pill fw-bold">
                                <i class="fas fa-check me-1.5"></i>Add Author
                            </button>
                            <a href="{{ route('admin.authors.index') }}" class="btn btn-outline-dark px-4 rounded-pill fw-semibold">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection