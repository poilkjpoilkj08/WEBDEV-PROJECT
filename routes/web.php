<?php

use App\Http\Controllers\AuthController;
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

Route::get('/login', [AuthController::class, 'show_login'])->name('login.show')->middleware('guest');
Route::post('/login_auth', [AuthController::class, 'login_auth'])->name('login.auth')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order_id}', [OrderController::class, 'order_details'])->name('orders.show');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::get('/wishlist/check/{bookId}', [WishlistController::class, 'isInWishlist'])->name('wishlist.check');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/mark-payment-complete', [CheckoutController::class, 'markPaymentComplete'])->name('checkout.mark-payment-complete');
    Route::post('/checkout/save-address', [CheckoutController::class, 'saveAddress'])->name('checkout.save-address');

    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('books.reviews.store');

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
    });
});
