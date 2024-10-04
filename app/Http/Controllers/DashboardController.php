<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if (Auth::user()->user_type == 'admin') {
            return view('dashboard', compact('count', 'stock', 'stock_min'));
        } else {
            $products = Product::where('stock', '>', 1)
                ->where('is_active', 1)
                ->limit(12)
                ->get();

            return view('products.consume', compact('products'));
        }
    }

    public function home()
    {
        return view('welcome');
    }
}
