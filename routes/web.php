<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AccountController,
    UtilityController,
    DashboardController,
    ProfileController,
    AdminController
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'));

/*
|--------------------------------------------------------------------------
| Authenticated & Verified Users
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /**
     * Dashboard
     */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /**
     * Banking Operations
     */
    Route::post('/transfer/confirm', [AccountController::class, 'confirmTransfer'])
        ->name('account.transfer.confirm');

    Route::post('/transfer/execute', [AccountController::class, 'executeTransfer'])
        ->name('account.transfer.execute');

    Route::post('/deposit', [AccountController::class, 'deposit'])
        ->name('account.deposit');

    Route::post('/withdraw', [AccountController::class, 'withdraw'])
        ->name('account.withdraw');

    Route::post('/account/update-pin', [AccountController::class, 'updatePin'])
        ->name('pin.update');

    /**
     * Unified Utilities (Airtime, Data, TV, Electricity)
     * This matches the {{ route('utility.pay') }} called in your modals
     */
    Route::post('/utility/pay', [UtilityController::class, 'pay'])
        ->name('utility.pay');

    // Legacy Alias: Redirects airtime.purchase to the new unified Utility pay method
    Route::post('/airtime/purchase', [UtilityController::class, 'pay'])
        ->name('airtime.purchase');

    /**
     * Profile Management
     */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * Admin Panel (Pro Max Restricted)
     */
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/accounts', [AdminController::class, 'accounts'])->name('admin.accounts');
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('admin.transactions');
    });
});

require __DIR__ . '/auth.php';
