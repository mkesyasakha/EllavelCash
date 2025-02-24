<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Discount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $transactions = Transaction::whereHas('customers', function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%");
        })->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
        ->orderBy('id', 'desc')
        ->paginate(6)
        ->appends(['search' => $search]);
        
        $transaction_customers = Transaction::where('user_id', auth()->id())
        ->whereHas('customers', function ($q) use ($search) {
            $q->where('name', 'like', "%$search%");
        })->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
        ->orderBy('id', 'desc')
        ->paginate(6)
        ->appends(['search' => $search]);

        $items = Item::all();
        $customers = User::role('customers')->get();
        return view('transactions.index', compact('transactions', 'customers', 'items', 'transaction_customers'));
    }

    public function downloadPDF($id)
    {
        $transaction = Transaction::with('items', 'customers')->findOrFail($id);

        $pdf = PDF::loadView('transactions.receipt', compact('transaction'));

        return $pdf->download('struk_transaksi_' . $transaction->id .'.pdf');
    }

    public function store(StoreTransactionRequest $request)
    {
        $total = 0;
        $itemsToAttach = [];

        foreach ($request->items as $index => $item_id) {
            $item = Item::find($item_id);
            $quantity = $request->quantities[$index] ?? 1;

            // Cek apakah stok cukup sebelum menyimpan gambar
            if (!$item || $item->stock < $quantity) {
                return redirect()->back()->with('error', "Stok untuk {$item->name} tidak mencukupi! Stok tersedia: {$item->stock}");
            }

            // Tambahkan item ke transaksi (jika stok cukup)
            $itemsToAttach[$item_id] = ['quantity' => $quantity];
            $total += $item->price * $quantity;
        }

        // Buat transaksi baru setelah stok diverifikasi
        $transaction = Transaction::create([
            'user_id' => $request->user_id,
            'total' => $total,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date ?? now(),
            'status' => $request->status,
        ]);

        // Simpan item yang telah diverifikasi ke transaksi
        $transaction->items()->attach($itemsToAttach);

        // Kurangi stok setelah transaksi berhasil dibuat
        foreach ($request->items as $index => $item_id) {
            $item = Item::find($item_id);
            $quantity = $request->quantities[$index] ?? 1;
            $item->stock -= $quantity;
            $item->save();
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        
        if ($request->hasFile('proof')) {
            if ($transaction->proof) {
                Storage::disk('public')->delete($transaction->proof);
            }
            $path = $request->file('proof')->store('proofs', 'public');
        }
        
        $transaction->update([
            'proof' => $path,
            'user_id' => $request->user_id,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
            'status' => $request->status,
        ]);
        
        $total = 0;
        $olditems = $transaction->items;
        foreach ($olditems as $olditem) {
            $item = Item::find($olditem->id);
            $oldquantity = $olditem->pivot->quantity;
            $item->increment('stock', $oldquantity);
        }
        $transaction->items()->detach();
        foreach ($request->items as $index => $item_id) {
            $item = Item::find($item_id);
            $quantity = $request->quantities[$index] ?? 1;
            $transaction->items()->attach($item_id, ['quantity' => $quantity]);
            $item->decrement('stock', $quantity);
            $total += $item->price * $quantity;
        }
        
        $transaction->update(['total' => $total]);
        
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function applyPromo(Request $request)
    {
        $promo = Discount::where('code', $request->promo_code)->first();

        if (!$promo) {
            return redirect()->back()->withErrors('Invalid promo code');
        }

        if($promo->status == 'expired'){
            return redirect()->back()->withErrors('Promo code expired');
        }else{
            $transaction = Transaction::findOrFail($request->transaction_id);
            $total = $transaction->total * ($promo->discount_percentage/100);
            $discount = $transaction->total - $total;
            $transaction->update([
                'discount_id' => $promo->id,
                'discount' => $discount,
                'total' => $discount,
            ]);
        }


        return redirect()->back()->with('success', 'Promo code applied successfully!');
    }


    

    public function destroy(Transaction $transaction){
        try{
            $transaction->items()->detach();
            if($transaction->proof) {
                Storage::disk('public')->delete($transaction->proof);
            }
            $transaction->delete();
            return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
        }catch(\Exception $e){
            return redirect()->route('transactions.index')->with('error', 'Failed to delete transaction. Please try again.');
        }
    }
}
