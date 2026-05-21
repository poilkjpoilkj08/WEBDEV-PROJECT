<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('book')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('wishlist.index', compact('wishlists'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already in your wishlist'
                ], 400);
            }
            return back()->with('warning', 'Already in your wishlist');
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Added to wishlist'
            ]);
        }

        return back()->with('success', 'Added to wishlist');
    }

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        Wishlist::where('user_id', Auth::id())
            ->where('book_id', $validated['book_id'])
            ->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Removed from wishlist'
            ]);
        }

        return back()->with('success', 'Removed from wishlist');
    }

    public function isInWishlist($bookId)
    {
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('book_id', $bookId)
            ->exists();

        return response()->json([
            'inWishlist' => $exists
        ]);
    }
}
