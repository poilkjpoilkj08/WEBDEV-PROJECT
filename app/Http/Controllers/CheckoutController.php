<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\StoreLocation;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderReceiptMail;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    // Shipping methods with flat rates (in IDR)
    const SHIPPING_METHODS = [
        'jne_reg'    => ['name' => 'JNE Regular (3-5 days)',    'base_cost' => 15000],
        'jne_yes'    => ['name' => 'JNE YES (1-2 days)',        'base_cost' => 30000],
        'jnt_reg'    => ['name' => 'J&T Regular (2-4 days)',    'base_cost' => 12000],
        'sicepat'    => ['name' => 'SiCepat BEST (2-3 days)',   'base_cost' => 13000],
        'pos_biasa'  => ['name' => 'Pos Indonesia (5-7 days)',  'base_cost' => 9000],
        'gosend'     => ['name' => 'GoSend Same Day',           'base_cost' => 25000],
        'grab_instant'=> ['name'=> 'GrabExpress Instant',       'base_cost' => 28000],
    ];

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $R * $c;
        
        return round($distance, 2);
    }

    /**
     * Calculate shipping cost based on distance and method
     * Cost per km: IDR 5000 per km (adjust as needed)
     */
    private function calculateShippingCost($basePrice, $distance, $method)
    {
        $costPerKm = 5000; // IDR per km
        $distanceCost = $distance * $costPerKm;
        
        // Total = base price + distance cost
        $totalCost = $basePrice + $distanceCost;
        
        return (int)$totalCost;
    }

    public function show()
    {
        $cart = session('cart', []);
        $books = Book::whereIn('id', array_keys($cart))->get();

        $items = $books->map(function (Book $book) use ($cart) {
            $cartItem = $cart[$book->id];
            $quantity = is_array($cartItem) ? $cartItem['quantity'] : $cartItem;
            $storeId = is_array($cartItem) ? $cartItem['store_id'] : null;
            
            return [
                'book'     => $book,
                'quantity' => $quantity,
                'store_id' => $storeId,
                'subtotal' => $book->price * $quantity,
            ];
        });

        $total = $items->sum('subtotal');

        if ($total <= 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Check if all stores are selected
        $unselectedStores = $items->filter(fn($item) => !$item['store_id'])->count();
        if ($unselectedStores > 0) {
            return redirect()->route('cart.index')->with('error', 'Please select a store location for all items before checkout.');
        }

        $clientKey = config('midtrans.client_key');
        $stores = StoreLocation::active()->get();
        $indonesianLocations = self::indonesianLocations();
        $provinces = self::indonesianProvinces();
        
        // Load user's saved address if they have one
        $user = Auth::user();
        $savedAddress = null;
        if ($user && $user->saved_latitude && $user->saved_longitude) {
            $savedAddress = [
                'latitude' => $user->saved_latitude,
                'longitude' => $user->saved_longitude,
                'street' => $user->saved_street,
                'postal_code' => $user->saved_postal_code,
                'province' => $user->saved_province,
                'city' => $user->saved_city,
                'district' => $user->saved_district,
            ];
        }

        return view('checkout.checkout', compact('items', 'total', 'clientKey', 'stores', 'indonesianLocations', 'provinces', 'savedAddress'));
    }

    public function process(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            \Log::info('Checkout process request', [
                'all_input' => $request->except('_token'),
            ]);

            $validated = $request->validate([
                'customer_name'          => 'nullable|string|max:255',
                'shipping_name'          => 'required|string|max:255',
                'shipping_phone'         => 'required|string|max:50',
                'shipping_address'       => 'required|string|max:500',
                'shipping_city'          => 'required|string|max:100',
                'shipping_province'      => 'required|string|max:100',
                'shipping_postal_code'   => 'required|string|max:20',
                'shipping_country'       => 'required|string|max:100',
                'shipping_latitude'      => 'required|numeric|between:-90,90',
                'shipping_longitude'     => 'required|numeric|between:-180,180',
                'shipping_method'        => 'required|string|in:' . implode(',', array_keys(self::SHIPPING_METHODS)),
                'store_ids'              => 'required|array',
                'store_ids.*'            => 'required|integer|exists:store_locations,id',
                'shipping_cost'          => 'nullable|numeric|min:0',
                'shipping_zone'          => 'nullable|string|in:A,B,C,D,E',
                'shipping_breakdown'     => 'nullable|json',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Checkout validation failed', [
                'errors' => $e->errors(),
            ]);
            
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'Please check all required fields',
                'errors' => $e->errors()
            ], 422);
        }

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        try {
            // CRITICAL: Wrap entire order creation in transaction with pessimistic locks
            return DB::transaction(function () use ($cart, $validated) {
                $books = Book::whereIn('id', array_keys($cart))
                    ->lockForUpdate()  // Pessimistic lock: prevents other transactions from reading
                    ->get();
                    
                $items = [];
                $subtotal = 0;
                $primaryStoreId = null;

                foreach ($books as $book) {
                    $cartItem = $cart[$book->id];
                    $quantity = is_array($cartItem) ? $cartItem['quantity'] : $cartItem;
                    $storeId = is_array($cartItem) ? $cartItem['store_id'] : null;
                    
                    if ($quantity < 1) continue;
                    
                    // Validate book status is 'available'
                    if ($book->status !== 'available') {
                        $statusMsg = $book->status === 'out_of_stock' ? 'out of stock' : $book->status;
                        return response()->json(['error' => sprintf('"%s" is currently %s.', $book->title, $statusMsg)], 400);
                    }
                    
                    // Validate store is selected for this book
                    if (!$storeId || !isset($validated['store_ids'][$book->id])) {
                        return response()->json(['error' => sprintf('Store location not selected for "%s".', $book->title)], 400);
                    }
                    
                    $storeId = $validated['store_ids'][$book->id];
                    
                    // Lock and validate store stock for this book
                    $storeBook = $book->storeLocations()
                        ->where('store_location_id', $storeId)
                        ->lockForUpdate()
                        ->first();
                    
                    $storeStock = $storeBook ? $storeBook->pivot->stock : 0;
                    
                    if ($storeStock <= 0) {
                        return response()->json(['error' => sprintf('"%s" is not available at the selected store.', $book->title)], 400);
                    }
                    
                    if ($quantity > $storeStock) {
                        return response()->json(['error' => sprintf('Only %d copies of "%s" available at this store.', $storeStock, $book->title)], 400);
                    }
                    
                    $items[] = [
                        'book' => $book,
                        'quantity' => $quantity,
                        'store_id' => $storeId,
                        'subtotal' => $book->price * $quantity
                    ];
                    $subtotal += $book->price * $quantity;
                    
                    // Set primary store for shipping calculation (use first store)
                    if (!$primaryStoreId) {
                        $primaryStoreId = $storeId;
                    }
                }

                if (empty($items)) {
                    return response()->json(['error' => 'Your cart is empty.'], 400);
                }

                $shippingMethod = self::SHIPPING_METHODS[$validated['shipping_method']];
                
                // CRITICAL: Use frontend-calculated shipping cost if provided
                // This ensures zone-based calculation from frontend is stored
                $shippingCost = $validated['shipping_cost'] ?? 0;
                $shippingZone = $validated['shipping_zone'] ?? 'C';
                $shippingBreakdown = null;
                
                if ($validated['shipping_breakdown']) {
                    try {
                        $shippingBreakdown = json_decode($validated['shipping_breakdown'], true);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to decode shipping breakdown', ['error' => $e->getMessage()]);
                    }
                }
                
                $grandTotal = $subtotal + $shippingCost;

                $invoiceNumber = 'BH-' . now()->format('YmdHis') . '-' . rand(100, 999);

                $order = Order::create([
                    'invoice_number'      => $invoiceNumber,
                    'user_id'             => Auth::id(),
                    'store_id'            => $primaryStoreId,
                    'customer_name'       => $validated['customer_name'] ?? Auth::user()->name,
                    'total_price'         => $subtotal,
                    'status'              => 'pending',
                    'payment_url'         => null,
                    'payment_processed'   => false,
                    'shipping_name'       => $validated['shipping_name'],
                    'shipping_phone'      => $validated['shipping_phone'],
                    'shipping_address'    => $validated['shipping_address'],
                    'shipping_city'       => $validated['shipping_city'],
                    'shipping_province'   => $validated['shipping_province'],
                    'shipping_postal_code'=> $validated['shipping_postal_code'],
                    'shipping_country'    => $validated['shipping_country'],
                    'shipping_method'     => $shippingMethod['name'],
                    'shipping_cost'       => $shippingCost,
                    'shipping_zone'       => $shippingZone,
                    'shipping_breakdown'  => $shippingBreakdown,
                    'shipping_status'     => 'pending',
                ]);

                foreach ($items as $item) {
                    OrderDetail::create([
                        'order_id'   => $order->id,
                        'book_id'    => $item['book']->id,
                        'store_id'   => $item['store_id'],
                        'book_title' => $item['book']->title,
                        'quantity'   => $item['quantity'],
                        'price'      => $item['book']->price,
                        'subtotal'   => $item['subtotal'],
                        'weight_grams' => $item['book']->weight_grams ?? 300,
                    ]);
                    
                    // IMPORTANT: Stock will be decremented ONLY after successful payment in markPaymentComplete()
                    // This prevents stock reduction if user cancels payment
                }

                $snapToken = null;
                $serverKey = config('midtrans.server_key');
                $clientKey = config('midtrans.client_key');

                if (empty($serverKey) || empty($clientKey)) {
                    \Log::warning('Midtrans configuration incomplete', [
                        'server_key_set' => !empty($serverKey),
                        'client_key_set' => !empty($clientKey),
                    ]);
                    return response()->json([
                        'success' => false,
                        'error' => 'Payment gateway not configured',
                        'details' => 'Midtrans keys are missing. Please contact administrator.'
                    ], 500);
                }

                try {
                    // Initialize Midtrans configuration
                    Config::$serverKey    = $serverKey;
                    Config::$clientKey    = $clientKey;
                    Config::$isProduction = filter_var(config('midtrans.is_production', false), FILTER_VALIDATE_BOOLEAN);
                    Config::$isSanitized  = true;
                    Config::$is3ds        = false;

                    $midtransItems = array_map(function ($item) {
                        return [
                            'id'       => (string)$item['book']->id,
                            'price'    => (int)round($item['book']->price),
                            'quantity' => (int)$item['quantity'],
                            'name'     => substr(trim($item['book']->title), 0, 50),
                        ];
                    }, $items);

                    // Add shipping as a line item
                    if ($shippingCost > 0) {
                        $midtransItems[] = [
                            'id'       => 'SHIPPING',
                            'price'    => (int)round($shippingCost),
                            'quantity' => 1,
                            'name'     => 'Shipping',
                        ];
                    }

                    // Validate that item total equals gross amount
                    $itemTotal = array_reduce($midtransItems, function ($carry, $item) {
                        return $carry + ($item['price'] * $item['quantity']);
                    }, 0);

                    if ($itemTotal !== (int)$grandTotal) {
                        \Log::warning('Midtrans amount mismatch', [
                            'itemTotal' => $itemTotal,
                            'grandTotal' => $grandTotal,
                            'difference' => $itemTotal - $grandTotal,
                        ]);
                    }

                    $transaction = [
                        'transaction_details' => [
                            'order_id'     => $invoiceNumber,
                            'gross_amount' => (int)$grandTotal,
                        ],
                        'item_details'     => $midtransItems,
                        'customer_details' => [
                            'first_name' => substr(Auth::user()->name ?? 'Customer', 0, 50),
                            'email'      => Auth::user()->email,
                            'phone'      => $validated['shipping_phone'],
                        ],
                        'billing_address' => [
                            'first_name'   => substr($validated['shipping_name'], 0, 50),
                            'phone'        => $validated['shipping_phone'],
                            'address'      => substr($validated['shipping_address'], 0, 100),
                            'city'         => substr($validated['shipping_city'], 0, 100),
                            'postal_code'  => $validated['shipping_postal_code'],
                            'country_code' => 'IDN',
                        ],
                    ];

                    // Retry Snap token generation up to 3 times with delays
                    $snapToken = null;
                    $attempts = 0;
                    $maxAttempts = 3;
                    
                    while ($snapToken === null && $attempts < $maxAttempts) {
                        try {
                            $snapToken = Snap::getSnapToken($transaction);
                            \Log::info('Snap token created successfully', ['invoice' => $invoiceNumber, 'attempt' => $attempts + 1]);
                            break;
                        } catch (\Exception $retryE) {
                            $attempts++;
                            \Log::warning('Snap token retry', [
                                'attempt' => $attempts,
                                'error' => $retryE->getMessage(),
                            ]);
                            
                            if ($attempts < $maxAttempts) {
                                // Wait before retrying (exponential backoff)
                                sleep(1 * $attempts);
                            } else {
                                throw $retryE;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Midtrans Snap Token Error', [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]);
                    
                    // Return user-friendly error message
                    $errorMsg = $e->getMessage();
                    if (strpos($errorMsg, 'Could not resolve host') !== false) {
                        $errorMsg = 'Payment gateway temporarily unavailable. Please try again in a moment.';
                    }
                    
                    return response()->json([
                        'error' => 'Failed to create payment token',
                        'details' => $errorMsg,
                        'debug_message' => 'Error: ' . $e->getMessage()
                    ], 500);
                }

                return response()->json([
                    'success'       => true,
                    'snapToken'     => $snapToken,
                    'orderId'       => $order->id,
                    'invoiceNumber' => $invoiceNumber,
                ]);
            }, 5);  // Maximum 5 transaction retries for deadlocks

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Checkout transaction deadlock or error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return response()->json(['error' => 'Database error - please retry checkout'], 500);
        } catch (\Exception $e) {
            \Log::error('Checkout process fatal error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return response()->json(['error' => 'Checkout failed - please try again'], 500);
        }
    }
    
    /**
     * Mark order as payment completed (called from frontend after successful payment)
     * CRITICAL: Idempotent - safe to call multiple times without double-decrementing stock
     */
    public function markPaymentComplete(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            return DB::transaction(function () use ($user, $request) {
                // Prefer specific order_id if provided (Pay Now on order history page)
                $orderId = $request->input('order_id');
                
                $query = Order::where('user_id', $user->id)
                    ->where('payment_processed', false)
                    ->lockForUpdate();  // Pessimistic lock
                
                if ($orderId) {
                    $order = $query->where('id', (int)$orderId)->first();
                } else {
                    // Fallback: find most recent unprocessed order (checkout flow)
                    $order = $query->latest('created_at')->first();
                }
                
                if (!$order) {
                    // Check if already processed (idempotency guard)
                    if ($orderId) {
                        $alreadyDone = Order::where('user_id', $user->id)
                            ->where('id', (int)$orderId)
                            ->where('payment_processed', true)
                            ->first();
                        if ($alreadyDone) {
                            return response()->json(['success' => true, 'message' => 'Payment already processed', 'isRetry' => true]);
                        }
                    }
                    return response()->json(['error' => 'Order not found'], 404);
                }

                // CRITICAL IDEMPOTENCY CHECK: Prevent double-decrementing on webhook retry
                if ($order->payment_processed) {
                    \Log::warning('Payment already processed for order', ['order_id' => $order->id]);
                    return response()->json([
                        'success' => true, 
                        'message' => 'Payment already processed',
                        'isRetry' => true
                    ]);
                }

                $updateData = [
                    'status'              => 'paid',
                    'shipping_status'     => 'processing',
                    'paid_at'             => now(),
                    'payment_processed'   => true,  // Mark as processed to prevent retries
                ];

                // Capture payment_method sent from the Midtrans onSuccess result object
                $paymentType = request()->input('payment_type');
                if ($paymentType && !$order->payment_method) {
                    $updateData['payment_method'] = $paymentType;
                }

                $order->update($updateData);
                
                \Log::info('Order status updated to PAID', [
                    'order_id' => $order->id,
                    'invoice' => $order->invoice_number,
                    'user_id' => $order->user_id,
                    'old_status' => 'pending',
                    'new_status' => 'paid',
                    'payment_processed' => true,
                    'paid_at' => now()->toDateTimeString(),
                ]);
                // Use locks to prevent concurrent stock modifications
                foreach ($order->order_details as $detail) {
                    $storeBook = $detail->book->storeLocations()
                        ->where('store_location_id', $detail->store_id)
                        ->lockForUpdate()  // Pessimistic lock on stock
                        ->first();
                    
                    if ($storeBook) {
                        $newStock = max(0, $storeBook->pivot->stock - $detail->quantity);
                        $detail->book->storeLocations()
                            ->updateExistingPivot($detail->store_id, ['stock' => $newStock]);
                        
                        \Log::info('Stock decremented', [
                            'book_id' => $detail->book_id,
                            'store_id' => $detail->store_id,
                            'quantity' => $detail->quantity,
                            'old_stock' => $storeBook->pivot->stock,
                            'new_stock' => $newStock,
                        ]);
                    }
                }
                
                // Send receipt email if user logged in via Google
                if ($user->google_id) {
                    try {
                        Mail::to($user->email)->send(new OrderReceiptMail($order));
                        \Log::info('Receipt email sent', [
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'email' => $user->email,
                        ]);
                    } catch (\Exception $emailError) {
                        \Log::warning('Failed to send receipt email', [
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'error' => $emailError->getMessage(),
                        ]);
                        // Don't fail the payment if email fails to send
                    }
                }
                
                return response()->json(['success' => true, 'message' => 'Payment marked complete']);
            }, 5);  // Maximum 5 transaction retries
            
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Payment processing database error', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Database error - payment may still be processing'], 500);
        } catch (\Exception $e) {
            \Log::error('Mark payment complete error', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update order'], 500);
        }
    }

    /**
     * Generate new snap token for pending order (retry payment)
     */
    public function generatePaymentToken(Request $request)
    {
        try {
            // Log detailed CSRF and session info for debugging
            \Log::info('generatePaymentToken called', [
                'path' => $request->path(),
                'method' => $request->method(),
                'has_auth' => auth()->check(),
                'user_id' => auth()->id(),
                'csrf_from_header' => $request->header('X-CSRF-TOKEN') ? substr($request->header('X-CSRF-TOKEN'), 0, 20) . '...' : 'MISSING',
                'csrf_from_session' => csrf_token() ? substr(csrf_token(), 0, 20) . '...' : 'MISSING',
                'session_id' => session()->getId(),
                'cookies' => array_keys($_COOKIE),
                'request_data' => $request->all(),
            ]);

            $user = Auth::user();
            if (!$user) {
                \Log::warning('generatePaymentToken: User not authenticated');
                return response()->json(['error' => 'Unauthorized - Not authenticated'], 401);
            }

            \Log::info('User authenticated', ['user_id' => $user->id]);

            try {
                $request->validate([
                    'order_id' => 'required|integer|exists:orders,id',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::warning('generatePaymentToken: Validation failed', [
                    'errors' => $e->errors(),
                    'order_id' => $request->order_id,
                ]);
                throw $e;
            }

            $order = Order::findOrFail($request->order_id);

            \Log::info('generatePaymentToken: Order found', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'order_user_id' => $order->user_id,
                'status' => $order->status,
            ]);

            // Verify order belongs to authenticated user
            if ($order->user_id != $user->id) {
                \Log::warning('generatePaymentToken: User not authorized for order', [
                    'user_id' => $user->id,
                    'order_user_id' => $order->user_id,
                    'order_id' => $order->id,
                ]);
                return response()->json(['error' => 'Unauthorized - Order does not belong to user'], 403);
            }

            // Only allow payment retry for pending orders
            if ($order->status !== 'pending') {
                return response()->json([
                    'error' => 'Cannot retry payment',
                    'message' => 'Only pending orders can be paid. Order status: ' . ucfirst($order->status)
                ], 422);
            }

            // Get order details with items
            $orderDetails = $order->order_details;
            if ($orderDetails->isEmpty()) {
                return response()->json(['error' => 'Order has no items'], 400);
            }

            $serverKey = config('midtrans.server_key');
            $clientKey = config('midtrans.client_key');

            if (!empty($serverKey) && !empty($clientKey)) {
                try {
                    // Initialize Midtrans configuration
                    Config::$serverKey    = $serverKey;
                    Config::$clientKey    = $clientKey;
                    Config::$isProduction = filter_var(config('midtrans.is_production', false), FILTER_VALIDATE_BOOLEAN);
                    Config::$isSanitized  = true;
                    Config::$is3ds        = false;

                    $midtransItems = [];
                    foreach ($orderDetails as $detail) {
                        $midtransItems[] = [
                            'id'       => (string)$detail->book_id,
                            'price'    => (int)round($detail->price),
                            'quantity' => (int)$detail->quantity,
                            'name'     => substr(trim($detail->book_title), 0, 50),
                        ];
                    }

                    // Add shipping as a line item
                    if ($order->shipping_cost > 0) {
                        $midtransItems[] = [
                            'id'       => 'SHIPPING',
                            'price'    => (int)round($order->shipping_cost),
                            'quantity' => 1,
                            'name'     => substr(trim($order->shipping_method), 0, 50),
                        ];
                    }

                    $grossAmount = (int)($order->total_price + $order->shipping_cost);

                    // Generate unique order_id for retry by appending timestamp
                    // Midtrans doesn't allow duplicate order_ids, so we need unique ID for each retry attempt
                    $retryOrderId = $order->invoice_number . '-' . substr(md5(microtime()), 0, 8);

                    $transaction = [
                        'transaction_details' => [
                            'order_id'     => $retryOrderId,
                            'gross_amount' => $grossAmount,
                        ],
                        'item_details'     => $midtransItems,
                        'customer_details' => [
                            'first_name' => substr($user->name ?? 'Customer', 0, 50),
                            'email'      => $user->email,
                            'phone'      => $order->shipping_phone,
                        ],
                        'billing_address' => [
                            'first_name'   => substr($order->shipping_name, 0, 50),
                            'phone'        => $order->shipping_phone,
                            'address'      => substr($order->shipping_address, 0, 100),
                            'city'         => substr($order->shipping_city, 0, 100),
                            'postal_code'  => $order->shipping_postal_code,
                            'country_code' => 'IDN',
                        ],
                    ];

                    // Retry Snap token generation up to 3 times with delays
                    $snapToken = null;
                    $attempts = 0;
                    $maxAttempts = 3;
                    
                    while ($snapToken === null && $attempts < $maxAttempts) {
                        try {
                            $snapToken = Snap::getSnapToken($transaction);
                            \Log::info('Payment token created successfully', ['order_id' => $order->id, 'attempt' => $attempts + 1]);
                            break;
                        } catch (\Exception $retryE) {
                            $attempts++;
                            \Log::warning('Payment token retry', [
                                'order_id' => $order->id,
                                'attempt' => $attempts,
                                'error' => $retryE->getMessage(),
                            ]);
                            
                            if ($attempts < $maxAttempts) {
                                sleep(1 * $attempts);
                            } else {
                                throw $retryE;
                            }
                        }
                    }

                    return response()->json([
                        'success'   => true,
                        'snapToken' => $snapToken,
                        'orderId'   => $order->id,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Midtrans Snap Token Error for payment retry', [
                        'order_id' => $order->id,
                        'message' => $e->getMessage(),
                    ]);
                    return response()->json([
                        'error' => 'Failed to generate payment token',
                        'message' => $e->getMessage()
                    ], 500);
                }
            }

            return response()->json(['error' => 'Midtrans not configured'], 500);
        } catch (\Exception $e) {
            \Log::error('Generate payment token outer catch', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Failed to generate payment token',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Save user's address after successful order
     */
    public function saveAddress(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'street' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
        ]);
        
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $user->update([
            'saved_latitude' => $validated['latitude'],
            'saved_longitude' => $validated['longitude'],
            'saved_street' => $validated['street'],
            'saved_postal_code' => $validated['postal_code'],
            'saved_province' => $validated['province'],
            'saved_city' => $validated['city'],
            'saved_district' => $validated['district'],
        ]);
        
        return response()->json(['success' => true, 'message' => 'Address saved successfully']);
    }

    public function callback(Request $request)
    {
        $serverKey  = config('midtrans.server_key');
        $notifBody  = file_get_contents('php://input');
        $notifData  = json_decode($notifBody, true);

        if ($notifData) {
            $signature      = $notifData['signature_key'] ?? $request->input('signature_key');
            $orderId        = $notifData['order_id'] ?? null;
            $statusCode     = $notifData['status_code'] ?? null;
            $grossAmount    = $notifData['gross_amount'] ?? null;
            
            if ($orderId && $statusCode !== null && $grossAmount !== null) {
                $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

                if ($signature === $signatureKey) {
                    // Try to find order by invoice_number
                    $order = Order::where('invoice_number', $orderId)->first();
                    
                    // If not found and this is a retry order_id (has -suffix), extract base invoice_number
                    if (!$order && strpos($orderId, '-') !== false) {
                        // Retry format: {invoice_number}-{8char_hash}
                        // Extract the base invoice_number
                        preg_match('/^(.+)-[a-f0-9]{8}$/', $orderId, $matches);
                        if (!empty($matches[1])) {
                            $order = Order::where('invoice_number', $matches[1])->first();
                        }
                    }
                    
                    if ($order) {
                        $txStatus = $notifData['transaction_status'] ?? null;
                        $paymentType = $notifData['payment_type'] ?? null;
                        
                        if ($statusCode == 200 || $statusCode == 201) {
                            if ($txStatus === 'capture' || $txStatus === 'settlement') {
                                // IMPORTANT: Only update if not already processed
                                // Let markPaymentComplete handle the full status/stock update
                                if (!$order->payment_processed) {
                                    $order->payment_method = $paymentType;
                                    $order->save();
                                    
                                    \Log::info('Webhook received payment settlement', [
                                        'order_id' => $order->id,
                                        'invoice' => $order->invoice_number,
                                        'status' => 'awaiting_payment_complete_call'
                                    ]);
                                }
                            }
                        } elseif ($txStatus === 'pending') {
                            $order->payment_method = $paymentType;
                            $order->save();
                        } elseif (in_array($txStatus, ['deny', 'cancel', 'expire'])) {
                            // Payment was cancelled, denied, or expired
                            if ($order->status !== 'paid') {
                                $order->status = 'cancelled';
                                $order->payment_method = $paymentType;
                                $order->save();
                                
                                // Restore stock for all items in cancelled order to their respective stores
                                foreach ($order->order_details as $detail) {
                                    if ($detail->store_id) {
                                        $book = $detail->book;
                                        $storeBook = $book->storeLocations()
                                            ->where('store_location_id', $detail->store_id)
                                            ->first();
                                        
                                        if ($storeBook) {
                                            $newStock = $storeBook->pivot->stock + $detail->quantity;
                                            $book->storeLocations()
                                                ->updateExistingPivot($detail->store_id, ['stock' => $newStock]);
                                        }
                                    }
                                }
                            }
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

    /**
     * Get Indonesian locations with districts (kecamatan), postal codes, coordinates
     */
    public static function indonesianLocations(): array
    {
        return [
            'Aceh' => [
                'Banda Aceh' => [
                    'Baiturrahman' => ['postal_code' => '23111', 'lat' => 5.5500, 'lng' => 95.3333, 'streets' => ['Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani', 'Jl. Gatot Subroto']],
                    'Meuraxa' => ['postal_code' => '23124', 'lat' => 5.5667, 'lng' => 95.3500, 'streets' => ['Jl. Meuraxa', 'Jl. Raya Meuraxa', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                    'Jaya Baru' => ['postal_code' => '23128', 'lat' => 5.5834, 'lng' => 95.3667, 'streets' => ['Jl. Jaya Baru', 'Jl. Raya Jaya Baru', 'Jl. Gatot Subroto', 'Jl. Merdeka']],
                ],
                'Sabang' => [
                    'Sabang' => ['postal_code' => '25811', 'lat' => 5.8967, 'lng' => 95.3244, 'streets' => ['Jl. Raya Sabang', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Kuala Raya']],
                ],
            ],
            'Sumatera Utara' => [
                'Medan' => [
                    'Medan Baru' => ['postal_code' => '20111', 'lat' => 3.5952, 'lng' => 98.6722, 'streets' => ['Jl. Merdeka', 'Jl. Gatot Subroto', 'Jl. Diponegoro', 'Jl. Ahmad Yani']],
                    'Medan Merdeka' => ['postal_code' => '20154', 'lat' => 3.5834, 'lng' => 98.6834, 'streets' => ['Jl. Merdeka Raya', 'Jl. Cinde', 'Jl. Sudirman', 'Jl. Raya Medan']],
                    'Medan Sunggal' => ['postal_code' => '20154', 'lat' => 3.5645, 'lng' => 98.6945, 'streets' => ['Jl. Sunggal', 'Jl. Raya Sunggal', 'Jl. Zainul Arifin', 'Jl. Iskandar Muda']],
                    'Medan Timur' => ['postal_code' => '20233', 'lat' => 3.5567, 'lng' => 98.7234, 'streets' => ['Jl. Medan Timur', 'Jl. Raya Medan Timur', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                    'Medan Perjuangan' => ['postal_code' => '20212', 'lat' => 3.5789, 'lng' => 98.6545, 'streets' => ['Jl. Perjuangan', 'Jl. Raya Perjuangan', 'Jl. Jamin Ginting', 'Jl. Merdeka']],
                ],
                'Binjai' => [
                    'Binjai' => ['postal_code' => '20711', 'lat' => 3.6013, 'lng' => 99.2711, 'streets' => ['Jl. Raya Binjai', 'Jl. Sudirman', 'Jl. Merdeka', 'Jl. Gatot Subroto']],
                    'Binjai Timur' => ['postal_code' => '20725', 'lat' => 3.6145, 'lng' => 99.2834, 'streets' => ['Jl. Binjai Timur', 'Jl. Raya Binjai Timur', 'Jl. Pendidikan', 'Jl. Ahmad Yani']],
                ],
                'Deli Serdang' => [
                    'Deli Serdang' => ['postal_code' => '20382', 'lat' => 2.9745, 'lng' => 99.6045, 'streets' => ['Jl. Raya Deli Serdang', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
                'Pematang Siantar' => [
                    'Pematang Siantar' => ['postal_code' => '21111', 'lat' => 2.9564, 'lng' => 99.0767, 'streets' => ['Jl. Raya Pematang Siantar', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Gatot Subroto']],
                ],
                'Tebing Tinggi' => [
                    'Tebing Tinggi' => ['postal_code' => '20632', 'lat' => 3.3327, 'lng' => 99.1663, 'streets' => ['Jl. Raya Tebing Tinggi', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Jawa Timur' => [
                'Surabaya' => [
                    'Krembangan' => ['postal_code' => '60181', 'lat' => -7.2234, 'lng' => 112.7167, 'streets' => ['Jl. Krembangan Utara', 'Jl. Krembangan Selatan', 'Jl. Margomulyo', 'Jl. Raya Krembangan']],
                    'Bubutan' => ['postal_code' => '60173', 'lat' => -7.2512, 'lng' => 112.7423, 'streets' => ['Jl. Bubutan', 'Jl. Priyai', 'Jl. Raya Bubutan', 'Jl. Sememi']],
                    'Simokerto' => ['postal_code' => '60186', 'lat' => -7.2234, 'lng' => 112.7634, 'streets' => ['Jl. Simokerto', 'Jl. Ngagel', 'Jl. Kedung Cowek', 'Jl. Raya Simokerto']],
                    'Genteng' => ['postal_code' => '60275', 'lat' => -7.2512, 'lng' => 112.7267, 'streets' => ['Jl. Genteng', 'Jl. Gajah Mada', 'Jl. Embong Malang', 'Jl. Ahmad Yani']],
                    'Tanjungsari' => ['postal_code' => '60188', 'lat' => -7.2134, 'lng' => 112.7845, 'streets' => ['Jl. Tanjungsari', 'Jl. Raya Tanjungsari', 'Jl. Karangmenjangan', 'Jl. Kedung Cowek']],
                    'Wonokromo' => ['postal_code' => '60241', 'lat' => -7.2678, 'lng' => 112.7456, 'streets' => ['Jl. Raya Wonokromo', 'Jl. Wonokromo Raya', 'Jl. Nyamplungan', 'Jl. Kertajaya']],
                    'Rungkut' => ['postal_code' => '60295', 'lat' => -7.2945, 'lng' => 112.7823, 'streets' => ['Jl. Rungkut', 'Jl. Raya Rungkut', 'Jl. Rungkut Kidul', 'Jl. Rungkut Utara']],
                    'Sukolilo' => ['postal_code' => '60211', 'lat' => -7.2634, 'lng' => 112.8234, 'streets' => ['Jl. Sukolilo', 'Jl. Raya Sukolilo', 'Jl. Menur', 'Jl. Menur Raya']],
                    'Tenggilis Mejoyo' => ['postal_code' => '60181', 'lat' => -7.3001, 'lng' => 112.7745, 'streets' => ['Jl. Tenggilis', 'Jl. Raya Tenggilis', 'Jl. Mejoyo', 'Jl. Tenggilis Mejoyo']],
                    'Kenjeran' => ['postal_code' => '60187', 'lat' => -7.1956, 'lng' => 112.7645, 'streets' => ['Jl. Kenjeran', 'Jl. Raya Kenjeran', 'Jl. Pantai Cemara', 'Jl. Pantai Tiram']],
                ],
                'Malang' => [
                    'Klojen' => ['postal_code' => '65111', 'lat' => -7.9827, 'lng' => 112.6345, 'streets' => ['Jl. Merdeka', 'Jl. Basuki Rahmat', 'Jl. Malioboro', 'Jl. Tirtodipuran']],
                    'Sukun' => ['postal_code' => '65128', 'lat' => -7.9945, 'lng' => 112.6234, 'streets' => ['Jl. Sukun', 'Jl. Raya Sukun', 'Jl. Sutoyo', 'Jl. Semeru']],
                    'Lowokwaru' => ['postal_code' => '65145', 'lat' => -7.9678, 'lng' => 112.6456, 'streets' => ['Jl. Gajayana', 'Jl. Ijen', 'Jl. Raya Dieng', 'Jl. Sudanco Supriyadi']],
                    'Blimbing' => ['postal_code' => '65122', 'lat' => -7.9534, 'lng' => 112.6123, 'streets' => ['Jl. Blimbing', 'Jl. Raya Blimbing', 'Jl. Sumatra', 'Jl. Kompol Maksum']],
                    'Kedungkandang' => ['postal_code' => '65139', 'lat' => -8.0001, 'lng' => 112.6512, 'streets' => ['Jl. Kedungkandang', 'Jl. Raya Kedungkandang', 'Jl. Semarang', 'Jl. Veteran']],
                    'Kasihan' => ['postal_code' => '65119', 'lat' => -8.0145, 'lng' => 112.6234, 'streets' => ['Jl. Kasihan', 'Jl. Raya Kasihan', 'Jl. Pendidikan', 'Jl. Ahmad Yani']],
                ],
                'Sidoarjo' => [
                    'Sidoarjo' => ['postal_code' => '61211', 'lat' => -7.4409, 'lng' => 112.7192, 'streets' => ['Jl. Raya Sidoarjo', 'Jl. Pahlawan', 'Jl. Pelangi', 'Jl. Kemakmuran']],
                    'Waru' => ['postal_code' => '61256', 'lat' => -7.4512, 'lng' => 112.7034, 'streets' => ['Jl. Waru', 'Jl. Raya Waru', 'Jl. Pendidikan', 'Jl. Kapten Poniman']],
                    'Porong' => ['postal_code' => '61274', 'lat' => -7.3892, 'lng' => 112.7234, 'streets' => ['Jl. Raya Porong', 'Jl. Porong', 'Jl. Jenawi', 'Jl. Sidoarjo Raya']],
                    'Buduran' => ['postal_code' => '61252', 'lat' => -7.3634, 'lng' => 112.6845, 'streets' => ['Jl. Buduran', 'Jl. Raya Buduran', 'Jl. Sudirman', 'Jl. Gatot Subroto']],
                    'Candi' => ['postal_code' => '61271', 'lat' => -7.4678, 'lng' => 112.7345, 'streets' => ['Jl. Candi', 'Jl. Raya Candi', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                ],
                'Gresik' => [
                    'Gresik' => ['postal_code' => '61111', 'lat' => -7.1534, 'lng' => 112.6476, 'streets' => ['Jl. Raya Gresik', 'Jl. Moh Joyoboyo', 'Jl. Kalimantan', 'Jl. Pesantren']],
                    'Kebomas' => ['postal_code' => '61127', 'lat' => -7.1678, 'lng' => 112.6234, 'streets' => ['Jl. Kebomas', 'Jl. Raya Kebomas', 'Jl. Soekarno Hatta', 'Jl. Ahmad Yani']],
                    'Manyar' => ['postal_code' => '61175', 'lat' => -7.0945, 'lng' => 112.5834, 'streets' => ['Jl. Manyar', 'Jl. Raya Manyar', 'Jl. Pendidikan', 'Jl. Merdeka']],
                    'Cerme' => ['postal_code' => '61181', 'lat' => -7.1123, 'lng' => 112.6345, 'streets' => ['Jl. Cerme', 'Jl. Raya Cerme', 'Jl. Gatot Subroto', 'Jl. Ahmad Yani']],
                ],
                'Tuban' => [
                    'Tuban' => ['postal_code' => '62311', 'lat' => -6.9004, 'lng' => 112.7567, 'streets' => ['Jl. Raya Tuban', 'Jl. Panglima Sudirman', 'Jl. Merdeka', 'Jl. Gatot Subroto']],
                    'Jenu' => ['postal_code' => '62368', 'lat' => -6.8234, 'lng' => 112.6834, 'streets' => ['Jl. Jenu', 'Jl. Raya Jenu', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                    'Plumpang' => ['postal_code' => '62381', 'lat' => -6.8945, 'lng' => 112.7145, 'streets' => ['Jl. Plumpang', 'Jl. Raya Plumpang', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
                'Pasuruan' => [
                    'Pasuruan' => ['postal_code' => '67111', 'lat' => -7.6434, 'lng' => 112.9012, 'streets' => ['Jl. Raya Pasuruan', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Gatot Subroto']],
                    'Kabupaten Pasuruan' => ['postal_code' => '67200', 'lat' => -7.7001, 'lng' => 112.8834, 'streets' => ['Jl. Raya Kabupaten', 'Jl. Pendidikan', 'Jl. Ahmad Yani', 'Jl. Soekarno Hatta']],
                    'Probolinggo' => ['postal_code' => '67211', 'lat' => -7.7234, 'lng' => 112.9345, 'streets' => ['Jl. Probolinggo', 'Jl. Raya Probolinggo', 'Jl. Gatot Subroto', 'Jl. Merdeka']],
                ],
                'Mojokerto' => [
                    'Mojokerto' => ['postal_code' => '61311', 'lat' => -7.4845, 'lng' => 112.4367, 'streets' => ['Jl. Raya Mojokerto', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Jatirejo' => ['postal_code' => '61352', 'lat' => -7.5123, 'lng' => 112.4234, 'streets' => ['Jl. Jatirejo', 'Jl. Raya Jatirejo', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                    'Jetis' => ['postal_code' => '61363', 'lat' => -7.5345, 'lng' => 112.4456, 'streets' => ['Jl. Jetis', 'Jl. Raya Jetis', 'Jl. Ahmad Yani', 'Jl. Merdeka']],
                ],
                'Lamongan' => [
                    'Lamongan' => ['postal_code' => '62211', 'lat' => -6.8945, 'lng' => 112.2234, 'streets' => ['Jl. Raya Lamongan', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Sugio' => ['postal_code' => '62272', 'lat' => -6.9234, 'lng' => 112.3045, 'streets' => ['Jl. Sugio', 'Jl. Raya Sugio', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                    'Laren' => ['postal_code' => '62281', 'lat' => -6.8567, 'lng' => 112.2456, 'streets' => ['Jl. Laren', 'Jl. Raya Laren', 'Jl. Gatot Subroto', 'Jl. Ahmad Yani']],
                ],
                'Bangkalan' => [
                    'Bangkalan' => ['postal_code' => '69411', 'lat' => -7.0378, 'lng' => 112.7309, 'streets' => ['Jl. Raya Bangkalan', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Kwanyar' => ['postal_code' => '69472', 'lat' => -7.0789, 'lng' => 112.7456, 'streets' => ['Jl. Kwanyar', 'Jl. Raya Kwanyar', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
            ],
            'DKI Jakarta' => [
                'Jakarta Pusat' => [
                    'Menteng' => ['postal_code' => '10230', 'lat' => -6.1908, 'lng' => 106.8330, 'streets' => ['Jl. Gatot Subroto', 'Jl. Sudirman', 'Jl. Thamrin', 'Jl. Merdeka Selatan', 'Jl. Kebon Kacang']],
                    'Tanah Abang' => ['postal_code' => '10160', 'lat' => -6.1973, 'lng' => 106.8101, 'streets' => ['Jl. Tanah Abang', 'Jl. Raya Tanah Abang', 'Jl. Hayam Wuruk', 'Jl. Bungur']],
                    'Cempaka Putih' => ['postal_code' => '10510', 'lat' => -6.1644, 'lng' => 106.8494, 'streets' => ['Jl. Cempaka Putih', 'Jl. Gajah Mada', 'Jl. Raya Cempaka Putih', 'Jl. Diponegoro']],
                    'Gambir' => ['postal_code' => '10110', 'lat' => -6.1744, 'lng' => 106.8234, 'streets' => ['Jl. Merdeka Utara', 'Jl. Medan Merdeka Barat', 'Jl. Lada', 'Jl. Wawasan']],
                    'Senen' => ['postal_code' => '10410', 'lat' => -6.1534, 'lng' => 106.8567, 'streets' => ['Jl. Senen Raya', 'Jl. Senen', 'Jl. Kramat', 'Jl. Sonang']],
                    'Johar Baru' => ['postal_code' => '10560', 'lat' => -6.1834, 'lng' => 106.8745, 'streets' => ['Jl. Johar Baru', 'Jl. Raya Johar Baru', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                    'Kemayoran' => ['postal_code' => '10610', 'lat' => -6.1567, 'lng' => 106.8456, 'streets' => ['Jl. Kemayoran', 'Jl. Raya Kemayoran', 'Jl. Ahmad Yani', 'Jl. Merdeka']],
                ],
                'Jakarta Selatan' => [
                    'Kebayoran Baru' => ['postal_code' => '12110', 'lat' => -6.2749, 'lng' => 106.7964, 'streets' => ['Jl. Sudirman', 'Jl. Gatot Subroto', 'Jl. Terogong Raya', 'Jl. Kemang']],
                    'Mampang Prapatan' => ['postal_code' => '12790', 'lat' => -6.2567, 'lng' => 106.8123, 'streets' => ['Jl. Mampang Prapatan', 'Jl. Raya Mampang', 'Jl. Letjen Soeprapto', 'Jl. Kebo Iwa']],
                    'Cilandak' => ['postal_code' => '12560', 'lat' => -6.2934, 'lng' => 106.7845, 'streets' => ['Jl. Raya Cilandak', 'Jl. Cilandak Raya', 'Jl. Warung Buncit', 'Jl. Cawang']],
                    'Pasar Minggu' => ['postal_code' => '12510', 'lat' => -6.2834, 'lng' => 106.8234, 'streets' => ['Jl. Pasar Minggu', 'Jl. Raya Pasar Minggu', 'Jl. Raya Bogor', 'Jl. Setia Budi']],
                    'Tebet' => ['postal_code' => '12810', 'lat' => -6.2645, 'lng' => 106.8456, 'streets' => ['Jl. Tebet', 'Jl. Raya Tebet', 'Jl. Srengseng', 'Jl. Warung Buncit']],
                    'Jagakarsa' => ['postal_code' => '12620', 'lat' => -6.3045, 'lng' => 106.8234, 'streets' => ['Jl. Jagakarsa', 'Jl. Raya Jagakarsa', 'Jl. Raya Bogor', 'Jl. Parung Raya']],
                    'Pesanggrahan' => ['postal_code' => '12640', 'lat' => -6.3234, 'lng' => 106.7645, 'streets' => ['Jl. Pesanggrahan', 'Jl. Raya Pesanggrahan', 'Jl. Lebak Bulus', 'Jl. Benda']],
                    'Setiabudi' => ['postal_code' => '12910', 'lat' => -6.2256, 'lng' => 106.8234, 'streets' => ['Jl. Setiabudi', 'Jl. Raya Setiabudi', 'Jl. Penghibur', 'Jl. Irian']],
                ],
                'Jakarta Barat' => [
                    'Tanah Kusir' => ['postal_code' => '11320', 'lat' => -6.1456, 'lng' => 106.7123, 'streets' => ['Jl. Tanah Kusir', 'Jl. Raya Tanah Kusir', 'Jl. Tanjung Duren', 'Jl. Grogol']],
                    'Cengkareng' => ['postal_code' => '11730', 'lat' => -6.1234, 'lng' => 106.7234, 'streets' => ['Jl. Raya Cengkareng', 'Jl. Cengkareng Timur', 'Jl. Cengkareng Barat', 'Jl. Yani Raya']],
                    'Palmerah' => ['postal_code' => '11410', 'lat' => -6.1534, 'lng' => 106.7834, 'streets' => ['Jl. Palmerah', 'Jl. Raya Palmerah', 'Jl. Jend Sudirman', 'Jl. Tomang Raya']],
                    'Grogol Petamburan' => ['postal_code' => '11450', 'lat' => -6.1378, 'lng' => 106.7456, 'streets' => ['Jl. Grogol', 'Jl. Raya Grogol', 'Jl. Panjang', 'Jl. Benda']],
                    'Tambora' => ['postal_code' => '11210', 'lat' => -6.1645, 'lng' => 106.7023, 'streets' => ['Jl. Tambora', 'Jl. Raya Tambora', 'Jl. Ancol', 'Jl. Pecenongan']],
                    'Kebon Jeruk' => ['postal_code' => '11530', 'lat' => -6.1823, 'lng' => 106.7234, 'streets' => ['Jl. Kebon Jeruk', 'Jl. Raya Kebon Jeruk', 'Jl. Benda', 'Jl. Gatot Subroto']],
                    'Jatiluhur' => ['postal_code' => '11540', 'lat' => -6.1756, 'lng' => 106.7345, 'streets' => ['Jl. Jatiluhur', 'Jl. Raya Jatiluhur', 'Jl. Pendidikan', 'Jl. Ahmad Yani']],
                ],
                'Jakarta Utara' => [
                    'Kelapa Gading' => ['postal_code' => '14240', 'lat' => -6.1267, 'lng' => 106.8867, 'streets' => ['Jl. Kelapa Gading', 'Jl. Raya Kelapa Gading', 'Jl. Sunter', 'Jl. Ancol']],
                    'Penjaringan' => ['postal_code' => '14450', 'lat' => -6.0894, 'lng' => 106.8894, 'streets' => ['Jl. Penjaringan Raya', 'Jl. Penjaringan', 'Jl. Yos Sudarso', 'Jl. Jatibaru Raya']],
                    'Tanjung Priok' => ['postal_code' => '14310', 'lat' => -6.0756, 'lng' => 106.9067, 'streets' => ['Jl. Raya Tanjung Priok', 'Jl. Priok Raya', 'Jl. Pakin', 'Jl. Tentara Pelajar']],
                    'Pademangan' => ['postal_code' => '14410', 'lat' => -6.1123, 'lng' => 106.8234, 'streets' => ['Jl. Pademangan', 'Jl. Raya Pademangan', 'Jl. Sunter Agung', 'Jl. Lodan']],
                    'Ancol' => ['postal_code' => '14430', 'lat' => -6.0945, 'lng' => 106.8145, 'streets' => ['Jl. Ancol', 'Jl. Raya Ancol', 'Jl. Pantai Indah Kapuk', 'Jl. Ancol Timur']],
                    'Cilincing' => ['postal_code' => '14120', 'lat' => -6.0556, 'lng' => 106.9223, 'streets' => ['Jl. Cilincing', 'Jl. Raya Cilincing', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Jakarta Timur' => [
                    'Kramat Jati' => ['postal_code' => '13530', 'lat' => -6.2512, 'lng' => 106.8645, 'streets' => ['Jl. Kramat Jati', 'Jl. Raya Kramat Jati', 'Jl. Pondok Kelapa', 'Jl. Bekasi Raya']],
                    'Cakung' => ['postal_code' => '13910', 'lat' => -6.2134, 'lng' => 106.9234, 'streets' => ['Jl. Raya Cakung', 'Jl. Cakung', 'Jl. Intan', 'Jl. Jatinegara Barat']],
                    'Jatinegara' => ['postal_code' => '13310', 'lat' => -6.2456, 'lng' => 106.8834, 'streets' => ['Jl. Jatinegara Raya', 'Jl. Jatinegara', 'Jl. Warung Buncit Raya', 'Jl. Raya Pulo Gadung']],
                    'Makasar' => ['postal_code' => '13560', 'lat' => -6.2834, 'lng' => 106.9123, 'streets' => ['Jl. Makasar Raya', 'Jl. Makasar', 'Jl. Raya Bogor', 'Jl. Benda Raya']],
                    'Pondok Kelapa' => ['postal_code' => '13450', 'lat' => -6.2345, 'lng' => 106.8945, 'streets' => ['Jl. Pondok Kelapa', 'Jl. Raya Pondok Kelapa', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                    'Cipayung' => ['postal_code' => '13820', 'lat' => -6.3456, 'lng' => 106.9234, 'streets' => ['Jl. Cipayung', 'Jl. Raya Cipayung', 'Jl. Pendidikan', 'Jl. Ahmad Yani']],
                    'Pulogadung' => ['postal_code' => '13930', 'lat' => -6.2145, 'lng' => 106.8956, 'streets' => ['Jl. Pulogadung', 'Jl. Raya Pulogadung', 'Jl. Gatot Subroto', 'Jl. Merdeka']],
                ],
            ],
            'Jawa Barat' => [
                'Bandung' => [
                    'Andir' => ['postal_code' => '40181', 'lat' => -6.8945, 'lng' => 107.6234, 'streets' => ['Jl. Andir', 'Jl. Raya Andir', 'Jl. Dipati Ukur', 'Jl. Pasteur']],
                    'Cidadap' => ['postal_code' => '40143', 'lat' => -6.9267, 'lng' => 107.6345, 'streets' => ['Jl. Cidadap', 'Jl. Raya Cidadap', 'Jl. Setiabudi', 'Jl. Braga']],
                    'Cibeureum' => ['postal_code' => '40121', 'lat' => -6.8734, 'lng' => 107.6456, 'streets' => ['Jl. Cibeureum', 'Jl. Raya Cibeureum', 'Jl. Tangkuban Perahu', 'Jl. Merdeka']],
                    'Bandung Wetan' => ['postal_code' => '40111', 'lat' => -6.8945, 'lng' => 107.6145, 'streets' => ['Jl. Bandung Wetan', 'Jl. Gatot Subroto', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Bandung Kidul' => ['postal_code' => '40267', 'lat' => -6.9234, 'lng' => 107.6023, 'streets' => ['Jl. Bandung Kidul', 'Jl. Raya Bandung Kidul', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                    'Rancasari' => ['postal_code' => '40292', 'lat' => -6.8734, 'lng' => 107.5834, 'streets' => ['Jl. Rancasari', 'Jl. Raya Rancasari', 'Jl. Dewi Sartika', 'Jl. Merdeka']],
                ],
                'Bogor' => [
                    'Bogor Tengah' => ['postal_code' => '16110', 'lat' => -6.5959, 'lng' => 106.8060, 'streets' => ['Jl. Raya Pajajaran', 'Jl. Merdeka', 'Jl. Kebun Raya', 'Jl. Jend Ahmad Yani']],
                    'Bogor Utara' => ['postal_code' => '16151', 'lat' => -6.5734, 'lng' => 106.7945, 'streets' => ['Jl. Raya Bogor Utara', 'Jl. Siliwangi', 'Jl. Arjuno', 'Jl. Sudirman']],
                    'Bogor Timur' => ['postal_code' => '16810', 'lat' => -6.6045, 'lng' => 106.8234, 'streets' => ['Jl. Bogor Timur', 'Jl. Raya Bogor Timur', 'Jl. Penghibur', 'Jl. Jalan Cinere']],
                    'Bogor Selatan' => ['postal_code' => '16310', 'lat' => -6.6234, 'lng' => 106.7834, 'streets' => ['Jl. Bogor Selatan', 'Jl. Raya Bogor Selatan', 'Jl. Pendidikan', 'Jl. Ahmad Yani']],
                ],
                'Bekasi' => [
                    'Bekasi Barat' => ['postal_code' => '17111', 'lat' => -6.2349, 'lng' => 107.0019, 'streets' => ['Jl. Jend. Ahmad Yani', 'Jl. Pattimura', 'Jl. Raya Bekasi', 'Jl. Gatot Subroto']],
                    'Bekasi Timur' => ['postal_code' => '17148', 'lat' => -6.2456, 'lng' => 107.0234, 'streets' => ['Jl. Bekasi Timur', 'Jl. Raya Bekasi Timur', 'Jl. Raya Cakung', 'Jl. Jatiwaringin']],
                    'Bekasi Selatan' => ['postal_code' => '17144', 'lat' => -6.2834, 'lng' => 107.0145, 'streets' => ['Jl. Bekasi Selatan', 'Jl. Raya Bekasi Selatan', 'Jl. Raya Jatiasih', 'Jl. Pendidikan']],
                ],
                'Depok' => [
                    'Depok' => ['postal_code' => '16411', 'lat' => -6.3874, 'lng' => 106.8229, 'streets' => ['Jl. Raya Depok', 'Jl. Margonda Raya', 'Jl. Pancasila', 'Jl. Soekarno Hatta']],
                    'Cinere' => ['postal_code' => '16514', 'lat' => -6.3945, 'lng' => 106.8045, 'streets' => ['Jl. Cinere Raya', 'Jl. Raya Cinere', 'Jl. Komodo', 'Jl. Nusantara']],
                    'Limo' => ['postal_code' => '16515', 'lat' => -6.4023, 'lng' => 106.8123, 'streets' => ['Jl. Limo', 'Jl. Raya Limo', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                ],
                'Cirebon' => [
                    'Cirebon' => ['postal_code' => '45111', 'lat' => -6.7034, 'lng' => 108.4534, 'streets' => ['Jl. Raya Cirebon', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Kuningan' => ['postal_code' => '45511', 'lat' => -6.9934, 'lng' => 108.4234, 'streets' => ['Jl. Raya Kuningan', 'Jl. Merdeka', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Sumedang' => [
                    'Sumedang' => ['postal_code' => '45311', 'lat' => -6.8534, 'lng' => 107.9234, 'streets' => ['Jl. Raya Sumedang', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
                'Tasikmalaya' => [
                    'Tasikmalaya' => ['postal_code' => '46311', 'lat' => -7.3534, 'lng' => 108.2234, 'streets' => ['Jl. Raya Tasikmalaya', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
                'Cianjur' => [
                    'Cianjur' => ['postal_code' => '43200', 'lat' => -6.8234, 'lng' => 107.1456, 'streets' => ['Jl. Raya Cianjur', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
                'Garut' => [
                    'Garut' => ['postal_code' => '44111', 'lat' => -7.2156, 'lng' => 107.8956, 'streets' => ['Jl. Raya Garut', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Jawa Tengah' => [
                'Semarang' => [
                    'Semarang Utara' => ['postal_code' => '50191', 'lat' => -6.9456, 'lng' => 110.4234, 'streets' => ['Jl. Pahlawan', 'Jl. Raya Utara', 'Jl. Singosari', 'Jl. Imam Bonjol']],
                    'Semarang Timur' => ['postal_code' => '50198', 'lat' => -6.9674, 'lng' => 110.4399, 'streets' => ['Jl. Gajah Mada', 'Jl. Sudirman', 'Jl. Pekunden', 'Jl. Raya Timur']],
                    'Semarang Tengah' => ['postal_code' => '50132', 'lat' => -6.9567, 'lng' => 110.4123, 'streets' => ['Jl. Raya Semarang Tengah', 'Jl. Letjen Soeprapto', 'Jl. Merdeka', 'Jl. Ronggowarsito']],
                    'Semarang Selatan' => ['postal_code' => '50243', 'lat' => -6.9834, 'lng' => 110.4045, 'streets' => ['Jl. Raya Semarang Selatan', 'Jl. Kaligawe Raya', 'Jl. Jenderal Sudirman', 'Jl. Raya Kaligawe']],
                    'Semarang Barat' => ['postal_code' => '50141', 'lat' => -6.9645, 'lng' => 110.3834, 'streets' => ['Jl. Semarang Barat', 'Jl. Raya Semarang Barat', 'Jl. Pandanaran', 'Jl. Pemuda']],
                ],
                'Yogyakarta' => [
                    'Mantrijeron' => ['postal_code' => '55143', 'lat' => -7.7956, 'lng' => 110.3695, 'streets' => ['Jl. Malioboro', 'Jl. Sosrowijayan', 'Jl. Raya Mantrijeron', 'Jl. Kusumanegara']],
                    'Kraton' => ['postal_code' => '55126', 'lat' => -7.8056, 'lng' => 110.3834, 'streets' => ['Jl. Kraton', 'Jl. Raya Kraton', 'Jl. Pringgokusuman', 'Jl. Alun-alun Kidul']],
                    'Gondomanan' => ['postal_code' => '55212', 'lat' => -7.8134, 'lng' => 110.3945, 'streets' => ['Jl. Gondomanan', 'Jl. Raya Gondomanan', 'Jl. Diponegoro', 'Jl. Cokroaminoto']],
                    'Danurejan' => ['postal_code' => '55223', 'lat' => -7.8001, 'lng' => 110.3567, 'streets' => ['Jl. Danurejan', 'Jl. Raya Danurejan', 'Jl. Pendidikan', 'Jl. Sutrisno']],
                ],
                'Solo' => [
                    'Pasar Kliwon' => ['postal_code' => '57111', 'lat' => -7.5606, 'lng' => 110.8206, 'streets' => ['Jl. Slamet Riyadi', 'Jl. Merdeka', 'Jl. Yosodipuro', 'Jl. Ahmad Yani']],
                    'Serengan' => ['postal_code' => '57156', 'lat' => -7.5534, 'lng' => 110.8134, 'streets' => ['Jl. Serengan', 'Jl. Raya Serengan', 'Jl. Urip Sumoharjo', 'Jl. Pramuka Raya']],
                    'Jebres' => ['postal_code' => '57126', 'lat' => -7.5701, 'lng' => 110.8267, 'streets' => ['Jl. Jebres', 'Jl. Raya Jebres', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                    'Laweyan' => ['postal_code' => '57141', 'lat' => -7.5834, 'lng' => 110.8023, 'streets' => ['Jl. Laweyan', 'Jl. Raya Laweyan', 'Jl. Gatot Subroto', 'Jl. Merdeka']],
                ],
                'Surakarta' => [
                    'Surakarta Pusat' => ['postal_code' => '57111', 'lat' => -7.5650, 'lng' => 110.8150, 'streets' => ['Jl. Raya Surakarta', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
                'Pekalongan' => [
                    'Pekalongan' => ['postal_code' => '51111', 'lat' => -6.8889, 'lng' => 109.6756, 'streets' => ['Jl. Raya Pekalongan', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
                'Magelang' => [
                    'Magelang' => ['postal_code' => '56111', 'lat' => -7.4800, 'lng' => 110.2165, 'streets' => ['Jl. Raya Magelang', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'DI Yogyakarta' => [
                'Yogyakarta' => [
                    'Mantrijeron' => ['postal_code' => '55143', 'lat' => -7.7956, 'lng' => 110.3695, 'streets' => ['Jl. Malioboro', 'Jl. Sosrowijayan', 'Jl. Raya Mantrijeron', 'Jl. Kusumanegara']],
                    'Kraton' => ['postal_code' => '55126', 'lat' => -7.8056, 'lng' => 110.3834, 'streets' => ['Jl. Kraton', 'Jl. Raya Kraton', 'Jl. Pringgokusuman', 'Jl. Alun-alun Kidul']],
                    'Gondomanan' => ['postal_code' => '55212', 'lat' => -7.8134, 'lng' => 110.3945, 'streets' => ['Jl. Gondomanan', 'Jl. Raya Gondomanan', 'Jl. Diponegoro', 'Jl. Cokroaminoto']],
                ],
                'Sleman' => [
                    'Sleman' => ['postal_code' => '55511', 'lat' => -7.6471, 'lng' => 110.4067, 'streets' => ['Jl. Raya Yogyakarta-Sleman', 'Jl. Monginsidi', 'Jl. Merapi', 'Jl. Sayap Raya']],
                    'Tempel' => ['postal_code' => '55551', 'lat' => -7.6234, 'lng' => 110.3945, 'streets' => ['Jl. Tempel', 'Jl. Raya Tempel', 'Jl. Affandi', 'Jl. Raya Depok']],
                    'Mlati' => ['postal_code' => '55286', 'lat' => -7.6634, 'lng' => 110.4234, 'streets' => ['Jl. Mlati', 'Jl. Raya Mlati', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                ],
                'Bantul' => [
                    'Bantul' => ['postal_code' => '55711', 'lat' => -7.8853, 'lng' => 110.3273, 'streets' => ['Jl. Raya Yogyakarta-Bantul', 'Jl. Imogiri', 'Jl. Parangtritis', 'Jl. Muja Muju']],
                    'Kasihan' => ['postal_code' => '55184', 'lat' => -7.8945, 'lng' => 110.3834, 'streets' => ['Jl. Raya Kasihan', 'Jl. Kasihan', 'Jl. Pendidikan', 'Jl. Raya Imogiri']],
                    'Sedayu' => ['postal_code' => '55751', 'lat' => -7.9234, 'lng' => 110.3123, 'streets' => ['Jl. Sedayu', 'Jl. Raya Sedayu', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Gunung Kidul' => [
                    'Gunung Kidul' => ['postal_code' => '55811', 'lat' => -8.0123, 'lng' => 110.4234, 'streets' => ['Jl. Raya Gunung Kidul', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Wonosari' => ['postal_code' => '55812', 'lat' => -8.0034, 'lng' => 110.4145, 'streets' => ['Jl. Wonosari', 'Jl. Raya Wonosari', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Kulon Progo' => [
                    'Wates' => ['postal_code' => '55611', 'lat' => -7.8234, 'lng' => 110.1834, 'streets' => ['Jl. Raya Wates', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Sumatera Barat' => [
                'Padang' => [
                    'Padang Tengah' => ['postal_code' => '25111', 'lat' => -0.9467, 'lng' => 100.4183, 'streets' => ['Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani', 'Jl. Gatot Subroto']],
                    'Padang Utara' => ['postal_code' => '25141', 'lat' => -0.9267, 'lng' => 100.4045, 'streets' => ['Jl. Padang Utara', 'Jl. Raya Padang Utara', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                    'Padang Selatan' => ['postal_code' => '25221', 'lat' => -0.9745, 'lng' => 100.4234, 'streets' => ['Jl. Padang Selatan', 'Jl. Raya Padang Selatan', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Bukittinggi' => [
                    'Bukittinggi' => ['postal_code' => '26111', 'lat' => -0.3019, 'lng' => 100.3689, 'streets' => ['Jl. Raya Bukittinggi', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
                'Payakumbuh' => [
                    'Payakumbuh' => ['postal_code' => '26211', 'lat' => -0.2234, 'lng' => 100.6156, 'streets' => ['Jl. Raya Payakumbuh', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Riau' => [
                'Pekanbaru' => [
                    'Pekanbaru Kota' => ['postal_code' => '28111', 'lat' => 0.5067, 'lng' => 101.4476, 'streets' => ['Jl. Raya Pekanbaru', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Bukit Raya' => ['postal_code' => '28286', 'lat' => 0.5345, 'lng' => 101.4234, 'streets' => ['Jl. Bukit Raya', 'Jl. Raya Bukit Raya', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                ],
                'Dumai' => [
                    'Dumai' => ['postal_code' => '28800', 'lat' => 1.6674, 'lng' => 101.4456, 'streets' => ['Jl. Raya Dumai', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Jambi' => [
                'Jambi' => [
                    'Jambi Pusat' => ['postal_code' => '36111', 'lat' => -1.6119, 'lng' => 104.7453, 'streets' => ['Jl. Raya Jambi', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Jambi Utara' => ['postal_code' => '36141', 'lat' => -1.5934, 'lng' => 104.7234, 'streets' => ['Jl. Jambi Utara', 'Jl. Raya Jambi Utara', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
            ],
            'Sumatera Selatan' => [
                'Palembang' => [
                    'Palembang Kota' => ['postal_code' => '30111', 'lat' => -2.9161, 'lng' => 104.7520, 'streets' => ['Jl. Merdeka', 'Jl. Sudirman', 'Jl. Jend Sudirman', 'Jl. Gatot Subroto']],
                    'Seko' => ['postal_code' => '30411', 'lat' => -2.8945, 'lng' => 104.7834, 'streets' => ['Jl. Seko', 'Jl. Raya Seko', 'Jl. Kapten A Rivai', 'Jl. Raya Palembang']],
                    'Ilir Barat' => ['postal_code' => '30133', 'lat' => -2.9234, 'lng' => 104.7234, 'streets' => ['Jl. Ilir Barat', 'Jl. Raya Ilir Barat', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                ],
                'Lubuklinggau' => [
                    'Lubuklinggau' => ['postal_code' => '31626', 'lat' => -3.2944, 'lng' => 102.8189, 'streets' => ['Jl. Raya Lubuklinggau', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Lampung' => [
                'Bandar Lampung' => [
                    'Bandar Lampung Pusat' => ['postal_code' => '35111', 'lat' => -5.4164, 'lng' => 105.2648, 'streets' => ['Jl. Raya Bandar Lampung', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Bandar Lampung Utara' => ['postal_code' => '35141', 'lat' => -5.3945, 'lng' => 105.2834, 'streets' => ['Jl. Bandar Lampung Utara', 'Jl. Raya Bandar Lampung Utara', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Metro' => [
                    'Metro' => ['postal_code' => '34111', 'lat' => -5.1136, 'lng' => 104.7756, 'streets' => ['Jl. Raya Metro', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Banten' => [
                'Tangerang' => [
                    'Tangerang Kota' => ['postal_code' => '15111', 'lat' => -6.1747, 'lng' => 106.6294, 'streets' => ['Jl. Merdeka', 'Jl. Sudirman', 'Jl. Gatot Subroto', 'Jl. Ahmad Yani']],
                    'Cikokol' => ['postal_code' => '15145', 'lat' => -6.1834, 'lng' => 106.6145, 'streets' => ['Jl. Cikokol', 'Jl. Raya Cikokol', 'Jl. Jend Sudirman', 'Jl. Raya Tangerang']],
                    'Pinang' => ['postal_code' => '15144', 'lat' => -6.1945, 'lng' => 106.6023, 'streets' => ['Jl. Pinang', 'Jl. Raya Pinang', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                ],
                'Serang' => [
                    'Serang' => ['postal_code' => '42111', 'lat' => -6.1064, 'lng' => 106.1506, 'streets' => ['Jl. Raya Serang', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Gatot Subroto']],
                    'Serang Timur' => ['postal_code' => '42136', 'lat' => -6.1234, 'lng' => 106.1834, 'streets' => ['Jl. Serang Timur', 'Jl. Raya Serang Timur', 'Jl. Pendidikan', 'Jl. Ahmad Yani']],
                ],
                'Cilegon' => [
                    'Cilegon' => ['postal_code' => '42411', 'lat' => -6.0111, 'lng' => 106.1942, 'streets' => ['Jl. Raya Cilegon', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Kalimantan Barat' => [
                'Pontianak' => [
                    'Pontianak Pusat' => ['postal_code' => '78111', 'lat' => -0.0261, 'lng' => 109.3346, 'streets' => ['Jl. Raya Pontianak', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Pontianak Timur' => ['postal_code' => '78124', 'lat' => -0.0045, 'lng' => 109.3834, 'streets' => ['Jl. Pontianak Timur', 'Jl. Raya Pontianak Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Singkawang' => [
                    'Singkawang' => ['postal_code' => '79111', 'lat' => 0.9097, 'lng' => 109.8063, 'streets' => ['Jl. Raya Singkawang', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Kalimantan Tengah' => [
                'Palangka Raya' => [
                    'Palangka Raya Pusat' => ['postal_code' => '73111', 'lat' => -1.9705, 'lng' => 113.9118, 'streets' => ['Jl. Raya Palangka Raya', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Palangka Raya Timur' => ['postal_code' => '73123', 'lat' => -1.9534, 'lng' => 113.9345, 'streets' => ['Jl. Palangka Raya Timur', 'Jl. Raya Palangka Raya Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Sampit' => [
                    'Sampit' => ['postal_code' => '74811', 'lat' => -2.5331, 'lng' => 112.6822, 'streets' => ['Jl. Raya Sampit', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Kalimantan Selatan' => [
                'Banjarmasin' => [
                    'Banjarmasin Pusat' => ['postal_code' => '70111', 'lat' => -3.3286, 'lng' => 114.5896, 'streets' => ['Jl. Raya Banjarmasin', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Banjarmasin Timur' => ['postal_code' => '70123', 'lat' => -3.3145, 'lng' => 114.6234, 'streets' => ['Jl. Banjarmasin Timur', 'Jl. Raya Banjarmasin Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Banjarbaru' => [
                    'Banjarbaru' => ['postal_code' => '70711', 'lat' => -3.4399, 'lng' => 114.8111, 'streets' => ['Jl. Raya Banjarbaru', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Kalimantan Timur' => [
                'Samarinda' => [
                    'Samarinda Pusat' => ['postal_code' => '75111', 'lat' => -0.5000, 'lng' => 117.1333, 'streets' => ['Jl. Raya Samarinda', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Samarinda Timur' => ['postal_code' => '75124', 'lat' => -0.4845, 'lng' => 117.1645, 'streets' => ['Jl. Samarinda Timur', 'Jl. Raya Samarinda Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Balikpapan' => [
                    'Balikpapan Pusat' => ['postal_code' => '76111', 'lat' => -1.2704, 'lng' => 116.8308, 'streets' => ['Jl. Raya Balikpapan', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
                'Tarakan' => [
                    'Tarakan' => ['postal_code' => '77111', 'lat' => 3.2956, 'lng' => 117.5833, 'streets' => ['Jl. Raya Tarakan', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Sulawesi Utara' => [
                'Manado' => [
                    'Manado Pusat' => ['postal_code' => '95111', 'lat' => 1.4748, 'lng' => 124.8621, 'streets' => ['Jl. Raya Manado', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Manado Timur' => ['postal_code' => '95124', 'lat' => 1.4934, 'lng' => 124.8945, 'streets' => ['Jl. Manado Timur', 'Jl. Raya Manado Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
                'Bitung' => [
                    'Bitung' => ['postal_code' => '95411', 'lat' => 1.4427, 'lng' => 125.1833, 'streets' => ['Jl. Raya Bitung', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Sulawesi Tengah' => [
                'Palu' => [
                    'Palu Pusat' => ['postal_code' => '94111', 'lat' => -0.8917, 'lng' => 119.8602, 'streets' => ['Jl. Raya Palu', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Palu Timur' => ['postal_code' => '94123', 'lat' => -0.8745, 'lng' => 119.8934, 'streets' => ['Jl. Palu Timur', 'Jl. Raya Palu Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
            ],
            'Sulawesi Selatan' => [
                'Makassar' => [
                    'Makassar Barat' => ['postal_code' => '90111', 'lat' => -5.1477, 'lng' => 119.4327, 'streets' => ['Jl. Veteran', 'Jl. Soekarno Hatta', 'Jl. Sultan Alauddin', 'Jl. Ahmad Yani']],
                    'Makassar Selatan' => ['postal_code' => '90245', 'lat' => -5.1745, 'lng' => 119.4234, 'streets' => ['Jl. Makassar Selatan', 'Jl. Cendrawasih', 'Jl. Merdeka', 'Jl. Sudirman']],
                    'Ujung Pandang' => ['postal_code' => '90124', 'lat' => -5.1567, 'lng' => 119.4456, 'streets' => ['Jl. Ujung Pandang', 'Jl. Raya Ujung Pandang', 'Jl. Penghibur', 'Jl. Irian']],
                    'Makassar Utara' => ['postal_code' => '90111', 'lat' => -5.1234, 'lng' => 119.4145, 'streets' => ['Jl. Makassar Utara', 'Jl. Raya Makassar Utara', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                ],
                'Pare-pare' => [
                    'Pare-pare' => ['postal_code' => '91111', 'lat' => -4.7267, 'lng' => 119.6345, 'streets' => ['Jl. Raya Pare-pare', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Sulawesi Tenggara' => [
                'Kendari' => [
                    'Kendari Pusat' => ['postal_code' => '93111', 'lat' => -3.9667, 'lng' => 122.6000, 'streets' => ['Jl. Raya Kendari', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Kendari Timur' => ['postal_code' => '93123', 'lat' => -3.9534, 'lng' => 122.6345, 'streets' => ['Jl. Kendari Timur', 'Jl. Raya Kendari Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
            ],
            'Bali' => [
                'Denpasar' => [
                    'Denpasar Pusat' => ['postal_code' => '80111', 'lat' => -8.6704, 'lng' => 115.2126, 'streets' => ['Jl. Raya Denpasar', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Denpasar Timur' => ['postal_code' => '80124', 'lat' => -8.6534, 'lng' => 115.2456, 'streets' => ['Jl. Denpasar Timur', 'Jl. Raya Denpasar Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                    'Denpasar Barat' => ['postal_code' => '80111', 'lat' => -8.6823, 'lng' => 115.1834, 'streets' => ['Jl. Denpasar Barat', 'Jl. Raya Denpasar Barat', 'Jl. Pendidikan', 'Jl. Soekarno Hatta']],
                ],
                'Ubud' => [
                    'Ubud' => ['postal_code' => '80571', 'lat' => -8.5069, 'lng' => 115.2625, 'streets' => ['Jl. Raya Ubud', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
                'Gianyar' => [
                    'Gianyar' => ['postal_code' => '80511', 'lat' => -8.5045, 'lng' => 115.3234, 'streets' => ['Jl. Raya Gianyar', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                ],
            ],
            'Nusa Tenggara Barat' => [
                'Mataram' => [
                    'Mataram Pusat' => ['postal_code' => '83111', 'lat' => -8.5904, 'lng' => 116.1399, 'streets' => ['Jl. Raya Mataram', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Mataram Timur' => ['postal_code' => '83123', 'lat' => -8.5745, 'lng' => 116.1634, 'streets' => ['Jl. Mataram Timur', 'Jl. Raya Mataram Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
            ],
            'Nusa Tenggara Timur' => [
                'Kupang' => [
                    'Kupang Pusat' => ['postal_code' => '85111', 'lat' => -10.1772, 'lng' => 123.6145, 'streets' => ['Jl. Raya Kupang', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Kupang Timur' => ['postal_code' => '85123', 'lat' => -10.1634, 'lng' => 123.6456, 'streets' => ['Jl. Kupang Timur', 'Jl. Raya Kupang Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
            ],
            'Papua' => [
                'Jayapura' => [
                    'Jayapura Pusat' => ['postal_code' => '99111', 'lat' => -2.5897, 'lng' => 140.7014, 'streets' => ['Jl. Raya Jayapura', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Jayapura Timur' => ['postal_code' => '99123', 'lat' => -2.5745, 'lng' => 140.7345, 'streets' => ['Jl. Jayapura Timur', 'Jl. Raya Jayapura Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
            ],
            'Papua Barat' => [
                'Manokwari' => [
                    'Manokwari Pusat' => ['postal_code' => '98311', 'lat' => -0.8636, 'lng' => 134.0757, 'streets' => ['Jl. Raya Manokwari', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Manokwari Timur' => ['postal_code' => '98323', 'lat' => -0.8534, 'lng' => 134.1045, 'streets' => ['Jl. Manokwari Timur', 'Jl. Raya Manokwari Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
            ],
            'Maluku' => [
                'Ambon' => [
                    'Ambon Pusat' => ['postal_code' => '97111', 'lat' => -3.6959, 'lng' => 128.1814, 'streets' => ['Jl. Raya Ambon', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Ambon Timur' => ['postal_code' => '97123', 'lat' => -3.6834, 'lng' => 128.2134, 'streets' => ['Jl. Ambon Timur', 'Jl. Raya Ambon Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
            ],
            'Maluku Utara' => [
                'Ternate' => [
                    'Ternate Pusat' => ['postal_code' => '97711', 'lat' => 0.7667, 'lng' => 127.3833, 'streets' => ['Jl. Raya Ternate', 'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Ahmad Yani']],
                    'Ternate Timur' => ['postal_code' => '97723', 'lat' => 0.7834, 'lng' => 127.4145, 'streets' => ['Jl. Ternate Timur', 'Jl. Raya Ternate Timur', 'Jl. Pendidikan', 'Jl. Gatot Subroto']],
                ],
            ],
        ];
    }

    /**
     * Get provinces from Indonesian locations
     */
    public static function indonesianProvinces(): array
    {
        return array_keys(self::indonesianLocations());
    }

    /**
     * Get cities by province
     */
    public static function indonesianCitiesByProvince(string $province): array
    {
        $locations = self::indonesianLocations();
        return array_keys($locations[$province] ?? []);
    }

    /**
     * Get location details (coordinates, postal code)
     */
    public static function getLocationDetails(string $province, string $city): ?array
    {
        $locations = self::indonesianLocations();
        return $locations[$province][$city] ?? null;
    }

    /**
     * Get cities by province
     */
    public function getCitiesByProvince(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'province' => 'required|string',
        ]);

        $province = $request->province;
        $cities = self::indonesianCitiesByProvince($province);

        if (empty($cities)) {
            return response()->json([
                'success' => false,
                'error' => 'Province not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'cities' => $cities,
        ]);
    }

    /**
     * Get streets by city
     */
    public function getStreetsByCity(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'province' => 'required|string',
            'city' => 'required|string',
        ]);

        $locations = self::indonesianLocations();
        $streets = $locations[$request->province][$request->city]['streets'] ?? [];

        return response()->json([
            'success' => true,
            'streets' => $streets,
        ]);
    }

    /**
     * Get location details (postal code, coordinates)
     */
    public function fetchLocationDetails(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'province' => 'required|string',
            'city' => 'required|string',
        ]);

        $details = self::getLocationDetails($request->province, $request->city);

        if (empty($details)) {
            return response()->json([
                'success' => false,
                'error' => 'Location not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'postal' => $details['postal'],
            'latitude' => $details['lat'],
            'longitude' => $details['lng'],
        ]);
    }

    /**
     * API endpoint to calculate shipping cost using Binderbyte API
     * Falls back to local calculation if API fails
     */
    public function calculateShipping(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'store_id'              => 'required|integer|exists:store_locations,id',
            'latitude'              => 'required|numeric|between:-90,90',
            'longitude'             => 'required|numeric|between:-180,180',
            'method'                => 'required|string|in:' . implode(',', array_keys(self::SHIPPING_METHODS)),
            'weight_grams'          => 'required|numeric|min:100',
            'destination_city'      => 'nullable|string|max:100',
            'destination_province'  => 'nullable|string|max:100',
            'zone'                  => 'nullable|string|in:A,B,C,D,E', // Optional: allow manual zone override
        ]);

        try {
            $store = StoreLocation::findOrFail($request->store_id);
            $shippingService = new ShippingService();
            
            $weight_grams = $request->weight_grams;
            $method = $request->method;
            
            // Initialize province variables
            $origin_province = null;
            $destination_province = null;
            
            // Determine zone
            // Priority: 1) Manual zone override, 2) Auto-determine from provinces
            if ($request->zone) {
                $zone = $request->zone;
                $origin_province = '?';
                $destination_province = '?';
                Log::info('[CHECKOUT] Using manual zone override', ['zone' => $zone]);
            } else {
                // Auto-determine zone from origin (store city → province) and destination provinces
                $origin_city = $store->city ?? '';
                $origin_province = $this->getCityProvince($origin_city) ?? 'Central Java'; // Fallback
                $destination_province = $request->destination_province ?? 'Central Java'; // Fallback
                
                $zone = $shippingService->determineZone($origin_province, $destination_province);
                
                Log::info('[CHECKOUT] Auto-determined zone', [
                    'store_city' => $origin_city,
                    'origin_province' => $origin_province,
                    'destination_province' => $destination_province,
                    'zone' => $zone,
                ]);
            }
            
            // Calculate shipping cost using zone-based formula:
            // cost = zone_base + weight_fee + service_fee
            $result = $shippingService->calculateShippingCost($weight_grams, $zone, $method);
            $cost = $result['cost'];
            $breakdown = $result['breakdown'];
            
            Log::info('[CHECKOUT] Shipping cost calculated (zone-based)', [
                'zone' => $zone,
                'courier' => $method,
                'weight_grams' => $weight_grams,
                'cost' => $cost,
                'breakdown' => $breakdown,
            ]);
            
            return response()->json([
                'success' => true,
                'zone' => $zone,
                'cost' => $cost,
                'method' => $method,
                'display' => 'Rp ' . number_format($cost, 0, ',', '.'),
                'note' => $store->name . ' → ' . ($request->destination_city ?? 'Unknown'),
                'origin_province' => $origin_province,
                'destination_province' => $destination_province,
                'breakdown' => [
                    'zone' => $breakdown['zone'],
                    'zone_base' => $breakdown['zone_base'],
                    'weight_kg' => $breakdown['weight_kg'],
                    'weight_fee' => $breakdown['weight_fee'],
                    'extra_kg' => $breakdown['extra_kg'] ?? 0,
                    'service_level' => $breakdown['service_level'],
                    'service_surcharge' => $breakdown['service_surcharge'],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('[CHECKOUT] Shipping calculation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to calculate shipping cost: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Map city name to province name
     * Uses mapping from major Indonesian cities
     */
    private function getCityProvince($city)
    {
        // City to province mapping for Indonesian cities
        $cityToProvince = [
            'jakarta' => 'DKI Jakarta',
            'bandung' => 'Jawa Barat',
            'bogor' => 'Jawa Barat',
            'depok' => 'Jawa Barat',
            'tasikmalaya' => 'Jawa Barat',
            'bekasi' => 'Jawa Barat',
            'surabaya' => 'Jawa Timur',
            'malang' => 'Jawa Timur',
            'gresik' => 'Jawa Timur',
            'kediri' => 'Jawa Timur',
            'yogyakarta' => 'Daerah Istimewa Yogyakarta',
            'semarang' => 'Jawa Tengah',
            'solo' => 'Jawa Tengah',
            'denpasar' => 'Bali',
            'medan' => 'Sumatera Utara',
            'palembang' => 'Sumatera Selatan',
            'bandar lampung' => 'Lampung',
            'batam' => 'Kepulauan Bangka Belitung',
            'pekanbaru' => 'Riau',
            'jambi' => 'Jambi',
            'makassar' => 'Sulawesi Selatan',
            'manado' => 'Sulawesi Utara',
            'pontianak' => 'Kalimantan Barat',
            'banjarmasin' => 'Kalimantan Selatan',
            'samarinda' => 'Kalimantan Timur',
            'jayapura' => 'Papua',
            'ambon' => 'Maluku',
            'ternate' => 'Maluku Utara',
        ];
        
        $cityLower = strtolower(trim($city ?? ''));
        return $cityToProvince[$cityLower] ?? null;
    }
}
