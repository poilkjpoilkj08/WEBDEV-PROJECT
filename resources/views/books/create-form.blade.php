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
                    <h1 class="h3 mb-4">Add New Book</h1>

                    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Book Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="{{ old('title') }}" required class="form-control @error('title') is-invalid @enderror">
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">ISBN</label>
                                <input type="text" name="isbn" value="{{ old('isbn') }}" class="form-control @error('isbn') is-invalid @enderror">
                                @error('isbn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" step="0.01" value="{{ old('price') }}" required class="form-control @error('price') is-invalid @enderror">
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pages</label>
                                <input type="number" name="pages" min="1" value="{{ old('pages') }}" class="form-control @error('pages') is-invalid @enderror">
                                @error('pages') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Language <span class="text-danger">*</span></label>
                                <select name="language" required class="form-select @error('language') is-invalid @enderror">
                                    <option value="">Select Language</option>
                                    <option value="English" {{ old('language') == 'English' ? 'selected' : '' }}>English</option>
                                    <option value="Spanish" {{ old('language') == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                                    <option value="French" {{ old('language') == 'French' ? 'selected' : '' }}>French</option>
                                    <option value="German" {{ old('language') == 'German' ? 'selected' : '' }}>German</option>
                                    <option value="Italian" {{ old('language') == 'Italian' ? 'selected' : '' }}>Italian</option>
                                    <option value="Portuguese" {{ old('language') == 'Portuguese' ? 'selected' : '' }}>Portuguese</option>
                                    <option value="Chinese" {{ old('language') == 'Chinese' ? 'selected' : '' }}>Chinese</option>
                                    <option value="Japanese" {{ old('language') == 'Japanese' ? 'selected' : '' }}>Japanese</option>
                                    <option value="Korean" {{ old('language') == 'Korean' ? 'selected' : '' }}>Korean</option>
                                    <option value="Other" {{ old('language') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('language') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Publication Year</label>
                                <input type="number" name="publication_year" min="1000" max="{{ date('Y') + 1 }}" value="{{ old('publication_year') }}" class="form-control @error('publication_year') is-invalid @enderror">
                                @error('publication_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Weight (grams)</label>
                                <input type="number" name="weight_grams" min="1" value="{{ old('weight_grams') }}" class="form-control @error('weight_grams') is-invalid @enderror">
                                @error('weight_grams') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Publisher</label>
                                <input type="text" name="publisher" value="{{ old('publisher') }}" class="form-control @error('publisher') is-invalid @enderror">
                                @error('publisher') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" required class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($book_categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Author</label>
                                <select name="author_id" class="form-select @error('author_id') is-invalid @enderror">
                                    <option value="">Select Author</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                                    @endforeach
                                </select>
                                @error('author_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" required class="form-select @error('status') is-invalid @enderror">
                                    <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                    <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Cover Image <span class="text-danger">*</span></label>
                                <input type="file" name="cover_image_file" accept="image/*" required class="form-control @error('cover_image_file') is-invalid @enderror">
                                @error('cover_image_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Upload a high-quality cover image (JPEG, PNG, GIF, or WebP). Maximum 2MB.</div>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
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
                                               value="{{ old('store_stock.' . $store->id, 0) }}"
                                               class="form-control form-control-sm" style="width: 90px;" placeholder="0">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="form-text">Set stock per store location. Leave 0 if not available at that store.</div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">Add Book</button>
                            <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
