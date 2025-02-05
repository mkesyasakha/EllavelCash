<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::all();
        $customers = User::role('customers')->get();
        return view('transactions.index', compact('transactions', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $path = $request->file('proof')->store('proofs', 'public');
        $total = 0;
        Transaction::create([
            'proof' => $path,
            'user_id' => $request->user_id,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
            'status' => $request->status,
            'total' => $total,
        ]);
        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function acc(Transaction $transaction)
    {
        $transaction->update([
            'status' => 'success'
        ]);
        return redirect()->route('transactions.index')->with('success', 'Transaction accepted successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->proof) {
            Storage::disk('public')->delete($transaction->proof);
        }
        $path = $request->file('proof')->store('proofs', 'public');
        $total = 0;
        $transaction->update([
            'proof' => $path,
            'user_id' => $request->user_id,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
            'status' => $request->status,
            'total' => $total,
        ]);
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        try{
            if ($transaction->proof) {
                Storage::disk('public')->delete($transaction->proof);
            }
            $transaction->delete();
            return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
        }catch(\Exception $e){
            return redirect()->route('transactions.index')->with('error', 'Failed to delete transaction. Please try again.');
        }
    }
}
