<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $count = Product::where('is_active', 1)->count();
        $stock = Product::where('is_active', 1)
            ->get()
            ->sum(function ($product) {
                return $product->stock;
            });
        $stock_min = Product::where('is_active', 1)
            ->where('stock', '<=', 2)
            ->count();

        return view('dashboard', compact('count', 'stock', 'stock_min'));
    }
}
