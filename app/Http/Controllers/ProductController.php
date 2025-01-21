<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Product_category;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product_sub_category1;
use App\Models\Product_sub_category2;
use App\Models\Sizes;


class ProductController extends Controller
{

    public function index(Request $request)
    {
        try {
            // Initialize the query for products
            $query = Product::query();

            // Handle search input (search by product name, SKU, or category)
            $search = $request->input('search');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('p_long_name', 'LIKE', "%{$search}%")
                    ->orWhere('p_sku_no', 'LIKE', "%{$search}%")
                    ->orWhere('product_category', 'LIKE', "%{$search}%");
                });
            }

            // Handle filtering by product ID if provided
            $id = $request->input('id');
            if ($id) {
                $query->where('id', $id);
            }

            // Apply pagination with a default limit
            $limit = $request->input('limit', 10); // Default limit set to 10
            $productsPaginated = $query->paginate($limit);

            // Return the paginated response
            return response()->json([
                'status' => 200,
                'message' => 'OK',
                'result' => [
                    'data' => $productsPaginated,
                ],
            ]);
        } catch (\Exception $e) {
            // Catch any exceptions and return an error response
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'result' => [
                    'data' => [],
                ],
            ]);
        }
    }


    //Endpoint to get all products
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
            'eff_date' => 'nullable|string',
            'end_date' => 'nullable|string',
            'status' => 'nullable|string|max:50',
        ]);

        // Create the product
        $product = Product::create($validatedData);

        // Return a JSON response
        return response()->json([
            'status' => 200,
            'success' => 'Product created successfully.',
            'data' => $product,
        ], 201);
        return response()->json([
        'status' => 200,
        'message' => 'Product categories fetched successfully.',
        'result' => [
            'data' => $product
            ],
        ]);

    }



     // Helper Apis for Product Controller

     public function getproduct_cat(){
        $product_category = Product_category::select('id', 'category_name')->get(); // Fetch only id and category_name columns

        return response()->json([
        'status' => 200,
        'message' => 'Product categories fetched successfully.',
        'result' => [
            'data' => $product_category
            ],
        ]);
     }
     
     public function getproduct_sub_cat(){
        $product_sub_category1 = product_sub_category1::select('id', 'category_name')->get(); // Fetch only id and category_name columns

        return response()->json([
        'status' => 200,
        'message' => 'Product Sub categories 1 fetched successfully.',
        'result' => [
            'data' => $product_sub_category1
            ],
        ]);
     }

     public function getproduct_sub_cat2(){
        $product_sub_category2 = Product_sub_category2::select('id', 'category_name')->get(); // Fetch only id and category_name columns

        return response()->json([
        'status' => 200,
        'message' => 'Product Sub categories 2 fetched successfully.',
        'result' => [
            'data' => $product_sub_category2
            ],
        ]);
     }

     public function size_name(){
        $size = Sizes::select('size_name')->get(); 

        return response()->json([
        'status' => 200,
        'message' => 'Product Size fetched successfully.',
        'result' => [
            'data' => $size
            ],
        ]);

     }
     

}
