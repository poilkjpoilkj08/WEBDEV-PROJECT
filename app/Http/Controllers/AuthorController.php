<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuthorController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('authors.index', [
            'authors' => Author::where('is_active', 1)->with('books')->paginate(12),
        ]);
    }

    public function show($id): \Illuminate\View\View
    {
        $author = Author::with('books')->findOrFail($id);
        return view('authors.show', [
            'author' => $author,
            'books'  => $author->books()->where('status', 'available')->paginate(6),
        ]);
    }

    /** Admin: paginated list of ALL authors with edit/delete */
    public function admin_index(): \Illuminate\View\View
    {
        $authors = Author::withCount('books')->orderByDesc('id')->paginate(20);
        return view('admin.authors.index', compact('authors'));
    }

    public function create_form(): \Illuminate\View\View
    {
        return view('authors.create-form');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('insert-author');
        $validated = $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|email|unique:authors',
            'phone'     => 'nullable|string',
            'bio'       => 'nullable|string',
            'photo_url' => 'nullable|string',
            'publisher' => 'nullable|string',
        ]);
        Author::create($validated);
        return redirect()->route('admin.authors.index')->with('success', 'Author added successfully!');
    }

    public function edit_form($id): \Illuminate\View\View
    {
        return view('authors.edit-form', ['author' => Author::findOrFail($id)]);
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('update-author');
        $author = Author::findOrFail($id);
        $validated = $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|email|unique:authors,email,' . $id,
            'phone'     => 'nullable|string',
            'bio'       => 'nullable|string',
            'photo_url' => 'nullable|string',
            'publisher' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $author->update($validated);
        return redirect()->route('admin.authors.index')->with('success', 'Author updated successfully!');
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete-author');
        Author::findOrFail($id)->delete();
        return redirect()->route('admin.authors.index')->with('success', 'Author deleted successfully!');
    }
}
