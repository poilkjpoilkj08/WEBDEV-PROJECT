@extends('base.base')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 mb-1">Your Cart</h1>
            <p class="text-muted">Review your selected books before checkout.</p>
        </div>
        <a href="{{ route('books.listing') }}" class="btn btn-outline-secondary">Continue Shopping</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($items->isEmpty())
        <div class="alert alert-warning">Your cart is empty. Add books from the listing page.</div>
    @else
        <div class="table-responsive rounded-4 overflow-hidden shadow-sm border">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Book</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td class="align-middle">
                            <div class="d-flex gap-3 align-items-center">
                                <img src="{{ $item['book']->cover_image_src }}" alt="{{ $item['book']->title }}" width="80" class="rounded-3 border bg-white" />
                                <div>
                                    <h5 class="mb-1">{{ $item['book']->title }}</h5>
                                    <p class="mb-0 text-muted">by {{ $item['book']->author?->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle text-center text-primary fw-bold">Rp {{ number_format($item['book']->price, 0, ',', '.') }}</td>
                        <td class="align-middle text-center">
                            <input type="number" name="quantities[{{ $item['book']->id }}]" value="{{ $item['quantity'] }}" min="0" max="{{ $item['book']->stock }}" class="form-control form-control-sm text-center quantity-input" style="width: 80px; margin: 0 auto;" data-book-id="{{ $item['book']->id }}" />
                            <small class="text-muted">Stock: {{ $item['book']->stock }}</small>
                        </td>
                        <td class="align-middle text-center fw-semibold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                        <td class="align-middle text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" data-book-id="{{ $item['book']->id }}">Remove</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4">
            <div></div>
            <div class="text-end">
                <p class="mb-1 text-muted">Total</p>
                <h3 class="mb-0">Rp {{ number_format($total, 0, ',', '.') }}</h3>
                <a href="{{ route('checkout.show') }}" class="btn btn-warning mt-3">Proceed to Checkout</a>
            </div>
        </div>

        <!-- Hidden form for removing items -->
        <form id="removeItemForm" action="{{ route('cart.remove') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" id="removeBookId" name="book_id">
        </form>

        <!-- Hidden update form -->
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

            // Auto-update quantities on change
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const container = document.getElementById('quantitiesContainer');
                    container.innerHTML = '';
                    document.querySelectorAll('.quantity-input').forEach(quantityInput => {
                        const clone = quantityInput.cloneNode(true);
                        clone.name = 'quantities[' + quantityInput.getAttribute('data-book-id') + ']';
                        container.appendChild(clone);
                    });
                    // Auto-submit the form
                    document.getElementById('updateCartForm').submit();
                });
            });
        });
        </script>
    @endif
</div>
@endsection
