<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hupu;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Models\Uom_type;
use App\Models\Uom;
use App\Models\Uom_linked;
class HupuController extends Controller
{

   public function hupu_list(Request $request)
    {
       
        $pu_lists = Hupu::select([
                'id',
                'hu_pu_id',
                'hu_pu_code',
                'hu_pu_type',
                'flex',
                'hu_pu_id',
                'pu_hu_name',
                'description',
                'unit',
                'length',
                'width',
                'height',
                'bulk_code'
            ])->get();


        $pu_lists = $pu_lists->map(function ($pu_list) {
             $pu_list->hu_pu_type_name = Uom_type::where('id', $pu_list->hu_pu_type)->value('uom_name');

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
            'message' => 'HU List Ok.',
            'result' => $paginated
        ]);

    }


       public function hu_list(Request $request)
    {
       $hu_code = 'HU'; // Replace 'PU' with the actual value or variable
        $pu_lists = Hupu::select([
                'id',
                'hu_pu_id',
                'hu_pu_code',
                'hu_pu_type',
                'flex',
                'pu_hu_name',
                'description',
                'unit',
                'length',
                'width',
                'height',
                'min_weight',
                'max_weight',
                'bulk_code'
            ])
            ->where('hu_pu_code', $hu_code) // Use the variable or actual value
            ->get();


        $pu_lists = $pu_lists->map(function ($pu_list) {
            $pu_list->hu_pu_type_name = Uom_type::where('id', $pu_list->hu_pu_type)->value('uom_name');
            $length_cm = $width_cm = $height_cm = null;
            $length_in = $width_in = $height_in = null;
            if ($pu_list->unit == 0) {  
                $length_cm = $pu_list->length;
                $width_cm = $pu_list->width;
                $height_cm = $pu_list->height;
                $min_weight_kg = $pu_list->min_weight; 
                $max_weight_kg = $pu_list->max_weight; 
            } else {  
                $length_in = $pu_list->length;
                $width_in = $pu_list->width;
                $height_in = $pu_list->height;                
                $min_weight_lb = $pu_list->min_weight; 
                $max_weight_lb = $pu_list->max_weight;
            }

            $result = Hupu::fullName($pu_list->id);

            $pu_list->short_name = $result['short_name'];
            $pu_list->full_name = $result['full_name'];
            $pu_list->volumem3 = $result['volumem3'];
            $pu_list->volumeft3 = $result['volumeft3'];
            $pu_list->length_in = $length_in ?? $result['length_in']; 
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
            'message' => 'HU List Ok.',
            'result' => $paginated
        ]);

    }


