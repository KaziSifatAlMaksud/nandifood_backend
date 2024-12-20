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


        // Store a new warehouse record
     public function store(Request $request)
{
    try {        
        DB::beginTransaction();
    
        $uom_list = Uom::create($request->all());
        DB::commit();

        // Return a success response
        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => $uom_list
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Return a custom response with validation errors
        return response()->json([
            'status' => 422,
            'errors' => $e->errors() 
        ], 422);

    } catch (\Exception $e) {
        // Rollback the transaction in case of a general exception
        DB::rollBack();

        // Return a response with the exception message
        return response()->json([
            'status' => 500,
            'error' => $e->getMessage()
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
        $uom = Uom::findOrFail($id);

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
        $uom = Uom::findOrFail($id);
    if (!$uom) {
        return response()->json([
            'status' => '404',
            'message' => 'Error: UOM not found!',
        ], 404);
    }
    $uom->update($request->all());
    return response()->json([
        'status' => '200',
        'message' => 'Ok.',
        'result' => [
            'data' => $uom,
        ],
    ]);
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
