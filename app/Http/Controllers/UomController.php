<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Uom; 
use App\Models\Hupu;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UomExport;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Models\Uom_linked;

class UomController extends Controller
{

      public function all_uom(Request $request)
    {
        $uoms = Uom::select([
            'id',
            'uom_id',
            'description',
            'bulk_code',
            'unit',
            'inventory_uom',
            'production_uom',
            'purchase_uom',
            'sales_uom'
        ])->get();

    // Initialize the query for searching and filtering
    $query = Uom::query();

    $uoms = $uoms->map(function ($uom) {
        if ($uom->unit == 0) {      
            $length_cm = $uom->uom_length;  
            $width_cm = $uom->uom_width;  
            $height_cm = $uom->uom_height;
            $weight_kg = $uom->weight; 
        } else { 
            $length_in = $uom->uom_length;
            $width_in = $uom->uom_width; 
            $height_in = $uom->uom_height;
            $weight_lb = $uom->weight; 
        }
        $result = Uom::fullName($uom->id);
        $uom->uom_type_name = $result['uom_type_name'];
        $uom->short_name = $result['short_name']; 
        $uom->full_name = $result['full_name']; 
        $uom->volumem3 = $result['volumem3']; 
        $uom->volumeft3 = $result['volumeft3'];
        $uom->length_in = $result['length_in'];
        $uom->width_in = $result['width_in'];
        $uom->height_in = $result['height_in'];
        $uom->length_cm = $result['length_cm']; 
        $uom->width_cm = $result['width_cm'];   
        $uom->height_cm = $result['height_cm'];
        $uom->weight_kg = $result['weight_kg'];
        $uom->weight_lb = $result['weight_lb'];

        return $uom;
    });

        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => $uoms
        ]);
    }

    public function index(Request $request)
    {
    $uoms = Uom::select([
        'id',
        'uom_id',
        'description',
        'bulk_code',
        'unit',
        'inventory_uom',
        'production_uom',
        'purchase_uom',
        'sales_uom'
    ])->get();

    // Initialize the query for searching and filtering
    $query = Uom::query();

     // Handle search input (space-separated values)
    $search = $request->input('search');
    if ($search) {
        $terms = explode(' ', $search); // Split the input into search terms
        foreach ($terms as $term) {
            $query->where('description', 'LIKE', "%{$term}%")
                  ->orWhere('uom_id', 'LIKE', "%{$term}%"); // Adjust the columns as needed
        }
    }
    // Apply pagination with a default limit
    $limit = $request->input('limit', 10); // Default limit set to 5
    $uomPaginated = $query->paginate($limit);


    $uoms = $uoms->map(function ($uom) {
        if ($uom->unit == 0) {      
            $length_cm = $uom->uom_length;  
            $width_cm = $uom->uom_width;  
            $height_cm = $uom->uom_height; 
            $weight_kg = $uom->weight; 
        } else { 
            $length_in = $uom->uom_length;
            $width_in = $uom->uom_width; 
            $height_in = $uom->uom_height;
            $weight_lb = $uom->weight;
        }
        $result = Uom::fullName($uom->id);
        $uom->uom_type_name = $result['uom_type_name'];
        $uom->short_name = $result['short_name']; 
        $uom->full_name = $result['full_name']; 
        $uom->volumem3 = $result['volumem3']; 
        $uom->volumeft3 = $result['volumeft3'];
        $uom->length_in = $result['length_in'];
        $uom->width_in = $result['width_in'];
        $uom->height_in = $result['height_in'];
        $uom->length_cm = $result['length_cm']; 
        $uom->width_cm = $result['width_cm'];   
        $uom->height_cm = $result['height_cm'];
        $uom->weight_kg = $result['weight_kg'];
        $uom->weight_lb = $result['weight_lb'];

        return $uom;
    });

     // Search logic
    $search = $request->input('search');
    if ($search) {
        $terms = explode(' ', $search); // Split by spaces
        $uoms = $uoms->filter(function ($uom) use ($terms) {
            foreach ($terms as $term) {
                if (
                    stripos($uom->description, $term) !== false || 
                    stripos($uom->bulk_code, $term) !== false
                ) {
                    return true;
                }
            }
            return false;
        });
    }
       
       // Pagination logic
        $currentPage = Paginator::resolveCurrentPage(); // Get current page
        $limit = (int) $request->input('limit', 5); // Default limit to 5
        $paginatedItems = $uoms->slice(($currentPage - 1) * $limit, $limit)->values(); // Slice the collection

        // Create a LengthAwarePaginator
        $paginated = new LengthAwarePaginator(
            $paginatedItems, // Items for the current page
            $uoms->count(), // Total items
            $limit, // Items per page
            $currentPage, // Current page number
            [
                'path' => Paginator::resolveCurrentPath(), // Set pagination path
                'query' => $request->query() // Preserve query parameters
            ]
        );
        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => $paginated
        ]);
    }



