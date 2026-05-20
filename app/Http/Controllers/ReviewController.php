<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required|string|max:1000',
        ]);

        $hasPurchased = Order::where('user_id', Auth::id())
            ->whereHas('order_details', function ($query) use ($book) {
                $query->where('book_id', $book->id);
            })
            ->exists();

        if (! $hasPurchased) {
            return back()->with('error', 'You can only review books you have purchased.');
        }

        $alreadyReviewed = Review::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'You have already reviewed this book.');
        }

        Review::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'rating' => $validated['rating'],
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Thank you! Your review has been submitted.');
    }
}
