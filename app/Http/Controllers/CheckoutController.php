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
    // Shipping methods with flat rates (in IDR)
    const SHIPPING_METHODS = [
        'jne_reg'    => ['name' => 'JNE Regular (3-5 days)',    'cost' => 15000],
        'jne_yes'    => ['name' => 'JNE YES (1-2 days)',        'cost' => 30000],
        'jnt_reg'    => ['name' => 'J&T Regular (2-4 days)',    'cost' => 12000],
        'sicepat'    => ['name' => 'SiCepat BEST (2-3 days)',   'cost' => 13000],
        'pos_biasa'  => ['name' => 'Pos Indonesia (5-7 days)',  'cost' => 9000],
        'gosend'     => ['name' => 'GoSend Same Day',           'cost' => 25000],
        'grab_instant'=> ['name'=> 'GrabExpress Instant',       'cost' => 28000],
        'store_pickup'=> ['name'=> 'Store Pickup (Free)',        'cost' => 0],
    ];

    public function show()
    {
        $cart = session('cart', []);
        $books = Book::whereIn('id', array_keys($cart))->get();

        $items = $books->map(function (Book $book) use ($cart) {
            return [
                'book'     => $book,
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
            'customer_name'      => 'nullable|string|max:255',
            'shipping_name'      => 'required|string|max:255',
            'shipping_phone'     => 'required|string|max:50',
            'shipping_address'   => 'required|string|max:500',
            'shipping_city'      => 'required|string|max:100',
            'shipping_province'  => 'required|string|max:100',
            'shipping_postal_code'=> 'required|string|max:20',
            'shipping_country'   => 'nullable|string|max:100',
            'shipping_method'    => 'required|string|in:' . implode(',', array_keys(self::SHIPPING_METHODS)),
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        $books = Book::whereIn('id', array_keys($cart))->get();
        $items = [];
        $subtotal = 0;

        foreach ($books as $book) {
            $quantity = $cart[$book->id] ?? 0;
            if ($quantity < 1) continue;
            if ($quantity > $book->stock) {
                return response()->json(['error' => sprintf('Only %d copies of "%s" available.', $book->stock, $book->title)], 400);
            }
            $items[] = ['book' => $book, 'quantity' => $quantity, 'subtotal' => $book->price * $quantity];
            $subtotal += $book->price * $quantity;
        }

        if (empty($items)) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        $shippingMethod = self::SHIPPING_METHODS[$validated['shipping_method']];
        $shippingCost   = $shippingMethod['cost'];
        $grandTotal     = $subtotal + $shippingCost;

        $invoiceNumber = 'BH-' . now()->format('YmdHis') . '-' . rand(100, 999);

        $order = Order::create([
            'invoice_number'      => $invoiceNumber,
            'user_id'             => Auth::id(),
            'customer_name'       => $validated['customer_name'] ?? Auth::user()->name,
            'total_price'         => $subtotal,
            'status'              => 'pending',
            'payment_url'         => null,
            'shipping_name'       => $validated['shipping_name'],
            'shipping_phone'      => $validated['shipping_phone'],
            'shipping_address'    => $validated['shipping_address'],
            'shipping_city'       => $validated['shipping_city'],
            'shipping_province'   => $validated['shipping_province'],
            'shipping_postal_code'=> $validated['shipping_postal_code'],
            'shipping_country'    => $validated['shipping_country'] ?? 'Indonesia',
            'shipping_method'     => $shippingMethod['name'],
            'shipping_cost'       => $shippingCost,
            'shipping_status'     => 'pending',
        ]);

        foreach ($items as $item) {
            OrderDetail::create([
                'order_id'   => $order->id,
                'book_id'    => $item['book']->id,
                'book_title' => $item['book']->title,
                'quantity'   => $item['quantity'],
                'price'      => $item['book']->price,
                'subtotal'   => $item['subtotal'],
            ]);
            $item['book']->decrement('stock', $item['quantity']);
        }

        $snapToken = null;
        $serverKey = config('midtrans.server_key');

        if (!empty($serverKey)) {
            Config::$serverKey    = $serverKey;
            Config::$isProduction = filter_var(config('midtrans.is_production', false), FILTER_VALIDATE_BOOLEAN);
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            $midtransItems = array_map(function ($item) {
                return [
                    'id'       => (string)$item['book']->id,
                    'price'    => (int)$item['book']->price,
                    'quantity' => (int)$item['quantity'],
                    'name'     => substr($item['book']->title, 0, 50),
                ];
            }, $items);

            // Add shipping as a line item
            if ($shippingCost > 0) {
                $midtransItems[] = [
                    'id'       => 'SHIPPING',
                    'price'    => (int)$shippingCost,
                    'quantity' => 1,
                    'name'     => substr($shippingMethod['name'], 0, 50),
                ];
            }

            $transaction = [
                'transaction_details' => [
                    'order_id'     => $invoiceNumber,
                    'gross_amount' => (int)$grandTotal,
                ],
                'item_details'     => $midtransItems,
                'customer_details' => [
                    'first_name'      => Auth::user()->name,
                    'email'           => Auth::user()->email,
                    'phone'           => $validated['shipping_phone'],
                    'shipping_address'=> [
                        'first_name'  => $validated['shipping_name'],
                        'phone'       => $validated['shipping_phone'],
                        'address'     => $validated['shipping_address'],
                        'city'        => $validated['shipping_city'],
                        'postal_code' => $validated['shipping_postal_code'],
                        'country_code'=> 'IDN',
                    ],
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($transaction);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to create payment token: ' . $e->getMessage()], 500);
            }
        }

        return response()->json([
            'success'       => true,
            'snapToken'     => $snapToken,
            'orderId'       => $order->id,
            'invoiceNumber' => $invoiceNumber,
        ]);
    }

    public function callback(Request $request)
    {
        $serverKey  = config('midtrans.server_key');
        $notifBody  = file_get_contents('php://input');
        $notifData  = json_decode($notifBody, true);

        if ($notifData) {
            $signature  = $request->input('signature_key');
            $orderId    = $notifData['order_id'];
            $statusCode = $notifData['status_code'];
            $grossAmount= $notifData['gross_amount'];
            $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($signature === $signatureKey) {
                $order = Order::where('invoice_number', $orderId)->first();
                if ($order) {
                    $txStatus = $notifData['transaction_status'];
                    if ($statusCode == 200 || $statusCode == 201) {
                        if ($txStatus === 'capture' || $txStatus === 'settlement') {
                            $order->status          = 'paid';
                            $order->paid_at         = now();
                            $order->shipping_status = 'processing';
                            $order->save();
                        }
                    } elseif ($txStatus === 'pending') {
                        $order->status = 'pending';
                        $order->save();
                    } elseif (in_array($txStatus, ['deny', 'cancel', 'expire'])) {
                        $order->status = 'cancelled';
                        $order->save();
                        foreach ($order->order_details as $detail) {
                            $detail->book->increment('stock', $detail->quantity);
                        }
                    }
                }
            }
        }
        return response()->json(['status' => 'ok']);
    }

    public static function shippingMethods(): array
    {
        return self::SHIPPING_METHODS;
    }
}
