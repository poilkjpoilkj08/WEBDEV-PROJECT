@extends('base.base')
@section('content')
<div class="container py-5 text-center">
    <div class="py-5">
        <i class="fas fa-heart-broken fa-4x text-muted mb-4"></i>
        <h1 class="h3 fw-bold">You've been unsubscribed</h1>
        <p class="text-muted mb-4">We're sorry to see you go! You won't receive any more emails from BookHive.</p>
        <a href="{{ route('subscribe.plans') }}" class="btn btn-outline-primary me-2">Resubscribe</a>
        <a href="{{ route('home') }}" class="btn btn-primary">Back to Books</a>
    </div>
</div>
@endsection
