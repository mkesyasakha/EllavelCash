<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function getLatestTransactions()
    {
        $user = Auth::user(); 
        // dd($user);
        if ($user->hasRole('admin')) {
            $transactions = Transaction::with('customers')->latest()->take(5)->get();
        } else {
            $transactions = Transaction::with('customers')
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return response()->json($transactions);
    }
}

