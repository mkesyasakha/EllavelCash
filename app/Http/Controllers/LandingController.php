<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\User;

class LandingController extends Controller
{
    public function index()
    {
        $items = Item::latest()->take(6)->get(); // Ambil 6 item terbaru
        return view('welcome', compact('items'));
    }

    public function dashboard()
    {
        $items = Item::count();
        $customers = User::count();
        $categories = Category::count();
        $discounts = Discount::count();

        return view('home', compact('items', 'customers', 'categories', 'discounts'));
    }
}
