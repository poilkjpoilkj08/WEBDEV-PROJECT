@extends('base.base')
@section("content")
<h2>This is My Home Page</h2>
<div class="row row-cols-1 row-cols-md-3 g-4">
@foreach ($product_categories as $product_category)
<div class="col">
<div class="card">
<div class="card-body">
<h5 class="card-title"> {{ $product_category->name }}</h5>
<p class="card-text"><i>{{ $product_category->description}}</i></p>
<p class="card-text">Total Products: {{ $product_category->products->count() }}</p>
</div>
</div>
</div>
@endforeach
</div>
@endsection
