@extends('base.base')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary bg-opacity-10 border-0 p-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus me-2 text-primary"></i>Add New Publisher
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.publishers.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">Publisher Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Gramedia">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Create Publisher
                            </button>
                            <a href="{{ route('admin.publishers.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ===== RESPONSIVE STYLES FOR PUBLISHER CREATE FORM ===== */
    @media (max-width: 768px) {
        /* Container padding */
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Column sizing */
        .col-lg-6 {
            max-width: 100%;
        }

        /* Card styling */
        .card {
            border-radius: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
        }

        .card-header {
            padding: 1.25rem !important;
        }

        .card-body {
            padding: 1.25rem !important;
        }

        /* Heading sizing */
        .h5 {
            font-size: 1rem;
        }

        /* Button sizing */
        .btn {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        /* Form controls */
        .form-control, .form-select, .form-control-lg {
            font-size: 0.95rem;
        }

        .form-label {
            font-size: 0.95rem;
        }

        /* Row spacing */
        .row {
            gap: 0.75rem;
        }

        .g-3 {
            gap: 0.75rem;
        }

        /* Margin utilities */
        .mb-4 {
            margin-bottom: 1rem !important;
        }

        .me-2 {
            margin-right: 0.5rem !important;
        }
    }

    @media (max-width: 576px) {
        /* Extra small screens */
        body {
            padding-left: 0;
            padding-right: 0;
        }

        /* Container padding */
        .container {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .py-5 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        /* Column sizing */
        .col-md-8, .col-lg-6 {
            max-width: 100%;
        }

        /* Card styling */
        .card {
            border-radius: 1rem;
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }

        .card-header {
            padding: 1rem !important;
        }

        .card-body {
            padding: 1rem !important;
        }

        /* Heading sizing */
        .h5 {
            font-size: 0.95rem;
        }

        .card-title {
            margin-bottom: 0 !important;
        }

        /* Button sizing and layout */
        .btn {
            padding: 0.65rem 1rem;
            font-size: 0.8rem;
            min-height: 44px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Button group */
        .d-flex {
            flex-direction: column;
            gap: 0.5rem;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }

        /* Form controls */
        .form-control, .form-select, .form-control-lg {
            font-size: 16px;
            padding: 0.6rem 0.75rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        /* Error feedback */
        .invalid-feedback {
            font-size: 0.75rem;
        }

        /* Row spacing */
        .row {
            gap: 0.5rem;
            flex-direction: column;
        }

        .g-3 {
            gap: 0.5rem !important;
        }

        .col-md-6 {
            max-width: 100%;
            flex-basis: 100%;
        }

        /* Margin utilities */
        .mb-4 {
            margin-bottom: 0.75rem !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .me-2 {
            margin-right: 0.25rem !important;
        }

        .text-danger {
            font-size: 0.9rem;
        }

        /* Icon sizing */
        .fa {
            font-size: 0.9rem;
        }

        /* Prevent horizontal overflow */
        body {
            overflow-x: hidden;
        }
    }
</style>
@endsection
