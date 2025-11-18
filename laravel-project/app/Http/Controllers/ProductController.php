<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // BUG: No pagination - could return thousands of products
        // BUG: No filtering or sorting options
        $products = Product::all();

        return response()->json($products);
    }

    public function show($id)
    {
        // BUG: No input validation on $id
        $product = Product::findOrFail($id);

        return response()->json($product);
    }

    public function store(Request $request)
    {
        // BUG: Missing authorization check - should be admin only
        // BUG: Incomplete validation - missing price validation, stock validation
        $request->validate([
            'name' => 'required|string',
            'description' => 'string',
            // BUG: Price can be negative or zero
            // BUG: Stock can be negative
        ]);

        // BUG: No check for duplicate product names
        $product = Product::create($request->all());

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        // BUG: Missing authorization check
        // BUG: No validation rules
        // BUG: Can update price to negative values
        
        $product = Product::findOrFail($id);
        $product->update($request->all());

        return response()->json($product);
    }

    public function destroy($id)
    {
        // BUG: Missing authorization check
        // BUG: No check if product has orders - will cause data integrity issues
        
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}

