<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class AccController extends Controller
{
    public function acc(Transaction $transaction)
    {
        $transaction->update([
            'status' => 'success',
        ]);
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }
}
