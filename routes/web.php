<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StoreLocationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RefundController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('/api/current-locale', [LanguageController::class, 'getCurrentLocale'])->name('language.current');

Route::get('/', [BookController::class, 'index'])->name('home');
Route::get('/books', [BookController::class, 'listing'])->name('books.listing');
Route::get('/books/search', [BookController::class, 'search'])->name('books.search');
Route::get('/books/{id}', [BookController::class, 'show'])->whereNumber('id')->name('books.show');

Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/authors/{id}', [AuthorController::class, 'show'])->whereNumber('id')->name('authors.show');

Route::get('/about', function () { return view('about'); })->name('about');

Route::get('/subscribe', [SubscriptionController::class, 'plans'])->name('subscribe.plans');
Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
Route::get('/unsubscribe/{token}', [SubscriptionController::class, 'unsubscribe'])->name('unsubscribe');

Route::get('/api/stores', [StoreLocationController::class, 'all'])->name('stores.all');
Route::get('/api/stores/book/{bookId}', [StoreLocationController::class, 'forBook'])->name('stores.for-book');

Route::post('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.calculate-shipping');
Route::post('/checkout/get-cities', [CheckoutController::class, 'getCitiesByProvince'])->name('checkout.get-cities');
Route::post('/checkout/get-streets', [CheckoutController::class, 'getStreetsByCity'])->name('checkout.get-streets');
Route::post('/checkout/fetch-location-details', [CheckoutController::class, 'fetchLocationDetails'])->name('checkout.fetch-location-details');

// Debug route for testing shipping calculation
Route::get('/debug/shipping-test', function () {
    $shippingService = new \App\Services\ShippingService();
    
    $testCases = [
        [
            'from' => 'Yogyakarta',
            'to' => 'Jakarta',
            'weight' => 1,
            'method' => 'jne_reg',
        ],
        [
            'from' => 'Surabaya',
            'to' => 'Bandung',
            'weight' => 1,
            'method' => 'sicepat',
        ],
        [
            'from' => 'Denpasar',
            'to' => 'Jakarta',
            'weight' => 1,
            'method' => 'gosend',
        ],
    ];

    $results = [];
    foreach ($testCases as $test) {
        $cost = $shippingService->calculateShippingCost(
            $test['from'],
            $test['to'],
            $test['weight'],
            $test['method']
        );
        $results[] = [
            'from' => $test['from'],
            'to' => $test['to'],
            'method' => $test['method'],
            'cost' => $cost,
            'display' => 'Rp ' . number_format($cost, 0, ',', '.'),
        ];
    }

    return response()->json([
        'message' => 'Shipping calculation test results',
        'results' => $results,
        'log_file' => storage_path('logs/laravel.log'),
    ]);
})->name('debug.shipping-test');

Route::get('/login', [AuthController::class, 'show_login'])->name('login.show')->middleware('guest');
Route::post('/login_auth', [AuthController::class, 'login_auth'])->name('login.auth')->middleware('guest');

Route::get('/register', [AuthController::class, 'show_register'])->name('register.show')->middleware('guest');
Route::post('/register', [AuthController::class, 'register_store'])->name('register.store')->middleware('guest');

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google')->middleware('guest');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback')->middleware('guest');

// Debug test routes
Route::middleware('auth')->get('/test/auth-check', function () {
    return response()->json([
        'authenticated' => true,
        'user_id' => auth()->id(),
        'user_email' => auth()->user()->email,
        'csrf_token_exists' => csrf_token() ? 'yes' : 'no',
    ]);
});

Route::middleware('auth')->post('/test/echo-csrf', function () {
    return response()->json([
        'csrf_header_received' => request()->header('X-CSRF-TOKEN') ? 'yes' : 'no',
        'csrf_token_correct' => csrf_token() === request()->header('X-CSRF-TOKEN') ? 'yes' : 'no',
    ]);
});

