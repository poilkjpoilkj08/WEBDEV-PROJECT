@php $active = $active ?? ''; @endphp
<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="{{ route('admin.books.index') }}"
       class="btn btn-sm {{ $active === 'books' ? 'btn-dark' : 'btn-outline-dark' }}">
        <i class="fas fa-book me-1"></i> Manage Books
    </a>
    <a href="{{ route('admin.stores.index') }}"
       class="btn btn-sm {{ $active === 'stores' ? 'btn-dark' : 'btn-outline-dark' }}">
        <i class="fas fa-store me-1"></i> Manage Stores
    </a>
    <a href="{{ route('admin.orders.index') }}"
       class="btn btn-sm {{ $active === 'orders' ? 'btn-dark' : 'btn-outline-dark' }}">
        <i class="fas fa-receipt me-1"></i> Manage Orders
    </a>
</div>
