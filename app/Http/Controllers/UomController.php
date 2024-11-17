<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Uom; 
use App\Models\Hupu;
use Illuminate\Support\Facades\DB;

class UomController extends Controller
{
    public function index()
    {
    $uoms = Uom::select([
        'uom_id',
        'description',
        'bulk_code',
        'unit',
        'inventory_uom',
        'production_uom',
        'purchase_uom',
        'sales_uom'
    ])->get();

    $uoms = $uoms->map(function ($uom) {
        if ($uom->unit == 0) {      
            $length_cm = $uom->uom_length;  
            $width_cm = $uom->uom_width;  
            $height_cm = $uom->uom_height; 
        } else { 
            $length_in = $uom->uom_length;
            $width_in = $uom->uom_width; 
            $height_in = $uom->uom_height;
        }
        $result = Uom::fullName($uom->uom_id);
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

        return $uom;
    });
        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => $uoms
        ]);
    }


        // Store a new warehouse record
     public function store(Request $request)
{
    // Attempt to validate the request data
    try {
        // Validate the request data
        $validated = $request->validate([
            'uom_type_id' => 'required|string|max:8',             
            'description' => 'required|string',                  
            'weight' => 'required|numeric',                      
            'bulk_code' => 'required|string|max:8',             
            'unit' => 'required|string|max:8',                   
            'inventory_uom' => 'required|string|max:255',       
            'production_uom' => 'nullable|string',              
            'purchase_uom' => 'nullable|string',               
            'uom_length' => 'nullable|numeric',                   
            'uom_width' => 'nullable|numeric',                   
            'uom_height' => 'nullable|numeric',                  
        ]);

        
        // Begin database transaction
        DB::beginTransaction();
    
        $uom_list = Uom::create($validated);
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


  
}
