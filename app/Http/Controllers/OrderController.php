<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetails;
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

        // Start a database transaction
        DB::transaction(function () use ($request) {
            // Create a new order
            $order = Order::create([
                'user_id' => Auth::id(), // Assuming the user is authenticated
                'total_amount' => $request->total_amount,
            ]);

            // Create order details
            foreach ($request->items as $item) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        });

        // Return a response (you can include more data as needed)
        return response()->json(['order' => $order], 201);
    }
}
