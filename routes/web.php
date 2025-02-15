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
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('items', App\Http\Controllers\ItemController::class);
    Route::resource('users', CustomerController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('transactions', TransactionController::class)->except(['show']);
    Route::resource('discounts', DiscountController::class);
    Route::patch('/transactions/{transaction}', [AccController::class, 'acc'])->name('transactions.acc');
    Route::get('/transactions/status-chart', [TransactionController::class, 'getTransactionStatusData']);
    Route::get('/transactions/{id}/download-pdf', [TransactionController::class, 'downloadPDF'])->name('transactions.download-pdf');
    Route::get('/latest-transactions', [MessageController::class, 'getLatestTransactions']);
    Route::post('/transactions/apply-promo', [TransactionController::class, 'applyPromo'])->name('transactions.applyPromo');

});



