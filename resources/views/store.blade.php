@extends('base.base')
@if(session('success')) 
   <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-4 shadow" style="z-index: 1050; width: auto; max-width: 90%; padding-right: 2.5rem;" role="alert" id="successAlert">
      <strong>Success!</strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="cursor: pointer;"></button>
   </div>
   <script>
      (function() {
         setTimeout(function() {
            const alertElement = document.getElementById('successAlert');
            if (alertElement) {
               alertElement.classList.remove('show');
               setTimeout(function() {
                  alertElement.remove();
               }, 150);
            }
         }, 4000);
      })();
   </script>
@endif
@section('content')
<h2>This is Store Page</h2>

@can('insert-product')
<a href="{{ route('product.insert-form') }}" class="btn btn-primary">Insert New Product</a>
@endcan

<div class="row row-cols-1 row-cols-md-3 g-4">

    
@foreach ($products as $product)
<div class="col">
<div class="card">
    <img src="{{ $product->image_path ? asset('product_image/' . $product->image_path) : 'https://placehold.co/200x200?text=No+Image' }}" class="card-img-top" alt="{{ $product->name }}" style="object-fit: cover; height: 200px;">
<div class="card-body d-flex flex-column">
<h5 class="card-title"> {{ $product->name }}</h5>
<p class="card-text"><i>{{ $product->product_category->name }}</i></p>
<p class="card-text">Rp {{ number_format($product->price, 2) }}</p>
<p class="card-text flex-grow-1">{{ $product->details }}</p>
<!-- Add to Cart Trigger -->
                  <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addToCartModal{{ $product->id }}" @if($product->stock < 1) disabled @endif>
                     {{ $product->stock > 0 ? 'Add to Cart' : 'Out of Stock' }}
                  </button>

                  <!-- Add to Cart Modal -->
                  <div class="modal fade" id="addToCartModal{{ $product->id }}" tabindex="-1" aria-labelledby="addToCartModalLabel{{ $product->id }}" aria-hidden="true">
                     <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                           <div class="modal-header border-bottom-0 pb-0">
                              <h5 class="modal-title fs-6 fw-bold text-truncate" id="addToCartModalLabel{{ $product->id }}">{{ $product->name }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                           </div>
                           <form action="{{ route('add_to_cart', $product->id) }}" method="POST" id="addToCartForm{{ $product->id }}">
                              @csrf
                              <div class="modal-body text-center pt-2">
                                 <p class="text-muted small mb-3">Available Stock: <strong class="{{ $product->stock < 5 ? 'text-danger' : 'text-success' }}">{{ $product->stock }}</strong></p>
                                 <div class="input-group mb-4 mx-auto" style="max-width: 140px;">
                                    <button class="btn btn-outline-secondary px-3" type="button" onclick="const input = this.nextElementSibling; if(input.value > 1) input.value--">-</button>
                                    <input type="number" name="quantity" class="form-control text-center bg-white px-1" value="1" min="1" max="{{ $product->stock }}" readonly>
                                    <button class="btn btn-outline-secondary px-3" type="button" onclick="const input = this.previousElementSibling; const maxQty = {{ $product->stock }}; if(input.value < maxQty) input.value++">+</button>
                                 </div>
                              </div>
                              <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                 <button type="submit" class="btn btn-primary">Confirm Add</button>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
<div class="d-flex gap-2 mt-3">

@can('edit-product')
<a href="{{ route('product_edit_form', $product->id) }}" class="btn btn-warning">Edit Product</a>
@endcan

@can('delete-product')
<button type="button" class="btn btn-danger deleteBtn" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-delete-route="{{ route('delete_product', $product->id) }}">Delete</button>
@endcan
</div>
</div>
</div>
</div>
@endforeach
</div>
</div>

<!-- Delete Confirmation Dialog -->
<dialog id="deleteDialog" class="border rounded-3 shadow-lg" style="width: 400px; padding: 0;">
  <div style="padding: 20px 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
      <h5 style="margin: 0; font-weight: bold; color: #333;">Confirm Deletion</h5>
      <button type="button" id="closeDialog" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #999;">&times;</button>
    </div>
    <p style="margin: 0 0 10px 0; color: #333;">Are you sure you want to delete <strong id="productName"></strong>?</p>
    <p style="margin: 0; color: #dc3545; font-size: 0.875rem;">This action cannot be undone.</p>
  </div>
  
  <div style="padding: 15px 25px; border-top: 1px solid #dee2e6; display: flex; gap: 10px; justify-content: flex-end;">
    <button type="button" id="cancelDelete" class="btn btn-secondary" style="padding: 6px 20px;">Cancel</button>
    <button type="button" id="confirmDelete" class="btn btn-danger" style="padding: 6px 20px;">Yes, Delete</button>
  </div>
</dialog>

<!-- Hidden form for deletion -->
<form id="deleteForm" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
  let selectedProductId = null;
  let deleteRoute = null;
  const dialog = document.getElementById('deleteDialog');
  const closeBtn = document.getElementById('closeDialog');
  const cancelBtn = document.getElementById('cancelDelete');
  const confirmBtn = document.getElementById('confirmDelete');
  
  // Delete buttons
  document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      selectedProductId = this.getAttribute('data-product-id');
      deleteRoute = this.getAttribute('data-delete-route');
      const productName = this.getAttribute('data-product-name');
      document.getElementById('productName').textContent = productName;
      dialog.showModal();
    });
  });
  
  // Close dialog buttons
  closeBtn.addEventListener('click', function() {
    dialog.close();
  });
  
  cancelBtn.addEventListener('click', function() {
    dialog.close();
  });
  
  // Confirm delete
  confirmBtn.addEventListener('click', function() {
    if (selectedProductId && deleteRoute) {
      const form = document.getElementById('deleteForm');
      form.action = deleteRoute;
      form.submit();
    }
  });
</script>

@endsection