     public function hu_all(Request $request)
{
    $pu_code = 'HU';
    $pu_lists = Hupu::select([
            'id', 'hu_pu_code', 'hu_pu_type', 'hu_pu_id', 'bulk_code', 'flex', 'pu_hu_name', 'description', 'unit', 'length', 'width', 'height', 'min_weight', 'max_weight'
        ])
        ->where('hu_pu_code', $pu_code)
        ->get(); 
    $pu_lists = $pu_lists->map(function ($pu_list) {
        $pu_list->hu_pu_type_name = Uom_type::where('id', $pu_list->hu_pu_type)->value('uom_name');
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
        $result = Hupu::fullName($pu_list->id);
        $pu_list->short_name = $result['short_name'];
        $pu_list->full_name = $result['full_name'];
        $pu_list->volumem3 = $result['volumem3'];
        $pu_list->volumeft3 = $result['volumeft3'];
        $pu_list->length_in = $length_in ?? $result['length_in'];
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
    $search = $request->input('search');

    if ($search) {
        $terms = explode(' ', $search); 
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
    return response()->json([
        'status' => 200,
        'message' => 'All HU List.',
        'result' => $pu_lists
    ]);
}

    public function pu_all(Request $request)
{
    $pu_code = 'PU'; // Replace 'PU' with the actual value or variable
    $pu_lists = Hupu::select([
            'id', 'hu_pu_code','hu_pu_type','hu_pu_id', 'bulk_code', 'flex', 'pu_hu_name', 'description', 'unit', 'length', 'width', 'height', 'min_weight', 'max_weight'
        ])
        ->where('hu_pu_code', $pu_code)
        ->get();
    $pu_lists = $pu_lists->map(function ($pu_list) {
        $pu_list->hu_pu_type_name = Uom_type::where('id', $pu_list->hu_pu_type)->value('uom_name');
        // Variables for centimeters and inches
        $length_cm = $width_cm = $height_cm = null;
        $length_in = $width_in = $height_in = null;
        if ($pu_list->unit == 0) { 
            $length_cm = $pu_list->length;
            $width_cm = $pu_list->width;
            $height_cm = $pu_list->height;
            $min_weight_kg = $pu_list->min_weight; 
            $max_weight_kg = $pu_list->max_weight; 
        } else { 
            $length_in = $pu_list->length;
            $width_in = $pu_list->width;
            $height_in = $pu_list->height;                
            $min_weight_lb = $pu_list->min_weight; 
            $max_weight_lb = $pu_list->max_weight;
        }

        // Get full name and volume calculations
        $result = Hupu::fullName($pu_list->id);

        // Assign calculated values
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

    // Search functionality
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

    // Return all PU records as JSON without pagination
    return response()->json([
        'status' => 200,
        'message' => 'All PU List.',
        'result' => $pu_lists
    ]);
}


     public function pu_list(Request $request)
    {
       $pu_code = 'PU'; // Replace 'PU' with the actual value or variable
        $pu_lists = Hupu::select([
                'id', 'hu_pu_code','hu_pu_type', 'hu_pu_id', 'bulk_code','flex', 'pu_hu_name', 'description','unit','length','width','height', 'min_weight',
                'max_weight'
            ])
            ->where('hu_pu_code', $pu_code) // Use the variable or actual value
            ->get();

     
        $pu_lists = $pu_lists->map(function ($pu_list) {
            // Variables for centimeters and inches
            $length_cm = $width_cm = $height_cm = null;
            $length_in = $width_in = $height_in = null;
            $pu_list->hu_pu_type_name = Uom_type::where('id', $pu_list->hu_pu_type)->value('uom_name');
            if ($pu_list->unit == 0) { 
                $length_cm = $pu_list->length;
                $width_cm = $pu_list->width;
                $height_cm = $pu_list->height;
                $min_weight_kg = $pu_list->min_weight; 
                $max_weight_kg = $pu_list->max_weight; 
            } else {  
                $length_in = $pu_list->length;
                $width_in = $pu_list->width;
                $height_in = $pu_list->height;                
                $min_weight_lb = $pu_list->min_weight; 
                $max_weight_lb = $pu_list->max_weight;
            }
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

        $hupu->hu_pu_type_name = Uom_type::where('id', $hupu->hu_pu_type)->value('uom_name');

      //   $hupu->hu_pu_type = Uom_type::where('id', $hupu->hu_pu_type)->value('uom_name');
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

       // dd($hupu->linkedhupus);

       // Enrich linked Hupu data
       $link_uom = $hupu->linkedhupus->map(function ($linkedhupu) {
        $relatedUom = Uom::find($linkedhupu->conv_form_id);
       // DD($relatedUom);
        if ($relatedUom) {
            return [
                'id' => $linkedhupu->id,
                'conv_form_id' => $linkedhupu->conv_form_id,
                'related_uom_length' => $relatedUom->uom_length,
                'related_uom_width' => $relatedUom->uom_width,
                'related_uom_height' => $relatedUom->uom_height,
                'related_unit' => $relatedUom->unit,
                'related_uom_weight' => $relatedUom->weight,
                'related_uom_type_name' =>  Uom_type::where('id', $relatedUom->uom_type_id)->value('uom_name'),
                
                'related_uom_full_name' => 
                        $relatedUom->uom_id . ' ' . 
                        Uom_type::where('id', $relatedUom->uom_type_id)->value('uom_name') . 
                        ' (' . $relatedUom->description . ')',

                
                'conv_to_id' => $linkedhupu->conv_to_id,
                'min_qty' => $linkedhupu->min_qty,
                'max_qty' => $linkedhupu->max_qty,
                'created_at' => $linkedhupu->created_at,
                'updated_at' => $linkedhupu->updated_at,
            ];
        }

        return [
            'conv_form_id' =>  $linkedhupu->conv_to_id,
            'conv_to_id' => $linkedhupu->conv_to_id,
            'min_qty' => $linkedhupu->min_qty,
            'max_qty' => $linkedhupu->max_qty,
            'created_at' => $linkedhupu->created_at,
            'updated_at' => $linkedhupu->updated_at,
        ];
    });


        // Retrieve UOM full details
        $result = Hupu::fullName($hupu->id);

        

        // Attach additional details to the Hupu object
        $hupu->short_name = $result['short_name'];
        $hupu->full_name = $result['full_name'];
        $hupu->volumem3 = $result['volumem3'];
        $hupu->volumeft3 = $result['volumeft3'];
        $hupu->length_in = $result['length_in'];

        $hupu->width_in = $result['width_in'];
        $hupu->height_in = $result['height_in'];
        $hupu->length_cm = $result['length_cm'];

        $hupu->width_cm = $result['width_cm'];
        $hupu->height_cm = $result['height_cm'];
        $hupu->min_weight_kg = $result['min_weight_kg'];
        $hupu->max_weight_kg = $result['max_weight_kg'];
        $hupu->min_weight_lb = $result['min_weight_lb'];
         $hupu->max_weight_lb = $result['max_weight_lb'];

        // Rename "linked_uoms" to "link_uom"
        $hupu->link_uom = $link_uom;
      unset($hupu->linkedhupus);
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> 1d4e2b0c648686684ea0c9af400bb6ad325f974f





    public function update(Request $request, $id)
<<<<<<< HEAD
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
            $width = $request->input('width');
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
            $hupu->width = $width;
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
            $linkedUoms = Uom_linked::where('uom_id', $hupu->id)->get();
    
            // Add the linked UOMs to the $hupu object
            $hupu->link_uom = $linkedUoms;
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
    


    public function update(Request $request, $id){
    $hupu = Hupu::find($id);
=======
>>>>>>> 1d4e2b0c648686684ea0c9af400bb6ad325f974f





    public function update(Request $request, $id)
=======
>>>>>>> 1d4e2b0c648686684ea0c9af400bb6ad325f974f
{
    try {
        DB::beginTransaction();

        // Retrieve the Hupu record to update
        $hupu = Hupu::with('linkedhupus')->findOrFail($id);

        // Update Hupu fields
        $hupu->hu_pu_code = $request->input('hu_pu_code');
        $hupu->hu_pu_type = $request->input('hu_pu_type');
        $hupu->flex = $request->input('flex');
        $hupu->pu_hu_name = $request->input('pu_hu_name');
        $hupu->description = $request->input('description');
        $hupu->eff_date = $request->input('eff_date');
        $hupu->width = $request->input('width');
        $hupu->bulk_code = $request->input('bulk_code');
        $hupu->unit = $request->input('unit');
        $hupu->hu_empty_weight = $request->input('hu_empty_weight');
        $hupu->min_weight = $request->input('min_weight');
        $hupu->hu_loaded_weight = $request->input('hu_loaded_weight');
        $hupu->max_weight = $request->input('max_weight');
        $hupu->length = $request->input('length');
        $hupu->height = $request->input('height');
        $hupu->status = $request->input('status');

        // Generate a new ID if hu_pu_type is updated
        $new_uom_type_id = $request->input('hu_pu_type');
        if ($new_uom_type_id && $new_uom_type_id !== $hupu->hu_pu_type) {
            $numeric_part = substr($hupu->hu_pu_id, strlen($hupu->hu_pu_type) + 1);
            $hupu->hu_pu_id = 'U' . $new_uom_type_id . $numeric_part;
        }
        $hupu->save();

        // Remove existing linked UOMs
        Uom_linked::where('uom_id', $hupu->id)->delete();

        // Save the updated linked UOM data
        $link_uom = $request->input('link_uom');
        if (!empty($link_uom) && is_array($link_uom)) {
            foreach ($link_uom as $link) {
                $uomLink = new Uom_linked();
                $uomLink->uom_id = $hupu->id; // Link to the Hupu ID
                $uomLink->conv_form_id = $link['conv_form_id'];
                $uomLink->conv_to_id = $link['conv_to_id'];
                $uomLink->max_qty = $link['max_qty'];
                $uomLink->min_qty = $link['min_qty'];
                $uomLink->save();
            }
        }

        $linkedUoms = Uom_linked::where('uom_id', $hupu->id)->get();

        // Add the linked UOMs to the $hupu object
        $hupu->link_uom = $linkedUoms;

        DB::commit();

        unset($hupu->linkedhupus);

        return response()->json([
            'status' => 200,
            'message' => 'Updated successfully',
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
        $width = $request->input('width');
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
        $hupu->width = $width;
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
                $uomLink->max_qty = $link['max_qty'];
                $uomLink->min_qty = $link['min_qty'];
                $uomLink->save();
            }
        }
        $linkedUoms = Uom_linked::where('uom_id', $hupu->id)->get();

        // Add the linked UOMs to the $hupu object
        $hupu->link_uom = $linkedUoms;
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

public function linked_hu_pu($id)
{
    try {
        // Fetch all linked UOMs based on the given ID
        $LinkedHupus = Uom_linked::where('conv_form_id', $id)->get();
        $result = [];
  
        foreach ($LinkedHupus as $key => $LinkedHupu) {
            $linkedUom = Hupu::find($LinkedHupu->uom_id);

            $extra_conv_form = Uom::fullName($LinkedHupu->conv_form_id);
            $extra_conv_to = Hupu::fullName($LinkedHupu->conv_to_id);

            $result[] = [
                'linked_id' => $LinkedHupu->id,
                'conv_form_id' => $LinkedHupu->conv_form_id,
                'conv_form_full_name' => $extra_conv_form['full_name'],
                'max_qty' => $LinkedHupu->max_qty,
                'min_qty' => $LinkedHupu->min_qty,

                'conv_to_id' => [
                    'id' => $LinkedHupu->conv_to_id,
                    'hu_pu_code' => $linkedUom->hu_pu_code,
                    'flex' => $linkedUom->flex,
                    'unit' => $linkedUom->unit,
                    'length' => $linkedUom->length,
                    'width' => $linkedUom->width,
                    'height' => $linkedUom->height,
                    'min_weight' => $linkedUom->min_weight,
                    'max_weight' => $linkedUom->max_weight,
                    'bulk_code' => $linkedUom->bulk_code,
                    'hu_pu_id' => $linkedUom->hu_pu_id,
                    'full_name' => $extra_conv_to['full_name'],
                    'volume' => $linkedUom->unit == 0 
                        ? $extra_conv_to['volumem3'] 
                        : $extra_conv_to['volumeft3']
                    
                    ]

            ];
        }

        return response()->json([
            'status' => 200,
            'message' => 'Linked HU/PU information retrieved successfully.',
            'result' => $result,
        ]);
    } catch (\Exception $e) {
        // Handle exceptions and return an error response
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred while retrieving linked HU/PU information.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



<<<<<<< HEAD

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
=======
            $extra_conv_form = Uom::fullName($LinkedHupu->conv_form_id);
            $extra_conv_to = Hupu::fullName($LinkedHupu->conv_to_id);

            $result[] = [
                'linked_id' => $LinkedHupu->id,
                'conv_form_id' => $LinkedHupu->conv_form_id,
                'conv_form_full_name' => $extra_conv_form['full_name'],
                'max_qty' => $LinkedHupu->max_qty,
                'min_qty' => $LinkedHupu->min_qty,

                'conv_to_id' => [
                    'id' => $LinkedHupu->conv_to_id,
                    'hu_pu_code' => $linkedUom->hu_pu_code,
                    'flex' => $linkedUom->flex,
                    'unit' => $linkedUom->unit,
                    'length' => $linkedUom->length,
                    'width' => $linkedUom->width,
                    'height' => $linkedUom->height,
                    'min_weight' => $linkedUom->min_weight,
                    'max_weight' => $linkedUom->max_weight,
                    'bulk_code' => $linkedUom->bulk_code,
                    'hu_pu_id' => $linkedUom->hu_pu_id,
                    'full_name' => $extra_conv_to['full_name'],
                    'volume' => $linkedUom->unit == 0 
                        ? $extra_conv_to['volumem3'] 
                        : $extra_conv_to['volumeft3']
                    
                    ]

            ];
        }

        return response()->json([
            'status' => 200,
            'message' => 'Linked HU/PU information retrieved successfully.',
            'result' => $result,
        ]);
    } catch (\Exception $e) {
        // Handle exceptions and return an error response
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred while retrieving linked HU/PU information.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



>>>>>>> 1d4e2b0c648686684ea0c9af400bb6ad325f974f
=======
>>>>>>> 1d4e2b0c648686684ea0c9af400bb6ad325f974f
    public function destroy($id)
    {
        try {
            $uom = Hupu::findOrFail($id);
            $uom_linked = Uom_linked::where('uom_id', $id)->delete();
            $uom->delete();
            return response()->json([
                'status' => '200',
                'message' => 'HU/PU deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
