@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-9">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">Edit Store Location</h1>
                    <p class="text-muted mb-0">{{ $store->name }}</p>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('admin.stores.update', $store->id) }}" method="POST">
                        @csrf @method('PUT')
                        @include('admin.store-locations.partials.form', ['store' => $store])
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Update Store
                            </button>
                            <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
