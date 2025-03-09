<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Uom;
use App\Models\Uom_type;    
use App\Models\Product_category;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product_sub_category1;
use App\Models\Product_sub_category2;
use App\Models\Sizes;
use App\Models\Employee;
use App\Models\Price;
use App\Models\ProductNote;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
 use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{

  
    public function index(Request $request)
    {
        try {
            $query = Product::query();
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
            $productsPaginated = $query->orderBy('id','DESC')->paginate($limit);

            // Enrich each product with additional data
            $productsPaginated->getCollection()->transform(function ($product) {
                $product->product_category_name = Product_category::find($product->product_category)?->category_name ?? '';
                $product->sub_category1_name = Product_sub_category1::find($product->sub_category1)?->category_name ?? '';
                $product->sub_category2_name = Product_sub_category2::find($product->sub_category2)?->category_name ?? '';
                

                $product->default_sales_uom_name = Uom::find($product->default_sales_uom)?->uom_id 
                ? Uom::find($product->default_sales_uom)?->uom_id . ' ' . 
                Uom_type::find(Uom::find($product->default_sales_uom)?->uom_type_id)?->uom_name 
                : '';

                $product->inventory_uom_name = Uom::find($product->inventory_uom)?->uom_id 
                ? Uom::find($product->inventory_uom)?->uom_id . ' ' . 
                Uom_type::find(Uom::find($product->inventory_uom)?->uom_type_id)?->uom_name 
                : '';

                $product->purchase_uom_name = Uom::find($product->purchase_uom)?->uom_id 
                ? Uom::find($product->purchase_uom)?->uom_id . ' ' . 
                Uom_type::find(Uom::find($product->purchase_uom)?->uom_type_id)?->uom_name 
                : '';

                $product->production_uom_name = Uom::find($product->production_uom)?->uom_id 
                ? Uom::find($product->production_uom)?->uom_id . ' ' . 
                Uom_type::find(Uom::find($product->production_uom)?->uom_type_id)?->uom_name 
                : '';
           
           
                $product->size_kg = Sizes::find($product->size)?->size_kg ?? '';
                $product->size_lb = Sizes::find($product->size)?->size_lb ?? '';
                return $product;
            });

            // Return the paginated and enriched response
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

    

    public function product_price_list($product_id)
    {
        try {
            // Find the product by ID
            $product = Product::where('id', $product_id)->first();

            // Check if product is found
            if (!$product) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Product not found.',
                ], 404);
            }

            // Get the price list for the product based on SKU
            $price_list = Price::where('sku', $product->p_sku_no)->get();

            // Return the price list successfully
            return response()->json([
                'status' => 200,
                'message' => 'Product price fetched successfully.',
                'result' => [
                    'data' => $price_list,
                ],
            ]);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the product prices.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function product_quintity_list($product_id)
    {
        try {
            // Find the product by ID
            $product = Product::find($product_id);

            // Check if product is found
            if (!$product) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Product not found.',
                ], 404);
            }

            // Get the price list for the product based on SKU
            $price_list = Price::where('sku', $product->p_sku_no)->get();

            // Return the price list successfully
            return response()->json([
                'status' => 200,
                'message' => 'Product Quantity fetched successfully.',
                'result' => [
                    'data' => $price_list,
                ],
            ]);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the product prices.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function product_uom($product_id)
    {
        try {
            $product = Product::find($product_id);

            // Check if product is found
            if (!$product) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Product not found.',
                ], 404);
            }

            // Get the price list for the product based on SKU (combining results)
        $price_list = Uom::whereIn('id', [
                    $product->default_sales_uom,
                    $product->inventory_uom,
                    $product->purchase_uom,
                    $product->production_uom
                ])->get(); 
                 $modified_price_list = [];
        foreach ($price_list as $uom) {
                $result = Uom::fullName($uom->id);

                // Append the additional details
                $uom->uom_type_name = $result['uom_type_name'] ?? null;
                $uom->short_name = $result['short_name'] ?? null;
                $uom->full_name = $result['full_name'] ?? null;
                $modified_price_list[] = $uom;
            }
            // Return the price list successfully
            return response()->json([
                'status' => 200,
                'message' => 'Product Quantity fetched successfully.',
                'result' => [
                    'data' => $modified_price_list,
                ],
            ]);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the product prices.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function get_all_notes($productId)
    {
        try {
            $product_info = Product::where('id', $productId)->select('notes')->first();

            $product_notes = ProductNote::where('product_id', $productId)->get();
            $product_notes->map(function ($note) {
                if ($note->file_path) {
                    $note->file = Storage::disk('spaces')->url($note->file_path);
                    $note->file_name = basename($note->file_path);
                } else {
                    $note->file = null; // If there's no file, set it to null
                    $note->file_name = null;
                }
                return $note;
            });
            return response()->json([
                'status' => 200,
                'message' => 'Product Notes retrieved successfully.',
                'result' => [
                    'data' => $product_notes,
                    'product_info' => $product_info,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function product_notes_store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|string|max:11',
                'file_path' => 'nullable|file|mimes:pdf,png,jpg,jpeg',
                'note_date' => 'nullable|date',
                'file_description' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            $productInfo = Product::find($validated['product_id']);
            $productNote = null;

            if ($productInfo) {
                $productInfo->notes = $validated['file_description'];
                $productInfo->save();
            }

            // Handle file upload if a file is provided
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = "uploads/product_notes/{$fileName}";

                if (Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public'])) {
                    $validated['file_path'] = $path;
                    $productNote = ProductNote::create($validated);
                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
            }

            DB::commit();

            return response()->json([
                    'status' => 200,
                    'message' => ($productNote || $productInfo)
                        ? trim(
                            ($productNote ? 'Product Notes created successfully. ' : '') .
                            ($productInfo ? 'Product updated successfully.' : '')
                        )
                        : 'No changes were made.',
                    'result' => [
                        'data' => $productNote,
                        'productInfo' => $productInfo ? ['notes' => $productInfo->notes] : null
                    ]
                ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function product_notes_delete($id)
    {
        try {
            // Find the product note by ID
            $productNote = ProductNote::findOrFail($id);
            if ($productNote->file_path) {
                $filePath = $productNote->file_path;
                if (Storage::disk('spaces')->exists($filePath)) {
                    Storage::disk('spaces')->delete($filePath);
                }
            }
            $productNote->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Product Note deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    //Endpoint to get all products
    public function store(Request $request)
    {
        $action = $request->action; 
        $isApprove = ($action == 'approve') ? 2 : 1;
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
            'purchase_uom' => 'nullable|string|max:50',
            'production_uom' => 'nullable|string|max:50',
            'product_cert1' => 'nullable|string|max:255',
            'product_cert2' => 'nullable|string|max:255',
            'product_cert3' => 'nullable|string|max:255',
            'product_upc' => 'nullable|string|max:50',
            'default_warehouse' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',      
            'product_manager' => 'nullable|string|max:100',
            'eff_date' => 'nullable|string',
            'end_date' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'img1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            'upc_barcode' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);

        $validatedData['is_approved'] = $isApprove;

        // Create the product
        $product = Product::create($validatedData);

        // Handle image file (img1)
        if ($request->hasFile('img1')) {
            $file = $request->file('img1');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Ensure unique file names
            $path = "uploads/products/{$fileName}";
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);

            if ($uploaded) {
                $product->img1 = $path;
            } else {
                throw new \Exception('Failed to upload image to DigitalOcean Spaces.');
            }
        }

        // Handle barcode file (upc_barcode)
        if ($request->hasFile('upc_barcode')) {
            $file = $request->file('upc_barcode');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Ensure unique file names
            $path = "uploads/products/{$fileName}";
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);

            if ($uploaded) {
                $product->upc_barcode = $path;  // Assuming you want to store the path in a different field
            } else {
                throw new \Exception('Failed to upload barcode to DigitalOcean Spaces.');
            }
        }

        // Save the product with the file paths if uploaded
        $product->save();

        // Return a JSON response
        return response()->json([
            'status' => 200,
            'success' => 'Product created successfully.',
            'data' => $product,
        ], 201);
    }


    public function update2(Request $request, $id)
{
    try {
        $action = $request->action; 
        $isApprove = ($action == 'approve') ? 2 : 1;
        // Find the product or return a 404 error
        $product = Product::findOrFail($id);
        $product->fill($request->except(['img1', 'upc_barcode']));
        if ($request->hasFile('img1')) {
            $file = $request->file('img1');
            $fileName = $file->getClientOriginalName();
            $path = "uploads/products/{$fileName}";
            if (!empty($product->img1)) {
                Storage::disk('spaces')->delete($product->img1);
            }
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
            if ($uploaded) {
                $product->img1 = $path;
            } else {
                throw new \Exception('Failed to upload img1 to DigitalOcean Spaces.');
            }
        }
        $product->is_approved = $isApprove;
        if ($request->hasFile('upc_barcode')) {
            $file = $request->file('upc_barcode');
            $fileName = $file->getClientOriginalName();
            $path = "uploads/barcodes/{$fileName}";

            // Delete old barcode if exists
            if (!empty($product->upc_barcode)) {
                Storage::disk('spaces')->delete($product->upc_barcode);
            }

            // Upload new barcode to DigitalOcean Spaces
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
            if ($uploaded) {
                $product->upc_barcode = $path;
            } else {
                throw new \Exception('Failed to upload upc_barcode to DigitalOcean Spaces.');
            }
        }
        $product->save();

        return response()->json([
            'status' => 200,
            'message' => 'Product updated successfully.',
            'data' => $product,
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'status' => 404,
            'message' => 'Product not found.',
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred: ' . $e->getMessage(),
        ], 500);
    }
}


    
  public function show($id)
{
    try {
        // Find the product by ID or throw a 404 error if not found
        $product = Product::findOrFail($id);

        // Fetch related data
        $product->product_category_name = Product_category::where('id', $product->product_category_id)->value('category_name') ?? '';
        $product->sub_category1_name = product_sub_category1::where('id', $product->sub_category1_id)->value('category_name') ?? '';
        $product->sub_category2_name = product_sub_category2::where('id', $product->sub_category2_id)->value('category_name') ?? '';

        // Fetch UOM names
        $defaultSalesUom = Uom::find($product->default_sales_uom);
        $product->default_sales_uom_name = $defaultSalesUom ? $defaultSalesUom->uom_id . ' ' . Uom_type::where('id', $defaultSalesUom->uom_type_id)->value('uom_name') : '';

        $inventoryUom = Uom::find($product->inventory_uom);
        $product->inventory_uom_name = $inventoryUom ? $inventoryUom->uom_id . ' ' . Uom_type::where('id', $inventoryUom->uom_type_id)->value('uom_name') : '';

        $purchaseUom = Uom::find($product->purchase_uom);
        $product->purchase_uom_name = $purchaseUom ? $purchaseUom->uom_id . ' ' . Uom_type::where('id', $purchaseUom->uom_type_id)->value('uom_name') : '';

        $productionUom = Uom::find($product->production_uom);
        $product->production_uom_name = $productionUom ? $productionUom->uom_id . ' ' . Uom_type::where('id', $productionUom->uom_type_id)->value('uom_name') : '';

        // Fetch Employee Name
        $employee = Employee::find($product->product_manager_id);
        $product->product_manager_name = $employee ? trim("{$employee->first_name} {$employee->middle_name} {$employee->last_name}") : null;

        // Fetch images
        $product->img1 = $product->img1 ? Storage::disk('spaces')->url($product->img1) : null;
        $product->upc_barcode = $product->upc_barcode ? Storage::disk('spaces')->url($product->upc_barcode) : null;

        // Fetch Sizes
        $product->size_kg = Sizes::where('id', $product->size)->value('size_kg') ?? '';
        $product->size_lb = Sizes::where('id', $product->size)->value('size_lb') ?? '';

        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'Product fetched successfully.',
            'data' => $product,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 404,
            'success' => false,
            'message' => 'Product not found.',
            'error' => $e->getMessage(),
        ], 404);
    }
}


    

 public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            ProductNote::where('product_id', $id)->delete();

            // Delete the product
            $product->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Product Deleted Successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'An error occurred while deleting the product',
            ], 500);
        }
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
        $size = Sizes::all(); 

        return response()->json([
        'status' => 200,
        'message' => 'Product Size fetched successfully.',
        'result' => [
            'data' => $size
            ],
        ]);

     }
     

}
