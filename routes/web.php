<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Public\GuestBookController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'public.home')->name('home');
Route::get('/guest-book', fn () => view('public.guest-book-form'))->name('guest-book.form');
Route::post('/guest-book', [GuestBookController::class, 'store'])->name('guest-book.store');
Route::get('/api/companies/active', function () {
    return response()->json(
        \App\Models\Company::active()->select('id', 'name')->orderBy('name')->get()
    );
})->name('companies.active');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('throttle:10,1');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
        Route::get('/guest-books', fn () => view('admin.guest-books'))->name('guest-books');
        Route::get('/companies', fn () => view('admin.companies'))->name('companies');
        Route::get('/settings', fn () => view('admin.settings'))->name('settings');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});
