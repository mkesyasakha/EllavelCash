<?php

use App\Http\Controllers\AccController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Auth::routes();

Route::middleware('auth')->group(function(){
    Route::get('/home', [App\Http\Controllers\LandingController::class, 'dashboard'])->name('home');
    Route::resource('items', App\Http\Controllers\ItemController::class);
    Route::resource('transactions', TransactionController::class)->except(['show']);
    Route::get('/transactions/status-chart', [TransactionController::class, 'getTransactionStatusData']);
    Route::get('/transactions/{id}/download-pdf', [TransactionController::class, 'downloadPDF'])->name('transactions.download-pdf');
    Route::get('/latest-transactions', [MessageController::class, 'getLatestTransactions']);
    Route::post('/transactions/apply-promo', [TransactionController::class, 'applyPromo'])->name('transactions.applyPromo');
});

Route::middleware('auth', 'role:admin')->group(function(){
    Route::patch('/transactions/{transaction}', [AccController::class, 'acc'])->name('transactions.acc');
    Route::put('/transactions/{transaction}', [AccController::class, 'reject'])->name('transactions.reject');
    Route::resource('categories', CategoryController::class);
    Route::resource('users', CustomerController::class);
    Route::resource('discounts', DiscountController::class);
});



