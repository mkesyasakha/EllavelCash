<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort_by;
        $items = Item::with('category') // Eager load kategori untuk menghindari N+1 problem
            ->when($sort, function ($query) use ($sort) {
                $query->whereHas('category', function ($q) use ($sort) {
                    $q->where('id', $sort);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(5)
            ->appends(request()->query());
        $categories = Category::all();
        return view('items.index', compact('items', 'categories'));
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
    public function store(StoreItemRequest $request)
    {
        $path = $request->file('photo')->store('photo', 'public');
        Item::create([
            'photo' => $path,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock' => $request->stock,
        ]);
        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        if($request->hasFile('photo')){
            if($item->photo){
                Storage::disk('public')->delete($item->photo);
            }
            $path = $request->file('photo')->store('photo', 'public');
        }
        $item->update([
            'photo' => $path,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock' => $request->stock,
        ]);
        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        try{
            $item->delete();
            return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
        }catch(Exception $e){
            return redirect()->route('items.index')->with('error', 'Failed to delete item. It may be referenced by other records.');
        }
    }
}