// Diagnostic endpoint to trace the 403 error
Route::middleware('auth')->post('/test/diagnose-payment-token', function (\Illuminate\Http\Request $request) {
    $diagnosis = [
        'timestamp' => now()->toIso8601String(),
        'request' => [
            'method' => $request->method(),
            'path' => $request->path(),
            'url' => $request->url(),
            'origin' => $request->header('Origin'),
        ],
        'auth' => [
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
        ],
        'csrf' => [
            'header_sent' => $request->header('X-CSRF-TOKEN') ? 'YES' : 'NO',
            'header_value' => $request->header('X-CSRF-TOKEN') ? substr($request->header('X-CSRF-TOKEN'), 0, 20) . '...' : 'NONE',
            'session_token' => csrf_token() ? substr(csrf_token(), 0, 20) . '...' : 'NONE',
            'tokens_match' => ($request->header('X-CSRF-TOKEN') === csrf_token()) ? 'YES' : 'NO',
        ],
        'session' => [
            'session_id' => session()->getId(),
            'session_driver' => config('session.driver'),
            'session_cookie_name' => config('session.cookie'),
            'session_path' => config('session.path'),
            'session_domain' => config('session.domain'),
            'session_secure' => config('session.secure'),
            'session_http_only' => config('session.http_only'),
            'session_same_site' => config('session.same_site'),
        ],
        'cookies' => [
            'received' => array_keys($_COOKIE),
            'has_session_cookie' => isset($_COOKIE[config('session.cookie')]) ? 'YES' : 'NO',
        ],
        'headers' => [
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'user_agent' => $request->header('User-Agent'),
        ],
    ];

    \Log::info('DIAGNOSIS - Payment Token Request', $diagnosis);
    
    return response()->json($diagnosis);
});

// Test endpoint WITHOUT CSRF validation to verify session cookie and credentials
Route::middleware('auth')->post('/test/session-test', function (\Illuminate\Http\Request $request) {
    $result = [
        'status' => 'SESSION TEST - No CSRF validation',
        'session_maintained' => session()->has('_token') ? 'YES' : 'NO',
        'session_id' => session()->getId(),
        'user_authenticated' => auth()->check() ? 'YES' : 'NO',
        'user_id' => auth()->id(),
        'timestamp' => now()->toIso8601String(),
    ];
    
    \Log::info('SESSION TEST - Session is working', $result);
    
    return response()->json($result);
});

// Diagnostic endpoint to check order ownership
Route::middleware('auth')->get('/test/check-orders', function () {
    $user = auth()->user();
    $orders = \App\Models\Order::where('user_id', $user->id)->get(['id', 'user_id', 'status', 'created_at']);
    
    $diagnosis = [
        'authenticated_user_id' => $user->id,
        'authenticated_user_email' => $user->email,
        'orders_owned_by_user' => [
            'count' => $orders->count(),
            'orders' => $orders->map(fn($o) => [
                'id' => $o->id,
                'status' => $o->status,
                'created_at' => $o->created_at->toIso8601String(),
            ])->toArray(),
        ],
        'google_id' => $user->google_id,
        'timestamp' => now()->toIso8601String(),
    ];
    
    \Log::info('ORDER DIAGNOSIS - User Orders', $diagnosis);
    
    return response()->json($diagnosis);
});

// Endpoint to check specific order ownership (debug)
Route::middleware('auth')->post('/test/check-order-ownership', function (\Illuminate\Http\Request $request) {
    $user = auth()->user();
    $order_id = $request->input('order_id');
    
    if (!$order_id) {
        return response()->json(['error' => 'Missing order_id'], 400);
    }
    
    $order = \App\Models\Order::find($order_id);
    
    if (!$order) {
        return response()->json([
            'error' => 'Order not found',
            'order_id_requested' => $order_id,
        ], 404);
    }
    
    $diagnosis = [
        'request' => [
            'authenticated_user_id' => $user->id,
            'authenticated_user_email' => $user->email,
            'requested_order_id' => $order_id,
        ],
        'order' => [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'status' => $order->status,
            'created_at' => $order->created_at->toIso8601String(),
        ],
        'ownership' => [
            'user_id_match' => $user->id === $order->user_id,
            'error' => $user->id !== $order->user_id ? 'Order does not belong to user' : null,
        ],
        'timestamp' => now()->toIso8601String(),
    ];
    
    \Log::info('ORDER OWNERSHIP DEBUG', $diagnosis);
    
    return response()->json($diagnosis);
});

