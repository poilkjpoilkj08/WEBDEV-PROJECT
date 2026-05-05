<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuthorController extends Controller
{
    // Display all authors
    public function index()
    {
        $authors = Author::where('is_active', '=', 1)
            ->with('books')
            ->paginate(12);

        return view('authors.index', [
            'authors' => $authors,
        ]);
    }

    // Display single author details
    public function show($id)
    {
        $author = Author::with('books')->findOrFail($id);

        return view('authors.show', [
            'author' => $author,
            'books' => $author->books()->where('status', '=', 'available')->paginate(6),
        ]);
    }

    // Show create form
    public function create_form()
    {
        return view('authors.create-form');
    }

    // Store author
    public function store(Request $request)
    {
        if(!Gate::allows('insert-author')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:authors',
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
            'photo_url' => 'nullable|string',
            'publisher' => 'nullable|string',
        ]);

        Author::create($validated);

        return redirect()->route('authors.index')
            ->with('success', 'Author added successfully!');
    }

    // Show edit form
    public function edit_form($id)
    {
        $author = Author::findOrFail($id);
        return view('authors.edit-form', [
            'author' => $author,
        ]);
    }

    // Update author
    public function update(Request $request, $id)
    {
        if(!Gate::allows('update-author')) {
            abort(403, 'Unauthorized action.');
        }

        $author = Author::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:authors,email,' . $id,
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
            'photo_url' => 'nullable|string',
            'publisher' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $author->update($validated);

        return redirect()->route('authors.show', $author->id)
            ->with('success', 'Author updated successfully!');
    }

    // Delete author
    public function destroy($id)
    {
        if(!Gate::allows('delete-author')) {
            abort(403, 'Unauthorized action.');
        }

        $author = Author::findOrFail($id);
        $author->delete();

        return redirect()->route('authors.index')
            ->with('success', 'Author deleted successfully!');
    }
}
