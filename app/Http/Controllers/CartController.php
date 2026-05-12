<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $cart = session('cart', []);
        $books = Book::whereIn('id', array_keys($cart))->get();

        $items = $books->map(function (Book $book) use ($cart) {
            $quantity = $cart[$book->id] ?? 0;
            return [
                'book' => $book,
                'quantity' => $quantity,
                'subtotal' => $book->price * $quantity,
            ];
        });

        $total = $items->sum('subtotal');

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $quantity = max(1, $validated['quantity'] ?? 1);

        if ($book->status !== 'available') {
            return back()->with('error', 'This book is not available for purchase.');
        }

        if ($quantity > $book->stock) {
            return back()->with('error', 'Requested quantity exceeds current stock.');
        }

        $cart = session('cart', []);
        $existingQuantity = $cart[$book->id] ?? 0;
        $newQuantity = min($book->stock, $existingQuantity + $quantity);
        $cart[$book->id] = $newQuantity;
        session(['cart' => $cart]);

        return back()->with('success', 'Book added to cart successfully.');
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:0',
        ]);

        $cart = session('cart', []);
        $errors = [];

        foreach ($validated['quantities'] as $bookId => $quantity) {
            $book = Book::find($bookId);
            if (! $book) {
                continue;
            }

            if ($quantity <= 0) {
                unset($cart[$bookId]);
                continue;
            }

            if ($quantity > $book->stock) {
                $errors[] = sprintf('Maximum stock for "%s" is %d.', $book->title, $book->stock);
                $quantity = $book->stock;
            }

            $cart[$bookId] = $quantity;
        }

        session(['cart' => $cart]);

        if (! empty($errors)) {
            return back()->with('error', implode(' ', $errors));
        }

        return back()->with('success', 'Cart updated successfully.');
    }

    public function remove(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $cart = session('cart', []);
        unset($cart[$validated['book_id']]);
        session(['cart' => $cart]);

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(): \Illuminate\Http\JsonResponse
    {
        session()->forget('cart');
        return response()->json(['success' => true]);
    }
}
