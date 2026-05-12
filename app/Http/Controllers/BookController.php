<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    // Display home page with featured books
    public function index(): \Illuminate\View\View
    {
        return view('home', [
            'featured_books' => Book::where('is_featured', '=', 1)
                ->where('status', '=', 'available')
                ->with(['author', 'category'])
                ->limit(6)
                ->get(),
            'book_categories' => BookCategory::with('books')
                ->get(),
            'total_books' => Book::where('status', '=', 'available')->count(),
            'authors_count' => Author::where('is_active', '=', 1)->count(),
            'categories_count' => BookCategory::count(),
        ]);
    }

    // Display all books listing
    public function listing(Request $request): \Illuminate\View\View
    {
        $query = Book::where('status', '=', 'available')->with(['author', 'category']);

        // Search filters
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('author')) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->author . '%');
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('language')) {
            $query->where('language', '=', $request->language);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', '=', $request->category_id);
        }

        $books = $query->paginate(12);

        return view('books.listing', [
            'books' => $books,
            'book_categories' => BookCategory::all(),
            'min_price' => Book::where('status', '=', 'available')->min('price'),
            'max_price' => Book::where('status', '=', 'available')->max('price'),
        ]);
    }

    // Display single book details
    public function show($id): \Illuminate\View\View
    {
        $book = Book::with(['author', 'category'])->findOrFail($id);
        $similar_books = Book::where('status', '=', 'available')
            ->where('category_id', '=', $book->category_id)
            ->where('id', '!=', $id)
            ->limit(4)
            ->get();

        // Get previous book (id < current, last one)
        $previous_book = Book::where('status', '=', 'available')
            ->where('id', '<', $id)
            ->orderBy('id', 'desc')
            ->first();

        // Get next book (id > current, first one)
        $next_book = Book::where('status', '=', 'available')
            ->where('id', '>', $id)
            ->orderBy('id', 'asc')
            ->first();

        return view('books.show', [
            'book' => $book,
            'similar_books' => $similar_books,
            'previous_book' => $previous_book,
            'next_book' => $next_book,
        ]);
    }

    // Search properties
    public function search(Request $request): \Illuminate\View\View
    {
        return $this->listing($request);
    }

    // Show create form
    public function create_form(): \Illuminate\View\View
    {
        return view('books.create-form', [
            'book_categories' => BookCategory::all(),
            'authors' => Author::where('is_active', '=', 1)->get(),
        ]);
    }

    // Store book
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        if(!Gate::allows('insert-book')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'isbn' => 'nullable|string',
            'pages' => 'nullable|integer',
            'language' => 'required|string',
            'publication_year' => 'nullable|integer',
            'publisher' => 'nullable|string',
            'author_id' => 'nullable|exists:authors,id',
            'category_id' => 'required|exists:book_categories,id',
            'cover_image_url' => 'nullable|string',
            'weight_grams' => 'nullable|numeric',
            'is_featured' => 'nullable|boolean',
        ]);

        Book::create($validated);

        return redirect()->route('books.listing')
            ->with('success', 'Book added successfully!');
    }

    // Show edit form
    public function edit_form($id): \Illuminate\View\View
    {
        $book = Book::findOrFail($id);
        return view('books.edit-form', [
            'book' => $book,
            'book_categories' => BookCategory::all(),
            'authors' => Author::where('is_active', '=', 1)->get(),
        ]);
    }

    // Update book
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        if(!Gate::allows('update-book')) {
            abort(403, 'Unauthorized action.');
        }

        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'isbn' => 'nullable|string',
            'pages' => 'nullable|integer',
            'language' => 'required|string',
            'publication_year' => 'nullable|integer',
            'publisher' => 'nullable|string',
            'status' => 'required|in:available,out_of_stock,discontinued',
            'author_id' => 'nullable|exists:authors,id',
            'category_id' => 'required|exists:book_categories,id',
            'cover_image_url' => 'nullable|string',
            'weight_grams' => 'nullable|numeric',
            'is_featured' => 'nullable|boolean',
        ]);

        $book->update($validated);

        return redirect()->route('books.show', $book->id)
            ->with('success', 'Book updated successfully!');
    }

    // Delete book
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        if(!Gate::allows('delete-book')) {
            abort(403, 'Unauthorized action.');
        }

        $book = Book::findOrFail($id);
        $book->delete();

        return redirect()->route('books.listing')
            ->with('success', 'Book deleted successfully!');
    }
}
