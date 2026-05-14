<?php

namespace App\Http\Controllers;

use App\Models\StoreLocation;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StoreLocationController extends Controller
{
    public function forBook(int $bookId)
    {
        $book = Book::findOrFail($bookId);
        $stores = StoreLocation::active()
            ->whereHas('books', fn($q) => $q->where('book_id', $bookId))
            ->with(['books' => fn($q) => $q->where('book_id', $bookId)])
            ->get()
            ->map(fn($store) => [
                'id'            => $store->id,
                'name'          => $store->name,
                'address'       => $store->address,
                'city'          => $store->city,
                'latitude'      => $store->latitude,
                'longitude'     => $store->longitude,
                'phone'         => $store->phone,
                'opening_hours' => $store->opening_hours,
                'stock'         => $store->books->first()?->pivot->stock ?? 0,
            ]);
        return response()->json($stores);
    }

    public function all()
    {
        $stores = StoreLocation::active()->get()->map(fn($s) => [
            'id'            => $s->id,
            'name'          => $s->name,
            'address'       => $s->address,
            'city'          => $s->city,
            'latitude'      => $s->latitude,
            'longitude'     => $s->longitude,
            'phone'         => $s->phone,
            'opening_hours' => $s->opening_hours,
        ]);
        return response()->json($stores);
    }

    public function index()
    {
        $stores = StoreLocation::orderBy('city')->orderBy('name')->paginate(15);
        return view('admin.store-locations.index', compact('stores'));
    }

    public function create_form()
    {
        return view('admin.store-locations.create-form');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-stores');
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'address'       => 'required|string|max:500',
            'city'          => 'required|string|max:100',
            'country'       => 'required|string|max:100',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',
            'opening_hours' => 'nullable|string|max:255',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        StoreLocation::create($validated);
        return redirect()->route('admin.stores.index')->with('success', 'Store location added successfully!');
    }

    public function edit_form($id)
    {
        $store = StoreLocation::findOrFail($id);
        return view('admin.store-locations.edit-form', compact('store'));
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('manage-stores');
        $store = StoreLocation::findOrFail($id);
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'address'       => 'required|string|max:500',
            'city'          => 'required|string|max:100',
            'country'       => 'required|string|max:100',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',
            'opening_hours' => 'nullable|string|max:255',
        ]);
        $validated['is_active'] = $request->boolean('is_active', false);
        $store->update($validated);
        return redirect()->route('admin.stores.index')->with('success', 'Store location updated successfully!');
    }

    public function destroy($id)
    {
        Gate::authorize('manage-stores');
        StoreLocation::findOrFail($id)->delete();
        return redirect()->route('admin.stores.index')->with('success', 'Store location deleted successfully!');
    }
}
