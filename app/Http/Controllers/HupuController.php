<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hupu;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Models\Uom_linked;
class HupuController extends Controller
{
   public function hu_list(Request $request)
    {
        $hu_lists = Hupu::select([
            'id',
            'hu_pu_code',
            'pu_hu_name',
            'description',
            'unit',
            'length',
            'width',
            'height'
        ])
        ->where('hu_pu_type', 1)
        ->get();

        $hu_lists = $hu_lists->map(function ($hu_list) {
            // Variables for centimeters and inches
            $length_cm = $width_cm = $height_cm = null;
            $length_in = $width_in = $height_in = null;

            // Convert based on unit type
            if ($hu_list->unit == 0) {  // Assuming 0 is for centimeters
                // Values in cm
                $length_cm = $hu_list->length;
                $width_cm = $hu_list->width;
                $height_cm = $hu_list->height;
            } else {  // Assuming 1 is for inches
                // Values in inches
                $length_in = $hu_list->length;
                $width_in = $hu_list->width;
                $height_in = $hu_list->height;
            }
            // dd($hu_list->id);
            // Get full name and volume calculations
            $result = Hupu::fullName($hu_list->id);

            $hu_list->short_name = $result['short_name'];
            $hu_list->full_name = $result['full_name'];
            $hu_list->volumem3 = $result['volumem3'];
            $hu_list->volumeft3 = $result['volumeft3'];

            // Add values for both inches and centimeters based on unit
            $hu_list->length_in = $length_in ?? $result['length_in'];  // Default to result if not set
            $hu_list->width_in = $width_in ?? $result['width_in'];
            $hu_list->height_in = $height_in ?? $result['height_in'];
            $hu_list->length_cm = $length_cm ?? $result['length_cm'];
            $hu_list->width_cm = $width_cm ?? $result['width_cm'];
            $hu_list->height_cm = $height_cm ?? $result['height_cm'];

            return $hu_list;
        });

        $search = $request->input('search'); // Space-separated values
        if ($search) {
            $terms = explode(' ', $search); // Split by spaces
            $hu_lists = $hu_lists->filter(function ($hu_list) use ($terms) {
                foreach ($terms as $term) {
                    if (stripos($hu_list->pu_hu_name, $term) !== false || 
                        stripos($hu_list->description, $term) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }
        // Pagination logic
        $currentPage = Paginator::resolveCurrentPage(); // Get current page
        $limit = (int) $request->input('limit', 5); // Default limit to 5
        $paginatedItems = $hu_lists->slice(($currentPage - 1) * $limit, $limit)->values(); // Slice the collection

        // Create a LengthAwarePaginator
        $paginated = new LengthAwarePaginator(
            $paginatedItems, // Items for the current page
            $hu_lists->count(), // Total items
            $limit, // Items per page
            $currentPage, // Current page number
            [
                'path' => Paginator::resolveCurrentPath(), // Set pagination path
                'query' => $request->query() // Preserve query parameters
            ]
        );

        return response()->json([
            'status' => 200,
            'message' => 'HU list Ok..',
            'result' => $paginated
        ]);
    }


     public function pu_list(Request $request)
    {
       $pu_code = 'PU'; // Replace 'PU' with the actual value or variable
        $pu_lists = Hupu::select([
                'id',
                'hu_pu_code',
                'pu_hu_name',
                'description',
                'unit',
                'length',
                'width',
                'height'
            ])
            ->where('hu_pu_code', $pu_code) // Use the variable or actual value
            ->get();


        $pu_lists = $pu_lists->map(function ($pu_list) {
            // Variables for centimeters and inches
            $length_cm = $width_cm = $height_cm = null;
            $length_in = $width_in = $height_in = null;

            // Convert based on unit type
            if ($pu_list->unit == 0) {  // Assuming 0 is for centimeters
                // Values in cm
                $length_cm = $pu_list->length;
                $width_cm = $pu_list->width;
                $height_cm = $pu_list->height;
                $min_weight_kg = $pu_list->min_weight; 
                $max_weight_kg = $pu_list->max_weight; 
            } else {  // Assuming 1 is for inches
                // Values in inches
                $length_in = $pu_list->length;
                $width_in = $pu_list->width;
                $height_in = $pu_list->height;                
                $min_weight_lb = $pu_list->min_weight; 
                $max_weight_lb = $pu_list->max_weight;
            }
            // dd($hu_list->id);
            // Get full name and volume calculations
            $result = Hupu::fullName($pu_list->id);

            $pu_list->short_name = $result['short_name'];
            $pu_list->full_name = $result['full_name'];
            $pu_list->volumem3 = $result['volumem3'];
            $pu_list->volumeft3 = $result['volumeft3'];
            $pu_list->length_in = $length_in ?? $result['length_in'];  // Default to result if not set
            $pu_list->width_in = $width_in ?? $result['width_in'];
            $pu_list->height_in = $height_in ?? $result['height_in'];
            $pu_list->length_cm = $length_cm ?? $result['length_cm'];
            $pu_list->width_cm = $width_cm ?? $result['width_cm'];
            $pu_list->height_cm = $height_cm ?? $result['height_cm'];

            $pu_list->max_weight_kg = $result['max_weight_kg'];
            $pu_list->min_weight_kg = $result['min_weight_kg'];
            $pu_list->max_weight_lb = $result['max_weight_lb'];
            $pu_list->min_weight_lb = $result['min_weight_lb'];
            return $pu_list;
        });     
        
        $search = $request->input('search'); // Space-separated values
    if ($search) {
        $terms = explode(' ', $search); // Split by spaces
        $pu_lists = $pu_lists->filter(function ($pu_list) use ($terms) {
            foreach ($terms as $term) {
                if (stripos($pu_list->pu_hu_name, $term) !== false || 
                    stripos($pu_list->description, $term) !== false) {
                    return true;
                }
            }
            return false;
        });
    }
         // Pagination logic
        $currentPage = Paginator::resolveCurrentPage(); // Get current page
        $limit = (int) $request->input('limit', 5); // Default limit to 5
        $paginatedItems = $pu_lists->slice(($currentPage - 1) * $limit, $limit)->values(); // Slice the collection

        // Create a LengthAwarePaginator
        $paginated = new LengthAwarePaginator(
            $paginatedItems, // Items for the current page
            $pu_lists->count(), // Total items
            $limit, // Items per page
            $currentPage, // Current page number
            [
                'path' => Paginator::resolveCurrentPath(), // Set pagination path
                'query' => $request->query() // Preserve query parameters
            ]
        );

        return response()->json([
            'status' => 200,
            'message' => 'PU List Ok.',
            'result' => $paginated
        ]);

    }

  public function show($id)
{
    try {
        $hupu = Hupu::with('linkedhupus')->findOrFail($id);


        $length_cm = $width_cm = $height_cm = null;
        $length_in = $width_in = $height_in = null;
        $min_weight_kg = $min_weight_lb = null;
        $max_weight_kg = $max_weight_lb = null;

        // Check if the unit is metric (0) or imperial (non-0)
        if ($hupu->unit == 0) {
            // Metric system (centimeters)
            $length_cm = $hupu->length;
            $width_cm = $hupu->width;
            $height_cm = $hupu->height;

            $min_weight_kg = $hupu->min_weight;
            $max_weight_kg = $hupu->max_weight;
        } else {
            // Imperial system (inches)
            $length_in = $hupu->length;
            $width_in = $hupu->width;
            $height_in = $hupu->height;

            $min_weight_lb = $hupu->min_weight;
            $max_weight_lb = $hupu->max_weight;
        }

        // Enrich linked UOM data
        // $linkUom = $hupu->linkedhupus->map(function ($linkedUom) {
        //     $relatedUom = Hupu::find($linkedUom->conv_form_id);

        //     if ($relatedUom) {
        //         return [
        //             'id' => $linkedUom->id,
        //             'conv_form_id' => $linkedUom->conv_form_id,
        //             'related_uom_length' => $relatedUom->length,
        //             'related_uom_width' => $relatedUom->width,
        //             'related_uom_height' => $relatedUom->height
        //             // 'related_uom_type_name' => $relatedUom->uom_type_name,
        //             // 'related_uom_full_name' => $relatedUom->full_name,
        //             // 'conv_to_id' => $linkedUom->conv_to_id,
        //             // 'conv_qty' => $linkedUom->conv_qty,
        //             // 'status' => $linkedUom->status,
        //             // 'created_at' => $linkedUom->created_at,
        //             // 'updated_at' => $linkedUom->updated_at,
        //         ];
        //     }

        //     return [
        //         'conv_form_id' => null,
        //         'conv_to_id' => $linkedUom->conv_to_id,
        //         'conv_qty' => $linkedUom->conv_qty,
        //         'status' => $linkedUom->status,
        //         'created_at' => $linkedUom->created_at,
        //         'updated_at' => $linkedUom->updated_at,
        //     ];
        // });

        // Retrieve UOM full details
        $result = Hupu::fullName($hupu->id);

        // Attach additional details to the Hupu object
        // $hupu->uom_type_name = $result['uom_type_name'];
        // $hupu->short_name = $result['short_name'];
        // $hupu->full_name = $result['full_name'];
        // $hupu->volumem3 = $result['volumem3'];
        // $hupu->volumeft3 = $result['volumeft3'];
        // $hupu->length_in = $result['length_in'];
        // $hupu->width_in = $result['width_in'];
        // $hupu->height_in = $result['height_in'];
        // $hupu->length_cm = $result['length_cm'];
        // $hupu->width_cm = $result['width_cm'];
        // $hupu->height_cm = $result['height_cm'];
        // $hupu->weight_kg = $result['weight_kg'];
        // $hupu->weight_lb = $result['weight_lb'];

        // Rename "linked_uoms" to "link_uom"
        // $hupu->link_uom = $linkUom;

        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => [
                'data' => $hupu,
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
            $hupu = Hupu::findOrFail($id);
            return response()->json([
                'status' => 200,
                'message' => 'Ok',
                'result' => [
                    'data' => $hupu, 
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
    $hupu = Hupu::find($id);

    if (!$hupu) {
        return response()->json([
            'status' => '404',
            'message' => 'Error: UOM not found!',
        ], 404);
    }
    $hupu->update($request->all());
    return response()->json([
        'status' => '200',
        'message' => 'Ok.',
        'result' => [
            'data' => $hupu,
        ],
    ]);
}

public function store(Request $request)
{
    try {
        DB::beginTransaction();

        // Retrieve input data
        $hu_pu_code = $request->input('hu_pu_code');
        $hu_pu_type = $request->input('hu_pu_type');
        $flex = $request->input('flex');
        $pu_hu_name = $request->input('pu_hu_name');
        $description = $request->input('description');
        $unit = $request->input('unit');
        $eff_date = $request->input('eff_date');
        $length = $request->input('length');
        $weight = $request->input('weight');
        $height = $request->input('height');
        $hu_empty_weight = $request->input('hu_empty_weight');
        $min_weight = $request->input('min_weight');
        $hu_loaded_weight = $request->input('hu_loaded_weight');
        $max_weight = $request->input('max_weight');
        $bulk_code = $request->input('bulk_code');
        $status = $request->input('status');
        $link_uom = $request->input('link_uom');

        // Generate a unique hu_pu_id
        $max_hupu_id = Hupu::max('id');
        $hu_pu_id = 'U' . $hu_pu_type . str_pad(($max_hupu_id + 1), 3, '0', STR_PAD_LEFT);

        // Save the Hupu data
        $hupu = new Hupu();
        $hupu->hu_pu_id = $hu_pu_id;
        $hupu->hu_pu_code = $hu_pu_code;
        $hupu->hu_pu_type = $hu_pu_type;
        $hupu->flex = $flex;
        $hupu->pu_hu_name = $pu_hu_name;
        $hupu->description = $description;
        $hupu->eff_date = $eff_date;
        $hupu->weight = $weight;
        $hupu->bulk_code = $bulk_code;
        $hupu->unit = $unit;
        $hupu->hu_empty_weight = $hu_empty_weight;
        $hupu->min_weight = $min_weight;
        $hupu->hu_loaded_weight = $hu_loaded_weight;
        $hupu->max_weight = $max_weight;
        $hupu->length = $length;
        $hupu->height = $height;
        $hupu->status = $status;
        $hupu->save();

        // Save the linked UOM data
        if (!empty($link_uom) && is_array($link_uom)) {
            foreach ($link_uom as $link) {
                $uomLink = new Uom_linked();
                $uomLink->uom_id = $hupu->id; // Link to the Hupu ID
                $uomLink->conv_form_id = $link['conv_form_id'];
                $uomLink->conv_to_id = $link['conv_to_id'];
                $uomLink->conv_qty = $link['conv_qty'];
                $uomLink->save();
            }
        }

        DB::commit();

        return response()->json([
            'status' => 200,
            'message' => 'Created successfully',
            'result' => $hupu,
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 500,
            'error' => $e->getMessage(),
        ], 500);
    }
}



    //   public function storeadsfasd(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'hu_pu_code' => 'required|string',                
    //             'hu_pu_type' => 'required|integer',                
    //             'flex' => 'nullable|string',                        
    //             'pu_hu_name' => 'required|integer',                
    //             'description' => 'required|string',               
    //             'unit' => 'required|integer',                    
    //             'length' => 'required|numeric',                   
    //             'weight' => 'required|numeric',                        
    //             'height' => 'required|numeric',                       
    //             'hu_empty_weight' => 'nullable|numeric',              
    //             'hu_minimum_weight' => 'nullable|numeric',            
    //             'hu_loaded_weight' => 'nullable|numeric',             
    //             'hu_maximum_weight' => 'nullable|numeric',          
    //         ]);


    //         DB::beginTransaction();
    //            $hupu_list = Hupu::create($validated);
    //         DB::commit();
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'created successfully',
    //             'result' => $hupu_list,
    //         ]);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'status' => 422,
    //             'errors' => $e->errors(),
    //         ], 422);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => 500,
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
