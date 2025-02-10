<?php

use App\Http\Controllers\AccController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function(){
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('items', App\Http\Controllers\ItemController::class);
    Route::resource('users', CustomerController::class);
    Route::resource('transactions', TransactionController::class)->except(['show']);
    Route::patch('/transactions/{transaction}', [AccController::class, 'acc'])->name('transactions.acc');
    Route::get('/transactions/status-chart', [TransactionController::class, 'getTransactionStatusData']);
    Route::get('/transactions/{id}/download-pdf', [TransactionController::class, 'downloadPDF'])->name('transactions.download-pdf');
});



