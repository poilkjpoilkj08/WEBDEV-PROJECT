@extends('base.base')
@section('styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-9">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">Add Store Location</h1>
                    <p class="text-muted mb-0">Fill in the details for the new physical store.</p>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('admin.stores.store') }}" method="POST">
                        @csrf
                        @include('admin.store-locations.partials.form', ['store' => null])
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Store
                            </button>
                            <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* ===== RESPONSIVE STYLES FOR STORE LOCATION CREATE FORM ===== */
    @media (max-width: 768px) {
        /* Container padding */
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Column sizing */
        .col-xl-9 {
            max-width: 100%;
        }

        /* Heading sizing */
        .h3, h3 {
            font-size: 1.1rem;
        }

        /* Card sizing */
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Button sizing */
        .btn {
            font-size: 0.9rem;
            padding: 0.6rem 0.9rem;
        }

        /* Gap utilities */
        .gap-2 {
            gap: 0.5rem !important;
        }

        /* Text utilities */
        .text-muted {
            font-size: 0.95rem;
        }

        /* Form controls */
        .form-control, .form-select {
            font-size: 0.95rem;
        }

        /* Row spacing */
        .row {
            gap: 1rem !important;
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

        /* Column sizing */
        .col-xl-9 {
            max-width: 100%;
        }

        .py-5 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        /* Header layout */
        .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .mb-4 {
            margin-bottom: 1rem !important;
        }

        .me-3 {
            margin-right: 0 !important;
            margin-bottom: 0.5rem !important;
        }

        /* Heading sizing */
        .h3, h3 {
            font-size: 1rem;
        }

        .h1 {
            font-size: 1rem;
        }

        /* Card styling */
        .card {
            border-radius: 12px;
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }

        .card-body {
            padding: 1rem;
        }

        /* Button sizing and layout */
        .btn {
            font-size: 0.8rem;
            padding: 0.65rem 1rem;
            min-height: 44px;
            width: 100%;
            border-radius: 0.375rem;
        }

        .px-4 {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        /* Button group layout */
        .gap-2 {
            gap: 0.5rem !important;
            flex-direction: column;
        }

        /* Alert sizing */
        .alert {
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
        }

        .alert ul {
            margin-bottom: 0 !important;
            padding-left: 1.25rem;
        }

        /* Text utilities */
        .text-muted {
            font-size: 0.85rem;
        }

        .small {
            font-size: 0.8rem;
        }

        /* Form controls */
        .form-control, .form-select {
            font-size: 16px;
            padding: 0.6rem 0.75rem;
        }

        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
        }

        /* Row spacing */
        .row {
            gap: 0.75rem !important;
        }

        /* Margin utilities */
        .mt-4 {
            margin-top: 1rem !important;
        }

        .me-2 {
            margin-right: 0.5rem !important;
        }

        /* Icon sizing */
        .fa {
            font-size: 0.9rem;
        }

        /* Prevent horizontal overflow */
        body {
            overflow-x: hidden;
        }

        /* Map container */
        #map {
            height: 250px !important;
            margin-bottom: 1rem;
        }
    }
</style>
@endsection
