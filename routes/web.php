<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ── Authenticated ─────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Reports PDF export
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    // Inside auth middleware group
    Route::get('/orders/export',       [OrderController::class, 'export'])->name('orders.export');
    Route::get('/products/export',     [ProductController::class, 'export'])->name('products.export');
    Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');

    // Transactions (read-only, auto-generated from completed orders)
    Route::resource('transactions', TransactionController::class)->only(['index', 'destroy']);

    // Orders (multi-item POS)
    Route::resource('orders', OrderController::class);
    Route::post('/orders/{order}/items',         [OrderController::class, 'addItem'])->name('orders.addItem');
    Route::delete('/orders/{order}/items/{item}', [OrderController::class, 'removeItem'])->name('orders.removeItem');
    Route::post('/orders/{order}/complete',       [OrderController::class, 'complete'])->name('orders.complete');

    Route::patch('/orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.updatePayment');
    Route::patch('/orders/{order}/items/{item}/qty', [OrderController::class, 'updateItemQty'])->name('orders.updateItemQty');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password',   [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Admin only ────────────────────────────────────
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::resource('products', ProductController::class);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

});

require __DIR__.'/auth.php';