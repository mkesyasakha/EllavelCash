<?php

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
    Route::resource('transactions', TransactionController::class);
    Route::put('/transactions/{transaction}', [TransactionController::class, 'acc'])->name('transactions.acc');
});