public function store(Request $request)
{
    try {
        // Begin database transaction
        DB::beginTransaction();

        // Manually retrieve input data
        $uom_type_id = $request->input('uom_type_id');
        $description = $request->input('description');
        $weight = $request->input('weight');
        $bulk_code = $request->input('bulk_code');
        $unit = $request->input('unit');
        $inventory_uom = $request->input('inventory_uom');
        $production_uom = $request->input('production_uom');
        $purchase_uom = $request->input('purchase_uom');
        $uom_length = $request->input('uom_length');
        $uom_width = $request->input('uom_width');
        $uom_height = $request->input('uom_height');

        $link_uom = $request->input('link_uom');

        // Step 1: Get the maximum uom_id for the given uom_type_id
        $max_uom_id = Uom::max('id');

        // Step 2: Generate the new uom_id
        $new_uom_id = 'U' . $uom_type_id . str_pad(($max_uom_id + 1), 3, '0', STR_PAD_LEFT);

        // Step 3: Create a new UOM record
        $uom = new Uom();
        $uom->uom_id = $new_uom_id;
        $uom->uom_type_id = $uom_type_id;
        $uom->description = $description;
        $uom->weight = $weight;
        $uom->bulk_code = $bulk_code;
        $uom->unit = $unit;
        $uom->inventory_uom = $inventory_uom;
        $uom->production_uom = $production_uom;
        $uom->purchase_uom = $purchase_uom;
        $uom->uom_length = $uom_length;
        $uom->uom_width = $uom_width;
        $uom->uom_height = $uom_height;

        // Save the UOM record to the database
     
        $uom->save();
          // Step 4: Save the linked UOM data

        if (!empty($link_uom) && is_array($link_uom)) {
            $linkedUoms = [];
            foreach ($link_uom as $link) {
                $uomLink = new Uom_linked(); // Ensure the model name matches your table
                $uomLink->uom_id = $uom->id; // Link it to the newly created UOM ID
                $uomLink->conv_form_id = $link['conv_form_id']; // Ensure field exists
                $uomLink->conv_to_id = $link['conv_to_id']; // Ensure field exists
                $uomLink->conv_qty = $link['conv_qty']; // Ensure field exists
                $uomLink->save();

                // Add the linked UOM data to the array
                $linkedUoms[] = $uomLink;
            }

            // Attach linked UOM data to the $uom variable
            $uom->linked_uoms = $linkedUoms;
        }

      
        // Commit the transaction
        DB::commit();

        // Return a success response with the created UOM record
        return response()->json([
            'status' => 200,
            'message' => 'UOM Created Successfully',
            'result' => $uom,
        ]);
    } catch (\Exception $e) {
        // Rollback the transaction in case of a general exception
        DB::rollBack();

        // Return a response with the exception message
        return response()->json([
            'status' => 500,
            'error' => $e->getMessage(),
        ], 500);
    }
}



public function uom_export() 
{
    $slugDate = Str::slug(date('Y-m-d')); 
    $fileName = "{$slugDate}_uomList.xlsx";
    return Excel::download(new UomExport, $fileName);
}

    // Show a single warehouse record
public function show($id)
{
    try {
        // Find the Uom by ID
        //$uom = Uom::findOrFail($id);
        $uom = Uom::with('linkedUoms')->findOrFail($id);
        // Initialize conversion variables (for metric and imperial units)
        $length_cm = $width_cm = $height_cm = null;
        $length_in = $width_in = $height_in = null;

        // Check if the unit is metric (0) or imperial (non-0)
        if ($uom->unit == 0) {      
            // Metric system (centimeters)
            $length_cm = $uom->uom_length;  
            $width_cm = $uom->uom_width;  
            $height_cm = $uom->uom_height; 
            $weight_kg = $uom->weight;
        } else { 
            // Imperial system (inches)
            $length_in = $uom->uom_length;
            $width_in = $uom->uom_width; 
            $height_in = $uom->uom_height;
            $weight_lb = $uom->weight;
        }

        // Assuming `Uom::fullName($uom->uom_id)` returns an array with additional data
        $result = Uom::fullName($uom->id);

        // Assign the result data to the Uom object
         $uom->uom_type_name = $result['uom_type_name'];
        $uom->short_name = $result['short_name']; 
        $uom->full_name = $result['full_name']; 
        $uom->volumem3 = $result['volumem3']; 
        $uom->volumeft3 = $result['volumeft3'];
        $uom->length_in = $result['length_in'];
        $uom->width_in = $result['width_in'];
        $uom->height_in = $result['height_in'];
        $uom->length_cm = $result['length_cm']; 
        $uom->width_cm = $result['width_cm'];   
        $uom->height_cm = $result['height_cm'];
        $uom->weight_kg = $result['weight_kg'];
        $uom->weight_lb = $result['weight_lb'];




        // Return the modified Uom object in the response
        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => [
                'data' => $uom, // Return the modified Uom object
            ],
        ]);

    } catch (\Exception $e) {
        // Return error response if something goes wrong
        return response()->json([
            'status' => 500,
            'error' => $e->getMessage(),
        ], 500);
    }
}




   public function edit($id)
    {
        try {
            $uom = Uom::findOrFail($id);
    
            return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => [
                'data' =>$uom
                
            ],
        ]);


                

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }


