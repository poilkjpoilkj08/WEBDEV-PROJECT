<?php

namespace App\Http\Controllers;

use App\Models\StoreLocation;
use App\Models\Book;
use Illuminate\Http\Request;

class StoreLocationController extends Controller
{
    /**
     * Return JSON of stores that carry a specific book — used by the map.
     */
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

    /**
     * Return ALL active store locations as JSON.
     */
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
}
