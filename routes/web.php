<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventSettingsController;
use App\Http\Controllers\Admin\GuestBookController as AdminGuestBookController;
use App\Http\Controllers\Public\GuestBookController as PublicGuestBookController;
use App\Http\Controllers\Public\MessageWallController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'public.home')->name('home');
Route::view('/display', 'public.message-wall')->name('message-wall');
Route::get('/guest-book', fn () => view('public.guest-book-form'))->name('guest-book.form');
Route::post('/guest-book', [PublicGuestBookController::class, 'store'])->name('guest-book.store');
Route::get('/api/companies/active', function () {
    return response()->json(
        \App\Models\Company::active()->select('id', 'name')->orderBy('name')->get()
    );
})->name('companies.active');
Route::get('/api/messages', [MessageWallController::class, 'index'])->name('messages.index');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('throttle:10,1');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('guest-books')->name('guest-books.')->group(function () {
            Route::get('/', [AdminGuestBookController::class, 'index'])->name('index');
            Route::post('/{guestBook}/status', [AdminGuestBookController::class, 'updateStatus'])->name('status');
            Route::delete('/{guestBook}', [AdminGuestBookController::class, 'destroy'])->name('destroy');
            Route::get('/export', [AdminGuestBookController::class, 'export'])->name('export');
        });

        Route::prefix('companies')->name('companies.')->group(function () {
            Route::get('/', [CompanyController::class, 'index'])->name('index');
            Route::get('/create', [CompanyController::class, 'create'])->name('create');
            Route::post('/', [CompanyController::class, 'store'])->name('store');
            Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
            Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
            Route::post('/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
        });

        Route::get('/settings', [EventSettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [EventSettingsController::class, 'update'])->name('settings.update');

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});
