<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('home', [
            'featured_books'   => Book::where('is_featured', 1)->where('status', 'available')->with(['author', 'category'])->limit(6)->get(),
            'book_categories'  => BookCategory::with('books')->get(),
            'total_books'      => Book::where('status', 'available')->count(),
            'authors_count'    => Author::where('is_active', 1)->count(),
            'categories_count' => BookCategory::count(),
        ]);
    }

    public function listing(Request $request): \Illuminate\View\View
    {
        $query = Book::where('status', 'available')->with(['author', 'category']);
        if ($request->filled('title'))       $query->where('title', 'like', '%' . $request->title . '%');
        if ($request->filled('author'))      $query->whereHas('author', fn($q) => $q->where('name', 'like', '%' . $request->author . '%'));
        if ($request->filled('min_price'))   $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price'))   $query->where('price', '<=', $request->max_price);
        if ($request->filled('language'))    $query->where('language', $request->language);
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        return view('books.listing', [
            'books'          => $query->paginate(12),
            'book_categories'=> BookCategory::all(),
            'min_price'      => Book::where('status', 'available')->min('price'),
            'max_price'      => Book::where('status', 'available')->max('price'),
        ]);
    }

    public function show($id): \Illuminate\View\View
    {
        $book = Book::with(['author', 'category'])->findOrFail($id);
        return view('books.show', [
            'book'          => $book,
            'similar_books' => Book::where('status', 'available')->where('category_id', $book->category_id)->where('id', '!=', $id)->limit(4)->get(),
            'previous_book' => Book::where('status', 'available')->where('id', '<', $id)->orderByRaw('id DESC')->first(),
            'next_book'     => Book::where('status', 'available')->where('id', '>', $id)->orderByRaw('id ASC')->first(),
        ]);
    }

    public function search(Request $request): \Illuminate\View\View
    {
        return $this->listing($request);
    }

    /** Admin: paginated list of ALL books with edit/delete */
    public function admin_index(): \Illuminate\View\View
    {
        $books = Book::with(['author', 'category'])->orderByRaw('id DESC')->paginate(20);
        return view('admin.books.index', compact('books'));
    }

    public function create_form(): \Illuminate\View\View
    {
        return view('books.create-form', [
            'book_categories' => BookCategory::all(),
            'authors'         => Author::where('is_active', 1)->get(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('insert-book');
        $validated = $request->validate([
            'title'              => 'required|string',
            'description'        => 'nullable|string',
            'price'              => 'required|numeric',
            'isbn'               => 'nullable|string',
            'pages'              => 'nullable|integer',
            'language'           => 'required|string',
            'publication_year'   => 'nullable|integer',
            'publisher'          => 'nullable|string',
            'author_id'          => 'nullable|exists:authors,id',
            'category_id'        => 'required|exists:book_categories,id',
            'cover_image_url'    => 'nullable|string',
            'cover_image_file'   => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'weight_grams'       => 'nullable|numeric',
            'is_featured'        => 'nullable|boolean',
        ]);

        if ($request->hasFile('cover_image_file')) {
            $cover = $request->file('cover_image_file');
            $destinationPath = public_path('book_covers');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $fileName = time() . '_' . uniqid() . '.' . $cover->getClientOriginalExtension();
            $cover->move($destinationPath, $fileName);
            $validated['cover_image_url'] = '/book_covers/' . $fileName;
        }

        unset($validated['cover_image_file']);
        Book::create($validated);
        return redirect()->route('admin.books.index')->with('success', 'Book added successfully!');
    }

    public function edit_form($id): \Illuminate\View\View
    {
        return view('books.edit-form', [
          'book'            => Book::with('storeLocations')->findOrFail($id),
          'book_categories' => BookCategory::all(),
          'authors'         => Author::where('is_active', 1)->get(),
          'store_locations' => \App\Models\StoreLocation::orderBy('city')->get(),
        ]);
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('update-book');
        $book = Book::findOrFail($id);
        $validated = $request->validate([
            'title'              => 'required|string',
            'description'        => 'nullable|string',
            'price'              => 'required|numeric',
            'isbn'               => 'nullable|string',
            'pages'              => 'nullable|integer',
            'language'           => 'required|string',
            'publication_year'   => 'nullable|integer',
            'publisher'          => 'nullable|string',
            'status'             => 'required|in:available,out_of_stock,discontinued',
            'author_id'          => 'nullable|exists:authors,id',
            'category_id'        => 'required|exists:book_categories,id',
            'cover_image_url'    => 'nullable|string',
            'cover_image_file'   => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'weight_grams'       => 'nullable|numeric',
            'is_featured'        => 'nullable|boolean',
        ]);

        if ($request->hasFile('cover_image_file')) {
            $cover = $request->file('cover_image_file');
            $destinationPath = public_path('book_covers');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $fileName = time() . '_' . uniqid() . '.' . $cover->getClientOriginalExtension();
            $cover->move($destinationPath, $fileName);
            $validated['cover_image_url'] = '/book_covers/' . $fileName;
        }

        unset($validated['cover_image_file']);
        if (empty($validated['cover_image_url']) && !$request->hasFile('cover_image_file')) {
           unset($validated['cover_image_url']);
        }
        $book->update($validated);
        $storeStock = collect($request->input('store_stock', []))->mapWithKeys(fn($qty, $id) => [$id => ['stock' => max(0, (int)$qty)]])->filter(fn($pivot) => $pivot['stock'] > 0)->toArray();
        $book->storeLocations()->sync($storeStock);
        return redirect()->route('admin.books.index')->with('success', 'Book updated successfully!');
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete-book');
        Book::findOrFail($id)->delete();
        return redirect()->route('admin.books.index')->with('success', 'Book deleted successfully!');
    }
}
