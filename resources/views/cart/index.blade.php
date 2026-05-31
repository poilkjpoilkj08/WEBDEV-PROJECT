@extends('base.base')
@section('content')

<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-color: #ffffff; /* Overriding global master background image to pure clean white */
        min-height: 100vh;
        padding-top: 40px;
    }

    /* Fixed Header Logic Compatibility */
    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }

    /* Table Component Alignment */
    .cart-table th {
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #4a5568;
    }

    /* Modern scannable card layout adjustments */
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-2px);
        background-color: #f8f9fa !important;
    }
    
    .min-width-md-300 {
        min-width: 300px;
    }

    /* Recommendation Cards Custom Aesthetics */
    .recommendation-card {
        border: 1px solid #eef0f2 !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .recommendation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }

    /* Unified Muted Soft Orange Buttons global style helper */
    .btn-soft-orange {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
        transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }
    
    .btn-soft-orange:hover, .btn-soft-orange:focus {
        background-color: #a64f1e !important;
        border-color: #a64f1e !important;
        color: #ffffff !important;
    }

    .btn-outline-soft-orange {
        background-color: transparent !important;
        border: 2px solid #c25e25 !important;
        color: #c25e25 !important;
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .btn-outline-soft-orange:hover, .btn-outline-soft-orange.active {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
    }
</style>

<div class="bg-white" style="position: relative; z-index: 4; padding-top: 40px;">
    <div class="container py-5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-5 gap-3">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">
                    <i class="fas fa-shopping-basket me-2" style="color: #c25e25;"></i>Your Cart
                </h1>
                <p class="text-muted small mb-0">Review your selected items and select store locations before checking out</p>
            </div>
            <a href="{{ route('books.listing') }}" class="btn btn-outline-soft-orange btn-sm fw-bold px-4 rounded-pill shadow-sm">
                <i class="fas fa-store me-2"></i>Continue Shopping
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
        @endif

        @if($items->isEmpty())
            <div class="alert bg-light border text-center p-5 rounded-4" style="border-color: #eef0f2 !important;">
                <i class="fas fa-shopping-cart text-muted fa-3x mb-3 opacity-40" style="color: #c25e25 !important;"></i>
                <h4 class="h6 fw-bold text-dark mb-2">Your shopping cart is empty</h4>
                <p class="text-secondary small mb-4 mx-auto" style="max-width: 400px;">Looks like you haven't added any books yet. Explore our catalog dashboard to fill your hive!</p>
                <a href="{{ route('books.listing') }}" class="btn btn-soft-orange btn-sm fw-bold px-4 rounded-pill shadow-sm">
                    Browse Books
                </a>
            </div>
        @else
            <div class="table-responsive rounded-4 border overflow-hidden shadow-sm bg-white mb-4" style="border-color: #eef0f2 !important;">
                <table class="table mb-0 align-middle cart-table">
                    <thead class="bg-light text-dark">
                        <tr class="border-bottom" style="border-color: #eef0f2 !important;">
                            <th class="ps-4 py-3 fw-bold border-0">Book Details</th>
                            <th class="text-center py-3 fw-bold border-0">Store Location</th>
                            <th class="text-center py-3 fw-bold border-0">Price</th>
                            <th class="text-center py-3 fw-bold border-0">Quantity</th>
                            <th class="text-center py-3 fw-bold border-0">Subtotal</th>
                            <th class="pe-4 border-0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr class="hover-lift border-bottom" style="border-color: #eef0f2 !important;">
                            <td class="ps-4 py-4">
                                <div class="d-flex gap-3 align-items-center">
                                    @if($item['book']->cover_image_url || $item['book']->cover_image_src)
                                        <img src="{{ $item['book']->cover_image_src }}" alt="{{ $item['book']->title }}" width="70" class="rounded-3 border shadow-sm bg-white" style="height: 95px; object-fit: contain; padding: 4px;" />
                                    @else
                                        <div class="rounded-3 border bg-light text-muted d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 95px;">
                                            <i class="fas fa-book fa-lg opacity-30" style="color: #c25e25;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="mb-1 fw-bold text-dark fs-6">{{ $item['book']->title }}</h5>
                                        <p class="mb-0 text-muted small"><i class="fas fa-feather-alt me-1 text-secondary"></i>by {{ $item['book']->author?->name ?? 'Unknown Author' }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="text-center">
                                <select name="store_ids[{{ $item['book']->id }}]" class="form-select form-select-sm store-selector rounded-3 fw-semibold border text-secondary shadow-sm" data-book-id="{{ $item['book']->id }}" style="width: 190px; margin: 0 auto; border-color: #ced4da !important;">
                                    <option value="">Select Store Location</option>
                                    @foreach($stores as $store)
                                        @php
                                            $storeBook = $item['book']->storeLocations()->where('store_location_id', $store->id)->first();
                                            $storeStock = $storeBook ? $storeBook->pivot->stock : 0;
                                            $isSelected = $item['store_id'] == $store->id;
                                        @endphp
                                        <option value="{{ $store->id }}" {{ $isSelected ? 'selected' : '' }} data-stock="{{ $storeStock }}">
                                            {{ $store->city }} (Stock: {{ $storeStock }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="store-status-msg d-block mt-1 animate slideIn" style="display: {{ $item['store_id'] ? 'none' : 'block' }};">
                                    <span class="text-danger fw-medium" style="font-size: 0.75rem;"><i class="fas fa-exclamation-triangle me-1"></i>Required</span>
                                </small>
                            </td>

                            <td class="text-center text-dark fw-semibold">Rp {{ number_format($item['book']->price, 0, ',', '.') }}</td>
                            
                            <td class="text-center">
                                <div class="d-inline-block">
                                    <input type="number" name="quantities[{{ $item['book']->id }}]" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm text-center quantity-input fw-bold border rounded-3 shadow-sm mb-1" style="width: 85px; margin: 0 auto; border-color: #ced4da !important;" data-book-id="{{ $item['book']->id }}" />
                                    @if($item['store_id'])
                                        @php
                                            $storeBook = $item['book']->storeLocations()->where('store_location_id', $item['store_id'])->first();
                                            $storeStock = $storeBook ? $storeBook->pivot->stock : 0;
                                        @endphp
                                        <small class="text-muted text-uppercase font-monospace" style="font-size: 0.65rem;">Max: {{ $storeStock }}</small>
                                    @else
                                        <small class="text-warning text-uppercase font-monospace" style="font-size: 0.65rem;">Select Store</small>
                                    @endif
                                </div>
                            </td>

                            <td class="text-center text-dark fw-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                            
                            <td class="pe-4 text-end">
                                <button type="button" class="btn btn-sm btn-light text-danger border btn-remove-item rounded-3 px-3 fw-medium shadow-sm" data-book-id="{{ $item['book']->id }}">
                                    <i class="fas fa-trash-alt me-1"></i>Remove
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-end align-items-center gap-3 mt-4 mb-5">
                <div class="card bg-white border shadow-sm rounded-4 p-4 text-end min-width-md-300" style="border-color: #eef0f2 !important;">
                    <span class="text-muted small text-uppercase fw-bold tracking-wider mb-1 d-block" style="font-size: 0.7rem;">Grand Total</span>
                    <h2 class="mb-0 fw-bold text-dark" style="font-size: 1.75rem;">Rp {{ number_format($total, 0, ',', '.') }}</h2>
                    <a href="{{ route('checkout.show') }}" class="btn btn-soft-orange w-100 fw-bold rounded-3 shadow-sm mt-3 py-2 text-uppercase" style="font-size: 0.85rem;">
                        <i class="fas fa-shopping-bag me-2"></i>Proceed to Checkout
                    </a>
                </div>
            </div>

            @php
                if (!isset($recommended_books) || $recommended_books->isEmpty()) {
                    $cartBookIds = collect($items)->pluck('book.id')->all();
                    $recommended_books = \App\Models\Book::where('status', 'available')
                        ->whereNotIn('id', $cartBookIds)
                        ->inRandomOrder()
                        ->take(4)
                        ->get();
                }
            @endphp

            @if($recommended_books->count() > 0)
            <div class="mt-5 pt-4 border-top" style="border-color: #eef0f2 !important;">
                <div class="mb-4">
                    <h3 class="h5 mb-1 fw-bold text-dark"><i class="fas fa-magic me-2 text-warning"></i>Add to Your Hive</h3>
                    <p class="text-muted small mb-0">Recommended reads tailored specifically for your collection</p>
                </div>

                <div class="row g-4">
                    @foreach($recommended_books as $recBook)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card h-100 border-0 overflow-hidden recommendation-card bg-white rounded-4 shadow-sm">
                            <div style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; padding: 12px; position: relative;">
                                @if($recBook->cover_image_url || $recBook->cover_image_src)
                                    <img src="{{ $recBook->cover_image_src }}" class="img-fluid rounded-2" alt="{{ $recBook->title }}" style="max-height: 100%; object-fit: contain;" />
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-book fa-2x mb-1 opacity-20" style="color: #c25e25;"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="card-body d-flex flex-column p-3 pt-2">
                                <h4 class="h6 fw-bold text-dark mb-1 text-truncate" style="font-size: 0.85rem;" title="{{ $recBook->title }}">{{ $recBook->title }}</h4>
                                <p class="text-muted mb-3 text-truncate" style="font-size: 0.75rem;">by {{ $recBook->author?->name ?? 'Unknown Author' }}</p>
                                
                                <div class="mt-auto">
                                    <div class="fw-bold text-dark mb-2 small">Rp {{ number_format($recBook->price, 0, ',', '.') }}</div>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('books.show', $recBook->id) }}" class="btn btn-light btn-sm text-dark border flex-grow-1 px-1 fw-medium rounded-3" style="font-size: 0.7rem;">
                                            <i class="fas fa-info-circle me-1 opacity-60"></i>Details
                                        </a>
                                        <form method="POST" action="{{ route('cart.add') }}" class="d-inline flex-grow-1 m-0 p-0">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $recBook->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-soft-orange btn-sm w-100 px-1 fw-bold rounded-3" style="font-size: 0.7rem;">
                                                <i class="fas fa-plus me-1"></i>Add
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <form id="removeItemForm" action="{{ route('cart.remove') }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" id="removeBookId" name="book_id">
            </form>

            <form id="updateCartForm" action="{{ route('cart.update') }}" method="POST" style="display: none;">
                @csrf
                <div id="quantitiesContainer"></div>
            </form>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Helper: Get selected store stock for a book
                function getStoreStockForBook(bookId) {
                    const storeSelector = document.querySelector(`.store-selector[data-book-id="${bookId}"]`);
                    if (!storeSelector || !storeSelector.value) return 0;
                    const selectedOption = storeSelector.options[storeSelector.selectedIndex];
                    return parseInt(selectedOption.getAttribute('data-stock')) || 0;
                }
                
                // Handle store selector changes
                document.querySelectorAll('.store-selector').forEach(selector => {
                    selector.addEventListener('change', function() {
                        const bookId = this.getAttribute('data-book-id');
                        const selectedOption = this.options[this.selectedIndex];
                        const storeStock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
                        const quantityInput = document.querySelector(`.quantity-input[data-book-id="${bookId}"]`);
                        const statusMsg = this.closest('td').querySelector('.store-status-msg');
                        const subtextEl = quantityInput?.closest('.d-inline-block').querySelector('small');
                        
                        // Update required message visibility
                        if (statusMsg) {
                            statusMsg.style.display = this.value ? 'none' : 'block';
                        }
                        
                        if (quantityInput) {
                            if (storeStock <= 0) {
                                quantityInput.value = 0;
                                quantityInput.disabled = true;
                                quantityInput.max = 0;
                                if (subtextEl) {
                                    subtextEl.className = "text-danger text-uppercase font-monospace";
                                    subtextEl.textContent = "Max: 0";
                                }
                                showToast('Selected store has no stock for this book', 'warning', 3000);
                            } else {
                                quantityInput.disabled = false;
                                quantityInput.max = storeStock;
                                if (subtextEl) {
                                    subtextEl.className = "text-muted text-uppercase font-monospace";
                                    subtextEl.textContent = "Max: " + storeStock;
                                }
                                if (parseInt(quantityInput.value) > storeStock) {
                                    quantityInput.value = storeStock;
                                    showToast(`Quantity adjusted to max available: ${storeStock}`, 'info', 2000);
                                } else if (parseInt(quantityInput.value) < 1) {
                                    quantityInput.value = 1;
                                }
                            }
                        }
                        
                        updateCheckoutButton();
                    });
                });
                
                // Validate quantity against store stock on quantity change
                document.querySelectorAll('.quantity-input').forEach(input => {
                    input.addEventListener('change', function() {
                        const bookId = this.getAttribute('data-book-id');
                        const storeStock = getStoreStockForBook(bookId);
                        const currentQty = parseInt(this.value) || 0;
                        
                        if (storeStock > 0 && currentQty > storeStock) {
                            this.value = storeStock;
                            showToast(`Cannot exceed store stock of ${storeStock}. Quantity adjusted.`, 'warning', 2000);
                        } else if (currentQty < 1) {
                            this.value = 1;
                            showToast('Quantity must be at least 1', 'warning', 2000);
                        }
                    });
                    
                    input.addEventListener('blur', function() {
                        const currentQty = parseInt(this.value) || 0;
                        if (currentQty < 1) {
                            this.value = 1;
                        }
                    });
                    
                    const bookId = input.getAttribute('data-book-id');
                    const storeStock = getStoreStockForBook(bookId);
                    if (storeStock > 0) {
                        input.max = storeStock;
                    }
                });

                // Handle remove buttons
                document.querySelectorAll('.btn-remove-item').forEach(button => {
                    button.addEventListener('click', function() {
                        if (confirm('Remove this book from your shopping cart?')) {
                            const bookId = this.getAttribute('data-book-id');
                            document.getElementById('removeBookId').value = bookId;
                            document.getElementById('removeItemForm').submit();
                        }
                    });
                });

                // Prevent checkout if stores are not selected
                function updateCheckoutButton() {
                    const allStoresSelected = Array.from(document.querySelectorAll('.store-selector')).every(selector => {
                        return selector.value !== '';
                    });
                    
                    const checkoutBtn = document.querySelector('a[href*="checkout"]');
                    if (checkoutBtn) {
                        if (!allStoresSelected) {
                            checkoutBtn.classList.add('disabled');
                            checkoutBtn.style.pointerEvents = 'none';
                            checkoutBtn.style.opacity = '0.6';
                        } else {
                            checkoutBtn.classList.remove('disabled');
                            checkoutBtn.style.pointerEvents = 'auto';
                            checkoutBtn.style.opacity = '1';
                        }
                    }
                }
                
                updateCheckoutButton();

                // AJAX quantity and store updates without page reload
                document.querySelectorAll('.quantity-input, .store-selector').forEach(input => {
                    input.addEventListener('change', async function() {
                        const allStoresSelected = Array.from(document.querySelectorAll('.store-selector')).every(selector => {
                            return selector.value !== '';
                        });
                        
                        if (!allStoresSelected) {
                            return;
                        }
                        
                        try {
                            const formData = new FormData();
                            
                            document.querySelectorAll('.quantity-input').forEach(qInput => {
                                const bookId = qInput.getAttribute('data-book-id');
                                formData.append('quantities[' + bookId + ']', SkinnerInt(qInput.value) || 0);
                            });
                            
                            document.querySelectorAll('.store-selector').forEach(selector => {
                                const bookId = selector.getAttribute('data-book-id');
                                formData.append('store_ids[' + bookId + ']', selector.value || '');
                            });
                            
                            function SkinnerInt(val) { return parseInt(val) || 0; }

                            const response = await fetch('{{ route("cart.update") }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                                }
                            });
                            
                            if (response.ok) {
                                const rows = document.querySelectorAll('tbody tr');
                                let newGrandTotal = 0;
                                rows.forEach(row => {
                                    const priceText = row.querySelector('td:nth-child(3)').textContent;
                                    const price = parseInt(priceText.replace(/\D/g, ''));
                                    const rowQuantityInput = row.querySelector('.quantity-input');
                                    const rowQuantity = parseInt(rowQuantityInput.value) || 0;
                                    const rowSubtotal = price * rowQuantity;
                                    row.querySelector('td:nth-child(5)').textContent = 'Rp ' + rowSubtotal.toLocaleString('id-ID');
                                    newGrandTotal += rowSubtotal;
                                });
                                
                                const totalDisplay = document.querySelector('.tracking-wider').closest('div').querySelector('h2');
                                if (totalDisplay) {
                                    totalDisplay.textContent = 'Rp ' + newGrandTotal.toLocaleString('id-ID');
                                }
                                
                                updateCheckoutButton();
                                showToast('Cart updated successfully', 'success', 2000);
                            } else {
                                showToast('Error updating cart data configurations', 'error', 3000);
                            }
                        } catch (error) {
                            console.error('Error updating cart:', error);
                            showToast('Error updating cart parameters', 'error', 3000);
                        }
                    });
                });
            });
            </script>
        @endif
    </div>
</div>
@endsection