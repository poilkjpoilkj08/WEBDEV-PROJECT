@extends('base.base')
@section('styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h1 class="h3 mb-4">Edit Book</h1>

                    <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Book Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="{{ old('title', $book->title) }}" required class="form-control @error('title') is-invalid @enderror">
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">ISBN</label>
                                <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}" class="form-control @error('isbn') is-invalid @enderror">
                                @error('isbn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $book->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" step="0.01" value="{{ old('price', $book->price) }}" required class="form-control @error('price') is-invalid @enderror">
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pages</label>
                                <input type="number" name="pages" min="1" value="{{ old('pages', $book->pages) }}" class="form-control @error('pages') is-invalid @enderror">
                                @error('pages') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Language <span class="text-danger">*</span></label>
                                <select name="language" required class="form-select @error('language') is-invalid @enderror">
                                    <option value="">Select Language</option>
                                    <option value="English" {{ old('language', $book->language) == 'English' ? 'selected' : '' }}>English</option>
                                    <option value="Spanish" {{ old('language', $book->language) == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                                    <option value="French" {{ old('language', $book->language) == 'French' ? 'selected' : '' }}>French</option>
                                    <option value="German" {{ old('language', $book->language) == 'German' ? 'selected' : '' }}>German</option>
                                    <option value="Italian" {{ old('language', $book->language) == 'Italian' ? 'selected' : '' }}>Italian</option>
                                    <option value="Portuguese" {{ old('language', $book->language) == 'Portuguese' ? 'selected' : '' }}>Portuguese</option>
                                    <option value="Chinese" {{ old('language', $book->language) == 'Chinese' ? 'selected' : '' }}>Chinese</option>
                                    <option value="Japanese" {{ old('language', $book->language) == 'Japanese' ? 'selected' : '' }}>Japanese</option>
                                    <option value="Korean" {{ old('language', $book->language) == 'Korean' ? 'selected' : '' }}>Korean</option>
                                </select>
                                @error('language') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Publication Year</label>
                                <input type="number" name="publication_year" min="1000" max="{{ date('Y') + 1 }}" value="{{ old('publication_year', $book->publication_year) }}" class="form-control @error('publication_year') is-invalid @enderror">
                                @error('publication_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Weight (grams)</label>
                                <input type="number" name="weight_grams" min="1" value="{{ old('weight_grams', $book->weight_grams) }}" class="form-control @error('weight_grams') is-invalid @enderror">
                                @error('weight_grams') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Publishers</label>
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        @if($book->publishers && $book->publishers->count() > 0)
                                            <div class="mb-3">
                                                <h6 class="text-muted mb-2">Current Publishers:</h6>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($book->publishers as $publisher)
                                                        <span class="badge bg-primary d-flex align-items-center gap-2">
                                                            {{ $publisher->name }}
                                                            <button type="button" class="btn-close btn-close-white remove-publisher" data-publisher-id="{{ $publisher->id }}" style="font-size: 0.7rem;"></button>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <hr>
                                        @else
                                            <p class="text-muted small mb-3">No publishers assigned yet.</p>
                                            <hr>
                                        @endif
                                        <div id="selectedPublishersContainer"></div>
                                        <div class="row g-2">
                                            <div class="col-md-10">
                                                <select id="publisherSelect" class="form-select">
                                                    <option value="">-- Select a publisher to add --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" id="addPublisherBtn" class="btn btn-outline-primary w-100">
                                                    <i class="fas fa-plus me-1"></i>Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="publisherIds" name="publisher_ids" value="{{ $book->publishers->pluck('id')->implode(',') }}">
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" required class="form-select @error('status') is-invalid @enderror">
                                    <option value="available" {{ old('status', $book->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="out_of_stock" {{ old('status', $book->status) == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" required class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($book_categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Author</label>
                                <select name="author_id" class="form-select @error('author_id') is-invalid @enderror">
                                    <option value="">Select Author</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id', $book->author_id) == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                                    @endforeach
                                </select>
                                @error('author_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Cover Image</label>
                                <input type="file" name="cover_image_file" accept="image/*" class="form-control @error('cover_image_file') is-invalid @enderror">
                                @error('cover_image_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Upload a new cover image to replace the current one, or leave blank to keep the existing cover.</div>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $book->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Mark as Featured Book</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h5 class="fw-semibold mb-3"><i class="fas fa-store me-2"></i>Store Stock</h5>
                            <div class="row g-2">
                                @foreach($store_locations as $store)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-3 border rounded-3 p-2">
                                        <span class="flex-grow-1 small fw-semibold">{{ $store->name }}<br><span class="text-muted fw-normal">{{ $store->city }}</span></span>
                                        <input type="number" name="store_stock[{{ $store->id }}]" min="0"
                                               value="{{ old('store_stock.' . $store->id, $book->storeLocations->find($store->id)?->pivot->stock ?? 0) }}"
                                               class="form-control form-control-sm" style="width: 90px;" placeholder="0">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="form-text">Set stock to 0 to remove a store from the map.</div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Book</button>
                            <a href="{{ route('books.show', $book->id) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load publishers and initialize
    let allPublishers = [];
    let selectedPublisherIds = new Set();
    
    // Initialize selected publishers from existing book data
    const initialPublisherIds = document.getElementById('publisherIds').value;
    if (initialPublisherIds) {
        initialPublisherIds.split(',').forEach(id => {
            if (id) selectedPublisherIds.add(parseInt(id));
        });
    }
    
    fetch('{{ route('api.publishers.get-or-create') }}')
        .then(response => response.json())
        .then(data => {
            allPublishers = data;
            updatePublisherOptions();
        })
        .catch(error => console.error('Error loading publishers:', error));
    
    function updatePublisherOptions() {
        const select = document.getElementById('publisherSelect');
        const currentOptions = Array.from(select.options).slice(1); // Skip first option
        currentOptions.forEach(option => option.remove());
        
        allPublishers.forEach(publisher => {
            if (!selectedPublisherIds.has(publisher.id)) {
                const option = document.createElement('option');
                option.value = publisher.id;
                option.text = publisher.name;
                select.appendChild(option);
            }
        });
    }
    
    function renderSelectedPublishers() {
        const container = document.getElementById('selectedPublishersContainer');
        const publisherIds = document.getElementById('publisherIds');
        
        // Create display for selected publishers that were just added
        const newlySelectedPublishers = allPublishers.filter(p => selectedPublisherIds.has(p.id));
        
        let html = '';
        if (newlySelectedPublishers.length > 0) {
            html = '<h6 class="text-muted mb-2">Selected Publishers:</h6><div class="d-flex flex-wrap gap-2">' +
                newlySelectedPublishers.map(p => `
                    <span class="badge bg-primary d-flex align-items-center gap-2">
                        ${p.name}
                        <button type="button" class="btn-close btn-close-white remove-publisher" data-publisher-id="${p.id}" style="font-size: 0.7rem;"></button>
                    </span>
                `).join('') + '</div>';
        }
        
        // If there are no newly selected, but we still have current ones, they display in the blade template
        if (newlySelectedPublishers.length === 0) {
            container.innerHTML = '';
        } else {
            container.innerHTML = html;
        }
        
        publisherIds.value = Array.from(selectedPublisherIds).join(',');
        
        // Add event listeners to remove buttons
        document.querySelectorAll('.remove-publisher').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const publisherId = parseInt(btn.getAttribute('data-publisher-id'));
                selectedPublisherIds.delete(publisherId);
                renderSelectedPublishers();
                updatePublisherOptions();
            });
        });
    }
    
    document.getElementById('addPublisherBtn').addEventListener('click', (e) => {
        e.preventDefault();
        const select = document.getElementById('publisherSelect');
        const publisherId = parseInt(select.value);
        
        if (publisherId) {
            selectedPublisherIds.add(publisherId);
            select.value = '';
            renderSelectedPublishers();
            updatePublisherOptions();
        }
    });
    
    // Status change handler: disable stock inputs when out of stock
    const statusSelect = document.querySelector('select[name="status"]');
    const storeStockInputs = document.querySelectorAll('input[name^="store_stock"]');
    
    function updateStockFieldsState() {
        const isOutOfStock = statusSelect.value === 'out_of_stock';
        
        storeStockInputs.forEach(input => {
            input.disabled = isOutOfStock;
            if (isOutOfStock) {
                input.value = '0';
                input.style.backgroundColor = '#f5f5f5';
                input.style.cursor = 'not-allowed';
            } else {
                input.style.backgroundColor = '';
                input.style.cursor = '';
            }
        });
    }
    
    statusSelect.addEventListener('change', updateStockFieldsState);
    
    // Initialize on page load
    updateStockFieldsState();
});
</script>

<style>
    /* ===== RESPONSIVE STYLES FOR BOOK EDIT FORM ===== */
    @media (max-width: 768px) {
        /* Container padding */
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Column sizing */
        .col-xl-10 {
            max-width: 100%;
        }

        /* Card styling */
        .card {
            box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Heading sizing */
        .h3 {
            font-size: 1.1rem;
        }

        /* Button sizing */
        .btn {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        /* Form controls */
        .form-control, .form-select {
            font-size: 0.95rem;
        }

        .form-label {
            font-size: 0.9rem;
        }

        /* Row spacing */
        .row {
            gap: 0.75rem;
        }

        .g-3 {
            gap: 0.75rem;
        }

        /* Margin utilities */
        .mb-3, .mb-4 {
            margin-bottom: 1rem !important;
        }

        .mt-3 {
            margin-top: 1rem !important;
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

        .py-5 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        /* Column sizing */
        .col-xl-10 {
            max-width: 100%;
        }

        .col-md-6, .col-md-4 {
            max-width: 100%;
            flex-basis: 100%;
        }

        /* Card styling */
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }

        .card-body {
            padding: 1rem;
        }

        /* Heading sizing */
        .h3 {
            font-size: 1rem;
            margin-bottom: 1rem !important;
        }

        /* Button sizing and layout */
        .btn {
            padding: 0.65rem 1rem;
            font-size: 0.8rem;
            min-height: 44px;
            width: auto;
        }

        .btn-outline-secondary {
            padding: 0.6rem 0.8rem;
        }

        .btn-primary, .btn-success {
            width: 100%;
        }

        /* Form controls */
        .form-control, .form-select {
            font-size: 16px;
            padding: 0.6rem 0.75rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        /* Error feedback */
        .invalid-feedback {
            font-size: 0.75rem;
        }

        /* Row and column layout */
        .row {
            gap: 0.5rem;
            flex-direction: column;
        }

        .g-3 {
            gap: 0.5rem;
        }

        /* Input group */
        .input-group {
            flex-direction: row;
        }

        .input-group .form-control {
            min-width: 0;
        }

        /* Dropdown */
        #publisherDropdown {
            max-height: 150px !important;
            max-width: 95vw !important;
        }

        /* Margin utilities */
        .mb-3 {
            margin-bottom: 0.75rem !important;
        }

        .mb-4 {
            margin-bottom: 1rem !important;
        }

        .mt-3 {
            margin-top: 0.75rem !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .text-danger {
            font-size: 0.9rem;
        }

        /* Small text */
        .small {
            font-size: 0.75rem;
        }

        /* Icon sizing */
        .fa {
            font-size: 0.9rem;
        }

        /* Prevent horizontal overflow */
        body {
            overflow-x: hidden;
        }
    }
</style>
@endsection
