@php $active = $active ?? ''; @endphp
<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="{{ route('books.create-form') }}"
       class="btn btn-sm {{ $active === 'books' ? 'btn-dark' : 'btn-outline-dark' }}">
        <i class="fas fa-book me-1"></i> Manage Books
    </a>
    <a href="{{ route('authors.create-form') }}"
       class="btn btn-sm {{ $active === 'authors' ? 'btn-dark' : 'btn-outline-dark' }}">
        <i class="fas fa-user-pen me-1"></i> Manage Authors
    </a>
    <a href="{{ route('admin.stores.index') }}"
       class="btn btn-sm {{ $active === 'stores' ? 'btn-dark' : 'btn-outline-dark' }}">
        <i class="fas fa-store me-1"></i> Manage Stores
    </a>
</div>
