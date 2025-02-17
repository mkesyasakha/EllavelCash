<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use Illuminate\Support\Str;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discounts = Discount::orderBy('created_at', 'desc')->get();
        $randomCode = Str::upper(Str::random(8));
        return view('discounts.index', compact('discounts', 'randomCode'));
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
    public function store(StoreDiscountRequest $request)
    {
        Discount::create($request->all());
        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Discount $discount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discount $discount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDiscountRequest $request, Discount $discount)
    {
        $discount->update($request->all());
        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        try{
            $discount->delete();
            return redirect()->route('discounts.index')->with('success', 'Diskon berhasil dihapus.');
        }catch(\Exception $e){
            return redirect()->route('discounts.index')->with('error', 'Gagal menghapus diskon. Silakan coba lagi.');
        }
    }
}
