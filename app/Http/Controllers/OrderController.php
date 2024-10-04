<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'items' => 'required|array',
            'total_amount' => 'required|numeric',
        ]);

        // Create the order
        $order = Order::create([
            'user_id' => Auth::user()->id, // Get the authenticated user ID
            'total_amount' => $request->total_amount,
        ]);

        // Create order details for each item in the cart
        foreach ($request->items as $item) {
            // Create the order detail
            $orderDetail = OrderDetails::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            // Consume stock
            $product = Product::find($item['product_id']);
            if ($product) {
                // Check if there is enough stock to fulfill the order while maintaining minimal stock
                if ($product->stock >= ($item['quantity'] + $product->minimal_stock)) {
                    $product->stock -= $item['quantity']; // Decrease stock by quantity ordered
                    $product->save(); // Save the updated stock
                } else {
                    return response()->json(['error' => 'Insufficient stock for product ' . $product->name], 400);
                }
            }
        }

        return response()->json(['order' => $order], 201);
    }
}
