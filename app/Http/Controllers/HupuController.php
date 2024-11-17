<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hupu;

class HupuController extends Controller
{
   public function hu_list()
    {
        $hu_lists = Hupu::select([
            'id',
            'hu_pu_code',
            'pu_hu_name',
            'description',
            'unit',
            'length',
            'weight',
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
                $width_cm = $hu_list->weight;
                $height_cm = $hu_list->height;
            } else {  // Assuming 1 is for inches
                // Values in inches
                $length_in = $hu_list->length;
                $width_in = $hu_list->weight;
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

        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => $hu_lists
        ]);
    }


     public function pu_list()
    {
         $pu_lists = Hupu::select([
            'id',
            'hu_pu_code',
            'pu_hu_name',
            'description',
            'unit',
            'length',
            'weight',
            'height'
        ])
        ->where('hu_pu_type', 2)
        ->get();

        $pu_lists = $pu_lists->map(function ($pu_list) {
            // Variables for centimeters and inches
            $length_cm = $width_cm = $height_cm = null;
            $length_in = $width_in = $height_in = null;

            // Convert based on unit type
            if ($pu_list->unit == 0) {  // Assuming 0 is for centimeters
                // Values in cm
                $length_cm = $pu_list->length;
                $width_cm = $pu_list->weight;
                $height_cm = $pu_list->height;
            } else {  // Assuming 1 is for inches
                // Values in inches
                $length_in = $pu_list->length;
                $width_in = $pu_list->weight;
                $height_in = $pu_list->height;
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

            return $pu_list;
        });      
        return response()->json([
            'status' => 200,
            'message' => 'Unit of Measurement List',
            'result' => $pu_lists
        ]);

    }


     public function store(Request $request)
    {
        try {
        $validated = $request->validate([
        'hu_pu_code' => 'required|string',                
        'hu_pu_type' => 'required|integer',                
        'flex' => 'nullable|string',                        
        'pu_hu_name' => 'required|integer',                
        'description' => 'required|string',               
        'unit' => 'required|integer',                    
        'length' => 'required|float',                   
        'weight' => 'required|float',                        
        'height' => 'required|float',                       
        'hu_empty_weight' => 'nullable|float',              
        'hu_minimum_weight' => 'nullable|float',            
        'hu_loaded_weight' => 'nullable|float',             
        'hu_maximum_weight' => 'nullable|float',          
    ]);

            DB::beginTransaction();
               $hupu_list = Hupu::create($validated);
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'created successfully',
                'result' => $hupu_list,
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
     public function store_pu(Request $request)
    {
        try {
        $validated = $request->validate([
        'hu_pu_code' => 'required|string',                
        'hu_pu_type' => 'required|integer',                
        'flex' => 'nullable|string',                        
        'pu_hu_name' => 'required|integer',                
        'description' => 'required|string',               
        'unit' => 'required|integer',                    
        'length' => 'required|float',                   
        'weight' => 'required|float',                        
        'height' => 'required|float',                       
        'hu_empty_weight' => 'nullable|float',              
        'hu_minimum_weight' => 'nullable|float',            
        'hu_loaded_weight' => 'nullable|float',             
        'hu_maximum_weight' => 'nullable|float',          
    ]);

            DB::beginTransaction();
               $hupu_list = Hupu::create($validated);
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'created successfully',
                'result' => $hupu_list,
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
}
