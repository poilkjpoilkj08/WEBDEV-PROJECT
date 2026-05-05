<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

// Language switching routes
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('/api/current-locale', [LanguageController::class, 'getCurrentLocale'])->name('language.current');

Route::get('/', [BookController::class, 'index'])->name('home');
Route::get('/books', [BookController::class, 'listing'])->name('books.listing');
Route::get('/books/search', [BookController::class, 'search'])->name('books.search');
Route::get('/books/{id}', [BookController::class, 'show'])->whereNumber('id')->name('books.show');

Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/authors/{id}', [AuthorController::class, 'show'])->whereNumber('id')->name('authors.show');

Route::get('/about', function(){
    return view('about');
})->name('about');

Route::get('/login', [AuthController::class, 'show_login'])->name('login.show')->middleware('guest');

Route::post('/login_auth', [AuthController::class, 'login_auth'])->name('login.auth')->middleware('guest');

Route::middleware('auth')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['role:admin,owner'])->group (function() {
        Route::get('/books/create-form', [BookController::class, 'create_form'])->name('books.create-form');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{id}/edit-form', [BookController::class, 'edit_form'])->name('books.edit-form');
        Route::put('/books/{id}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');

        Route::get('/authors/create-form', [AuthorController::class, 'create_form'])->name('authors.create-form');
        Route::post('/authors', [AuthorController::class, 'store'])->name('authors.store');
        Route::get('/authors/{id}/edit-form', [AuthorController::class, 'edit_form'])->name('authors.edit-form');
        Route::put('/authors/{id}', [AuthorController::class, 'update'])->name('authors.update');
        Route::delete('/authors/{id}', [AuthorController::class, 'destroy'])->name('authors.destroy');
    });
});