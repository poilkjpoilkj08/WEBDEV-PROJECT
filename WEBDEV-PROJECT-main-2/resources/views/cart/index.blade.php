@extends('base.base')
@section('content')

<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-image: url("{{ asset('images/bg1.jpg') }}") !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important; 
        background-position: center center !important; 
        background-size: cover !important; 
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

    /* GLASS BOX FOR HEADERS */
    .glass-header-box {
        background: rgba(255, 255, 255, 0.45); 
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 10px 28px;
        border-radius: 50px;
        display: inline-block;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    /* Remove core white background wrapper to let background image shine */
    .content-wrapper {
        background-color: transparent !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
    }

    /* Card lift hover animations */
    .hover-lift:hover {
        transform: translateY(-4px);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .hover-lift {
        transition: transform 0.2s ease;
    }
    
    .min-width-md-300 {
        min-width: 300px;
    }
</style>

<div class="container py-5 content-wrapper">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-5 gap-3">
        <div>
            <div class="glass-header-box mb-2">
                <h1 class="h3 mb-0 fw-bold text-dark">Your Cart</h1>
            </div>
            <p class="text-white bg-dark bg-opacity-25 d-inline-block px-3 py-1 rounded-pill small ms-2 backdrop-blur mb-0">
                Review your selected books before checkout.
            </p>
        </div>
        <a href="{{ route('books.listing') }}" class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm">
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
        <div class="alert alert-warning border-0 shadow-sm bg-white bg-opacity-90 rounded-4 p-4 text-center">
            <i class="fas fa-shopping-basket fa-2x text-warning mb-3 d-block"></i>
            <span class="fw-medium text-dark">Your cart is empty.</span> Add books from the listing page to get started.
        </div>
    @else
        <div class="table-responsive rounded-4 overflow-hidden shadow-lg border-0 bg-white bg-opacity-95 mb-4">
            <table class="table mb-0 align-middle">
                <thead class="bg-dark bg-opacity-10 text-dark">
                    <tr>
                        <th class="ps-4 py-3 fw-bold border-0">Book Details</th>
                        <th class="text-center py-3 fw-bold border-0">Price</th>
                        <th class="text-center py-3 fw-bold border-0">Quantity</th>
                        <th class="text-center py-3 fw-bold border-0">Subtotal</th>
                        <th class="pe-4 border-0"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr class="hover-lift border-bottom">
                        <td class="ps-4 py-4">
                            <div class="d-flex gap-3 align-items-center">
                                @if($item['book']->cover_image_url || $item['book']->cover_image_src)
                                    <img src="{{ $item['book']->cover_image_src }}" alt="{{ $item['book']->title }}" width="70" class="rounded-3 border shadow-sm bg-white" style="height: 95px; object-fit: cover;" />
                                @else
                                    <div class="rounded-3 border bg-light text-muted d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 95px;">
                                        <i class="fas fa-image opacity-30"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="mb-1 fw-bold text-dark fs-6">{{ $item['book']->title }}</h5>
                                    <p class="mb-0 text-muted small"><i class="fas fa-feather-alt me-1 text-secondary"></i>by {{ $item['book']->author?->name ?? 'Unknown Author' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-center text-dark fw-medium">Rp {{ number_format($item['book']->price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="d-inline-block">
                                <input type="number" name="quantities[{{ $item['book']->id }}]" value="{{ $item['quantity'] }}" min="0" max="{{ $item['book']->stock }}" class="form-control form-control-sm text-center quantity-input fw-bold border-secondary rounded-3 shadow-sm mb-1" style="width: 80px; margin: 0 auto;" data-book-id="{{ $item['book']->id }}" />
                                <small class="text-muted text-uppercase font-monospace" style="font-size: 0.7rem;">Stock: {{ $item['book']->stock }}</small>
                            </div>
                        </td>
                        <td class="text-center text-success fw-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                        <td class="pe-4 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item rounded-pill px-3 fw-bold shadow-sm" data-book-id="{{ $item['book']->id }}">
                                <i class="fas fa-trash-alt me-1"></i>Remove
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-end align-items-center gap-3 mt-4">
            <div class="card bg-white bg-opacity-95 shadow-lg border-0 rounded-4 p-4 text-end min-width-md-300">
                <span class="text-muted small text-uppercase fw-bold tracking-wider mb-1 d-block">Grand Total</span>
                <h2 class="mb-0 text-success fw-bold">Rp {{ number_format($total, 0, ',', '.') }}</h2>
                <a href="{{ route('checkout.show') }}" class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm mt-3 py-2 text-uppercase fs-6">
                    <i class="fas fa-shopping-bag me-2"></i>Proceed to Checkout
                </a>
            </div>
        </div>

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
            // Handle remove buttons
            document.querySelectorAll('.btn-remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const bookId = this.getAttribute('data-book-id');
                    document.getElementById('removeBookId').value = bookId;
                    document.getElementById('removeItemForm').submit();
                });
            });

            // AJAX quantity updates without page reload
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', async function() {
                    const bookId = this.getAttribute('data-book-id');
                    const quantity = parseInt(this.value) || 0;
                    const maxStock = parseInt(this.getAttribute('max'));
                    
                    // Validate stock
                    if (quantity > maxStock) {
                        this.value = maxStock;
                        showToast(`Maximum available stock is ${maxStock}`, 'warning', 3000);
                        return;
                    }
                    
                    try {
                        const formData = new FormData();
                        formData.append('quantities[' + bookId + ']', quantity);
                        
                        const response = await fetch('{{ route("cart.update") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            }
                        });
                        
                        if (response.ok) {
                            // Get all quantities and recalculate
                            const quantities = {};
                            document.querySelectorAll('.quantity-input').forEach(qInput => {
                                quantities[qInput.getAttribute('data-book-id')] = parseInt(qInput.value) || 0;
                            });
                            
                            // Update subtotal for this row
                            const rows = document.querySelectorAll('tbody tr');
                            let newGrandTotal = 0;
                            rows.forEach(row => {
                                const priceText = row.querySelector('td:nth-child(2)').textContent;
                                const price = parseInt(priceText.replace(/\D/g, ''));
                                const rowQuantityInput = row.querySelector('.quantity-input');
                                const rowQuantity = parseInt(rowQuantityInput.value) || 0;
                                const rowSubtotal = price * rowQuantity;
                                row.querySelector('td:nth-child(4)').textContent = 'Rp ' + rowSubtotal.toLocaleString('id-ID');
                                newGrandTotal += rowSubtotal;
                            });
                            
                            // Update grand total
                            const totalDisplay = document.querySelector('.tracking-wider').closest('div').querySelector('h2');
                            if (totalDisplay) {
                                totalDisplay.textContent = 'Rp ' + newGrandTotal.toLocaleString('id-ID');
                            }
                            
                            showToast('Cart updated', 'success', 2000);
                        }
                    } catch (error) {
                        console.error('Error updating cart:', error);
                        showToast('Error updating cart', 'error', 3000);
                    }
                });
            });
        });
        </script>
    @endif
</div>
@endsection