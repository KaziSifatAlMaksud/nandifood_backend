<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'p_sku_no' => 'required|string|max:50',
            'p_long_name' => 'nullable|string|max:255',
            'product_short_name' => 'nullable|string|max:100',
            'product_category' => 'nullable|string|max:100',
            'sub_category1' => 'nullable|string|max:100',
            'sub_category2' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'default_sales_uom' => 'nullable|string|max:50',
            'inventory_uom' => 'nullable|string|max:50',
            'product_cert1' => 'nullable|string|max:255',
            'product_cert2' => 'nullable|string|max:255',
            'product_cert3' => 'nullable|string|max:255',
            'product_upc' => 'nullable|string|max:50',
            'default_warehouse' => 'nullable|string|max:100',
            'warehouse_city' => 'nullable|string|max:100',
            'warehouse_state' => 'nullable|string|max:100',
            'warehouse_country' => 'nullable|string|max:100',
            'product_manager' => 'nullable|string|max:100',
            'eff_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
            'img1' => 'nullable|string|max:255',
            'upc_barcode' => 'nullable|string|max:255',
        ]);

        // Create the product
        $product = Product::create($validatedData);

        // Return a JSON response
        return response()->json([
            'message' => 'Product created successfully.',
            'product' => $product,
        ], 201);
    }
}
