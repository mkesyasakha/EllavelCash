<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class LandingController extends Controller
{
    public function index()
    {
        $items = Item::latest()->take(6)->get(); // Ambil 6 item terbaru
        return view('welcome', compact('items'));
    }
}