Route::middleware('auth')->get('/test/send-email', function () {
    $user = auth()->user();
    $order = $user->orders()->latest()->first();
    
    if (!$order) {
        return response()->json(['error' => 'No orders found'], 404);
    }
    
    try {
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrderReceiptMail($order));
        return response()->json(['success' => 'Email sent to ' . $user->email]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::middleware('auth')
->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order_id}', [OrderController::class, 'order_details'])->name('orders.show');
    
    // User order actions - delivery confirmation and refund requests
    Route::post('/orders/{order_id}/confirm-delivery', [OrderController::class, 'confirmDelivery'])->name('orders.confirm-delivery');
    Route::post('/orders/{order_id}/request-refund', [OrderController::class, 'requestRefund'])->name('orders.request-refund');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::get('/wishlist/check/{bookId}', [WishlistController::class, 'isInWishlist'])->name('wishlist.check');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/mark-payment-complete', [CheckoutController::class, 'markPaymentComplete'])->name('checkout.mark-payment-complete');
    Route::post('/checkout/generate-payment-token', [CheckoutController::class, 'generatePaymentToken'])->name('checkout.generate-payment-token');
    Route::post('/checkout/save-address', [CheckoutController::class, 'saveAddress'])->name('checkout.save-address');

    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('books.reviews.store');

    // Refund routes
    Route::post('/refunds/request', [RefundController::class, 'request'])->name('refunds.request');

    Route::middleware(['role:admin,owner'])->group(function () {

        // ── Books CRUD ───────────────────────────────────────────────────────
        Route::get('/admin/books', [BookController::class, 'admin_index'])->name('admin.books.index');
        Route::get('/books/create-form', [BookController::class, 'create_form'])->name('books.create-form');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{id}/edit-form', [BookController::class, 'edit_form'])->name('books.edit-form');
        Route::put('/books/{id}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');

        // ── Authors CRUD ─────────────────────────────────────────────────────
        Route::get('/admin/authors', [AuthorController::class, 'admin_index'])->name('admin.authors.index');
        Route::get('/authors/create-form', [AuthorController::class, 'create_form'])->name('authors.create-form');
        Route::post('/authors', [AuthorController::class, 'store'])->name('authors.store');
        Route::get('/authors/{id}/edit-form', [AuthorController::class, 'edit_form'])->name('authors.edit-form');
        Route::put('/authors/{id}', [AuthorController::class, 'update'])->name('authors.update');
        Route::delete('/authors/{id}', [AuthorController::class, 'destroy'])->name('authors.destroy');

        // ── Store Locations CRUD ─────────────────────────────────────────────
        Route::get('/admin/stores', [StoreLocationController::class, 'index'])->name('admin.stores.index');
        Route::get('/admin/stores/create', [StoreLocationController::class, 'create_form'])->name('admin.stores.create');
        Route::post('/admin/stores', [StoreLocationController::class, 'store'])->name('admin.stores.store');
        Route::get('/admin/stores/{id}/edit', [StoreLocationController::class, 'edit_form'])->name('admin.stores.edit');
        Route::put('/admin/stores/{id}', [StoreLocationController::class, 'update'])->name('admin.stores.update');
        Route::delete('/admin/stores/{id}', [StoreLocationController::class, 'destroy'])->name('admin.stores.destroy');

        // ── Orders Management ────────────────────────────
        Route::get('/admin/orders', [OrderController::class, 'adminIndex'])->name('admin.orders.index');
        Route::put('/admin/orders/{order_id}', [OrderController::class, 'update'])->name('admin.orders.update');
        
        // Admin refund management
        Route::get('/admin/refunds', [RefundController::class, 'index'])->name('admin.refunds.index');
        Route::post('/admin/refunds/{refund}/approve', [RefundController::class, 'approve'])->name('admin.refunds.approve');
        Route::post('/admin/refunds/{refund}/reject', [RefundController::class, 'reject'])->name('admin.refunds.reject');
    });
});

// --- FAQ Terminal ---
Route::get('/faq', function () {
    // Optional array payload configuration matching your template manifest properties
    $indonesianLocations = \App\Http\Controllers\CheckoutController::indonesianLocations() ?? [];
    return view('faq.faq', compact('indonesianLocations'));
})->name('faq');

// --- Recommendation Roulette ---
    Route::get('/roulette', function () {
        return view('roulette.roulette');
    })->name('books.roulette');
