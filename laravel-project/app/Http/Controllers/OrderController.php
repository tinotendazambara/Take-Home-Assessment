<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // BUG: No authorization check - should only show user's own orders
        // BUG: No pagination
        
        // BUG: N+1 query problem - loads user relationship for each order
        $orders = Order::with('user')->get();

        return response()->json($orders);
    }

    public function show($id)
    {
        // BUG: No authorization check - users can see other users' orders
        // BUG: Eager loading missing - will cause N+1 when accessing items
        $order = Order::findOrFail($id);

        return response()->json($order);
    }

    public function store(Request $request)
    {
        // BUG: Missing validation for items array
        // BUG: No check if products exist
        // BUG: No check if products are in stock
        // BUG: No transaction handling - partial failures possible
        
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|integer',
        ]);

        // BUG: Race condition - stock check and update not atomic
        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            
            // BUG: No null check - will crash if product doesn't exist
            // BUG: No stock validation
            // BUG: Price taken from request instead of product - allows price manipulation
            
            $itemTotal = $product->price * $item['quantity'];
            $totalAmount += $itemTotal;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $item['price'] ?? $product->price, // BUG: Allows price override
            ];
        }

        // BUG: No transaction - if this fails, stock already updated
        $order = Order::create([
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'total_amount' => $totalAmount, // BUG: Calculated total may not match items
        ]);

        // BUG: No stock update
        // BUG: No creation of order items
        $order->items()->createMany($orderItems);

        return response()->json($order, 201);
    }

    public function update(Request $request, $id)
    {
        // BUG: Missing authorization check - should be admin only
        // BUG: No validation for status values
        // BUG: Can change status to invalid values
        
        $order = Order::findOrFail($id);
        
        // BUG: Direct assignment without validation
        $order->status = $request->input('status');
        $order->save();

        return response()->json($order);
    }

    public function getOrderStats(Request $request)
    {
        // BUG: Missing authorization - should be admin only
        // BUG: SQL injection vulnerability - raw query with user input
        $status = $request->input('status');
        
        // BUG: Direct SQL injection - $status not escaped
        $orders = DB::select("SELECT * FROM orders WHERE status = '$status'");

        return response()->json($orders);
    }
}

