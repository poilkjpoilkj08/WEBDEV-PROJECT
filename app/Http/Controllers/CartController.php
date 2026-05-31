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
            $cartItem = $cart[$book->id];
            $quantity = is_array($cartItem) ? $cartItem['quantity'] : $cartItem;
            $storeId = is_array($cartItem) ? $cartItem['store_id'] : null;
            
            return [
                'book' => $book,
                'quantity' => $quantity,
                'store_id' => $storeId,
                'subtotal' => $book->price * $quantity,
            ];
        });

        $total = $items->sum('subtotal');
        $stores = \App\Models\StoreLocation::all();

        return view('cart.index', compact('items', 'total', 'stores'));
    }

    public function add(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $quantity = max(1, $validated['quantity'] ?? 1);

        if ($book->status !== 'available') {
            $message = 'This book is not available for purchase.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return back()->with('error', $message);
        }

        // Use total stock from all stores, but cap at reasonable default (5) if user hasn't selected store yet
        $totalStock = $book->total_stock;
        // Limit initial cart quantity to 5 - users should adjust in cart view after selecting store
        $maxInitialQty = min($totalStock, 5);
        
        if ($quantity > $maxInitialQty) {
            $message = "Maximum $maxInitialQty copies can be added initially. Adjust quantity in cart after selecting a store.";
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return back()->with('warning', $message);
        }

        $cart = session('cart', []);
        $existingItem = $cart[$book->id] ?? null;
        
        // Handle both old format (just quantity) and new format (array with quantity and store_id)
        $existingQuantity = 0;
        if (is_array($existingItem)) {
            $existingQuantity = $existingItem['quantity'] ?? 0;
        } else {
            $existingQuantity = $existingItem ?? 0;
        }
        
        $newQuantity = min($totalStock, $existingQuantity + $quantity);
        
        // Store as array with quantity and store_id (store_id will be set in cart view)
        $cart[$book->id] = [
            'quantity' => $newQuantity,
            'store_id' => is_array($existingItem) ? $existingItem['store_id'] : null,
        ];
        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Book added to cart successfully. Select a store in cart to adjust quantity.',
                'cartCount' => count($cart)
            ]);
        }

        return back()->with('success', 'Book added to cart successfully. Select a store to adjust quantity.');
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:0',
            'store_ids' => 'required|array',
            'store_ids.*' => 'nullable|exists:store_locations,id',
        ]);

        $cart = session('cart', []);
        $errors = [];
        $warnings = [];

        foreach ($validated['quantities'] as $bookId => $quantity) {
            $book = Book::find($bookId);
            if (! $book) {
                continue;
            }

            if ($quantity <= 0) {
                unset($cart[$bookId]);
                continue;
            }

            $storeId = $validated['store_ids'][$bookId] ?? null;
            
            // Validate store is selected
            if (!$storeId) {
                $errors[] = sprintf('Please select a store location for "%s".', $book->title);
                continue;
            }
            
            // Validate store has stock
            $storeBook = $book->storeLocations()->where('store_location_id', $storeId)->first();
            $storeStock = $storeBook ? $storeBook->pivot->stock : 0;
            
            if ($storeStock <= 0) {
                $errors[] = sprintf('"%s" is not available at the selected store.', $book->title);
                continue;
            }
            
            if ($quantity > $storeStock) {
                $warnings[] = sprintf('Qty for "%s" reduced from %d to %d (store stock limit).', $book->title, $quantity, $storeStock);
                $quantity = $storeStock;
            }

            $cart[$bookId] = [
                'quantity' => $quantity,
                'store_id' => $storeId,
            ];
        }

        session(['cart' => $cart]);

        // Show errors first, then warnings
        if (! empty($errors)) {
            return back()->with('error', implode(' ', $errors));
        }

        if (! empty($warnings)) {
            return back()->with('warning', implode(' ', $warnings));
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