public function update(Request $request, $id)
{
    try {
        // Begin database transaction
        DB::beginTransaction();

        // Find the UOM record by ID and eager load linked UOMs
        $uom = Uom::with('linkedUoms')->findOrFail($id);

        // Retrieve input data
        $new_uom_type_id = $request->input('uom_type_id');
        $description = $request->input('description');
        $weight = $request->input('weight');
        $bulk_code = $request->input('bulk_code');
        $unit = $request->input('unit');
        $inventory_uom = $request->input('inventory_uom');
        $production_uom = $request->input('production_uom');
        $purchase_uom = $request->input('purchase_uom');
        $uom_length = $request->input('uom_length');
        $uom_width = $request->input('uom_width');
        $uom_height = $request->input('uom_height');
        $link_uom = $request->input('link_uom'); // Added: link_uom input from request

        // Check if the `uom_type_id` is being updated
        if ($new_uom_type_id && $new_uom_type_id != $uom->uom_type_id) {
            // Extract the numeric part from the existing uom_id
            $numeric_part = substr($uom->uom_id, strlen($uom->uom_type_id) + 1);

            // Generate the new uom_id using the new uom_type_id and the numeric part
            $new_uom_id = 'U' . $new_uom_type_id . $numeric_part;

            // Update the uom_id and uom_type_id
            $uom->uom_id = $new_uom_id;
            $uom->uom_type_id = $new_uom_type_id;
        }

        // Update other fields
        $uom->description = $description ?? $uom->description;
        $uom->weight = $weight ?? $uom->weight;
        $uom->bulk_code = $bulk_code ?? $uom->bulk_code;
        $uom->unit = $unit ?? $uom->unit;
        $uom->inventory_uom = $inventory_uom ?? $uom->inventory_uom;
        $uom->production_uom = $production_uom ?? $uom->production_uom;
        $uom->purchase_uom = $purchase_uom ?? $uom->purchase_uom;
        $uom->uom_length = $uom_length ?? $uom->uom_length;
        $uom->uom_width = $uom_width ?? $uom->uom_width;
        $uom->uom_height = $uom_height ?? $uom->uom_height;

        // Step 1: Delete the old linked UOM records if any
        Uom_linked::where('uom_id', $uom->id)->delete();

        // Step 2: Insert the new linked UOM records if provided
        if (!empty($link_uom) && is_array($link_uom)) {
            $linkedUoms = []; // Array to store the new Uom_linked instances
            foreach ($link_uom as $link) {
                // Validate the required fields
                if (isset($link['conv_form_id'], $link['conv_to_id'], $link['conv_qty'])) {
                    $linkedUoms[] = new Uom_linked([
                        'uom_id' => $uom->id, // Link it to the updated UOM ID
                        'conv_form_id' => $link['conv_form_id'],
                        'conv_to_id' => $link['conv_to_id'],
                        'conv_qty' => $link['conv_qty'],
                    ]);
                }
            }

            // Only save if there are valid linked UOMs
            if (!empty($linkedUoms)) {
                $uom->linkedUoms()->saveMany($linkedUoms);
            }
        }

        // Save the updated UOM record to the database
        $uom->save();
        $uom->load('linkedUoms');
        // Commit the transaction
        DB::commit();

        // Return a success response with the updated UOM record and linked UOMs
        return response()->json([
            'status' => 200,
            'message' => 'UOM Updated Successfully',
            'result' => [
                'uom' => $uom
            ],
        ]);
    } catch (ModelNotFoundException $e) {
        // Rollback the transaction in case of a not found exception
        DB::rollBack();

        // Return a response with the not found error
        return response()->json([
            'status' => 404,
            'message' => 'Error: UOM not found!',
        ], 404);
    } catch (\Exception $e) {
        // Rollback the transaction in case of a general exception
        DB::rollBack();

        // Return a response with the exception message
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred while updating the UOM.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



    public function destroy($id)
    {
        try {
            $uom = Uom::findOrFail($id);
            $uom->delete();
            return response()->json([
                'status' => '200',
                'message' => 'Uom deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }


  
}
