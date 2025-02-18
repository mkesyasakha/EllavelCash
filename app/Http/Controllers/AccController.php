<?php

namespace App\Http\Controllers;

use App\Models\Item;
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
    public function reject(Transaction $transaction)
    {
        $olditems = $transaction->items;
        foreach ($olditems as $olditem) {
            $item = Item::find($olditem->id);
            $oldquantity = $olditem->pivot->quantity;
            $item->increment('stock', $oldquantity);
        }
        
        $transaction->update([
            'status' => 'failed',
        ]);
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }
}
