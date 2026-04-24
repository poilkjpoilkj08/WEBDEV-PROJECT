<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FirstController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // dd('hello world');
    return view('welcome');
});

Route::get('/hi', function(){
    return 'hi';
})->name('hi');

Route::get('/helo', function(){
    return 'helooo isb';
})->name('helo');

Route::get('/home', [HomeController::class, 'show'])->name('home');

Route::get('/home/sum', [FirstController::class, 'sum'])->name('home.sum');

Route::get('/home/multiply/{param1}/{param2?}', [FirstController::class, 'multiply'])->name('home.multiply');

Route::get('/about', function(){
    return view('about');
})->name('about');

Route::get('/login', [AuthController::class, 'show_login'])->name('login.show')->middleware('guest');

Route::post('/login_auth', [AuthController::class, 'login_auth'])->name('login.auth')->middleware('guest');

Route::middleware('auth')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['role:admin,owner'])->group (function() {
        Route::get('/product/insert-form', [StoreController::class, 'product_insert_form'])->name('product.insert-form');
        Route::post('/product/insert', [StoreController::class, 'insert_product'])->name('insert_product');
        Route::get('/product/edit/{product_id}', [StoreController::class, 'product_edit_form'])->name('product_edit_form');
        Route::put('/product/update/{product_id}', [StoreController::class, 'update_product'])->name('update_product');
        Route::delete('/product/delete/{product_id}', [StoreController::class, 'delete_product'])->name('delete_product');
    });

    Route::middleware(['role:customer,admin,owner'])->group (function() {
        Route::get('/store', [StoreController::class, 'show'])->name('store');
    });

    Route::post('/add-to-cart/{product_id}', [StoreController::class, 'add_to_cart'])->name('add_to_cart');
    Route::get('/view-cart', [StoreController::class, 'view_cart'])->name('view_cart');
    Route::patch('/update-cart/{product_id}', [StoreController::class, 'update_cart'])->name('update_cart');
    Route::delete('/remove-from-cart/{product_id}', [StoreController::class, 'remove_from_cart'])->name('remove_from_cart');
    Route::post('/checkout', [StoreController::class, 'checkout'])->name('checkout');

    //Midtrans auto checkout status after return
    Route::get('/payment/return/{order_id}', [StoreController::class, 'payment_return'])->name('payment_return');
    //Midtrans manual check status
    Route::get('/payment/status/{order_id}', [StoreController::class, 'payment_status'])->name('payment_status');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders');

    Route::get('/orders/{order_id}', [OrderController::class, 'order_details'])->name('order_details');
});