@extends('base.base')
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
                                <label class="form-label">Book Title *</label>
                                <input type="text" name="title" value="{{ old('title', $book->title) }}" required class="form-control @error('title') is-invalid @enderror">
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ISBN</label>
                                <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}" class="form-control @error('isbn') is-invalid @enderror">
                                @error('isbn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $book->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Price *</label>
                                <input type="number" name="price" step="0.01" value="{{ old('price', $book->price) }}" required class="form-control @error('price') is-invalid @enderror">
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pages</label>
                                <input type="number" name="pages" min="1" value="{{ old('pages', $book->pages) }}" class="form-control @error('pages') is-invalid @enderror">
                                @error('pages') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-4">
                                <label class="form-label">Language *</label>
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
                                    <option value="Other" {{ old('language', $book->language) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('language') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Publication Year</label>
                                <input type="number" name="publication_year" min="1000" max="{{ date('Y') + 1 }}" value="{{ old('publication_year', $book->publication_year) }}" class="form-control @error('publication_year') is-invalid @enderror">
                                @error('publication_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Weight (grams)</label>
                                <input type="number" name="weight_grams" min="1" value="{{ old('weight_grams', $book->weight_grams) }}" class="form-control @error('weight_grams') is-invalid @enderror">
                                @error('weight_grams') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Publisher</label>
                                <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}" class="form-control @error('publisher') is-invalid @enderror">
                                @error('publisher') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status *</label>
                                <select name="status" required class="form-select @error('status') is-invalid @enderror">
                                    <option value="available" {{ old('status', $book->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="out_of_stock" {{ old('status', $book->status) == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                    <option value="discontinued" {{ old('status', $book->status) == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Category *</label>
                                <select name="category_id" required class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($book_categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Author</label>
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
                            <div class="col-md-6">
                                <label class="form-label">Cover Image URL</label>
                                <input type="url" name="cover_image_url" value="{{ old('cover_image_url', $book->cover_image_url) }}" class="form-control @error('cover_image_url') is-invalid @enderror">
                                @error('cover_image_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cover Image File</label>
                                <input type="file" name="cover_image_file" accept="image/*" class="form-control @error('cover_image_file') is-invalid @enderror">
                                @error('cover_image_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Upload a new image to replace the current cover, or leave blank to keep the existing cover.</div>
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
@endsection
