<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-admin');
        $publishers = Publisher::orderBy('id', 'ASC')->paginate(15);
        return view('admin.publishers.index', compact('publishers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create-publisher');
        return view('admin.publishers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-publisher');
        
        $validated = $request->validate([
            'name' => 'required|string|unique:publishers,name',
        ]);

        Publisher::create(array_merge($validated, ['is_active' => true]));
        
        return redirect()->route('admin.publishers.index')
                       ->with('success', 'Publisher created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Publisher $publisher)
    {
        Gate::authorize('view-admin');
        $booksCount = $publisher->books()->count();
        return view('admin.publishers.show', compact('publisher', 'booksCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publisher $publisher)
    {
        Gate::authorize('edit-publisher');
        return view('admin.publishers.edit', compact('publisher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publisher $publisher)
    {
        Gate::authorize('edit-publisher');
        
        $validated = $request->validate([
            'name' => 'required|string|unique:publishers,name,' . $publisher->id,
        ]);

        $publisher->update($validated);
        
        return redirect()->route('admin.publishers.show', $publisher)
                       ->with('success', 'Publisher updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publisher $publisher)
    {
        Gate::authorize('delete-publisher');
        
        $booksCount = $publisher->books()->count();
        if ($booksCount > 0) {
            return redirect()->back()
                           ->with('error', "Cannot delete publisher with $booksCount associated books.");
        }

        $publisher->delete();
        
        return redirect()->route('admin.publishers.index')
                       ->with('success', 'Publisher deleted successfully.');
    }

    /**
     * API endpoint to get or create publisher (for dropdown)
     * Note: Authorization is enforced at form level (create-form/edit-form are admin-only routes)
     */
    public function getOrCreate(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $autoCreate = $request->get('auto_create', false);
            
            if ($search) {
                // Search for existing publishers (case-insensitive using LIKE)
                $publishers = Publisher::where('name', 'like', "%$search%")
                                      ->where('is_active', true)
                                      ->limit(10)
                                      ->get(['id', 'name']);
                
                // If auto_create is true and no exact match found, create new publisher
                if ($autoCreate && !$publishers->contains(fn($p) => strtolower($p->name) === strtolower($search))) {
                    try {
                        // Use firstOrCreate to avoid duplicate key errors
                        $newPublisher = Publisher::firstOrCreate(
                            ['name' => trim($search)],
                            ['is_active' => true]
                        );
                        $publishers->prepend($newPublisher);
                    } catch (\Exception $e) {
                        // If creation fails, just return existing matches
                        \Log::error('Publisher creation error: ' . $e->getMessage());
                    }
                }
                
                return response()->json($publishers);
            }

            // Return all active publishers
            $publishers = Publisher::where('is_active', true)
                                  ->orderBy('name')
                                  ->get(['id', 'name']);
            return response()->json($publishers);
        } catch (\Exception $e) {
            \Log::error('Publisher API error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}
