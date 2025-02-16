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
use App\Models\Uom_type;

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
            $paginatedItems, 
            $uoms->count(), 
            $limit, 
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(), 
                'query' => $request->query()
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
        $eff_date = $request->input('eff_date');
        $inventory_uom = $request->input('inventory_uom');
        $production_uom = $request->input('production_uom');
        $purchase_uom = $request->input('purchase_uom');
        $sales_uom = $request->input('sales_uom');
        $uom_length = $request->input('uom_length');
        $uom_width = $request->input('uom_width');
        $uom_height = $request->input('uom_height');
        $status = $request->input('status');
        $link_uom = $request->input('link_uom');

        $action = $request->input('action');
        $isApprove = ($action == 'approve') ? 2 : 1;
        $uom->is_approved =  $isApprove;


        $max_uom_id = Uom::max('id');
        $new_uom_id = 'U' . $uom_type_id . str_pad(($max_uom_id + 1), 3, '0', STR_PAD_LEFT);
        $uom = new Uom();
        $uom->uom_id = $new_uom_id;
        $uom->uom_type_id = $uom_type_id;
        $uom->description = $description;
        $uom->eff_date = $eff_date;
        $uom->weight = $weight;
        $uom->bulk_code = $bulk_code;
        $uom->unit = $unit;
        $uom->inventory_uom = $inventory_uom;
        $uom->production_uom = $production_uom;
        $uom->sales_uom = $sales_uom;
        $uom->purchase_uom = $purchase_uom;
        $uom->uom_length = $uom_length;
        $uom->uom_width = $uom_width;
        $uom->uom_height = $uom_height;
        $uom->status = $status;

     
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
            $uom->link_uom = $linkedUoms;
        } else {
            // If $link_uom is empty, set $uom->linked_uoms to an empty array
            $uom->link_uom = [];
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

public function product_export() 
{
    $slugDate = Str::slug(date('Y-m-d')); 
    $fileName = "{$slugDate}_Product_List.xlsx";
    return Excel::download(new UomExport, $fileName);
}


    public function show($id)
{
    try {
        // Find the Uom by ID and load the linked UOMs
        $uom = Uom::with('linkedUoms')->findOrFail($id);

        // Initialize length, width, height, and weight variables
        $length_cm = $width_cm = $height_cm = null;
        $length_in = $width_in = $height_in = null;
        $weight_kg = $weight_lb = null;

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

        // Map over the linked UOMs and enrich with detailed UOM data
        $linkUom = $uom->linkedUoms->map(function ($linkedUom) {
            // Find the related UOM based on conv_form_id
            $relatedUom = Uom::find($linkedUom->conv_form_id);
            
            if ($relatedUom) {
                // If related UOM is found, add its detailed information
                return [
                    'id' => $linkedUom->id,
                    'conv_form_id' => $linkedUom->conv_form_id,
                    'related_uom_length' => $relatedUom->uom_length,
                    'related_uom_width' => $relatedUom->uom_width,
                    'related_uom_height' => $relatedUom->uom_height,
                    'related_uom_weight' => $relatedUom->weight,
                    'related_uom_type_name' => $relatedUom->uom_type_name,
                    'related_uom_full_name' => $relatedUom->full_name, // Missing comma added here
                    'conv_to_id' => $linkedUom->conv_to_id,
                    'conv_qty' => $linkedUom->conv_qty,
                    'status' => $linkedUom->status,
                    'created_at' => $linkedUom->created_at,
                    'updated_at' => $linkedUom->updated_at
                ];
            } else {
                // If related UOM is not found, return only the basic information
                return [
                    'conv_form_id' => null,
                    'conv_to_id' => $linkedUom->conv_to_id,
                    'conv_qty' => $linkedUom->conv_qty,
                    'status' => $linkedUom->status,
                    'created_at' => $linkedUom->created_at,
                    'updated_at' => $linkedUom->updated_at
                ];
            }
        });

        // Get full name and other details of the UOM
        $result = Uom::fullName($uom->id);

        // Enrich the UOM object with the full name and other calculated details
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
        $uom->link_uom = $linkUom;  // Add the enriched linked UOMs with the new key name
        unset($uom->linkedUoms);
        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => [
                'data' => $uom, // Return the enriched UOM object
            ],
        ]);
    } catch (\Exception $e) {
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
        $uom = Uom::with('linkedUoms')->find($id);

        if (!$uom) {
            return response()->json([
                'status' => 404,
                'message' => 'UOM not found',
            ], 404);
        }
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
        
        $sales_uom = $request->input('sales_uom');
        $eff_date = $request->input('eff_date');
        $status = $request->input('status');
        $link_uom = $request->input('link_uom'); 

        $action = $request->input('action');
        $isApprove = ($action == 'approve') ? 2 : 1;
        $uom->is_approved =  $isApprove;

        if ($new_uom_type_id && $new_uom_type_id != $uom->uom_type_id) {
            $numeric_part = substr($uom->uom_id, strlen($uom->uom_type_id) + 1);
            $new_uom_id = 'U' . $new_uom_type_id . $numeric_part;
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

        $uom->sales_uom = $sales_uom ?? $uom->sales_uom;
        $uom->eff_date = $eff_date ?? $uom->eff_date;
        $uom->status = $status ?? $uom->status;
      
        Uom_linked::where('uom_id', $uom->id)->delete();
        if (!empty($link_uom) && is_array($link_uom)) {
           
            $linkedUoms = [];
            foreach ($link_uom as $link) {
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
        $uom->save();
        $uom->load('linkedUoms');
        DB::commit();
        return response()->json([
            'status' => 200,
            'message' => 'UOM Updated Successfully',
            'result' => [
                'data' => $uom->makeHidden(['linkedUoms'])->setAttribute('link_uom', $uom->linkedUoms)
            ],
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
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
            $uom_linked = Uom_linked::where('uom_id', $id)->delete();
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

    public function uom_name()
    {
        $uoms = Uom::with('uomType')->select('id', 'uom_id', 'uom_type_id')->get();
        $uoms->each(function ($uom) { 
            $uom->uom_name = $uom->uomType->uom_name ?? null;
            unset($uom->uomType); 
        });

        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => [
                'data' => $uoms
            ],
        ]);
    }



  
}
