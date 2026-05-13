<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function show()
    {
        $cart = session('cart', []);
        $books = Book::whereIn('id', array_keys($cart))->get();

        $items = $books->map(function (Book $book) use ($cart) {
            return [
                'book' => $book,
                'quantity' => $cart[$book->id] ?? 0,
                'subtotal' => $book->price * ($cart[$book->id] ?? 0),
            ];
        });

        $total = $items->sum('subtotal');

        if ($total <= 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $clientKey = config('midtrans.client_key');

        return view('checkout.checkout', compact('items', 'total', 'clientKey'));
    }

    public function process(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        $books = Book::whereIn('id', array_keys($cart))->get();
        $items = [];
        $total = 0;

        foreach ($books as $book) {
            $quantity = $cart[$book->id] ?? 0;
            if ($quantity < 1) {
                continue;
            }

            if ($quantity > $book->stock) {
                return response()->json(['error' => sprintf('There are only %d copies of "%s" available.', $book->stock, $book->title)], 400);
            }

            $items[] = [
                'book' => $book,
                'quantity' => $quantity,
                'subtotal' => $book->price * $quantity,
            ];
            $total += $book->price * $quantity;
        }

        if (empty($items)) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        $invoiceNumber = 'BH-' . now()->format('YmdHis') . '-' . rand(100, 999);

        $order = Order::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => Auth::id(),
            'customer_name' => $validated['customer_name'] ?? Auth::user()->name,
            'total_price' => $total,
            'status' => 'pending',
            'payment_url' => null,
        ]);

        foreach ($items as $item) {
            OrderDetail::create([
                'order_id' => $order->id,
                'book_id' => $item['book']->id,
                'book_title' => $item['book']->title,
                'quantity' => $item['quantity'],
                'price' => $item['book']->price,
                'subtotal' => $item['subtotal'],
            ]);

            $item['book']->decrement('stock', $item['quantity']);
        }

        $snapToken = null;
        $serverKey = config('midtrans.server_key');

        if (! empty($serverKey)) {
            Config::$serverKey = $serverKey;
            Config::$isProduction = filter_var(config('midtrans.is_production', false), FILTER_VALIDATE_BOOLEAN);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $transaction = [
                'transaction_details' => [
                    'order_id' => $invoiceNumber,
                    'gross_amount' => (int)$total,
                ],
                'item_details' => array_map(function ($item) {
                    return [
                        'id' => (string)$item['book']->id,
                        'price' => (int)$item['book']->price,
                        'quantity' => (int)$item['quantity'],
                        'name' => substr($item['book']->title, 0, 50),
                    ];
                }, $items),
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
            ];

            try {
                $snap = Snap::getSnapToken($transaction);
                $snapToken = $snap;
            } catch (\Exception $exception) {
                return response()->json(['error' => 'Failed to create payment token: ' . $exception->getMessage()], 500);
            }
        }

        $order->payment_url = null;
        $order->save();

        return response()->json([
            'success' => true,
            'snapToken' => $snapToken,
            'orderId' => $order->id,
            'invoiceNumber' => $invoiceNumber,
        ]);
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $notifBody = file_get_contents("php://input");
        $notifData = json_decode($notifBody, true);

        if ($notifData) {
            // Verify signature
            $signature = $request->input('signature_key');
            $orderId = $notifData['order_id'];
            $statusCode = $notifData['status_code'];
            $grossAmount = $notifData['gross_amount'];

            $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($signature === $signatureKey) {
                $order = Order::where('invoice_number', $orderId)->first();

                if ($order) {
                    if ($statusCode == 200 || $statusCode == 201) {
                        // Payment successful
                        if ($notifData['transaction_status'] == 'capture' || $notifData['transaction_status'] == 'settlement') {
                            $order->status = 'paid';
                            $order->paid_at = now();
                            $order->save();
                        }
                    } elseif ($notifData['transaction_status'] == 'pending') {
                        $order->status = 'pending';
                        $order->save();
                    } elseif ($notifData['transaction_status'] == 'deny' || $notifData['transaction_status'] == 'cancel' || $notifData['transaction_status'] == 'expire') {
                        $order->status = 'cancelled';
                        $order->save();

                        // Restore stock
                        foreach ($order->order_details as $detail) {
                            $detail->book->increment('stock', $detail->quantity);
                        }
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
